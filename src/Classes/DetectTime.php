<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\NtpstampType;

class DetectTime extends AbstractClass
{
    protected static $_subclasses = array(
        'ntpstamp'       => NtpstampType::class,
    );
}
