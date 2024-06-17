<?php

namespace tfphp\framework\system\server;

use http\Exception;
use tfphp\tfphp;

class tfresponse{
    private tfphp $tfphp;
    const T_DATA_HTML = 0;
    const T_DATA_JSON = 1;
    const T_DATA_PLAINTEXT = 2;
    protected int $statusCode;
    protected int $dataType;
    protected string $dataCharset;
    protected $data;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->statusCode = 200;
        $this->dataType = tfresponse::T_DATA_HTML;
        $this->dataCharset = "UTF-8";
        $this->data = null;
    }
    public function setStatusCode(int $code){
        $this->statusCode = $code;
    }
    public function setDataType(int $dataType){
        $this->dataType = $dataType;
    }
    public function setDataCharset(string $charset){
        $this->dataCharset = $charset;
    }
    public function setData($data){
        $this->data = $data;
    }
    private function getStatusMessageByCode(int $code): ?string{
        $statusArr = [
            100=>"Continue",
            200=>"OK",
            301=>"Moved Permanently",
            302=>"Move Temporarily",
            304=>"Not Modified",
            400=>"Bad Request",
            401=>"Unauthorized",
            403=>"Forbidden",
            404=>"Not Found",
            405=>"Method Not Allowed",
            500=>"Internal Server Error",
            502=>"Bad Gateway",
            503=>"Service Unavailable",
            504=>"Gateway Timeout",
        ];
        if(isset($statusArr[$code])){
            return $statusArr[$code];
        }
        return null;
    }
    public function header(string $key, string $value){
        header($key. ": ". $value);
    }
    public function response(){
        header("HTTP/1.1 ". $this->statusCode. " ". $this->getStatusMessageByCode($this->statusCode));
        switch ($this->dataType){
            case tfresponse::T_DATA_JSON:
                $this->header("Content-Type", "application/json; charset=". $this->dataCharset);
                $jsonData = json_encode($this->data);
                echo $jsonData;
                break;
            case tfresponse::T_DATA_HTML:
                $this->header("Content-Type", "text/html; charset=". $this->dataCharset);
                echo $this->data;
                break;
            case tfresponse::T_DATA_PLAINTEXT:
                $this->header("Content-Type", "text/plaintext; charset=". $this->dataCharset);
                echo $this->data;
                break;
            default:
                throw new \Exception("invalid data type ". strval($this->dataType));
        }
    }
}