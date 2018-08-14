<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\UserIdTypeEnum;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\UserNameList;
use \fpoirotte\IDMEF\Classes\UserNumberList;

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
