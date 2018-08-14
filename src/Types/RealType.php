<?php

namespace fpoirotte\IDMEF\Types;

class RealType extends AbstractType
{
    const XML_TYPE = 'real';

    public function __construct($value)
    {
        if (!is_float($value)) {
            throw new \InvalidArgumentException($value);
        }
        $this->_value = $value;
    }
}
