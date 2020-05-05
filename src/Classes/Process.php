<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\StringType;
use fpoirotte\IDMEF\Types\IntegerType;

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

    protected static $_mandatory = array(
        'name',
    );
}
