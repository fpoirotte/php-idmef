<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\StringType;
use fpoirotte\IDMEF\Types\IntegerType;

class SNMPService extends AbstractClass
{
    protected static $_subclasses = array(
        'oid'                       => StringType::class,
        'messageProcessingModel'    => IntegerType::class,
        'securityModel'             => StringType::class,
        'securityName'              => StringType::class,
        'securityLevel'             => IntegerType::class,
        'contextName'               => StringType::class,
        'contextEngineID'           => StringType::class,
        'command'                   => StringType::class,
    );
}
