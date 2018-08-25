<?php

use PHPUnit\Framework\TestCase;
use \fpoirotte\IDMEF\Types\DateTimeType;

class DateTimeTest extends TestCase
{
    public function provideIntegralDateTime()
    {
        return array(
            array('2018-08-25T14:01:19Z'),
            array('2018-08-25T14:01:19.000Z'),
            array('2018-08-25T14:01:19,000Z'),
            array('2018-08-25T16:01:19+02:00'),
            array('2018-08-25T16:01:19.000+02:00'),
            array('2018-08-25T16:01:19,000+02:00'),
            array('2018-08-25T12:01:19-02:00'),
            array('2018-08-25T12:01:19.000-02:00'),
            array('2018-08-25T12:01:19,000-02:00'),
        );
    }

    public function provideFractionalDateTime()
    {
        return array(
            array('2018-08-25T14:01:19.1234Z'),
            array('2018-08-25T14:01:19,1234Z'),
            array('2018-08-25T16:01:19.1234+02:00'),
            array('2018-08-25T16:01:19,1234+02:00'),
            array('2018-08-25T12:01:19.1234-02:00'),
            array('2018-08-25T12:01:19,1234-02:00'),
        );
    }

    /**
     * @dataProvider provideIntegralDateTime
     */
    public function testIntegralDateTimeParsing($value)
    {
        $cls        = DateTimeType::class;
        $serialized = sprintf('C:%d:"%s":%d:{%s}', strlen($cls), $cls, strlen($value), $value);
        $datetime   = unserialize($serialized);
        $utcTime    = $datetime->getValue()->setTimezone(new \DateTimeZone('UTC'));
        $this->assertSame('2018-08-25T14:01:19.000000+00:00', $utcTime->format(DateTimeType::FORMAT));
    }

    /**
     * @dataProvider provideFractionalDateTime
     */
    public function testFractionalDateTimeParsing($value)
    {
        $cls        = DateTimeType::class;
        $serialized = sprintf('C:%d:"%s":%d:{%s}', strlen($cls), $cls, strlen($value), $value);
        $datetime   = unserialize($serialized);
        $utcTime    = $datetime->getValue()->setTimezone(new \DateTimeZone('UTC'));
        $this->assertSame('2018-08-25T14:01:19.123400+00:00', $utcTime->format(DateTimeType::FORMAT));
    }
}
