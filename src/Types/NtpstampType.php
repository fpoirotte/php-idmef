<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types;

class NtpstampType extends AbstractType
{
    const XML_TYPE = 'ntpstamp';

    protected $_ntpstamp;

    public function __construct($value)
    {
        if (is_object($value) && ($value instanceof \DateTimeInterface)) {
            $ntpEpoch = new \DateTimeImmutable('1900-01-01T00:00:00.0+00:00');
            $diff = $value->diff($ntpEpoch, true);

            // We do not rely on $diff's days/h/m/s fields because
            // \DateInterval does not handle leap seconds correctly.
            $integral = $value->getTimestamp() - $ntpEpoch->getTimestamp();
            $integral &= 0xFFFFFFFF;

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

    public function __toString(): string
    {
        return $this->_ntpstamp;
    }

    protected function unserialize(string $serialized): void
    {
        $parts = explode('.', $serialized, 2);
        foreach ($parts as &$part) {
            if (strlen($part) != 10 || substr($part, 0, 2) !== '0x' ||
                strspn($part, '1234567890abcdefABCDEF', 2) !== 8) {
                throw new \InvalidArgumentException($serialized);
            }
        }

        if (sscanf(strtolower($serialized), '%x.%x', $integral, $fraction) !== 2) {
            throw new \InvalidArgumentException($serialized);
        }

        // See section 6.4 of RFC 4765 for an explanation of why this is needed.
        $epoch = ($integral & 0x80000000) ? "1900-01-01T00:00:00" : "2036-02-07T06:28:16";
        $fraction = $fraction * 100000 / 0x100000000;
        $value = new \DateTime("$epoch.$fraction+00:00");
        $value->add(new \DateInterval('PT' . $integral . 'S'));

        $this->_value = \DateTimeImmutable::createFromMutable($value);
        $this->_ntpstamp = $serialized;
    }
}
