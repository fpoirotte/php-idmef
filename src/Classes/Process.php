<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Types\IntegerType;
use \fpoirotte\IDMEF\Classes\ArgumentList;
use \fpoirotte\IDMEF\Classes\EnvironmentList;

class Process extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'name'          => StringType::class,
        'pid'           => IntegerType::class,
        'path'          => StringType::class,
        'arg'           => ArgumentList::class,
        'env'           => EnvironmentList::class,
    );
}
