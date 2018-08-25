<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\ArgumentList;

class WebService extends AbstractClass
{
    protected static $_subclasses = array(
        'url'           => StringType::class,
        'cgi'           => StringType::class,
        'http-method'   => StringType::class,
        'arg'           => ArgumentList::class,
    );
}
