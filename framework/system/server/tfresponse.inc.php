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
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->statusCode = 200;
        $this->dataType = tfresponse::T_DATA_HTML;
        $this->dataCharset = "UTF-8";
        $this->data = null;
    }
    private function getStatus(int $code): ?string{
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
            return $code. " ". $statusArr[$code];
        }
        return null;
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
    public function header(string $key, string $value){
        header($key. ": ". $value);
    }
    public function response(){
        header("HTTP/1.1 ". $this->getStatus($this->statusCode));
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
                $this->header("Content-Type", "text/plain; charset=". $this->dataCharset);
                echo $this->data;
                break;
            default:
                throw new \Exception("invalid data type ". strval($this->dataType));
        }
    }
    public function responseMIMEData($data, ?int $dataMIMEType, ?string $dataCharset){
        $this->setDataType(($dataMIMEType !== null && $dataMIMEType >= 0 && $dataMIMEType <= 2) ? $dataMIMEType : tfresponse::T_DATA_PLAINTEXT);
        $this->setDataCharset(($dataCharset !== null) ? $dataCharset : "UTF-8");
        $this->setData($data);
        $this->response();
    }
    public function responseJsonData($data, string $dataCharset=null, bool $stopScript=true){
        $this->responseMIMEData($data, tfresponse::T_DATA_JSON, $dataCharset);
        if($stopScript) die;
    }
    public function responseHtmlData($data, string $dataCharset=null, bool $stopScript=true){
        $this->responseMIMEData($data, tfresponse::T_DATA_HTML, $dataCharset);
        if($stopScript) die;
    }
    public function responsePlaintextData($data, string $dataCharset=null, bool $stopScript=true){
        $this->responseMIMEData($data, tfresponse::T_DATA_PLAINTEXT, $dataCharset);
        if($stopScript) die;
    }
    public function responseMIMEFile(string $filepath, bool $stopScript=true){
        $extension = "";
        if(($p = strrpos($filepath, ".")) !== false) $extension = strtolower(substr($filepath, $p+1));
        $mimeType = "";
        switch ($extension){
            case "css":
                $mimeType = "text/css";
                break;
            case "js":
                $mimeType = "text/javascript";
                break;
            case "json":
                $mimeType = "application/json";
                break;
            case "xml":
                $mimeType = "text/xml";
                break;
            case "jpg":
            case "jpeg":
                $mimeType = "image/jpeg";
                break;
            case "png":
                $mimeType = "image/png";
                break;
            case "gif":
                $mimeType = "image/gif";
                break;
            case "txt":
                $mimeType = "text/plaintext";
                break;
            case "ico":
                $mimeType = "image/x-icon";
                break;
        }
        if(!file_exists($filepath)){
            $this->setStatusCode(404);
            $this->response();
        }
        else if(!$mimeType){
            $this->setStatusCode(403);
            $this->response();
        }
        else{
            header("HTTP/1.1 200 OK");
            header("Content-Type: ". $mimeType);
            echo file_get_contents($filepath);
        }
        if($stopScript) die;
    }
    public function location(string $url, bool $stopScript=true){
        header("Location: ". $url);
        if($stopScript) die;
    }
}