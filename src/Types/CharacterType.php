<?php
declare(strict_types=1);

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

    protected function unserialize(string $serialized): void
    {
        if (strlen($serialized) !== 1) {
            throw new \InvalidArgumentException($serialized);
        }
        $this->_value = $serialized;
    }
}
