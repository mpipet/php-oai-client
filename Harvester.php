<?php
namespace Oai;

/**
 * Date: 09/09/15
 * Time: 00:28
 */
class Harvester
{
    protected $metadataFormat;

    protected $set;

    protected $oaiClient;

    protected $maxRetry = 3;

    protected $currentRetry = 0;

    public function __construct($url, $metadataFormat, $set = '')
    {
        $this->oaiClient = new OaiClient($url);
        $this->metadataFormat = $metadataFormat;
        $this->set = $set;
    }

    public function launch(callable $callback)
    {
        $token = null;
        do {
            $token = $this->harvestSegment($callback, $token);
        } while ($token != null);
    }

    public function harvestSegment(callable $callback, $token = null)
    {
        $listRecord = $this->oaiClient->listRecords($this->metadataFormat, $this->set, $token);
        $parser = new OaiParser($listRecord);
        $records = $parser->parseRecordsList();
        $callback($records);
        return $records['infos']['token'];
    }

}