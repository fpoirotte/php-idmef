<?php

use PHPUnit\Framework\TestCase;
use \fpoirotte\IDMEF\Types\AbstractType;
use \fpoirotte\IDMEF\Classes\Heartbeat;
use \fpoirotte\IDMEF\Classes\Alert;
use \fpoirotte\IDMEF\Classes\CorrelationAlert;
use \fpoirotte\IDMEF\Classes\IDMEFMessage;
use \fpoirotte\IDMEF\Serializers\Xml;

class XmlTest extends TestCase
{
    public function setUp()
    {
        $this->heartbeat = new Heartbeat;
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

        $this->alert = new Alert;
        $this->alert->messageid = 'abc123456789';
        $this->alert->analyzer->analyzerid = 'hq-dmz-analyzer01';
        $this->alert->analyzer->node->category = 'dns';
        $this->alert->analyzer->node->location = 'Headquarters DMZ Network';
        $this->alert->analyzer->node->name = 'analyzer01.example.com';
        $this->alert->create_time->ntpstamp = '0xbc722ebe.0x00000000';
        $this->alert->source[  ]->ident = 'a1b2c3d4';
        $this->alert->source[-1]->node->ident = 'a1b2c3d4-001';
        $this->alert->source[-1]->node->category = 'dns';
        $this->alert->source[-1]->node->name = 'badguy.example.net';
        $this->alert->source[-1]->node->address[  ]->ident = 'a1b2c3d4-002';
        $this->alert->source[-1]->node->address[-1]->category = 'ipv4-net-mask';
        $this->alert->source[-1]->node->address[-1]->address = '192.0.2.50';
        $this->alert->source[-1]->node->address[-1]->netmask = '255.255.255.255';
        $this->alert->target[  ]->ident = 'd1c2b3a4';
        $this->alert->target[-1]->node->ident = 'd1c2b3a4-001';
        $this->alert->target[-1]->node->category = 'dns';
        $this->alert->target[-1]->node->address[  ]->category = 'ipv4-addr-hex';
        $this->alert->target[-1]->node->address[-1]->address = '0xde796f70';
        $this->alert->classification->text = 'Teardrop detected';
        $this->alert->classification->reference[  ]->origin = 'bugtraqid';
        $this->alert->classification->reference[-1]->name = '124';
        $this->alert->classification->reference[-1]->url = 'http://www.securityfocus.com/bid/124';

        $this->correlated = new Alert;
        $this->correlated->messageid = 'abc123456789';
        $this->correlated->analyzer->analyzerid = 'bc-corr-01';
        $this->correlated->analyzer->node->category = 'dns';
        $this->correlated->analyzer->node->name = 'correlator01.example.com';
        $this->correlated->create_time->ntpstamp = '0xbc72423b.0x00000000';
        $this->correlated->source[  ]->ident = 'a1';
        $this->correlated->source[-1]->node->ident = 'a1-1';
        $this->correlated->source[-1]->node->address[  ]->ident = 'a1-2';
        $this->correlated->source[-1]->node->address[-1]->category = 'ipv4-addr';
        $this->correlated->source[-1]->node->address[-1]->address = '192.0.2.200';
        $this->correlated->target[  ]->ident = 'a2';
        $this->correlated->target[-1]->node->ident = 'a2-1';
        $this->correlated->target[-1]->node->category = 'dns';
        $this->correlated->target[-1]->node->name = 'www.example.com';
        $this->correlated->target[-1]->node->address[  ]->ident = 'a2-2';
        $this->correlated->target[-1]->node->address[-1]->category = 'ipv4-addr';
        $this->correlated->target[-1]->node->address[-1]->address = '192.0.2.50';
        $this->correlated->target[-1]->service->ident = 'a2-3';
        $this->correlated->target[-1]->service->portlist = '5-25,37,42,43,53,69-119,123-514';
        $this->correlated->classification->text = 'Portscan';
        $this->correlated->classification->reference[  ]->origin = 'vendor-specific';
        $this->correlated->classification->reference[-1]->name = 'portscan';
        $this->correlated->classification->reference[-1]->url = 'http://www.vendor.com/portscan';
        $this->correlated->correlation_alert->name = 'multiple ports in short time';
        $this->correlated->correlation_alert->alertident[  ]->alertident = '123456781';
        $this->correlated->correlation_alert->alertident[  ]->alertident = '123456782';
        $this->correlated->correlation_alert->alertident[  ]->alertident = '123456783';
        $this->correlated->correlation_alert->alertident[  ]->alertident = '123456784';
        $this->correlated->correlation_alert->alertident[  ]->alertident = '123456785';
        $this->correlated->correlation_alert->alertident[  ]->alertident = '123456786';
        $this->correlated->correlation_alert->alertident[  ]->alertident = '987654321';
        $this->correlated->correlation_alert->alertident[-1]->analyzerid = 'a1b2c3d4';
        $this->correlated->correlation_alert->alertident[  ]->alertident = '987654322';
        $this->correlated->correlation_alert->alertident[-1]->analyzerid = 'a1b2c3d4';
    }

    public function testHeartbeatXmlSerialization()
    {
        $message    = new IDMEFMessage;
        $message[]  = $this->heartbeat;
        $testdata   = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testdata' . DIRECTORY_SEPARATOR . 'heartbeat.xml');
        $serializer = new Xml(true);
        $this->assertSame($testdata, $serializer->serialize($message));
    }

    public function testAlertXmlSerialization()
    {
        $message    = new IDMEFMessage;
        $message[]  = $this->alert;
        $testdata   = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testdata' . DIRECTORY_SEPARATOR . 'teardrop.xml');
        $serializer = new Xml(true);
        $this->assertSame($testdata, $serializer->serialize($message));
    }

    public function testCorrelationAlertXmlSerialization()
    {
        $message    = new IDMEFMessage;
        $message[]  = $this->correlated;
        $testdata   = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testdata' . DIRECTORY_SEPARATOR . 'correlationalert.xml');
        $serializer = new Xml(true);
        $this->assertSame($testdata, $serializer->serialize($message));
    }

    public function testHeartbeatXmlUnserialization()
    {
        $testdata   = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testdata' . DIRECTORY_SEPARATOR . 'heartbeat.xml');
        $serializer = new Xml(true);
        $idmefmessage = $serializer->unserialize($testdata);
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
