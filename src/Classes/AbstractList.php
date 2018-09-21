<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\LockException;

/**
 * Abstract class representing a list of IDMEF classes or attributes.
 */
abstract class AbstractList extends AbstractNode implements \ArrayAccess, \IteratorAggregate, \Countable
{
    protected $_type = null;

    public function getItemsType()
    {
        return $this->_type;
    }

    public function count()
    {
        return count($this->_children);
    }

    protected function changeChildParent($child)
    {
        $child->_parent = $this;
    }

    public function offsetExists($offset)
    {
        try {
            $this->_validateOffset($offset);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
        return ($offset !== null);
    }

    public function offsetGet($offset)
    {
        if (is_object($offset) && ($offset instanceof $this->_type)) {
            $position = array_search($offset, $this->_children, true);
            if ($position === false) {
                throw new \InvalidArgumentException($offset);
            }
            return $position;
        }

        $offset = $this->_validateOffset($offset);
        if (!isset($this->_children[$offset])) {
            $this->acquireLock(self::LOCK_EXCLUSIVE);
            try {
                $cls = $this->_type;
                $child = new $cls;
                $this->changeChildParent($child);
                if ($offset === '>>') {
                    $this->_children[] = $child;
                } elseif ($offset === '<<') {
                    array_unshift($this->_children, $child);
                } else {
                    $this->_children[$offset] = $child;
                }
            } finally {
                $this->releaseLock(self::LOCK_EXCLUSIVE);
            }
            return $child;
        }
        return $this->_children[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $offset = $this->_validateOffset($offset);
        if (!($value instanceof $this->_type)) {
            throw new \InvalidArgumentException($value);
        }

        $this->acquireLock(self::LOCK_EXCLUSIVE);
        try {
            $value = clone $value;
            $this->changeChildParent($value);
            if ($offset === '>>') {
                $this->_children[] = $value;
            } elseif ($offset === '<<') {
                array_unshift($this->_children, $child);
            } else {
                $this->_children[$offset] = $value;
            }
        } finally {
            $this->releaseLock(self::LOCK_EXCLUSIVE);
        }
    }

    public function offsetUnset($offset)
    {
        $this->acquireLock(self::LOCK_EXCLUSIVE);
        try {
            unset($this->_children[$offset]);
            $this->_children = array_values($this->_children);
        } finally {
            $this->releaseLock(self::LOCK_EXCLUSIVE);
        }
    }

    protected function _validateOffset($offset)
    {
        if ($offset === null || $offset === '>>') {
            return '>>';
        }

        if ($offset === '<<') {
            return '<<';
        }

        $len = count($this->_children);
        if ($offset === $len) {
            return $offset;
        }

        if ($offset > $len || $offset < -$len) {
            throw new \InvalidArgumentException($offset);
        }

        $len = max($len, 1);
        return ($offset + $len) % $len;
    }


    /**
     * Specialized iterator factory that acts as a passthrough
     * and does not modify the $minDepth/$maxDepth parameters.
     */
    public function getIterator($path = null, $value = null, $minDepth = 0, $maxDepth = 0)
    {
        foreach ($this->_children as $child) {
            foreach ($child->getIterator($path, $value, $minDepth, $maxDepth) as $subpath => $subnode) {
                yield $subpath => $subnode;
            }
        }
    }

    public function getPath($child = null)
    {
        return ($this->_parent === null) ? null : $this->_parent->getPath($this);
    }
}
