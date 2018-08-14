<?php

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class ActionCategoryEnum extends AbstractEnum
{
    protected $_choices = array('block-installed', 'notification-sent', 'taken-offline', 'other');
}
