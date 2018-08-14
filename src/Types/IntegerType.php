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
}
