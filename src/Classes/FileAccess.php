<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Classes\UserId;
use \fpoirotte\IDMEF\Classes\PermissionList;

class FileAccess extends AbstractClass
{
    protected static $_subclasses = array(
        'UserId'        => UserId::class,
        'Permission'    => PermissionList::class,
    );

    protected static $_mandatory = array(
        'UserId',
        'Permission',
    );
}
