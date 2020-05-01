<?php

use PHPUnit\Framework\TestCase;
use \fpoirotte\IDMEF\Classes\Alert;

class IteratorTest extends TestCase
{
    public function setUp() : void
    {
        $this->alert = new Alert();
        $this->alert->messageid = 'abc';
        $this->alert->classification->text = 'Teardrop detected';
        $this->alert->analyzer->analyzerid = 'hq-dmz-analyzer01';
        $this->alert->analyzer->node->location = 'here';
        $this->alert->analyzer->analyzer->analyzerid = 'hq-dmz-analyzer02';
        $this->alert->analyzer->analyzer->node->location = 'here';
    }

    protected function checkResults($it, $expected)
    {
        $children = 0;
        foreach ($expected as $path => $value) {
            $this->assertSame($path, $it->key());
            $this->assertSame($value, $it->current());
            $children++;
            $it->next();
        }
        $this->assertSame(count($expected), $children);
    }

    public function testBasicIteration()
    {
        // Basic iteration only iterates over immediate children of the node.
        $it = $this->alert->getIterator();
        $expected = array(
            'Alert.messageid'       => $this->alert->messageid,
            'Alert.Classification'  => $this->alert->classification,
            'Alert.Analyzer'        => $this->alert->analyzer,
        );
        $this->checkResults($it, $expected);

        // This holds true for any node in the tree.
        $it = $this->alert->analyzer->getIterator();
        $expected = array(
            'Alert.Analyzer.analyzerid' => $this->alert->analyzer->analyzerid,
            'Alert.Analyzer.Node'       => $this->alert->analyzer->node,
            'Alert.Analyzer.Analyzer'   => $this->alert->analyzer->analyzer,
        );
        $this->checkResults($it, $expected);
    }

    public function testMinimumDepth()
    {
        // Return all nodes at a distance >= 3 from the tree's root.
        $it = $this->alert->getIterator(null, null, 3, -1);
        $expected = array(
            'Alert.Analyzer.Node.location'          => $this->alert->analyzer->node->location,
            'Alert.Analyzer.Analyzer.analyzerid'    => $this->alert->analyzer->analyzer->analyzerid,
            'Alert.Analyzer.Analyzer.Node'          => $this->alert->analyzer->analyzer->node,
            'Alert.Analyzer.Analyzer.Node.location' => $this->alert->analyzer->analyzer->node->location,
        );
        $this->checkResults($it, $expected);
    }

    public function testMaximumDepth()
    {
        // Return all nodes at a distance <= 1 from the tree's root.
        $it = $this->alert->getIterator(null, null, 0, 1);
        $expected = array(
            'Alert'                 => $this->alert,
            'Alert.messageid'       => $this->alert->messageid,
            'Alert.Classification'  => $this->alert->classification,
            'Alert.Analyzer'        => $this->alert->analyzer,
        );
        $this->checkResults($it, $expected);
    }

    public function testBothMinimumAndMaximumDepth()
    {
        // Return all nodes at a distance between 2 and 3 from the tree's root.
        $it = $this->alert->getIterator(null, null, 2, 3);
        $expected = array(
            'Alert.Classification.text'             => $this->alert->classification->text,
            'Alert.Analyzer.analyzerid'             => $this->alert->analyzer->analyzerid,
            'Alert.Analyzer.Node'                   => $this->alert->analyzer->node,
            'Alert.Analyzer.Node.location'          => $this->alert->analyzer->node->location,
            'Alert.Analyzer.Analyzer'               => $this->alert->analyzer->analyzer,
            'Alert.Analyzer.Analyzer.analyzerid'    => $this->alert->analyzer->analyzer->analyzerid,
            'Alert.Analyzer.Analyzer.Node'          => $this->alert->analyzer->analyzer->node,
        );
        $this->checkResults($it, $expected);
    }

    public function testPathBasedIteration()
    {
        $it = $this->alert->getIterator('Alert.Classification.text', null, 0, -1);
        $expected = array(
            'Alert.Classification.text' => $this->alert->classification->text,
        );
        $this->checkResults($it, $expected);
    }

    public function testTypeBasedIteration()
    {
        $it = $this->alert->getIterator('{' . \fpoirotte\IDMEF\Classes\Analyzer::class . '}', null, 0, -1);
        $expected = array(
            'Alert.Analyzer'            => $this->alert->analyzer,
            'Alert.Analyzer.Analyzer'   => $this->alert->analyzer->analyzer,
        );
        $this->checkResults($it, $expected);
    }

    public function testAbstractTypeBasedIteration()
    {
        $it = $this->alert->getIterator('{' . \fpoirotte\IDMEF\Classes\AbstractIDMEFMessage::class . '}', null, 0, -1);
        $expected = array(
            'Alert' => $this->alert,
        );
        $this->checkResults($it, $expected);
    }

    public function testValueBasedIteration()
    {
        $it = $this->alert->getIterator(null, 'here', 0, -1);
        $expected = array(
            'Alert.Analyzer.Node.location'          => $this->alert->analyzer->node->location,
            'Alert.Analyzer.Analyzer.Node.location' => $this->alert->analyzer->analyzer->node->location,
        );
        $this->checkResults($it, $expected);
    }
}
