<?php

namespace tfphp\framework\database;

use tfphp\framework\tfphp;

class tfredis{
    private tfphp $tfphp;
    private array $params;
    private bool $ready;
    private \Redis $redis;
    public function __construct(tfphp $A, array $A0){
        $this->tfphp = $A;
        $this->params = $A0;
        $this->ready = false;
    }
    private function readyTest(){
        if(!$this->ready){
            $this->ready = true;
            if(!$this->params["host"]) $this->params["host"] = "localhost";
            if(!$this->params["port"]) $this->params["port"] = 6379;
            if(!$this->params["password"]) $this->params["password"] = "";
            if(!$this->params["timeout"]) $this->params["timeout"] = 0.0;
            $this->redis = new \Redis();
            $this->redis->connect($this->params["host"], $this->params["port"], $this->params["timeout"]);
            $this->redis->auth($this->params["password"]);
        }
    }
    public function setObject(string $AA, $AE): bool{
        $this->readyTest();
        $AE = serialize($AE);
        if($AE === false){
            return false;
        }
        $B3 = $this->redis->set($AA, $AE) ;
        if($B3 === false){
            return false;
        }
        return true;
    }
    public function getObject(string $AA){
        $this->readyTest();
        $AE = $this->redis->get($AA);
        if($AE === false){
            return false;
        }
        $AE = unserialize($AE) ;
        if($AE === false){
            return false;
        }
        return $AE;
    }
    public function expire(string $AA, int $B8): bool{
        $this->readyTest();
        $B3 = $this->redis->expire($AA, $B8);
        if($B3 === false){
            return false;
        }
        return true;
    }
    public function delete(string $AA): bool{
        $this->readyTest();
        $B3 = $this->redis->del($AA);
        if(!$B3){
            return false;
        }
        return true;
    }
    public function getRedis(): \Redis{
        $this->readyTest();
        return $this->redis;
    }
}