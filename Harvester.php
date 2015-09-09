<?php
namespace Oai;
require_once('OaiClient.php');
require_once('OaiParser.php');

/**
 * Date: 09/09/15
 * Time: 00:28
 */
class Harvester
{
    protected $metadataFormat;

    protected $set;

    protected $oaiClient;

    public function __construct($url, $metadataFormat, $set = '')
    {
        $this->oaiClient = new OaiClient($url);
        $this->metadataFormat = $metadataFormat;
        $this->set = $set;
    }

    public function launch($callback, $token = null)
    {
        $listRecord = $this->oaiClient->listRecords($this->metadataFormat, $this->set, $token);
        $parser = new OaiParser($listRecord);
        $records = $parser->parseRecordsList();
        $callback($records);
        if ($records['infos']['token'] != null) {
            $this->launch($callback, $records['infos']['token']);
        }

    }

}