<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\NtpstampType;

class CreateTime extends AbstractClass
{
    protected static $_subclasses = array(
        'ntpstamp'       => NtpstampType::class,
    );
}
