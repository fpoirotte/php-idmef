<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\ActionCategoryEnum;

class Action extends AbstractClass
{
    protected static $_subclasses = array(
        'category'      => ActionCategoryEnum::class,
    );
}
