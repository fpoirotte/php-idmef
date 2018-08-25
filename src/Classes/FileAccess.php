<?php

namespace fpoirotte\IDMEF\Classes;

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
