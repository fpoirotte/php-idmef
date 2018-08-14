<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\AlertIdentList;

class ToolAlert extends Alert
{
    protected static $_subclasses = array(
        'name'              => StringType::class,
        'command'           => StringType::class,
        // Named "alertident" in RFC 4765 and used as both a class
        // and an attribute, which is not really practical.
        // Therefore, we chose to rename it and use it like a class.
        'alerts'            => AlertIdentList::class,
    );
}
