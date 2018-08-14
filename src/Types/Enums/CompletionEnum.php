<?php

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class CompletionEnum extends AbstractEnum
{
    protected $_choices = array('failed', 'succeeded');
}
