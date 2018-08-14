<?php

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class RatingEnum extends AbstractEnum
{
    protected $_choices = array('low', 'medium', 'high', 'numeric');
}
