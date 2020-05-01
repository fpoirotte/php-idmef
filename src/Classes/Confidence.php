<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\RatingEnum;

class Confidence extends AbstractClass
{
    protected static $_subclasses = array(
        'rating'        => RatingEnum::class,
    );
}
