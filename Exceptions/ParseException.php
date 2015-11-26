<?php
namespace Oai\Exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bedervet
 * Date: 09/09/15
 * Time: 00:07
 */
class ParseException extends Exception
{

    const RECORD_LIST_EMPTY = 'RECORD_LIST_EMPTY';
    const EMPTY_XML_NODE = 'EMPTY_XML_NODE';
    const OAI_ERROR = 'OAI_ERROR';
    const MALFORMED_XML = 'MALFORMED_XML';


    protected $xml = '';
    protected $datas = [];

    /**
     * @param string $message
     * @param string $xml
     * @param array $datas
     * @param \Exception|null $previous
     */
    public function __construct($message = "", $xml = '', $datas = [], Exception $previous = null)
    {

        $this->xml = $xml;
        $this->datas = $datas;
        parent::__construct($message, 0, $previous);
    }


    /**
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }


    /**
     * @return array|\Exception
     */
    public function getDatas()
    {
        return $this->datas;
    }

}