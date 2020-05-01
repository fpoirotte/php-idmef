<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types;

class RealType extends AbstractType
{
    const XML_TYPE = 'real';

    public function __construct($value)
    {
        if (!is_float($value)) {
            throw new \InvalidArgumentException($value);
        }
        $this->_value = $value;
    }

    public function unserialize($serialized)
    {
        $value = filter_var(
            str_replace(',', '.', $serialized),
            FILTER_VALIDATE_FLOAT,
            array("flags" => FILTER_FLAG_ALLOW_FRACTION)
        );

        if ($value === false) {
            throw new \InvalidArgumentException($serialized);
        }
        $this->_value = $value;
    }
}
