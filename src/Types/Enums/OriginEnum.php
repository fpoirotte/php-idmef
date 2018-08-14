<?php

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class OriginEnum extends AbstractEnum
{
    protected $_choices = array('unknown', 'vendor-specific', 'user-specific', 'bugtraqid', 'cve', 'osvdb');
}
