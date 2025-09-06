<?php 

namespace tfphp\framework\system\server;

use http\Exception;
use tfphp\framework\tfphp;

class tfresponse{
    private tfphp $A;
    const T_DATA_HTML = 0;
    const T_DATA_JSON = 1;
    const T_DATA_PLAINTEXT = 2;
    protected int $statusCode;
    protected int $dataType;
    protected string $dataCharset;
    protected $data;
    public function __construct(tfphp $B8){
        $this->A = $B8;
        $this->statusCode = 200;
        $this->dataType = tfresponse::T_DATA_HTML;
        $this->dataCharset = "UTF-8";
        $this->data = null;
    }
    private function C0(int $C6): ?string{
        $CA = [
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
        if(isset($CA[$C6])){
            return $C6. " ". $CA[$C6];
        }
        return null;
    }
    public function setStatusCode(int $C6){
        $this->statusCode = $C6;
    }
    public function setDataType(int $AB){
        $this->dataType = $AB;
    }
    public function setDataCharset(string $CE){
        $this->dataCharset = $CE;
    }
    public function setData($B2){
        $this->data = $B2;
    }
    public function header(string $D2, string $D3){
        header($D2. ": ". $D3);
    }
    public function response(){
        header("HTTP/1.1 ". $this->C0($this->statusCode));
        switch ($this->dataType){
            case tfresponse::T_DATA_JSON:
                $this->header("Content-Type", "application/json; charset=". $this->dataCharset);
                $D5 = json_encode($this->data);
                echo $D5;
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
                throw new \Exception("invalid data type ". strval($this->dataType), 666051);
        }
    }
    public function responseMIMEData($B2, ?int $D7, ?string $AC){
        $this->setDataType(($D7 !== null && $D7 >= 0 && $D7 <= 2) ? $D7 : tfresponse::T_DATA_PLAINTEXT);
        $this->setDataCharset(($AC !== null) ? $AC : "UTF-8");
        $this->setData($B2);
        $this->response();
    }
    public function responseJSONData($B2, string $AC=null){
        $this->responseMIMEData($B2, tfresponse::T_DATA_JSON, $AC);
    }
    public function JSONData($B2, string $AC=null){
        $this->responseJSONData($B2, $AC);
    }
    public function responseHTMLData($B2, string $AC=null){
        $this->responseMIMEData($B2, tfresponse::T_DATA_HTML, $AC);
    }
    public function HTMLData($B2, string $AC=null){
        $this->responseHTMLData($B2, $AC);
    }
    public function responsePlainTextData($B2, string $AC=null){
        $this->responseMIMEData($B2, tfresponse::T_DATA_PLAINTEXT, $AC);
    }
    public function PlainTextData($B2, string $AC=null){
        $this->responsePlainTextData($B2, $AC);
    }
    public function JSON(int $A7, $B2, string $AC=null){
        $this->setStatusCode($A7);
        $this->responseJSONData($B2, $AC);
    }
    public function HTML(int $A7, $B2, string $AC=null){
        $this->setStatusCode($A7);
        $this->responseHTMLData($B2, $AC);
    }
    public function PlainText(int $A7, $B2, string $AC=null){
        $this->setStatusCode($A7);
        $this->responsePlainTextData($B2, $AC);
    }
    public function responseMIMEFile(string $D8){
        $DD = "";
        if(($E1 = strrpos($D8, ".")) !== false) $DD = strtolower(substr($D8, $E1+1));
        $E4 = "";
        switch ($DD){
            case "css":
                $E4 = "text/css";
                break;
            case "js":
                $E4 = "text/javascript";
                break;
            case "json":
                $E4 = "application/json";
                break;
            case "xml":
                $E4 = "text/xml";
                break;
            case "jpg":
            case "jpeg":
                $E4 = "image/jpeg";
                break;
            case "png":
                $E4 = "image/png";
                break;
            case "gif":
                $E4 = "image/gif";
                break;
            case "txt":
                $E4 = "text/plaintext";
                break;
            case "ico":
                $E4 = "image/x-icon";
                break;
        }
        if(!file_exists($D8)){
            $this->setStatusCode(404);
            $this->response();
        }
        else if(!$E4){
            $this->setStatusCode(403);
            $this->response();
        }
        else{
            header("HTTP/1.1 200 OK");
            header("Content-Type: ". $E4);
            echo file_get_contents($D8);
        }
    }
    public function location(string $E7){
        header("HTTP/1.1 302 Moved");
        header("Location: ". $E7);
    }
}