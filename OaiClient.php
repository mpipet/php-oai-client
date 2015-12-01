<?php

namespace Oai;
use OaiBundle\Exception\ParseException;

class OaiClient
{
    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->verbClient = new VerbClient($url);
    }


    /**
     * @param $metadataPrefix
     * @param null $setSpec
     * @param null $token
     * @return array
     * @throws ParseException
     */
    public function listRecords($metadataPrefix, $setSpec = null, $token = null){
        $xml = $this->verbClient->listRecords($metadataPrefix, $setSpec, $token);
        $parser = new OaiParser($xml);
        return $parser->parseRecordsList();
    }

    /**
     * @return array
     */
    public function listSets()
    {
        $xml = $this->verbClient->listSets();
        $parser = new OaiParser($xml);
        return $parser->parseSetList();
    }

    /**
     * @return array
     */
    public function listMetadataFormats()
    {
        $xml = $this->verbClient->listMetadataFormats();
        $parser = new OaiParser($xml);
        return $parser->parseMetadataFormats();
    }

    /**
     * @return array
     */
    public function identify()
    {
        $xml = $this->verbClient->identify();
        $parser = new OaiParser($xml);
        return $parser->parseIdentify();
    }


}