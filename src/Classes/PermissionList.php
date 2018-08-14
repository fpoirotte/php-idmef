<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\FileAccessEnum;

class PermissionList extends AbstractList
{
    protected $_type = FileAccessEnum::class;
}
