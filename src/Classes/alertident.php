<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class alertident extends AbstractClass
{
    protected static $_subclasses = array(
        'alertident'    => StringType::class,
        'analyzerid'    => StringType::class,
    );
}
