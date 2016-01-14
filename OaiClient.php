<?php

namespace Oai;

use Oai\Exceptions\ParseException;

class OaiClient extends VerbClient
{
    protected $parser;

    public function __construct($url = ''){
        parent::__construct($url);
        $this->parser = new OaiParser();
    }

    /**
     * @param $metadataPrefix
     * @param null $setSpec
     * @param null $token
     * @return array
     * @throws ParseException
     */
    public function listRecords($metadataPrefix, $setSpec = null, $token = null){
        $xml = parent::listRecords($metadataPrefix, $setSpec, $token);
        $this->parser->loadXml($xml);
        return $this->parser->parseRecordsList();
    }

    /**
     * @return array
     */
    public function listSets()
    {
        $xml = parent::listSets();
        $this->parser->loadXml($xml);
        return $this->parser->parseSetList();
    }

    /**
     * @return array
     */
    public function listMetadataFormats()
    {
        $xml = parent::listMetadataFormats();
        $this->parser->loadXml($xml);
        return $this->parser->parseMetadataFormats();
    }

    /**
     * @return array
     */
    public function identify()
    {
        $xml = parent::identify();
        $this->parser->loadXml($xml);
        return $this->parser->parseIdentify();
    }

}