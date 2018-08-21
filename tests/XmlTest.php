<?php

use PHPUnit\Framework\TestCase;
use \fpoirotte\IDMEF\Types\AbstractType;
use \fpoirotte\IDMEF\Classes\Heartbeat;
use \fpoirotte\IDMEF\Classes\IDMEFMessage;
use \fpoirotte\IDMEF\Serializers\Xml;

class XmlTest extends TestCase
{
    protected static $xml =<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<IDMEF-Message version="1.0" xmlns="http://iana.org/idmef">
  <Heartbeat messageid="abc123456789">
    <Analyzer analyzerid="hq-dmz-analyzer01">
      <Node category="dns">
        <location>Headquarters DMZ Network</location>
        <name>analyzer01.example.com</name>
      </Node>
    </Analyzer>
    <CreateTime ntpstamp="0xbc722ebe.0x00000000">2000-03-09T14:07:58.000000+00:00</CreateTime>
    <AdditionalData type="real" meaning="%memused">
      <real>62.5</real>
    </AdditionalData>
    <AdditionalData type="real" meaning="%diskused">
      <real>87.1</real>
    </AdditionalData>
  </Heartbeat>
</IDMEF-Message>

XML;

    public function setUp()
    {
        $this->heartbeat = new Heartbeat();
        $this->heartbeat->messageid = 'abc123456789';
        $this->heartbeat->analyzer->analyzerid = 'hq-dmz-analyzer01';
        $this->heartbeat->analyzer->node->category = 'dns';
        $this->heartbeat->analyzer->node->location = 'Headquarters DMZ Network';
        $this->heartbeat->analyzer->node->name = 'analyzer01.example.com';
        $this->heartbeat->create_time->ntpstamp = '0xbc722ebe.0x00000000';
        $this->heartbeat->additional_data[  ]->type = 'real';
        $this->heartbeat->additional_data[-1]->meaning = '%memused';
        $this->heartbeat->additional_data[-1]->data = 62.5;
        $this->heartbeat->additional_data[  ]->type = 'real';
        $this->heartbeat->additional_data[-1]->meaning = '%diskused';
        $this->heartbeat->additional_data[-1]->data = 87.1;
    }

    public function testXmlSerialization()
    {
        $serializer = new Xml(true);
        $message = new IDMEFMessage();
        $message[] = $this->heartbeat;
        $this->assertSame(self::$xml, $serializer->serialize($message));
    }

    public function testXmlUnserialization()
    {
        $serializer = new Xml(true);
        $idmefmessage = $serializer->unserialize(self::$xml);
        $this->assertSame(1, count($idmefmessage));
        $heartbeat = $idmefmessage[0];

        $expected = array(
            'Heartbeat.messageid'                   => 'abc123456789',
            'Heartbeat.Analyzer.analyzerid'         => 'hq-dmz-analyzer01',
            'Heartbeat.Analyzer.Node.category'      => 'dns',
            'Heartbeat.Analyzer.Node.location'      => 'Headquarters DMZ Network',
            'Heartbeat.Analyzer.Node.name'          => 'analyzer01.example.com',
            'Heartbeat.CreateTime.ntpstamp'         => '0xbc722ebe.0x00000000',
            'Heartbeat.AdditionalData(0).type'      => 'real',
            'Heartbeat.AdditionalData(0).meaning'   => '%memused',
            'Heartbeat.AdditionalData(0).data'      => '62.5',
            'Heartbeat.AdditionalData(1).type'      => 'real',
            'Heartbeat.AdditionalData(1).meaning'   => '%diskused',
            'Heartbeat.AdditionalData(1).data'      => '87.1',
        );

        $actual = array();
        foreach ($heartbeat->getIterator('{' . AbstractType::class . '}', null, 0, -1) as $path => $value) {
            $actual[$path] = (string) $value;
        }
        $this->assertSame($expected, $actual);
    }
}
