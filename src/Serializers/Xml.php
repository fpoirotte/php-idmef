<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Serializers;

use fpoirotte\IDMEF\Types\AbstractType;
use fpoirotte\IDMEF\Types\DateTimeType;
use fpoirotte\IDMEF\Classes\IDMEFMessage;
use fpoirotte\IDMEF\Classes\AbstractClass;
use fpoirotte\IDMEF\Classes\AbstractList;
use fpoirotte\IDMEF\Classes\AbstractNode;
use fpoirotte\IDMEF\Classes\AdditionalData;
use function fpoirotte\IDMEF\unserialize_type;

/**
 * Abstract class representing an IDMEF type.
 */
class Xml extends AbstractSerializer
{
    const XMLNS = 'http://iana.org/idmef';

    protected $indent;
    protected $out;
    protected $in;

    public function __construct($indent = false)
    {
        $this->indent = (bool) $indent;
    }

    //
    // Serialization
    //

    public function serialize(IDMEFMessage $message): string
    {
        $this->out = new \XMLWriter();
        $this->out->openMemory();
        $this->out->setIndent($this->indent);
        if ($this->indent) {
            $this->out->setIndentString('  ');
        }

        $this->out->startDocument('1.0', 'UTF-8');
        $message->acquireLock($message::LOCK_SHARED, true);
        try {
            if (!$message->isValid()) {
                throw new \InvalidArgumentException('Invalid IDMEF message');
            }

            $this->_serialize($message);
        } finally {
            $message->releaseLock($message::LOCK_SHARED, true);
        }
        $this->out->endDocument();
        return $this->out->outputMemory();
    }

    protected function _serialize(AbstractNode $node): void
    {
        $classes = array_merge(array(get_class($node)), class_parents($node));
        $visited = array();

        foreach (array_reverse($classes) as $cls) {
            $cls = ltrim(substr($cls, strrpos($cls, '\\')), '\\');
            $method = "visit$cls";
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $node);
                $visited[] = $cls;
            }
        }

        foreach (array_reverse($visited) as $cls) {
            $method = "depart$cls";
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $node);
            }
            // Close the tag opened by the visitXXX callback.
            $this->out->endElement();
        }

        if (empty($visited)) {
            throw new \InvalidArgumentException(implode(' < ', $classes));
        }
    }

    protected function writeAttributes(AbstractNode $node, string ...$attributes): void
    {
        foreach ($attributes as $attribute) {
            if (!isset($node->$attribute)) {
                continue;
            }

            $attr = $node->$attribute;
            if ($attr instanceof AbstractType) {
                $this->out->writeAttribute($attribute, (string) $attr);
            } else {
                throw new \InvalidArgumentException(get_class($node) . ".$attribute");
            }
        }
    }

    protected function writeElements(AbstractNode $node, string ...$elements): void
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

    protected function visitAbstractType(AbstractNode $node): void
    {
        $this->out->startElement($node::XML_TYPE);
        $this->out->text((string) $node);
    }

    protected function visitIDMEFMessage(AbstractNode $node): void
    {
        $this->out->startElementNS(null, 'IDMEF-Message', 'http://iana.org/idmef');
        $this->out->writeAttribute('version', '1.0');
        foreach ($node as $child) {
            $this->_serialize($child);
        }
    }

    protected function visitAlert(AbstractNode $node): void
    {
        $this->out->startElement('Alert');
        $this->writeAttributes($node, 'messageid');
        $this->writeElements(
            $node,
            'Analyzer',
            'CreateTime',
            'DetectTime',
            'AnalyzerTime',
            'Source',
            'Target',
            'Classification',
            'Assessment',
            'ToolAlert',
            'OverflowAlert',
            'CorrelationAlert',
            'AdditionalData'
        );
    }

    protected function visitHeartbeat(AbstractNode $node): void
    {
        $this->out->startElement('Heartbeat');
        $this->writeAttributes($node, 'messageid');
        $this->writeElements(
            $node,
            'Analyzer',
            'CreateTime',
            'HeartbeatInterval',
            'AnalyzerTime',
            'AdditionalData'
        );
    }

    protected function visitCorrelationAlert(AbstractNode $node): void
    {
        $this->out->startElement('CorrelationAlert');
        $this->writeElements($node, 'name', 'alertident');
    }

    protected function visitOverflowAlert(AbstractNode $node): void
    {
        $this->out->startElement('OverflowAlert');
        $this->writeAttributes($node, 'program', 'size', 'buffer');
    }

    protected function visitToolAlert(AbstractNode $node): void
    {
        $this->out->startElement('ToolAlert');
        $this->writeAttributes($node, 'name', 'command');
        $this->writeElements($node, 'alertident');
    }

    protected function visitAdditionalData(AbstractNode $node): void
    {
        $this->out->startElement('AdditionalData');
        $this->writeAttributes($node, 'type', 'meaning');
        $this->_serialize($node->data);
    }

    protected function visitAnalyzer(AbstractNode $node): void
    {
        $this->out->startElement('Analyzer');
        $this->writeAttributes(
            $node,
            'analyzerid',
            'name',
            'manufacturer',
            'model',
            'version',
            'class',
            'ostype',
            'osversion'
        );
        $this->writeElements($node, 'Node', 'Process', 'Analyzer');
    }

    protected function visitClassification(AbstractNode $node): void
    {
        $this->out->startElement('Classification');
        $this->writeAttributes($node, 'ident', 'text');
        $this->writeElements($node, 'Reference');
    }

    protected function visitSource(AbstractNode $node): void
    {
        $this->out->startElement('Source');
        $this->writeAttributes($node, 'ident', 'spoofed', 'interface');
        $this->writeElements($node, 'Node', 'User', 'Process', 'Service');
    }

    protected function visitTarget(AbstractNode $node): void
    {
        $this->out->startElement('Target');
        $this->writeAttributes($node, 'ident', 'decoy', 'interface');
        $this->writeElements($node, 'Node', 'User', 'Process', 'Service', 'File');
    }

    protected function visitAssessment(AbstractNode $node): void
    {
        $this->out->startElement('Assessment');
        $this->writeElements($node, 'Impact', 'Action', 'Confidence');
    }

    protected function visitReference(AbstractNode $node): void
    {
        $this->out->startElement('Reference');
        $this->writeAttributes($node, 'origin', 'meaning');
        $this->writeElements($node, 'name', 'url');
    }

    protected function visitNode(AbstractNode $node): void
    {
        $this->out->startElement('Node');
        $this->writeAttributes($node, 'ident', 'category');
        $this->writeElements($node, 'location', 'name', 'Address');
    }

    protected function visitAddress(AbstractNode $node): void
    {
        $this->out->startElement('Address');
        $this->writeAttributes($node, 'ident', 'category', 'vlan-name', 'vlan-num');
        $this->writeElements($node, 'address', 'netmask');
    }

    protected function visitFile(AbstractNode $node): void
    {
        $this->out->startElement('File');
        $this->writeAttributes($node, 'ident', 'category', 'fstype', 'file-type');
        $this->writeElements(
            $node,
            'name',
            'path',
            'create-time',
            'modify-time',
            'access-time',
            'data-size',
            'disk-size',
            'FileAccess',
            'Linkage',
            'Inode',
            'Checksum'
        );
    }

    protected function visitPermission(AbstractNode $node): void
    {
        $this->out->startElement('Permission');
        $this->writeAttributes($node, 'perms');
    }

    protected function visitFileAccess(AbstractNode $node): void
    {
        $this->out->startElement('FileAccess');
        $this->writeElements($node, 'UserId', 'Permission');
    }

    protected function visitInode(AbstractNode $node): void
    {
        $this->out->startElement('Inode');
        $this->writeElements(
            $node,
            'change-time',
            'number',
            'major-device',
            'minor-device',
            'c-major-device',
            'c-minor-device'
        );
    }

    protected function visitLinkage(AbstractNode $node): void
    {
        $this->out->startElement('Linkage');
        $this->writeAttributes($node, 'category');
        $this->writeElements($node, 'name', 'path', 'File');
    }

    protected function visitChecksum(AbstractNode $node): void
    {
        $this->out->startElement('Checksum');
        $this->writeAttributes($node, 'algorithm');
        $this->writeElements($node, 'value', 'key');
    }

    protected function visitProcess(AbstractNode $node): void
    {
        $this->out->startElement('Process');
        $this->writeAttributes($node, 'ident');
        $this->writeElements($node, 'name', 'pid', 'path', 'arg', 'env');
    }

    protected function visitService(AbstractNode $node): void
    {
        $this->out->startElement('Service');
        $this->writeAttributes(
            $node,
            'ident',
            'ip_version',
            'iana_protocol_number',
            'iana_protocol_name'
        );
        $this->writeElements(
            $node,
            'name',
            'port',
            'portlist',
            'protocol',
            'SNMPService',
            'WebService'
        );
    }

    protected function visitSNMPService(AbstractNode $node): void
    {
        $this->out->startElement('SNMPService');
        $this->writeElements(
            $node,
            'oid',
            'messageProcessingModel',
            'securityModel',
            'securityName',
            'securityLevel',
            'contextName',
            'contextEngineID',
            'command'
        );
    }

    protected function visitUser(AbstractNode $node): void
    {
        $this->out->startElement('User');
        $this->writeAttributes($node, 'ident', 'category');
        $this->writeElements($node, 'UserId');
    }

    protected function visitUserId(AbstractNode $node): void
    {
        $this->out->startElement('UserId');
        $this->writeAttributes($node, 'ident', 'type', 'tty');
        $this->writeElements($node, 'name', 'number');
    }

    protected function visitWebService(AbstractNode $node): void
    {
        $this->out->startElement('WebService');
        $this->writeElements($node, 'url', 'cgi', 'http-method', 'arg');
    }

    protected function visitAction(AbstractNode $node): void
    {
        $this->out->startElement('Action');
        $this->writeAttributes($node, 'category');
    }

    protected function visitCreateTime(AbstractNode $node): void
    {
        $this->out->startElement('CreateTime');
        $this->writeAttributes($node, 'ntpstamp');
        $this->out->text($node->ntpstamp->getValue()->format(DateTimeType::FORMAT));
    }

    protected function visitDetectTime(AbstractNode $node): void
    {
        $this->out->startElement('DetectTime');
        $this->writeAttributes($node, 'ntpstamp');
        $this->out->text($node->ntpstamp->getValue()->format(DateTimeType::FORMAT));
    }

    protected function visitAnalyzerTime(AbstractNode $node): void
    {
        $this->out->startElement('AnalyzerTime');
        $this->writeAttributes($node, 'ntpstamp');
        $this->out->text($node->ntpstamp->getValue()->format(DateTimeType::FORMAT));
    }

    protected function visitConfidence(AbstractNode $node): void
    {
        $this->out->startElement('Confidence');
        $this->writeAttributes($node, 'rating');
    }

    protected function visitImpact(AbstractNode $node): void
    {
        $this->out->startElement('Impact');
        $this->writeAttributes($node, 'severity', 'completion', 'type');
    }

    protected function visitAlertIdent(AbstractNode $node): void
    {
        $this->out->startElement('alertident');
        $this->writeAttributes($node, 'analyzerid');
        if (isset($node->alertident)) {
            $this->out->text((string) $node->alertident);
        }
    }

    //
    // Unserialization
    //

    public function unserialize(string $serialized): IDMEFMessage
    {
        $options = LIBXML_NONET | LIBXML_PARSEHUGE | LIBXML_HTML_NOIMPLIED |
                   LIBXML_HTML_NODEFDTD | LIBXML_NOXMLDECL;
        libxml_clear_errors();
        $uie = libxml_use_internal_errors(true);
        try {
            $this->in = new \XMLReader();
            $this->in->XML($serialized, null, $options);
            try {
                return $this->processDocument();
            } finally {
                $this->in->close();
            }
        } finally {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            libxml_use_internal_errors($uie);

            if (count($errors)) {
                XmlValidationErrors::raiseOnValidationErrors($errors);
            }
        }
    }

    protected function processDocument(): IDMEFMessage
    {
        $rng = dirname(dirname(__DIR__)) .
                DIRECTORY_SEPARATOR . 'data' .
                DIRECTORY_SEPARATOR . 'IDMEF.rng';
        $this->in->setRelaxNGSchema($rng);
        $this->in->setParserProperty(\XMLReader::SUBST_ENTITIES, false);

        $in = $this->in;
        libxml_set_external_entity_loader(function ($public, $system, $context) use ($in) {
            if ($public === "-//IETF//DTD IDMEF RFC 4765//EN") {
                $in->setParserProperty(\XMLReader::VALIDATE, true);
                $dtd = dirname(dirname(__DIR__)) .
                        DIRECTORY_SEPARATOR . 'data' .
                        DIRECTORY_SEPARATOR . 'IDMEF.dtd';
                return fopen($dtd, 'r');
            }
            return null;
        });

        try {
            return $this->iterateData();
        } finally {
            libxml_set_external_entity_loader(null);
        }
    }

    protected function iterateData(): IDMEFMessage
    {
        $value = null;
        $xmlStack = array(new IDMEFMessage());
        $successfulRead = $this->in->read();
        while ($successfulRead !== false) {
            switch ($this->in->nodeType) {
                case \XMLReader::TEXT:
                    $value = $this->in->value;
                    break;

                case \XMLReader::ELEMENT:
                    $value = null;

                    if ($this->in->namespaceURI !== self::XMLNS) {
                        $successfulRead = $this->in->next();
                        continue 2;
                    }

                    if ($this->in->localName === 'IDMEF-Message') {
                        break;
                    }

                    $current = $xmlStack[count($xmlStack) - 1];
                    try {
                        $current = $current->{$this->in->localName};
                        if ($current instanceof AbstractClass) {
                            $xmlStack[] = $current;
                        }
                    } catch (\InvalidArgumentException $e) {
                        // Non-existent attribute or it refers to a type
                        // which requires a value.
                    }

                    if ($current instanceof AbstractList) {
                        $cls = "\\fpoirotte\\IDMEF\\Classes\\" . $this->in->localName;
                        if (!class_exists($cls)) {
                            $successfulRead = $this->in->next();
                            continue 2;
                        }

                        $current[] = new $cls;
                        $current = $current[-1];
                        $xmlStack[] = $current;
                    }

                    $xmlns = $this->in->namespaceURI;
                    if ($this->in->hasAttributes) {
                        while ($this->in->moveToNextAttribute()) {
                            $attrns = $this->in->namespaceURI === '' ? $xmlns : $this->in->namespaceURI;
                            if ($attrns !== self::XMLNS) {
                                continue;
                            }

                            $current->{$this->in->localName} = $this->in->value;
                        }
                        $this->in->moveToElement();
                    }

                    if (!$this->in->isEmptyElement) {
                        break;
                    }

                    // Intentional fall-through

                case \XMLReader::END_ELEMENT:
                    $current = $xmlStack[count($xmlStack)-1];

                    if ($value !== null) {
                        $blacklist = array('CreateTime', 'DetectTime', 'AnalyzerTime');
                        if (!in_array($this->in->localName, $blacklist, true)) {
                            $adtypes = array(
                                'boolean',
                                'byte',
                                'character',
                                'date-time',
                                'integer',
                                'ntpstamp',
                                'portlist',
                                'real',
                                'string',
                                'byte-string',
                                'xmltext',
                            );

                            $attr   = $this->in->localName;
                            if (in_array($this->in->localName, $adtypes, true)) {
                                $attr   = str_replace(' ', '', ucwords(str_replace('-', ' ', $this->in->localName)));
                                $cls    = "fpoirotte\\IDMEF\\Types\\${attr}Type";
                                $value  = unserialize_type($cls, $value);

                                if ($current instanceof AdditionalData) {
                                    $attr   = 'data';
                                }
                            }

                            $current->$attr = $value;
                        }
                        $value = null;
                    }

                    $cls = "\\fpoirotte\\IDMEF\\Classes\\" . $this->in->localName;
                    if ($this->in->depth < count($xmlStack) &&
                        $xmlStack[$this->in->depth] instanceof $cls) {
                        array_pop($xmlStack);
                    }

                    break;
            }

            $successfulRead = $this->in->read();
        }

        if (count($xmlStack) !== 1) {
            throw new \RuntimeException('Smashed stack');
        }
        return $xmlStack[0];
    }
}
