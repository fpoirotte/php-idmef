<?php

namespace fpoirotte\IDMEF\Types;

class DateTimeType extends AbstractType
{
    const XML_TYPE = 'date-time';

    public function __construct($value)
    {
        if (!is_object($value) || !($value instanceof \DateTimeInterface)) {
            throw new \InvalidArgumentException($value);
        }
        if ($value instanceof \DateTime) {
            $value = \DateTimeImmutable::createFromMutable($value);
        }
        $this->_value = $value;
    }

    public function __toString()
    {
        return $this->_value->format('Y-m-d\\TH:i:s.uP');
    }
}
