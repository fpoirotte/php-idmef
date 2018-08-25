<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

class Alert extends AbstractIDMEFMessage
{
    protected static $_subclasses = array(
        'messageid'         => StringType::class,
        'Analyzer'          => Analyzer::class,
        'CreateTime'        => CreateTime::class,
        'DetectTime'        => DetectTime::class,
        'AnalyzerTime'      => AnalyzerTime::class,
        'Source'            => SourceList::class,
        'Target'            => TargetList::class,
        'Classification'    => Classification::class,
        'Assessment'        => Assessment::class,
        'ToolAlert'         => ToolAlert::class,
        'OverflowAlert'     => OverflowAlert::class,
        'CorrelationAlert'  => CorrelationAlert::class,
        'AdditionalData'    => AdditionalDataList::class,
    );

    protected static $_mandatory = array(
        'Analyzer',
        'CreateTime',
        'Classification',
    );
}
