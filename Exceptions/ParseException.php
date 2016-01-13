<?php
namespace Oai\Exceptions;

use Exception;

/**
 * User: mpipet
 * Date: 09/09/15
 * Time: 00:07
 */
class ParseException extends OaiException
{

    const RECORD_LIST_EMPTY = 'RECORD_LIST_EMPTY';
    const EMPTY_XML_NODE = 'EMPTY_XML_NODE';
    const OAI_ERROR = 'OAI_ERROR';
    const MALFORMED_XML = 'MALFORMED_XML';

    protected $xml = '';

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
        parent::__construct($message, $datas, $previous);
    }

    /**
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

}