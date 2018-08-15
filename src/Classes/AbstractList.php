<?php

namespace fpoirotte\IDMEF\Classes;

/**
 * Abstract class representing a list of IDMEF classes or attributes.
 */
abstract class AbstractList extends AbstractNode implements \ArrayAccess, \IteratorAggregate, \Countable
{
    protected $_mapping = array();
    protected $_type = null;

    public function count()
    {
        return count($this->_mapping);
    }

    public function offsetExists($offset)
    {
        return isset($this->_mapping[$offset]);
    }

    public function offsetGet($offset)
    {
        if (is_object($offset) && ($offset instanceof $this->_type)) {
            $position = array_search($offset, $this->_mapping, true);
            if ($position === false) {
                throw new \InvalidArgumentException($offset);
            }
            return $position;
        }

        $offset = $this->_validateOffset($offset);
        $cls    = $this->_type;
        if (!isset($this->_mapping[$offset])) {
            $child = new $cls;
            $child->_parent = $this;
            if ($offset === null) {
                $this->_mapping[] = $child;
            } else {
                $this->_mapping[$offset] = $child;
            }
            return $child;
        }
        return $this->_mapping[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $offset = $this->_validateOffset($offset);
        if (!($value instanceof $this->_type)) {
            throw new \InvalidArgumentException($value);
        }

        $value = clone $value;
        $value->_parent = $this;
        if ($offset === null) {
            $this->_mapping[] = $value;
        } else {
            $this->_mapping[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->_mapping[$offset]);
        $this->_mapping = array_values($this->_mapping);
    }

    protected function _validateOffset($offset)
    {
        if ($offset === null) {
            return null;
        }

        $len = count($this->_mapping);
        if ($offset > $len || $offset < -$len) {
            throw new \InvalidArgumentException($offset);
        }

        $len = max($len, 1);
        return ($offset + $len) % $len;
    }

    public function getIterator($path = null, $value = null, $minDepth = 0, $maxDepth = -1)
    {
        foreach ($this->_mapping as $child) {
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
