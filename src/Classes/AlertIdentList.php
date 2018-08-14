<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

class AlertIdentList extends AbstractList
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'analyzerid'    => StringType::class,
    );
}
