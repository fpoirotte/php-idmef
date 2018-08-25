<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\ReferenceList;

class Classification extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'text'          => StringType::class,
        'Reference'     => ReferenceList::class,
    );

    protected static $_mandatory = array(
        'text',
    );
}
