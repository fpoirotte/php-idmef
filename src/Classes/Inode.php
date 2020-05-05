<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\IntegerType;
use fpoirotte\IDMEF\Types\DateTimeType;

class Inode extends AbstractClass
{
    protected static $_subclasses = array(
        'change-time'       => DateTimeType::class,
        'number'            => IntegerType::class,
        'major-device'      => IntegerType::class,
        'minor-device'      => IntegerType::class,
        'c-major-device'    => IntegerType::class,
        'c-minor-device'    => IntegerType::class,
    );

    public function isValid(): bool
    {
        $this->acquireLock(self::LOCK_SHARED, true);
        try {
            if (!parent::isValid()) {
                return false;
            }

            $valid = 1;

            // number, major-device and minor-device must be defined together.
            if (isset($this->_children['number']) ||
                isset($this->_children['major-device']) ||
                isset($this->_children['minor-device'])) {
                $valid &= (int) isset(
                    $this->_children['number'],
                    $this->_children['major-device'],
                    $this->_children['minor-device']
                );
            }

            // c-major-device and c-minor-device must be defined together.
            if (isset($this->_children['c-major-device']) ||
                isset($this->_children['c-minor-device'])) {
                $valid &= (int) isset(
                    $this->_children['c-major-device'],
                    $this->_children['c-minor-device']
                );
            }

            return (bool) $valid;
        } finally {
            $this->releaseLock(self::LOCK_SHARED, true);
        }
    }
}
