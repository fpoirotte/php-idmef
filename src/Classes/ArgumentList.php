<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\StringType;

class ArgumentList extends AbstractList
{
    protected $_type = StringType::class;
}
