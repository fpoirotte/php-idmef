<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\Enums\SeverityEnum;
use fpoirotte\IDMEF\Types\Enums\CompletionEnum;
use fpoirotte\IDMEF\Types\Enums\ImpactTypeEnum;

class Impact extends AbstractClass
{
    protected static $_subclasses = array(
        'severity'      => SeverityEnum::class,
        'completion'    => CompletionEnum::class,
        'type'          => ImpactTypeEnum::class,
    );
}
