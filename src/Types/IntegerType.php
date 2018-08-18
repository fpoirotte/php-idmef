<?php

namespace fpoirotte\IDMEF\Types;

class IntegerType extends AbstractType
{
    const XML_TYPE = 'integer';

    public function __construct($value)
    {
        if (!is_int($value)) {
            throw new \InvalidArgumentException($value);
        }
        $this->_value = $value;
    }

    public function unserialize($serialized)
    {
        $serialized = strtolower($serialized);
        if (sscanf($serialized, '%d', $this->_value) !== 1 &&
            sscanf($serialized, '%x', $this->_value) !== 1) {
            throw new \InvalidArgumentException($serialized);
        }
    }
}
