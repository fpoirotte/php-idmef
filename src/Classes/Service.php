<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Types\IntegerType;
use \fpoirotte\IDMEF\Types\PortListType;

class Service extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'                 => StringType::class,
        'ip_version'            => IntegerType::class,
        'iana_protocol_number'  => IntegerType::class,
        'iana_protocol_name'    => StringType::class,
        'name'                  => StringType::class,
        'port'                  => IntegerType::class,
        'portlist'              => PortListType::class,
        'protocol'              => StringType::class,
        'SNMPService'           => SNMPService::class,
        'WebService'            => WebService::class,
    );
}
