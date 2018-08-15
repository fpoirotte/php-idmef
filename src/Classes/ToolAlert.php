<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\AlertIdentList;

class ToolAlert extends Alert
{
    protected static $_subclasses = array(
        'name'              => StringType::class,
        'command'           => StringType::class,
        // FIXME: "alertident" is defined as both a class and an attribute
        // in RFC 4765, which is not really practical.
        // Therefore, we chose to rename it and use it like a class.
        'alerts'            => AlertIdentList::class,
    );
}
