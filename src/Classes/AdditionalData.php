<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\AdditionalDataTypeEnum;
use \fpoirotte\IDMEF\Types\AbstractType;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Types\BooleanType;
use \fpoirotte\IDMEF\Types\IntegerType;
use \fpoirotte\IDMEF\Types\RealType;
use \fpoirotte\IDMEF\Types\DateTimeType;
use \fpoirotte\IDMEF\Types\XmlType;

class AdditionalData extends AbstractClass
{
    protected static $_subclasses = array(
        'type'          => AdditionalDataTypeEnum::class,
        'meaning'       => StringType::class,
        'data'          => true, // Gets a special handling (see below)
    );

    public function __set($offset, $value)
    {
        if ($offset === 'data') {
            if ($value === null) {
                // Do nothing
            } elseif (is_string($value)) {
                $value = new StringType($value);
            } elseif (is_bool($value)) {
                $value = new BooleanType($value);
            } elseif (is_int($value)) {
                $value = new IntegerType($value);
            } elseif (is_float($value)) {
                $value = new RealType($value);
            } elseif (is_object($value)) {
                if ($value instanceof AbstractType) {
                    $value = clone $value;
                } elseif ($value instanceof \DateTimeInterface) {
                    $value = new DateTimeType($value);
                } elseif ($value instanceof \XML) {
                    $value = new XmlType($value);
                } else {
                    throw new \InvalidArgumentException($value);
                }
            } else {
                throw new \InvalidArgumentException($value);
            }

            $value->_parent = $this;
            $this->_children['data'] = $value;
        } else {
            parent::__set($offset, $value);
        }

        if (isset($this->_children['data'], $this->_children['type'])) {
            $data = $this->_children['data'];
            if ($data::XML_TYPE !== $this->_children['type']->getValue()) {
                throw new \LogicException();
            }
        }
    }
}
