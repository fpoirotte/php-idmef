<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\UserCategoryEnum;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\UserIdList;

class User extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'category'      => UserCategoryEnum::class,
        'UserId'        => UserIdList::class,
    );
}
