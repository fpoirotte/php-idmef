<?php

namespace fpoirotte\IDMEF\Types;

class ByteStringType extends StringType
{
    const XML_TYPE = 'byte-string';

    public function __toString()
    {
        return base64_encode(parent::__toString());
    }
}
