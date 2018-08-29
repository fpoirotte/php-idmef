<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Types\IntegerType;
use \fpoirotte\IDMEF\Types\PortlistType;

class Service extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'                 => StringType::class,
        'ip_version'            => IntegerType::class,
        'iana_protocol_number'  => IntegerType::class,
        'iana_protocol_name'    => StringType::class,
        'name'                  => StringType::class,
        'port'                  => IntegerType::class,
        'portlist'              => PortlistType::class,
        'protocol'              => StringType::class,
        'SNMPService'           => SNMPService::class,
        'WebService'            => WebService::class,
    );

    public function isValid()
    {
        $this->acquireLock(self::LOCK_SHARED, true);
        try {
            if (!parent::isValid()) {
                return false;
            }

            $either = isset($this->_children['name']) || isset($this->_children['port']);
            $portlist = isset($this->_children['portlist']);

            // Either portlist must be defined, or one or both of name and port
            // must be defined.
            return $portlist ? (!$either) : $either;
        } finally {
            $this->releaseLock(self::LOCK_SHARED, true);
        }
    }
}
