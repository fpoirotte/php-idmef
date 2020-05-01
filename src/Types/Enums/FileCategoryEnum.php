<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class FileCategoryEnum extends AbstractEnum
{
    protected $_choices = array('current', 'original');
}
