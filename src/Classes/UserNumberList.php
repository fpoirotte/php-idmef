<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\IntegerType;

class UserNumberList extends AbstractList
{
    protected $_type = IntegerType::class;
}
