<?php
declare(strict_types=1);

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

    protected function unserialize(string $serialized): void
    {
        $this->_value = $serialized;
    }
}
