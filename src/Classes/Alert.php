<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Types\NtpStampType;
use \fpoirotte\IDMEF\Classes\Analyzer;
use \fpoirotte\IDMEF\Classes\SourceList;
use \fpoirotte\IDMEF\Classes\TargetList;
use \fpoirotte\IDMEF\Classes\Classification;
use \fpoirotte\IDMEF\Classes\Assessment;
use \fpoirotte\IDMEF\Classes\AdditionalDataList;

class Alert extends IDMEFMessage
{
    protected static $_subclasses = array(
        'messageid'         => StringType::class,
        'Analyzer'          => Analyzer::class,
        'CreateTime'        => NtpStampType::class,
        'DetectTime'        => NtpStampType::class,
        'AnalyzerTime'      => NtpStampType::class,
        'Source'            => SourceList::class,
        'Target'            => TargetList::class,
        'Classification'    => Classification::class,
        'Assessment'        => Assessment::class,
        'AdditionalData'    => AdditionalDataList::class,
    );
}
