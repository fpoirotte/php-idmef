<?php

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class FsTypeEnum extends AbstractEnum
{
    protected $_choices = array('ufs', 'efs', 'nfs', 'afs', 'ntfs', 'fat16', 'fat32', 'pcfs', 'joliet', 'iso9660');
}
