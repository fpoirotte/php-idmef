<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\AddressCategoryEnum;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Types\IntegerType;

class Address extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'category'      => AddressCategoryEnum::class,
        'vlan-name'     => StringType::class,
        'vlan-num'      => IntegerType::class,
        'address'       => StringType::class,
        'netmask'       => StringType::class,
    );
}
