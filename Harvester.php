<?php
namespace Oai;
use Exception;

/**
 * Date: 09/09/15
 * Time: 00:28
 */
class Harvester extends OaiClient
{
    protected $metadataFormat = 'oai_dc';

    protected $set;

    protected $maxRetry = 3;

    /**
     * @param callable $callback
     */
    public function launch(callable $callback)
    {
        $token = null;
        do {
            $token = $this->harvestSegment($callback, $token);
        } while ($token != null);
    }

    /**
     * @param callable $callback
     * @param null $token
     * @return mixed
     */
    public function harvestSegment(callable $callback, $token = null)
    {
        $retry = 0;
        while (empty($records) && $retry <= $this->maxRetry) {
            $records = [];
            try {
                $records = $this->listRecords($this->metadataFormat, $this->set, $token);
                $callback($records);
            } catch (Exception $e) {
                var_dump('retry');
                $retry += 1;
            }
        }

        return $records['infos']['token'];
    }

    /**
     * @param mixed $set
     */
    public function setSet($set)
    {
        $this->set = $set;
    }

    /**
     * @param mixed $metadataFormat
     */
    public function setMetadataFormat($metadataFormat)
    {
        $this->metadataFormat = $metadataFormat;
    }

}