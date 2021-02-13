<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types;

class PortlistType extends AbstractType
{
    const XML_TYPE = 'portlist';

    public function __construct($value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException($value);
        }
        $this->unserialize($value);
    }

    protected function unserialize(string $serialized): void
    {
        foreach (explode(',', $serialized) as $ports) {
            foreach (explode('-', $ports, 2) as $port) {
                if ($port === '' || strspn($port, '1234567890') !== strlen($port)) {
                    throw new \InvalidArgumentException($serialized);
                }
                $port = (int) $port;
                if ($port < 1 || $port > 65535) {
                    throw new \InvalidArgumentException($serialized);
                }
            }
        }
        $this->_value = $serialized;
    }
}
