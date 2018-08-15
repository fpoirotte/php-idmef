<?php

namespace fpoirotte\IDMEF\Types;

/**
 * Abstract class representing an enumeration.
 */
abstract class AbstractEnum extends AbstractType
{
    protected $_choices = array();

    public function __construct($value)
    {
        if (!in_array($value, $this->_choices, true)) {
            throw new \InvalidArgumentException($value);
        }
        $this->_value = $value;
    }
}
