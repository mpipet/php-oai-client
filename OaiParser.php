<?php

namespace Oai;

use Exception;
use Oai\Exceptions\ParseException;
use SimpleXMLElement;

class OaiParser
{

    protected $simpleXMLObject = null;
    protected $xml = null;

    /**
     * @param $xml
     * @throws ParseException
     */
    public function __construct($xml)
    {
        \libxml_use_internal_errors(true);
        libxml_clear_errors();
        $this->xml = $xml;
        try {
            $this->simpleXMLObject = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            throw new ParseException(ParseException::MALFORMED_XML, $this->xml);
        }
        $this->hasXmlErrorOccured();
        $this->hasOaiErrorOccured();

    }

    /**
     * @return array
     */
    public function parseIdentify()
    {
        $description = '';
        $descriptions = $this->simpleXMLObject->xpath('//dc:description[@xml:lang="fre"]');
        if (!$descriptions) {
            $descriptions = $this->simpleXMLObject->xpath('//dc:description[@xml:lang="fr"]');
        }
        if (!$descriptions) {
            $descriptions = [];
        }
        foreach ($descriptions as $desc) {
            $description .= (string)$desc . "\n";
        }
        return [
            'name' => (string)$this->simpleXMLObject->Identify->repositoryName,
            'email' => (string)$this->simpleXMLObject->Identify->adminEmail,
            'description' => $description
        ];

    }

    /**
     * @return array
     */
    public function parseMetadataFormats()
    {
        $metadataFormats = [];
        $metadataFormatList = $this->simpleXMLObject->ListMetadataFormats->metadataFormat;
        foreach ($metadataFormatList as $metadataFormat) {
            $metadataFormats[(string)$metadataFormat->metadataPrefix] = (string)$metadataFormat->metadataNamespace;
        }

        return $metadataFormats;
    }

    /**
     * @return array
     */
    public function parseSetList()
    {
        $sets = [];
        foreach ($this->simpleXMLObject->ListSets->set as $set) {
            $sets[(string)$set->setSpec] = (string)$set->setName;
        }
        return $sets;
    }

    /**
     * @return array
     * @throws ParseException
     */
    public function parseRecordsList()
    {

        $list = [];
        $records = $this->simpleXMLObject->ListRecords->record;
        if (empty($records)) {
            throw new ParseException(ParseException::RECORD_LIST_EMPTY, $this->xml);
        }

        foreach ($records as $record) {

            if ((string)$record->header['status'] !== 'deleted') {
                $list[(string)$record->header->identifier] = $this->getRecordFields($record);
            }
        }

        $recordsList = [
            'list' => $list,
            'infos' => $this->getResumptionToken()
        ];
        return $recordsList;
    }


    /**
     * @return array
     */
    private function getResumptionToken()
    {
        $tokenTag = $this->simpleXMLObject->ListRecords->resumptionToken;
        $infos = [
            'token' => (string)$tokenTag,
            'total' => (string)$tokenTag['completeListSize'],
            'cursor' => (string)$tokenTag['cursor'],
        ];
        return $infos;
    }


    /**
     * @param SimpleXMLElement $record
     * @return array
     * @throws ParseException
     */
    private function getRecordFields(SimpleXMLElement $record)
    {
        $recordFields = [];
        if (isset($record->header->datestamp)) {
            $recordFields['datestamp'] = (string)$record->header->datestamp;
        }

        if (isset($record->header->setSpec)) {
            $recordFields['setSpec'] = [];
            foreach ($record->header->setSpec as $set) {
                $recordFields['setSpec'][] = (string)$set;
            }
        }
        $namespaces = $record->metadata->getNameSpaces(true);
        $namespaces = array_diff($namespaces, ['http://www.openarchives.org/OAI/2.0/']);
        $prefixes = array_keys($namespaces);
        $prefixNode = $record->metadata->children($prefixes[0], true);
        if (empty($prefixNode)) {
            throw new ParseException(ParseException::EMPTY_XML_NODE, $this->xml);
        }

        $fieldsNodes = $prefixNode->children('dc', true);
        foreach ($fieldsNodes as $fieldType => $field) {
            $attrs = $field->attributes('http://www.w3.org/XML/1998/namespace');
            if (!isset($attrs['lang']) || $attrs['lang'] === 'fr' || $attrs['lang'] === 'fre') {
                $recordFields[$fieldType][] = (string)$field;
            }

        }

        return $recordFields;
    }

    /**
     * @throws ParseException
     */
    public function hasXmlErrorOccured()
    {
        if (count(\libxml_get_errors()) !== 0) {
            libxml_clear_errors();
            throw new ParseException(ParseException::MALFORMED_XML, $this->xml);
        }

    }

    /**
     * @throws ParseException
     */
    private function hasOaiErrorOccured()
    {
        $errorNode = $this->simpleXMLObject->error;
        if (!empty((string)$errorNode)) {
            throw new ParseException(ParseException::OAI_ERROR, '', ['OAI_MESSAGE' => (string)$errorNode[0]]);
        }
    }
}
