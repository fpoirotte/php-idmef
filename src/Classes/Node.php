<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\NodeCategoryEnum;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\AddressList;

class Node extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'category'      => NodeCategoryEnum::class,
        'location'      => StringType::class,
        'name'          => StringType::class,
        'address'       => AddressList::class,
    );
}
