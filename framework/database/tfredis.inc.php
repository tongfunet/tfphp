<?php

namespace tfphp\framework\database;

use tfphp\framework\tfphp;

class tfredis{
    private tfphp $tfphp;
    private array $params;
    private bool $ready;
    private \Redis $redis;
    public function __construct(tfphp $tfphp, array $params){
        $this->tfphp = $tfphp;
        $this->params = $params;
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
    public function setObject(string $key, $value): bool{
        $this->readyTest();
        $value = serialize($value);
        if($value === false){
            return false;
        }
        $ret = $this->redis->set($key, $value);
        if($ret === false){
            return false;
        }
        return true;
    }
    public function getObject(string $key){
        $this->readyTest();
        $value = $this->redis->get($key);
        if($value === false){
            return false;
        }
        $value = unserialize($value);
        if($value === false){
            return false;
        }
        return $value;
    }
    public function expire(string $key, int $expires): bool{
        $this->readyTest();
        $ret = $this->redis->expire($key, $expires);
        if($ret === false){
            return false;
        }
        return true;
    }
    public function delete(string $key): bool{
        $this->readyTest();
        $ret = $this->redis->del($key);
        if(!$ret){
            return false;
        }
        return true;
    }
    public function getRedis(): \Redis{
        $this->readyTest();
        return $this->redis;
    }
}