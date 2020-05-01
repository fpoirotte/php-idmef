<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class FilePermEnum extends AbstractEnum
{
    protected $_choices = array('noAccess', 'read', 'write', 'execute',
                                'search', 'delete', 'executeAs',
                                'changePermissions', 'takeOwnership');
}
