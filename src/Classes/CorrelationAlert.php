<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\StringType;

class CorrelationAlert extends AbstractClass
{
    protected static $_subclasses = array(
        'name'              => StringType::class,
        'alertident'        => AlertIdentList::class,
    );

    protected static $_mandatory = array(
        'name',
        'alertident',
    );
}
