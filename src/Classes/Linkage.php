<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\LinkageCategoryEnum;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\File;

class Linkage extends AbstractClass
{
    protected static $_subclasses = array(
        'category'      => LinkageCategoryEnum::class,
        'name'          => StringType::class,
        'path'          => StringType::class,
        'File'          => File::class,
    );
}
