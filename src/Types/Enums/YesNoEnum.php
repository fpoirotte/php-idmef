<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

abstract class YesNoEnum extends AbstractEnum
{
    protected $_choices = array('unknown', 'yes', 'no');
}
