<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types\Enums;

use \fpoirotte\IDMEF\Types\AbstractEnum;

class AddressCategoryEnum extends AbstractEnum
{
    protected $_choices = array('unknown', 'atm', 'e-mail', 'lotus-notes',
                                'mac', 'sna', 'vm',
                               'ipv4-addr', 'ipv4-addr-hex',
                               'ipv4-net', 'ipv4-net-mask',
                               'ipv6-addr', 'ipv6-addr-hex',
                               'ipv6-net', 'ipv6-net-mask');
}
