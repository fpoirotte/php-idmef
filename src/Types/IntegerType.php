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

        if (!strncasecmp($serialized, '0x', 2)) {
            if (sscanf((string) substr($serialized, 2), '%x%[^[]]', $value, $dummy) !== 1) {
                throw new \InvalidArgumentException($serialized);
            }
        } elseif (sscanf($serialized, '%d%[^[]]', $value, $dummy) !== 1) {
            throw new \InvalidArgumentException($serialized);
        }

        $this->_value = $value;
    }
}
