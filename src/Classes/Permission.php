<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\Enums\FilePermEnum;

class Permission extends AbstractClass
{
    protected static $_subclasses = array(
        'perms'         => FilePermEnum::class,
    );

    protected static $_mandatory = array(
        'perms',
    );
}
