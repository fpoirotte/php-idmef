<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

class ToolAlert extends AbstractClass
{
    protected static $_subclasses = array(
        'name'              => StringType::class,
        'command'           => StringType::class,
        'alertident'        => AlertIdentList::class,
    );
}
