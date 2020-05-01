<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

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
