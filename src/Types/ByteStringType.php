<?php

namespace fpoirotte\IDMEF\Types;

class ByteStringType extends StringType
{
    const XML_TYPE = 'byte-string';

    public function __toString()
    {
        return base64_encode(parent::__toString());
    }

    public function unserialize($serialized)
    {
        $value = base64_decode($serialized, true);
        if ($value === false) {
            throw new \InvalidArgumentException($serialized);
        }
        $this->_value = $value;
    }
}
