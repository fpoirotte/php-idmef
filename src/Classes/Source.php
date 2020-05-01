<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\SpoofedEnum;
use \fpoirotte\IDMEF\Types\StringType;

class Source extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'spoofed'       => SpoofedEnum::class,
        'interface'     => StringType::class,
        'Node'          => Node::class,
        'User'          => User::class,
        'Process'       => Process::class,
        'Service'       => Service::class,
    );
}
