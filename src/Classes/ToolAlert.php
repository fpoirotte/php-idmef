<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

class ToolAlert extends AbstractClass
{
    protected static $_subclasses = array(
        'name'              => StringType::class,
        'command'           => StringType::class,
        'alertident'        => AlertIdentList::class,
    );

    protected static $_mandatory = array(
        'name',
        'alertident',
    );
}
