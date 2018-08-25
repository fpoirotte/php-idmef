<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\UserIdTypeEnum;
use \fpoirotte\IDMEF\Types\StringType;

class UserId extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'type'          => UserIdTypeEnum::class,
        'tty'           => StringType::class,
        'name'          => UserNameList::class,
        'number'        => UserNumberList::class,
    );
}
