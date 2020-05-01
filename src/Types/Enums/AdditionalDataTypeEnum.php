<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class AdditionalDataTypeEnum extends AbstractEnum
{
    protected $_choices = array('boolean', 'byte', 'character', 'date-time',
                                'integer', 'ntpstamp', 'portlist', 'real',
                                'string', 'byte-string', 'xmltext');
}
