<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Serializers;

use fpoirotte\IDMEF\Classes\IDMEFMessage;

/**
 * Abstract class representing a serializer for IDMEF messages.
 */
abstract class AbstractSerializer
{
    abstract public function serialize(IDMEFMessage $message): string;

    abstract public function unserialize(string $serialized): IDMEFMessage;
}
