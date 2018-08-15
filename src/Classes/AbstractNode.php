<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\AbstractType;

/**
 * Abstract class representing a node in the IDMEF tree.
 */
abstract class AbstractNode implements \IteratorAggregate
{
    protected static $_subclasses = array();
    protected $_parent = null;
    protected $_children = array();

    public function __get($prop)
    {
        throw new \InvalidArgumentException($prop);
    }

    public function __set($prop, $value)
    {
        throw new \InvalidArgumentException($prop);
    }

    public function isValid()
    {
        throw new \RuntimeException();
    }

    public function getParent()
    {
        return $this->_parent;
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
    }

    public function getIterator($path = null, $value = null, $minDepth = 0, $maxDepth = -1)
    {
        $matchesPath = true;
        $matchesValue = ($value === null) || (($this instanceof AbstractType) && $this->getValue() === $value);
        $matchesDepth = ($minDepth <= 0);

        if ($path !== null && $matchesDepth) {
            $pathParts  = explode('.', $path);
            $thisParts  = explode('.', $this->getPath());
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
            yield $this;
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
