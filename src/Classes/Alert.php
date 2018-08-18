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
        'AdditionalData'    => AdditionalDataList::class,
    );
}
