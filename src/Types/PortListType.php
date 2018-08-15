<?php

namespace fpoirotte\IDMEF\Types;

class PortListType extends AbstractType
{
    const XML_TYPE = 'portlist';

    public function __construct($value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException($value);
        }

        foreach (explode(',', $value) as $ports) {
            foreach (explode('-', $ports, 2) as $port) {
                if ($port === '' || strspn($port, '1234567890') !== strlen($port)) {
                    throw new \InvalidArgumentException($value);
                }
                $port = (int) $port;
                if ($port < 1 || $port > 65535) {
                    throw new \InvalidArgumentException($value);
                }
            }
        }

        $this->_value = $value;
    }
}
