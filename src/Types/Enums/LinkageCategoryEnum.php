<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class LinkageCategoryEnum extends AbstractEnum
{
    protected $_choices = array('hard-link', 'mount-point', 'reparse-points',
                                'shortcut', 'stream', 'symbolic-link');
}
