<?php

namespace fpoirotte\IDMEF\Serializers;

use \fpoirotte\IDMEF\Classes\IDMEFMessage;

/**
 * Abstract class representing a serializer for IDMEF messages.
 */
abstract class AbstractSerializer
{
    abstract public function serialize(IDMEFMessage $message);

    abstract protected function _unserialize($serialized);

    public function unserialize($serialized)
    {
        if (!is_string($serialized)) {
            throw new \InvalidArgumentException($serialized);
        }
        return $this->_unserialize($serialized);
    }
}
