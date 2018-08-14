<?php

namespace fpoirotte\IDMEF\Types;

class CharacterType extends AbstractType
{
    const XML_TYPE = 'character';

    public function __construct($value)
    {
        if (!is_string($value) || strlen($value) != 1) {
            throw new \InvalidArgumentException($value);
        }
        $this->_value = $value;
    }
}
