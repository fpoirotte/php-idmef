<?php

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class FileAccessEnum extends AbstractEnum
{
    protected $_choices = array('noAccess', 'read', 'write', 'execute',
                                'search', 'delete', 'executeAs',
                                'changePermissions', 'takeOwnership');
}
