<?php

namespace fpoirotte\IDMEF\Types;

class ByteType extends CharacterType
{
    const XML_TYPE = 'byte';

    public function __toString()
    {
        return base64_encode(parent::__toString());
    }
}
