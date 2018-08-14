<?php

namespace fpoirotte\IDMEF\Types;

class XmlType extends AbstractType
{
    const XML_TYPE = 'xmltext';

    public function __construct($value)
    {
        if (!is_object($value) || !($value instanceof \XML)) {
            throw new \InvalidArgumentException($value);
        }
        $this->_value = $value;
    }
}
