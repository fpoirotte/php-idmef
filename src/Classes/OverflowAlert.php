<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\StringType;
use fpoirotte\IDMEF\Types\IntegerType;
use fpoirotte\IDMEF\Types\ByteStringType;

class OverflowAlert extends AbstractClass
{
    protected static $_subclasses = array(
        'program'           => StringType::class,
        'size'              => IntegerType::class,
        'buffer'            => ByteStringType::class,
    );

    protected static $_mandatory = array(
        'program',
    );
}
