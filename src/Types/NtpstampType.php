<?php

namespace fpoirotte\IDMEF\Types;

class NtpstampType extends AbstractType
{
    const XML_TYPE = 'ntpstamp';

    protected $ntpstamp;

    public function __construct($value)
    {
        if (is_object($value) && ($value instanceof \DateTimeInterface)) {
            $ntpEpoch = new \DateTimeImmutable('1900-01-01T00:00:00.0+00:00');
            $diff = $value->diff($ntpEpoch, true);

            // We do not rely on $diff's days/h/m/s fields because
            // \DateInterval does not handle leap seconds correctly.
            $integral = $value->getTimestamp() - $ntpEpoch->getTimestamp();

            // Convert microseconds into a fraction of 2**32.
            $fraction = (int) ($diff->f / (1000000 / (1 << 32)));

            // Account for NTP timestamp wrap-around (see Section 6.4 from RFC 4765)

            $value = sprintf("0x%08x.0x%08x", $integral, $fraction);
        }

        if (!is_string($value)) {
            throw new \InvalidArgumentException($value);
        }

        $this->unserialize($value);
    }

    public function unserialize($serialized)
    {
        $parts = explode('.', $serialized, 2);
        foreach ($parts as &$part) {
            if (strlen($part) != 10 || substr($part, 0, 2) !== '0x' ||
                strspn($part, '1234567890abcdefABCDEF', 2) !== 8 ||
                sscanf($part, '%i', $part) !== 1) {
                throw new \InvalidArgumentException($serialized);
            }
        }
        $this->ntpstamp = $serialized;
    }
}