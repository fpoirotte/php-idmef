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

    public function isValid()
    {
        $this->acquireLock(self::LOCK_SHARED, true);
        try {
            if (!parent::isValid()) {
                return false;
            }

            // An alert can only have one type (basic Alert, Correlation Alert,
            // Tool Alert or Overflow Alert).
            $specialType = 0;
            foreach (array('ToolAlert', 'CorrelationAlert', 'OverflowAlert') as $type) {
                if (isset($this->_children[$type])) {
                    $specialType++;
                }
            }

            // A basic Alert does not have a special type.
            return ($specialType <= 1);
        } finally {
            $this->releaseLock(self::LOCK_SHARED, true);
        }
    }
}
