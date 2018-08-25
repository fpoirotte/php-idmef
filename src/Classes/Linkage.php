<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\LinkageCategoryEnum;
use \fpoirotte\IDMEF\Types\StringType;

class Linkage extends AbstractClass
{
    protected static $_subclasses = array(
        'category'      => LinkageCategoryEnum::class,
        'name'          => StringType::class,
        'path'          => StringType::class,
        'File'          => File::class,
    );

    protected static $_mandatory = array(
        'category',
    );

    public function isValid()
    {
        $this->acquireLock(self::LOCK_SHARED, true);
        try {
            if (!parent::isValid()) {
                return false;
            }

            $either = isset($this->_children['name']) || isset($this->_children['path']);
            $both = isset($this->_children['name'], $this->_children['path']);
            $file = isset($this->_children['File']);

            // name and path must be defined together, but cannot be defined
            // if File is also defined (exclusive OR).
            return $file ? (!$either) : $both;
        } finally {
            $this->releaseLock(self::LOCK_SHARED, true);
        }
    }
}
