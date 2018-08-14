<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\SpoofedEnum;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\Node;
use \fpoirotte\IDMEF\Classes\User;
use \fpoirotte\IDMEF\Classes\Process;
use \fpoirotte\IDMEF\Classes\Service;

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
