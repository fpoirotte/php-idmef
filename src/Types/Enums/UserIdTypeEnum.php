<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class UserIdTypeEnum extends AbstractEnum
{
    protected $_choices = array('current-user', 'original-user', 'target-user',
                                'user-privs', 'current-group', 'group-privs',
                                'other-privs');
}
