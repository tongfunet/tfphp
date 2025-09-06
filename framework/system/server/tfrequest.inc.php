<?php 

namespace tfphp\framework\system\server;

use tfphp\framework\system\server\request\tfcookie;
use tfphp\framework\system\server\request\tffiles;
use tfphp\framework\system\server\request\tfget;
use tfphp\framework\system\server\request\tfglobals;
use tfphp\framework\system\server\request\tfpost;
use tfphp\framework\system\server\request\tfserver;
use tfphp\framework\system\server\request\tfsession;
use tfphp\framework\tfphp;

class tfrequest{
    protected tfphp $tfphp;
    private ?tfget $C;
    private ?tfpost $A3;
    private ?tffiles $A8;
    private ?tfcookie $A9;
    private ?tfserver $AD;
    private ?tfsession $B3;
    private ?tfglobals $B6;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $this->C = $this->A3 = $this->A8 = $this->A9 = $this->AD = $this->B3 = $this->B6 = null;
    }
    public function get(): tfget{
        return ($this->C === null) ? new tfget() : $this->C;
    }
    public function post(): tfpost{
        return ($this->A3 === null) ? new tfpost() : $this->A3;
    }
    public function files(): tffiles{
        return ($this->A8 === null) ? new tffiles() : $this->A8;
    }
    public function cookie(): tfcookie{
        return ($this->A9 === null) ? new tfcookie() : $this->A9;
    }
    public function server(): tfserver{
        return ($this->AD === null) ? new tfserver() : $this->AD;
    }
    public function session(): tfsession{
        return ($this->B3 === null) ? new tfsession() : $this->B3;
    }
    public function globals(): tfglobals{
        return ($this->B6 === null) ? new tfglobals() : $this->B6;
    }
    public function rawData(){
        return file_get_contents("php://input");
    }
    public function getResourceValue(): string{
        return (isset($_SERVER["RESTFUL_RESOURCE_VALUE"])) ? $_SERVER["RESTFUL_RESOURCE_VALUE"] : "";
    }
    public function getResourceFunction(): string{
        return (isset($_SERVER["RESTFUL_RESOURCE_FUNCTION"])) ? $_SERVER["RESTFUL_RESOURCE_FUNCTION"] : "";
    }
}