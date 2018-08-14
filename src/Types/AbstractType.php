<?php

namespace fpoirotte\IDMEF\Types;

use \fpoirotte\IDMEF\Classes\AbstractNode;

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

    public function getPath()
    {
        if ($this->_parent === null) {
            return null;
        }

        return $this->_parent->getPath($this) . '.' . $this->_parent->__get($this);
    }
}

