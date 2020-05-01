<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\UserIdTypeEnum;
use \fpoirotte\IDMEF\Types\StringType;

class UserId extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'type'          => UserIdTypeEnum::class,
        'tty'           => StringType::class,
        'name'          => UserNameList::class,
        'number'        => UserNumberList::class,
    );

    public function isValid(): bool
    {
        $this->acquireLock(self::LOCK_SHARED, true);
        try {
            if (!parent::isValid()) {
                return false;
            }

            // Either name or number (or both) must be defined.
            return (isset($this->_children['name']) || isset($this->_children['number']));
        } finally {
            $this->releaseLock(self::LOCK_SHARED, true);
        }
    }
}
