<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use fpoirotte\IDMEF\Types\AbstractEnum;

class SeverityEnum extends AbstractEnum
{
    protected $_choices = array('info', 'low', 'medium', 'high');
}
