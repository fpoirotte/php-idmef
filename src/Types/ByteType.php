<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types;

class ByteType extends CharacterType
{
    const XML_TYPE = 'byte';

    public function __toString(): string
    {
        return base64_encode(parent::__toString());
    }

    public function unserialize($serialized)
    {
        $value = base64_decode($serialized, true);
        if ($value === false || strlen($value) !== 1) {
            throw new \InvalidArgumentException($serialized);
        }
        $this->_value = $value;
    }
}
