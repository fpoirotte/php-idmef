<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use fpoirotte\IDMEF\Types\NtpstampType;

class AnalyzerTime extends AbstractClass
{
    protected static $_subclasses = array(
        'ntpstamp'       => NtpstampType::class,
    );

    protected static $_mandatory = array(
        'ntpstamp',
    );
}
