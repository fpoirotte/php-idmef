<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

class WebService extends AbstractClass
{
    protected static $_subclasses = array(
        'url'           => StringType::class,
        'cgi'           => StringType::class,
        'http-method'   => StringType::class,
        'arg'           => ArgumentList::class,
    );

    protected static $_mandatory = array(
        'url',
    );
}
