<?php
require('./Exceptions/ParseException.php');

class OaiParser
{

    protected $simpleXMLObject = null;

    public function __construct($xml)
    {
        \libxml_use_internal_errors(true);
        $this->simpleXMLObject = new SimpleXMLElement($xml);
        $this->simpleXMLObject = $this->registerOaiNameSpaces($this->simpleXMLObject);
        $this->hasXmlErrorOccured();

    }

    /**
     * @param SimpleXMLElement $simpleXml
     * @return SimpleXMLElement
     */
    public function registerOaiNameSpaces(SimpleXMLElement $simpleXml)
    {
        $simpleXml->registerXPathNamespace('oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
        $simpleXml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
        //$simpleXml->registerXPathNamespace('OAI-PMH', 'http://purl.org/dc/elements/1.1/');
        return $simpleXml;
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

    public function parseRecordsList()
    {


        $list = [];
        $records = $this->simpleXMLObject->ListRecords->record;

        foreach ($records as $record) {

            if ((string)$record->header['status'] !== 'deleted') {
                $list[(string)$record->header->identifier] = $this->getRecordFields($record);
            }
        }

        $recordsList = [
            'list' => [],
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

        $prefixNode = $record->metadata->children('oai_dc', true);


        $fieldsNodes = $prefixNode->children('dc', true);
        foreach ($fieldsNodes as $fieldType => $field) {
            $attrs = $field->attributes('http://www.w3.org/XML/1998/namespace');
            if(!isset($attrs['lang']) || $attrs['lang'] === 'fr' || $attrs['lang'] === 'fre'){
                $recordFields[$fieldType][] = (string)$field;
            }

        }


        var_dump($recordFields);
        die;

    }

    public function hasXmlErrorOccured()
    {
        if (count(\libxml_get_errors()) !== 0) {
            libxml_clear_errors();
            throw new ParseException('Oai returned data could not be parsed.');
        }

    }
}