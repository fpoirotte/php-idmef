<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types;

/**
 * Abstract class representing an enumeration.
 */
abstract class AbstractEnum extends AbstractType
{
    protected $_choices = array();

    public function __construct($value)
    {
        $this->unserialize($value);
    }

    protected function unserialize(string $serialized): void
    {
        if (!in_array($serialized, $this->_choices, true)) {
            throw new \InvalidArgumentException($serialized);
        }
        $this->_value = $serialized;
    }
}
