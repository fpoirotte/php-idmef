<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

class Heartbeat extends AbstractIDMEFMessage
{
    protected static $_subclasses = array(
        'messageid'         => StringType::class,
        'Analyzer'          => Analyzer::class,
        'CreateTime'        => CreateTime::class,
        'HeartbeatInterval' => StringType::class,
        'AnalyzerTime'      => AnalyzerTime::class,
        'AdditionalData'    => AdditionalDataList::class,
    );

    protected static $_mandatory = array(
        'Analyzer',
        'CreateTime',
    );
}
