<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\Enums\AdditionalDataTypeEnum;
use fpoirotte\IDMEF\Types\AbstractType;
use fpoirotte\IDMEF\Types\StringType;
use fpoirotte\IDMEF\Types\BooleanType;
use fpoirotte\IDMEF\Types\IntegerType;
use fpoirotte\IDMEF\Types\RealType;
use fpoirotte\IDMEF\Types\DateTimeType;
use fpoirotte\IDMEF\Types\XmlType;

class AdditionalData extends AbstractClass
{
    protected static $_subclasses = array(
        'type'          => AdditionalDataTypeEnum::class,
        'meaning'       => StringType::class,
        'data'          => true, // Special attribute (see below)
    );

    /* We obey a few rules:
     *
     * -    If neither a type nor data has been set yet, then the caller
     *      is free to set any of them to the value they want.
     *      For data, an attempt is made to convert the value into
     *      its IDMEF type counterpart (eg. PHP integer -> IDMEF integer).
     *      If it is already an IDMEF object, then it is used as-is.
     *      In both cases, the "type" attribute is set accordlingly.
     *      Unrecognized values throw an exception.
     *
     * -    Once the "type" attribute has been set, the "data" attribute
     *      can only be set to an object matching that type, or to a value
     *      that can be converted into an object of that type
     *      (either through type juggling or unserialization).
     *      Any other value will throw an exception.
     *
     * -    Once the "data" attribute has been set, the "type" attribute
     *      can only be set to match the type of the data.
     *      This is true regardless of whether a type had previously
     *      been set or not. Any other value will throw an exception.
     *
     * -    Unsetting the "type" attribute results in both
     *      the "data" and "type" attributes being unset simultaneously.
     *      The opposite is not true: unsetting the data does not change
     *      the expected type.
     *
     * -    The object is valid if and only if a value has been set.
     */

    public function __set(string $property, $value): void
    {
        $type = $this->_children['type'] ?? null;
        $data = $this->_children['data'] ?? null;

        if ($property === 'data') {
            if ($value === null) {
                throw new \InvalidArgumentException($value);
            } elseif (is_string($value)) {
                if ($type !== null) {
                    $cls = str_replace(' ', '', ucwords(str_replace('-', ' ', $type->getValue())));
                    $cls = "\\fpoirotte\\IDMEF\\Types\\${cls}Type";
                    $value  = sprintf('C:%d:"%s":%d:{%s}', strlen($cls), $cls, strlen($value), $value);
                    $value  = unserialize($value);
                } else {
                    $value = new StringType($value);
                }
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
                } elseif (($value instanceof \DOMNode) ||
                          ($value instanceof \SimpleXMLElement) ||
                          ($value instanceof \XMLWriter)) {
                    $value = new XmltextType($value);
                } else {
                    throw new \InvalidArgumentException($value);
                }
            } else {
                throw new \InvalidArgumentException($value);
            }

            if ($type !== null && $value::XML_TYPE !== $type->getValue()) {
                throw new \InvalidArgumentException('Incompatible data');
            }
        } elseif ($property === 'type') {
            if (is_string($value)) {
                $value = new AdditionalDataTypeEnum($value);
            } elseif (!is_object($value) || !($value instanceof AdditionalDataTypeEnum)) {
                throw new \InvalidArgumentException($value);
            }

            if ($data !== null && $data::XML_TYPE !== $value->getValue()) {
                throw new \InvalidArgumentException('Incompatible type');
            }
        }

        if ($property === 'data') {
            $this->acquireLock(self::LOCK_EXCLUSIVE);
            try {
                $value->_parent = $this;
                $this->_children['data'] = $value;

                if ($type === null) {
                    $this->_children['type'] = new AdditionalDataTypeEnum($value::XML_TYPE);
                }
            } finally {
                $this->releaseLock(self::LOCK_EXCLUSIVE);
            }
        } else {
            parent::__set($property, $value);
        }
    }

    public function __unset(string $property): void
    {
        if ($property === 'type') {
            $this->acquireLock(self::LOCK_EXCLUSIVE);
            try {
                unset($this->_children['data']);
                unset($this->_children['type']);
            } finally {
                $this->releaseLock(self::LOCK_EXCLUSIVE);
            }
        } else {
            parent::__unset($property);
        }
    }

    public function isValid(): bool
    {
        return isset($this->_children['data']);
    }
}
