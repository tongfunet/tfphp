<?php

namespace tfphp\framework\system\server;

use http\Exception;
use tfphp\framework\tfphp;

class tfresponse{
    private tfphp $tfphp;
    const T_DATA_HTML = 0;
    const T_DATA_JSON = 1;
    const T_DATA_PLAINTEXT = 2;
    protected int $statusCode;
    protected int $dataType;
    protected string $dataCharset;
    protected $data;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $this->statusCode = 200;
        $this->dataType = tfresponse::T_DATA_HTML;
        $this->dataCharset = "UTF-8";
        $this->data = null;
    }
    private function getStatus(int $A8): ?string{
        $AB = [
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
        if(isset($AB[$A8])){
            return $A8. " ". $AB[$A8];
        }
        return null;
    }
    public function setStatusCode(int $A8){
        $this->statusCode = $A8;
    }
    public function setDataType(int $E){
        $this->dataType = $E;
    }
    public function setDataCharset(string $AE){
        $this->dataCharset = $AE;
    }
    public function setData($A2){
        $this->data = $A2;
    }
    public function header(string $B4, string $B5){
        header($B4. ": ". $B5);
    }
    public function response(){
        header("HTTP/1.1 ". $this->getStatus($this->statusCode));
        switch ($this->dataType){
            case tfresponse::T_DATA_JSON:
                $this->header("Content-Type", "application/json; charset=". $this->dataCharset);
                $BA = json_encode($this->data);
                echo $BA;
                break;
            case tfresponse::T_DATA_HTML:
                $this->header("Content-Type", "text/html; charset=". $this->dataCharset);
                echo $this->data;
                break;
            case tfresponse::T_DATA_PLAINTEXT:
                $this->header("Content-Type", "text/plain; charset=". $this->dataCharset);
                echo $this->data;
                break;
            default:
                throw new \Exception("invalid data type ". strval($this->dataType));
        }
    }
    public function responseMIMEData($A2, ?int $BE, ?string $F){
        $this->setDataType(($BE !== null && $BE >= 0 && $BE <= 2) ? $BE : tfresponse::T_DATA_PLAINTEXT);
        $this->setDataCharset(($F !== null) ? $F : "UTF-8");
        $this->setData($A2);
        $this->response();
    }
    public function responseJsonData($A2, string $F=null, bool $C1=true){
        $this->responseMIMEData($A2, tfresponse::T_DATA_JSON, $F);
        if($C1) die;
    }
    public function responseHtmlData($A2, string $F=null, bool $C1=true){
        $this->responseMIMEData($A2, tfresponse::T_DATA_HTML, $F);
        if($C1) die;
    }
    public function responsePlaintextData($A2, string $F=null, bool $C1=true){
        $this->responseMIMEData($A2, tfresponse::T_DATA_PLAINTEXT, $F);
        if($C1) die;
    }
    public function responseMIMEFile(string $C4, bool $C1=true){
        $C8 = "";
        if(($CA = strrpos($C4, ".")) !== false) $C8 = strtolower(substr($C4, $CA+1));
        $CD = "" ;
        switch ($C8){
            case "css":
                $CD = "text/css";
                break;
            case "js":
                $CD = "text/javascript" ;
                break;
            case "json":
                $CD = "application/json" ;
                break;
            case "xml":
                $CD = "text/xml" ;
                break;
            case "jpg":
            case "jpeg":
                $CD = "image/jpeg" ;
                break;
            case "png":
                $CD = "image/png" ;
                break;
            case "gif":
                $CD = "image/gif" ;
                break;
            case "txt":
                $CD = "text/plaintext" ;
                break;
            case "ico":
                $CD = "image/x-icon" ;
                break;
        }
        if(!file_exists($C4)){
            $this->setStatusCode(404);
            $this->response();
        }
        else if(!$CD){
            $this->setStatusCode(403);
            $this->response();
        }
        else{
            header("HTTP/1.1 200 OK");
            header("Content-Type: ". $CD);
            echo file_get_contents($C4);
        }
        if($C1) die;
    }
    public function location(string $D3, bool $C1=true){
        header("Location: ". $D3);
        if($C1) die;
    }
}