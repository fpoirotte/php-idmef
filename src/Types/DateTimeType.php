<?php

namespace fpoirotte\IDMEF\Types;

class DateTimeType extends AbstractType
{
    const XML_TYPE = 'date-time';

    public function __construct($value)
    {
        if (!is_object($value) || !($value instanceof \DateTimeInterface)) {
            throw new \InvalidArgumentException($value);
        }
        $this->_value = $value;
    }
}
