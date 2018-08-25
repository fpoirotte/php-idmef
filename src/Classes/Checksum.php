<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\AlgorithmEnum;
use \fpoirotte\IDMEF\Types\StringType;

class Checksum extends AbstractClass
{
    protected static $_subclasses = array(
        'algorithm'     => AlgorithmEnum::class,
        'value'         => StringType::class,
        'key'           => StringType::class,
    );

    protected static $_mandatory = array(
        'algorithm',
        'value',
    );
}
