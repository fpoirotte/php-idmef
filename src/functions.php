<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF;

use fpoirotte\IDMEF\Types\AbstractType;

if (!function_exists(__NAMESPACE__ . '\\unserialize_type')) {

/**
 * Attempt to create an instance of the given IDMEF type
 * using the provided data.
 */
function unserialize_type(string $cls, string $data): AbstractType
{
    if (!is_subclass_of($cls, AbstractType::class)) {
        throw new \InvalidArgumentException("Invalid class for IDMEF type '$cls'");
    }
    $serialized = sprintf('O:%d:"%s":1:{s:5:"value";s:%d:"%s";}', strlen($cls), $cls, strlen($data), $data);
    return unserialize($serialized);
}

} // if (!function_exists(...))
