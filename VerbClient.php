<?php
namespace Oai;

use Oai\Exceptions\VerbException;

class VerbClient
{
    protected $url;
    protected $timeout = 5;

    /**
     * @param string $url
     */
    public function __construct($url = '')
    {
        $this->url = $url;
    }

    /**
     * @param $params
     * @return mixed
     * @throws VerbException
     */
    protected function fetchService($params)
    {
        $url = $this->url . '?verb=' . $params;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        $xml = curl_exec($curl);

        if (curl_error($curl) || $xml === '') {
            throw new VerbException(
                VerbException::STORE_UNREACHABLE,
                ['URL' => $url]
            );
        }
        curl_close($curl);

        return $xml;
    }

    /**
     * @return mixed
     */
    public function listSets()
    {
        return $this->fetchService('ListSets');
    }

    /**
     * @return mixed
     */
    public function identify()
    {
        return $this->fetchService('Identify');
    }

    /**
     * @return mixed
     */
    public function listMetadataFormats()
    {
        return $this->fetchService('ListMetadataFormats');
    }

    /**
     * @param $metadataPrefix
     * @param $setSpec
     * @param null $token
     * @return mixed
     */
    public function listRecords($metadataPrefix, $setSpec = null, $token = null)
    {
        if (!empty($setSpec)) {
            $params = 'ListRecords&metadataPrefix=' . $metadataPrefix . '&set=' . $setSpec;
        } else {
            $params = 'ListRecords&metadataPrefix=' . $metadataPrefix;
        }
        if ($token) {
            $params = 'ListRecords&resumptionToken=' . $token;
        }
        return $this->fetchService($params);
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}