<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types;

use fpoirotte\IDMEF\Classes\AbstractNode;

/**
 * Abstract class representing an IDMEF type.
 */
abstract class AbstractType extends AbstractNode
{
    protected $_value;
    const XML_TYPE = null;

    public function __toString()
    {
        return (string) $this->_value;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getPath(): ?string
    {
        if ($this->_parent === null) {
            return null;
        }

        return $this->_parent->getPath($this) . '.' . $this->_parent->__get($this);
    }

    final public function __unserialize(array $data): void
    {
        if (!isset($data['value'])) {
            throw RuntimeException();
        }
        $this->unserialize($data['value']);
    }

    abstract protected function unserialize(string $serialized): void;

    final public function __serialize(): array
    {
        return ['value' => (string) $this];
    }
}
