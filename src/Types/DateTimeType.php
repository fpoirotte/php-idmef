<?php

namespace fpoirotte\IDMEF\Types;

class DateTimeType extends AbstractType
{
    const XML_TYPE = 'date-time';
    const FORMAT = 'Y-m-d\\TH:i:s.uP';

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
        return $this->_value->format(static::FORMAT);
    }

    public function unserialize($serialized)
    {
        $formats = array(
            'Y-m-d\\TH:i:s.uP',
            'Y-m-d\\TH:i:s,uP',
            'Y-m-d\\TH:i:sP',
        );
        foreach ($formats as $format) {
            $res = \DateTimeImmutable::createFromFormat($format, $serialized);
            if ($res !== false) {
                $this->_value = $res;
                return;
            }
        }
        throw new \InvalidArgumentException($serialized);
    }
}
