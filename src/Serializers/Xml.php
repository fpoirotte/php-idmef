<?php

namespace fpoirotte\IDMEF\Serializers;

use \fpoirotte\IDMEF\Types\AbstractType;
use \fpoirotte\IDMEF\Classes\IDMEFMessage;
use \fpoirotte\IDMEF\Classes\AbstractClass;
use \fpoirotte\IDMEF\Classes\AbstractList;

/**
 * Abstract class representing an IDMEF type.
 */
class Xml extends AbstractSerializer
{
    protected $indent;
    protected $out;

    public function __construct($indent = false)
    {
        $this->indent = (bool) $indent;
    }

    public function serialize(IDMEFMessage $message)
    {
        $this->out = new \XMLWriter();
        $this->out->openMemory();
        $this->out->setIndent($this->indent);
        if ($this->indent) {
            $this->out->setIndentString('  ');
        }

        $this->out->startDocument('1.0', 'UTF-8');
        $this->_serialize($message);
        $this->out->endDocument();
        return $this->out->outputMemory();
    }

    protected function _serialize($node)
    {
        $found = false;
        $classes = array_merge(array(get_class($node)), class_parents($node));
        $visited = array();

        foreach (array_reverse($classes) as $cls) {
            $cls = ltrim(substr($cls, strrpos($cls, '\\')), '\\');
            $method = "visit$cls";
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $node);
                $visited[] = $cls;
                $found = true;
            }
        }

        foreach (array_reverse($visited) as $cls) {
            $method = "depart$cls";
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $node);
                $found = true;
            }
            // Close the tag opened by the visit callback.
            $this->out->endElement();
        }

        if (empty($visited)) {
            throw new \InvalidArgumentException(implode(' < ', $classes));
        }
    }

    protected function writeAttributes($node, ...$attributes)
    {
        foreach ($attributes as $attribute) {
            if (!isset($node->$attribute)) {
                continue;
            }

            $attr = $node->$attribute;
            if ($attr instanceof AbstractType) {
                $this->out->writeAttribute($attribute, $attr);
            } else {
                throw new \InvalidArgumentException(get_class($node) . ".$attribute");
            }
        }
    }

    protected function writeElements($node, ...$elements)
    {
        foreach ($elements as $element) {
            if (!isset($node->$element)) {
                continue;
            }

            $elem = $node->$element;
            if ($elem instanceof AbstractType) {
                $this->out->writeElement($element, (string) $elem);
            } elseif ($elem instanceof AbstractList) {
                foreach ($elem as $child) {
                    $this->_serialize($child);
                }
            } elseif ($elem instanceof AbstractClass) {
                $this->_serialize($elem);
            } else {
                throw new \InvalidArgumentException(get_class($node));
            }
        }
    }

    protected function visitAbstractType($node)
    {
        $this->out->writeElement($node::XML_TYPE, (string) $node);
    }

    protected function visitIDMEFMessage($node)
    {
        $this->out->startElementNS(null, 'IDMEF-Message', 'http://iana.org/idmef');
        $this->out->writeAttribute('version', '1.0');
    }

    protected function visitAlert($node)
    {
        $this->out->startElement('Alert');
        $this->writeAttributes($node, 'messageid');
        $this->writeElements(
            $node,
            'Analyzer', 'CreateTime', 'DetectTime', 'AnalyzerTime',
            'Source', 'Target', 'Classification', 'Assessment'
        );
    }

    protected function visitHeartbeat($node)
    {
        $this->out->startElement('Heartbeat');
        $this->writeAttributes($node, 'messageid');
        $this->writeElements(
            $node,
            'Analyzer', 'CreateTime', 'HeartbeatInterval',
            'AnalyzerTime', 'AdditionalData'
        );
    }

    protected function visitCorrelationAlert($node)
    {
        $this->out->startElement('CorrelationAlert');
        $this->writeAttributes($node, 'name');
        // FIXME: "alerts" (alertident)
        $this->writeElements($node, 'alerts');
    }

    protected function visitOverflowAlert($node)
    {
        $this->out->startElement('OverflowAlert');
        $this->writeAttributes($node, 'program', 'size', 'buffer');
    }

    protected function visitToolAlert($node)
    {
        $this->out->startElement('ToolAlert');
        $this->writeAttributes($node, 'name', 'command');
        // FIXME: "alerts" (alertident)
        $this->writeElements($node, 'alerts');
    }

    protected function visitAdditionalData($node)
    {
        $this->out->startElement('AdditionalData');
        $this->writeAttributes($node, 'type', 'meaning');
        $this->_serialize($node->data);
    }

    protected function visitAnalyzer($node)
    {
        $this->out->startElement('Analyzer');
        $this->writeAttributes(
            $node,
            'analyzerid', 'name', 'manufacturer', 'model',
            'version', 'class', 'ostype', 'osversion'
        );
        $this->writeElements($node, 'Node', 'Process', 'Analyzer');
    }

    protected function visitClassification($node)
    {
        $this->out->startElement('Classification');
        $this->writeAttributes($node, 'ident', 'text');
        $this->writeElements($node, 'Reference');
    }

    protected function visitSource($node)
    {
        $this->out->startElement('Source');
        $this->writeAttributes($node, 'ident', 'spoofed', 'interface');
        $this->writeElements($node, 'Node', 'User', 'Process', 'Service');
    }

    protected function visitTarget($node)
    {
        $this->out->startElement('Target');
        $this->writeAttributes($node, 'ident', 'decoy', 'interface');
        $this->writeElements($node, 'Node', 'User', 'Process', 'Service', 'File');
    }

    protected function visitAssessment($node)
    {
        $this->out->startElement('Assessment');
        $this->writeElements($node, 'Impact', 'Action', 'Confidence');
    }

    protected function visitReference($node)
    {
        $this->out->startElement('Reference');
        $this->writeAttributes($node, 'origin', 'meaning');
        $this->writeElements($node, 'name', 'url');
    }

    protected function visitNode($node)
    {
        $this->out->startElement('Node');
        $this->writeAttributes($node, 'ident', 'category');
        $this->writeElements($node, 'location', 'name', 'Address');
    }

    protected function visitAddress($node)
    {
        $this->out->startElement('Address');
        $this->writeAttributes($node, 'ident', 'category', 'vlan-name', 'vlan-num');
        $this->writeElements($node, 'address', 'netmask');
    }

    protected function visitFile($node)
    {
        $this->out->startElement('File');
        $this->writeAttributes($node, 'ident', 'category', 'fstype', 'file-type');
        $this->writeElements(
            $node,
            'name', 'path', 'create-time', 'modify-time', 'access-time',
            'data-size', 'disk-size', 'FileAccess', 'Linkage', 'Inode',
            'Checksum'
        );
    }

    protected function visitPermission($node)
    {
        $this->out->startElement('Permission');
        $this->writeAttributes($node, 'perms');
    }

    protected function visitFileAccess($node)
    {
        $this->out->startElement('FileAccess');
        $this->writeElements($node, 'UserId', 'Permission');
    }

    protected function visitInode($node)
    {
        $this->out->startElement('Inode');
        $this->writeElements(
            $node,
            'change-time',
            'number', 'major-device', 'minor-device',
            'c-major-device', 'c-minor-device'
        );
    }

    protected function visitLinkage($node)
    {
        $this->out->startElement('Linkage');
        $this->writeAttributes($node, 'category');
        $this->writeElements($node, 'name', 'path', 'File');
    }

    protected function visitChecksum($node)
    {
        $this->out->startElement('Checksum');
        $this->writeAttributes($node, 'algorithm');
        $this->writeElements($node, 'value', 'key');
    }

    protected function visitProcess($node)
    {
        $this->out->startElement('Process');
        $this->writeAttributes($node, 'ident');
        $this->writeElements($node, 'name', 'pid', 'path', 'arg', 'env');
    }

    protected function visitService($node)
    {
        $this->out->startElement('Service');
        $this->writeAttributes(
            $node,
            'ident', 'ip_version', 'iana_protocol_number', 'iana_protocol_name'
        );
        $this->writeElements(
            $node,
            'name', 'port', 'portlist', 'protocol', 'SNMPService', 'WebService'
        );
    }

    protected function visitSNMPService($node)
    {
        $this->out->startElement('SNMPService');
        $this->writeElements(
            $node,
            'oid', 'messageProcessingModel', 'securityModel', 'securityName',
            'securityLevel', 'contextName', 'contextEngineID', 'command'
        );
    }

    protected function visitUser($node)
    {
        $this->out->startElement('User');
        $this->writeAttributes($node, 'ident', 'category');
        $this->writeElements($node, 'UserId');
    }

    protected function visitUserId($node)
    {
        $this->out->startElement('UserId');
        $this->writeAttributes($node, 'ident', 'type', 'tty');
        $this->writeElements($node, 'name', 'number');
    }

    protected function visitWebService($node)
    {
        $this->out->startElement('WebService');
        $this->writeElements($node, 'url', 'cgi', 'http-method', 'arg');
    }

    protected function visitAction($node)
    {
        $this->out->startElement('Action');
        $this->writeAttributes($node, 'category');
    }

    protected function visitNtpStampType($node)
    {
        $this->out->startElement($node->getParent()->__get($node));
        $this->out->writeAttribute($node, 'ntpstamp');
    }

    protected function visitConfidence($node)
    {
        $this->out->startElement('Confidence');
        $this->writeAttributes($node, 'rating');
    }

    protected function visitImpact($node)
    {
        $this->out->startElement('Impact');
        $this->writeAttributes($node, 'severity', 'completion', 'type');
    }

    protected function visitAlertIdentList($node)
    {
        $this->out->startElement('alertident');
        $this->writeAttributes($node, 'analyzerid');
        if (isset($node->ident)) {
            $this->out->text($node->ident);
        }
    }

    protected function departAlert($node)
    {
        // This is done here so that classes that inherit from Alert
        // (namely OverflowAlert, ToolAlert, CorrelationAlert) can
        // add their own attributes before the additional data.
        // See the DTD in section 8 of RFC 4765 for more information.
        foreach ($node->AdditionalData as $child) {
            $this->visitAdditionalData($child);
        }
    }

    protected function _unserialize($serialized)
    {
    }
}
