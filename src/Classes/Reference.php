<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\OriginEnum;
use \fpoirotte\IDMEF\Types\StringType;

class Reference extends AbstractClass
{
    protected static $_subclasses = array(
        'origin'        => OriginEnum::class,
        'meaning'       => StringType::class,
        'name'          => StringType::class,
        'url'           => StringType::class,
    );

    protected static $_mandatory = array(
        'name',
        'url',
    );
}
