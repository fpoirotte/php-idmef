<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use fpoirotte\IDMEF\Types\AbstractEnum;

class ImpactTypeEnum extends AbstractEnum
{
    protected $_choices = array('admin', 'dos', 'file', 'recon',
                                'user', 'other');
}
