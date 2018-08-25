<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\NodeCategoryEnum;
use \fpoirotte\IDMEF\Types\StringType;

class Node extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'category'      => NodeCategoryEnum::class,
        'location'      => StringType::class,
        'name'          => StringType::class,
        'Address'       => AddressList::class,
    );

    public function isValid()
    {
        $this->acquireLock(self::LOCK_SHARED, true);
        try {
            if (!parent::isValid()) {
                return false;
            }

            // At least one name or address must be given to the node.
            if (isset($this->_children['name'])) {
                return true;
            }

            if (isset($this->_children['Address']) && count($this->_children['Address'])) {
                return true;
            }

            return false;
        } finally {
            $this->releaseLock(self::LOCK_SHARED, true);
        }
    }
}
