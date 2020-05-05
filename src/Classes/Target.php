<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\Enums\DecoyEnum;
use fpoirotte\IDMEF\Types\StringType;

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
        'File'          => FileList::class,
    );
}
