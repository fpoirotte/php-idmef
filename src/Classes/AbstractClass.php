<?php

namespace fpoirotte\IDMEF\Classes;

/**
 * Abstract class representing an IDMEF class.
 */
abstract class AbstractClass extends AbstractNode
{
    protected $_my_subclasses = array();

    public function __construct()
    {
        $subclasses = array();
        foreach (array_reverse(class_parents($this)) as $ancestor) {
            $subclasses[] = $ancestor::$_subclasses;
        }
        $subclasses[] = static::$_subclasses;
        $this->_my_subclasses = call_user_func_array('array_merge', $subclasses);
    }

    protected function _normalizeProperty($prop)
    {
        if (!is_string($prop)) {
            throw new \InvalidArgumentException($prop);
        }

        $mask = 'abcdefghijklmnopqrstuvwxyz-_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (strspn($prop, $mask) !== strlen($prop)) {
            throw new \InvalidArgumentException($prop);
        }

        $normProp = str_replace(array(' ', '-', '_'), '', ucwords($prop, ' -_'));
        if (isset($this->_my_subclasses[$normProp])) {
            return $normProp;
        }
        if (isset($this->_my_subclasses[$prop])) {
            return $prop;
        }
        throw new \InvalidArgumentException($prop);
    }

    public function __get($prop)
    {
        $key = array_search($prop, $this->_children, true);
        if ($key !== false) {
            return $key;
        }

        $prop = $this->_normalizeProperty($prop);
        if (isset($this->_children[$prop])) {
            return $this->_children[$prop];
        }

        $type = $this->_my_subclasses[$prop];
        if (is_a($type, AbstractClass::class, true) ||
            is_a($type, AbstractList::class, true)) {
            $child = new $type();
            $this->_children[$prop] = $child;
            $child->_parent = $this;
            return $child;
        }
        return null;
    }

    public function __set($prop, $value)
    {
        $prop = $this->_normalizeProperty($prop);
        $type = $this->_my_subclasses[$prop];

        if (!is_object($value)) {
            if (is_a($type, AbstractClass::class, true) ||
                is_a($type, AbstractList::class, true)) {
                throw new \InvalidArgumentException($value);
            }
            $value = new $type($value);
        }

        if (!($value instanceof $type)) {
            throw new \InvalidArgumentException($value);
        }

        $value = clone $value;
        $value->_parent = $this;
        $this->_children[$prop] = $value;
    }

    public function __unset($prop)
    {
        $prop = $this->_normalizeProperty($prop);
        unset($this->_children[$prop]);
    }
}
