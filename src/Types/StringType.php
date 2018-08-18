<?php

namespace fpoirotte\IDMEF\Types;

class StringType extends AbstractType
{
    const XML_TYPE = 'string';

    public function __construct($value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException($value);
        }
        $this->unserialize($value);
    }

    public function unserialize($serialized)
    {
        $this->_value = $serialized;
    }
}
