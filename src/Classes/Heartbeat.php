<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Types\NtpStampType;
use \fpoirotte\IDMEF\Classes\Analyzer;
use \fpoirotte\IDMEF\Classes\AdditionalDataList;

class Heartbeat extends AbstractIDMEFMessage
{
    protected static $_subclasses = array(
        'messageid'         => StringType::class,
        'Analyzer'          => Analyzer::class,
        'CreateTime'        => NtpStampType::class,
        'HeartbeatInterval' => StringType,
        'AnalyzerTime'      => NtpStampType::class,
        'AdditionalData'    => AdditionalData::class,
    );
}
