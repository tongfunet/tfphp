<?php 

namespace tfphp\framework\database;

use tfphp\framework\tfphp;

class tfredis{
    private tfphp $A;
    private array $A4;
    private bool $A5;
    private \Redis $A7;
    public function __construct(tfphp $AB, array $AE){
        $this->A = $AB;
        $this->A4 = $AE;
        $this->A5 = false;
    }
    private function B7(){
        if(!$this->A5){
            $this->A5 = true;
            if(empty($this->A4['host'])) $this->A4["host"] = "localhost";
            if(empty($this->A4['port'])) $this->A4["port"] = 6379;
            if(empty($this->A4['password'])) $this->A4["password"] = "";
            if(empty($this->A4['timeout'])) $this->A4["timeout"] = 0.0;
            $this->A7 = new \Redis();
            $this->A7->connect($this->A4["host"], $this->A4["port"], $this->A4["timeout"]);
            $this->A7->auth($this->A4["password"]);
        }
    }
    public function setObject(string $BA, $BF): bool{
        $this->B7();
        $BF = serialize($BF);
        if($BF === false){
            return false;
        }
        $C4 = $this->A7->set($BA, $BF);
        if($C4 === false){
            return false;
        }
        return true;
    }
    public function getObject(string $BA){
        $this->B7();
        $BF = $this->A7->get($BA);
        if($BF === false){
            return false;
        }
        $BF = unserialize($BF);
        if($BF === false){
            return false;
        }
        return $BF;
    }
    public function keys(string $C9): array{
        $this->B7();
        return $this->A7->keys($C9);
    }
    public function expire(string $BA, int $CA): bool{
        $this->B7();
        $C4 = $this->A7->expire($BA, $CA);
        if($C4 === false){
            return false;
        }
        return true;
    }
    public function delete(string $BA): bool{
        $this->B7();
        $C4 = $this->A7->del($BA);
        if(!$C4){
            return false;
        }
        return true;
    }
    public function getRedis(): \Redis{
        $this->B7();
        return $this->A7;
    }
}