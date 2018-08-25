<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

class alertident extends AbstractClass
{
    protected static $_subclasses = array(
        'alertident'    => StringType::class,
        'analyzerid'    => StringType::class,
    );
}
