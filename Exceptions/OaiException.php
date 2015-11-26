<?php
namespace Oai\Exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bedervet
 * Date: 09/09/15
 * Time: 00:07
 */
class OaiException extends \Exception
{
    const STORE_UNREACHABLE = 'STORE_UNREACHABLE';


    protected $xml = '';
    protected $datas = [];


    /**
     * @param string $message
     * @param array $datas
     * @param Exception|null $previous
     */
    public function __construct($message = "", $datas = [], Exception $previous = null)
    {

        $this->datas = $datas;
        parent::__construct($message, 0, $previous);
    }


    /**
     * @return array|\Exception
     */
    public function getDatas()
    {
        return $this->datas;
    }
}