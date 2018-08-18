<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\AbstractType;
use \fpoirotte\IDMEF\LockException;

/**
 * Abstract class representing a node in the IDMEF tree.
 */
abstract class AbstractNode implements \IteratorAggregate
{
    protected static $_subclasses = array();
    protected $_parent = null;
    protected $_children = array();
    protected $_locks = array(0, 0);

    const LOCK_EXCLUSIVE = 0;
    const LOCK_SHARED = 1;

    public function __get($prop)
    {
        throw new \InvalidArgumentException($prop);
    }

    public function __set($prop, $value)
    {
        throw new \InvalidArgumentException($prop);
    }

    public function __isset($prop)
    {
        throw new \InvalidArgumentException($prop);
    }

    public function __unset($prop)
    {
        throw new \InvalidArgumentException($prop);
    }

    public function isValid()
    {
        try {
            
        } finally {
            
        }
    }

    public function acquireLock($mode = self::LOCK_EXCLUSIVE, $recursive = false)
    {
        if (!in_array($mode, array(self::LOCK_EXCLUSIVE, self::LOCK_SHARED))) {
            throw new \InvalidArgumentException($mode);
        }

        if ($mode === self::LOCK_EXCLUSIVE && $this->isLocked()) {
            throw new LockException();
        }

        $locked = array();
        try {
            if ($recursive) {
                foreach ($this->_children as $child) {
                    $child->acquireLock($mode, $recursive);
                    $locked[] = $child;
                }
            }
        } catch (\Exception $e) {
            foreach ($locked as $child) {
                $child->releaseLock($mode, $recursive);
            }
            throw $e;
        }

        $this->_locks[$mode]++;
    }

    public function releaseLock($mode = self::LOCK_EXCLUSIVE, $recursive = false)
    {
        if (!in_array($mode, array(self::LOCK_EXCLUSIVE, self::LOCK_SHARED))) {
            throw new \InvalidArgumentException($mode);
        }

        if (!$this->isLocked($mode)) {
            throw new LockException();
        }

        if ($recursive) {
            foreach ($this->_children as $child) {
                $child->releaseLock($mode, $recursive);
            }
        }

        $this->_locks[$mode]--;
    }

    public function isLocked($mode = null)
    {
        $locks = (isset($this->_locks[$mode]) ? $this->_locks[$mode] : array_sum($this->_locks));
        return $locks > 0;
    }

    public function getParent()
    {
        $parent = $this->_parent;
        if (is_object($parent) && ($parent instanceof AbstractList)) {
            $parent = $parent->getParent();
        }
        return $parent;
    }

    public function getPath()
    {
        $cls = substr(strrchr(get_class($this), '\\'), 1);
        if ($this->_parent !== null) {
            $position = '';
            if ($this->_parent instanceof AbstractList) {
                $position = '(' . $this->_parent[$this] . ')';
            }

            return $this->_parent->getPath($this) . ".$cls$position";
        }
        return $cls;
    }

    public function __clone()
    {
        $children = array();
        foreach ($this->_children as $k => $v) {
            $child = clone $v;
            $child->_parent = $this;
            $children[$k] = $child;
        }
        $this->_children = $children;
        $this->_parent = null;
        $this->_locks = array(0, 0);
    }

    public function getIterator($path = null, $value = null, $minDepth = 1, $maxDepth = 1)
    {
        $matchesPath = true;
        $matchesValue = ($value === null) || (($this instanceof AbstractType) && $this->getValue() === $value);
        $matchesDepth = ($minDepth <= 0);
        $myPath = $this->getPath();

        if ($path !== null && $matchesDepth) {
            $pathParts  = explode('.', $path);
            $thisParts  = explode('.', $myPath);
            $pathLen    = count($pathParts);
            $thisLen    = count($thisParts);
            $isClass    = false;

            // "{class}"
            if ($pathLen === 1 && strlen($pathParts[0]) > 2 &&
                $pathParts[0][0] === '{' && substr($pathParts[0], -1) === '}') {
                $isClass = true;
                if (substr($pathParts[0], 1, -1) === get_class($this)) {
                    $pathParts = $thisParts;
                    $pathLen   = count($pathParts);
                }
            }

            if ($pathLen < $thisLen && !$isClass) {
                return;
            } elseif ($pathLen === $thisLen) {
                $mask = 'abcdefghijklmnopqrstuvwxyz-_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                foreach ($pathParts as $i => $pathPart) {
                    $thisPart   = $thisParts[$i];
                    $pathBase   = (string) substr($pathPart, 0, strcspn($pathPart, '('));
                    $thisBase   = (string) substr($thisPart, 0, strcspn($thisPart, '('));

                    if (!$isClass && strspn($pathBase, $mask) !== strlen($pathBase)) {
                        throw new \InvalidArgumentException($pathPart);
                    }

                    $normBase = str_replace(array(' ', '-', '_'), '', ucwords($pathBase, ' -_'));
                    if ($thisBase !== $pathBase && $thisBase !== $normBase) {
                        $matchesPath = false;
                        break;
                    }

                    $pathIndex  = (string) substr($pathPart, strcspn($pathPart, '('));
                    $thisIndex  = (string) substr($thisPart, strcspn($thisPart, '('));
                    if ($pathIndex !== '(*)' && $pathIndex !== $thisIndex) {
                        $matchesPath = false;
                        break;
                    }
                }
            } else {
                $matchesPath = false;
            }
        }

        if ($matchesPath && $matchesValue && $matchesDepth) {
            yield $myPath => $this;
        }

        if ($minDepth > 0) {
            $minDepth--;
        }
        if (!$maxDepth) {
            return;
        } else {
            $maxDepth = max(-1, $maxDepth - 1);
        }

        foreach ($this->_children as $child) {
            foreach ($child->getIterator($path, $value, $minDepth, $maxDepth) as $subpath => $subnode) {
                yield $subpath => $subnode;
            }
        }
    }
}
