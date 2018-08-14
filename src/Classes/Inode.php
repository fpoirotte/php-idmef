<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\IntegerType;
use \fpoirotte\IDMEF\Types\DateTimeType;

class Inode extends AbstractClass
{
    protected static $_subclasses = array(
        'change-time'       => DateTimeType::class,
        'number'            => IntegerType::class,
        'major-device'      => IntegerType::class,
        'minor-device'      => IntegerType::class,
        'c-major-device'    => IntegerType::class,
        'c-minor-device'    => IntegerType::class,
    );
}
