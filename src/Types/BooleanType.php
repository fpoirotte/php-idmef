<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types;

class BooleanType extends AbstractType
{
    const XML_TYPE = 'boolean';

    public function __construct($value)
    {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException($value);
        }
        $this->_value = $value;
    }

    public function __toString(): string
    {
        return $this->_value ? 'true' : 'false';
    }

    public function unserialize($serialized)
    {
        $possible = array('false', 'true');
        $pos = array_search($serialized, $possible, true);
        if ($pos === false) {
            throw new \InvalidArgumentException($serialized);
        }
        $this->_value = (bool) $pos;
    }
}
