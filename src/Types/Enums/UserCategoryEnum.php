<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use fpoirotte\IDMEF\Types\AbstractEnum;

class UserCategoryEnum extends AbstractEnum
{
    protected $_choices = array('unknown', 'application', 'os-device');
}
