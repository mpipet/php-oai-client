<?php
require('./Exceptions/OaiException.php');

class OaiClient
{

    protected $url = '';

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;

    }


    /**
     * @param $params
     * @return mixed
     * @throws OaiException
     */
    protected function fetchService($params)
    {
        $url = $this->url . '?verb=' . $params;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        var_dump($url);
        $xml = curl_exec($curl);
        curl_close($curl);

        if (curl_error($curl) || $xml === '') {
            throw new OaiException(
                sprintf('Le service à l\'adresse: "%s" est injoignable.', $url)
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
        if ($setSpec) {
            $params = 'ListRecords&metadataPrefix=' . $metadataPrefix . '&set=' . $setSpec;
        } else {
            $params = 'ListRecords&metadataPrefix=' . $metadataPrefix;
        }
        if ($token) {
            $params = 'ListRecords&resumptionToken=' . $token;
        }
        return $this->fetchService($params);
    }

}