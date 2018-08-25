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

    protected function checkXmlSerialization($testfile, $message)
    {
        $wrapper    = new IDMEFMessage;
        $wrapper[]  = $message;
        $testdata   = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testdata' . DIRECTORY_SEPARATOR . $testfile);
        $serializer = new Xml(true);
        $this->assertSame($testdata, $serializer->serialize($wrapper));
    }

    public function checkXmlUnserialization($testfile, $expected)
    {
        $testdata       = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'testdata' . DIRECTORY_SEPARATOR . $testfile);
        $serializer     = new Xml(true);
        $idmefmessage   = $serializer->unserialize($testdata);
        $this->assertSame(1, count($idmefmessage));

        // We only check that the leaf nodes' values match our expectations.
        $message    = $idmefmessage[0];
        $actual     = array();
        foreach ($message->getIterator('{' . AbstractType::class . '}', null, 0, -1) as $path => $value) {
            $actual[$path] = (string) $value;
        }
        $this->assertSame($expected, $actual);
    }

    public function testHeartbeatXmlSerialization()
    {
        $this->checkXmlSerialization('heartbeat.xml', $this->heartbeat);
    }

    public function testAlertXmlSerialization()
    {
        $this->checkXmlSerialization('teardrop.xml', $this->alert);
    }

    public function testCorrelationAlertXmlSerialization()
    {
        $this->checkXmlSerialization('correlationalert.xml', $this->correlated);
    }

    public function testHeartbeatXmlUnserialization()
    {
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
        $this->checkXmlUnserialization('heartbeat.xml', $expected);
    }

    public function testAlertXmlUnserialization()
    {
        $expected = array(
            'Alert.messageid'                           => 'abc123456789',
            'Alert.Analyzer.analyzerid'                 => 'hq-dmz-analyzer01',
            'Alert.Analyzer.Node.category'              => 'dns',
            'Alert.Analyzer.Node.location'              => 'Headquarters DMZ Network',
            'Alert.Analyzer.Node.name'                  => 'analyzer01.example.com',
            'Alert.CreateTime.ntpstamp'                 => '0xbc722ebe.0x00000000',
            'Alert.Source(0).ident'                     => 'a1b2c3d4',
            'Alert.Source(0).Node.ident'                => 'a1b2c3d4-001',
            'Alert.Source(0).Node.category'             => 'dns',
            'Alert.Source(0).Node.name'                 => 'badguy.example.net',
            'Alert.Source(0).Node.Address(0).ident'     => 'a1b2c3d4-002',
            'Alert.Source(0).Node.Address(0).category'  => 'ipv4-net-mask',
            'Alert.Source(0).Node.Address(0).address'   => '192.0.2.50',
            'Alert.Source(0).Node.Address(0).netmask'   => '255.255.255.255',
            'Alert.Target(0).ident'                     => 'd1c2b3a4',
            'Alert.Target(0).Node.ident'                => 'd1c2b3a4-001',
            'Alert.Target(0).Node.category'             => 'dns',
            'Alert.Target(0).Node.Address(0).category'  => 'ipv4-addr-hex',
            'Alert.Target(0).Node.Address(0).address'   => '0xde796f70',
            'Alert.Classification.text'                 => 'Teardrop detected',
            'Alert.Classification.Reference(0).origin'  => 'bugtraqid',
            'Alert.Classification.Reference(0).name'    => '124',
            'Alert.Classification.Reference(0).url'     => 'http://www.securityfocus.com/bid/124',
        );
        $this->checkXmlUnserialization('teardrop.xml', $expected);
    }

    public function testCorrelationAlertXmlUnserialization()
    {
        $expected = array(
            'Alert.messageid'                                   => 'abc123456789',
            'Alert.Analyzer.analyzerid'                         => 'bc-corr-01',
            'Alert.Analyzer.Node.category'                      => 'dns',
            'Alert.Analyzer.Node.name'                          => 'correlator01.example.com',
            'Alert.CreateTime.ntpstamp'                         => '0xbc72423b.0x00000000',
            'Alert.Source(0).ident'                             => 'a1',
            'Alert.Source(0).Node.ident'                        => 'a1-1',
            'Alert.Source(0).Node.Address(0).ident'             => 'a1-2',
            'Alert.Source(0).Node.Address(0).category'          => 'ipv4-addr',
            'Alert.Source(0).Node.Address(0).address'           => '192.0.2.200',
            'Alert.Target(0).ident'                             => 'a2',
            'Alert.Target(0).Node.ident'                        => 'a2-1',
            'Alert.Target(0).Node.category'                     => 'dns',
            'Alert.Target(0).Node.name'                         => 'www.example.com',
            'Alert.Target(0).Node.Address(0).ident'             => 'a2-2',
            'Alert.Target(0).Node.Address(0).category'          => 'ipv4-addr',
            'Alert.Target(0).Node.Address(0).address'           => '192.0.2.50',
            'Alert.Target(0).Service.ident'                     => 'a2-3',
            'Alert.Target(0).Service.portlist'                  => '5-25,37,42,43,53,69-119,123-514',
            'Alert.Classification.text'                         => 'Portscan',
            'Alert.Classification.Reference(0).origin'          => 'vendor-specific',
            'Alert.Classification.Reference(0).name'            => 'portscan',
            'Alert.Classification.Reference(0).url'             => 'http://www.vendor.com/portscan',
            'Alert.CorrelationAlert.name'                       => 'multiple ports in short time',
            'Alert.CorrelationAlert.alertident(0).alertident'   => '123456781',
            'Alert.CorrelationAlert.alertident(1).alertident'   => '123456782',
            'Alert.CorrelationAlert.alertident(2).alertident'   => '123456783',
            'Alert.CorrelationAlert.alertident(3).alertident'   => '123456784',
            'Alert.CorrelationAlert.alertident(4).alertident'   => '123456785',
            'Alert.CorrelationAlert.alertident(5).alertident'   => '123456786',
            'Alert.CorrelationAlert.alertident(6).analyzerid'   => 'a1b2c3d4',
            'Alert.CorrelationAlert.alertident(6).alertident'   => '987654321',
            'Alert.CorrelationAlert.alertident(7).analyzerid'   => 'a1b2c3d4',
            'Alert.CorrelationAlert.alertident(7).alertident'   => '987654322',
        );
        $this->checkXmlUnserialization('correlationalert.xml', $expected);
    }
}
