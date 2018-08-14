<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\DecoyEnum;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\Node;
use \fpoirotte\IDMEF\Classes\User;
use \fpoirotte\IDMEF\Classes\Process;
use \fpoirotte\IDMEF\Classes\Service;
use \fpoirotte\IDMEF\Classes\File;

class Target extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'decoy'         => DecoyEnum::class,
        'interface'     => StringType::class,
        'Node'          => Node::class,
        'User'          => User::class,
        'Process'       => Process::class,
        'Service'       => Service::class,
        'File'          => File::class,
    );
}
