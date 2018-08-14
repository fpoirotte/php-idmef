<?php

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class NodeCategoryEnum extends AbstractEnum
{
    protected $_choices = array('unknown', 'ads', 'afs', 'coda', 'dfs', 'dns',
                                'hosts', 'kerberos', 'nds', 'nis', 'nisplus',
                                'nt', 'wfw');
}
