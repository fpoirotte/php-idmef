<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Types;

class XmltextType extends AbstractType
{
    const XML_TYPE = 'xmltext';

    public function __construct($value)
    {
        $options =  LIBXML_NONET | LIBXML_PARSEHUGE | LIBXML_HTML_NOIMPLIED |
                    LIBXML_HTML_NODEFDTD | LIBXML_NOXMLDECL;

        $doc = new \DOMDocument();
        if (is_string($value)) {
            $doc->loadXML($value, $options);
            // This exports the XML fragment without an XML prolog.
            $value = $doc->saveXML($doc->documentElement);
        } elseif (is_object($value)) {
            if ($value instanceof \SimpleXMLElement) {
                $doc->loadXML($value->asXML(), $options);
                $value = $doc->saveXML($doc->documentElement);
            } elseif ($value instanceof \DOMDocument) {
                // This exports the XML fragment without an XML prolog.
                $value = $value->saveXML($value->documentElement);
            } elseif ($value instanceof \XMLWriter) {
                $doc->loadXML($value->flush(false), $options);
                // This exports the XML fragment without an XML prolog.
                $value = $value->saveXML($value->documentElement);
            }
        }

        if (!is_object($value) || !($value instanceof \DOMNode)) {
            throw new \InvalidArgumentException('Expected a SimpleXMLElement object');
        }

        $this->_value = $value->saveXML($value);
    }

    protected function unserialize(string $serialized): void
    {
        $options =  LIBXML_NONET | LIBXML_PARSEHUGE | LIBXML_HTML_NOIMPLIED |
                    LIBXML_HTML_NODEFDTD | LIBXML_NOXMLDECL;

        $doc = new \DOMDocument();
        $doc->loadXML($serialized, $options);

        // This exports the XML fragment without an XML prolog.
        $this->_value = $doc->saveXML($doc->documentElement);
    }
}
