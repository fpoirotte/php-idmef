<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\Enums\UserCategoryEnum;
use fpoirotte\IDMEF\Types\StringType;

class User extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'category'      => UserCategoryEnum::class,
        'UserId'        => UserIdList::class,
    );

    protected static $_mandatory = array(
        'UserId',
    );
}
