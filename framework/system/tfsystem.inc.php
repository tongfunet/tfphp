<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system;

use tfphp\framework\tfphp;

class tfsystem {
    protected tfphp $tfphp;
    private array $plugins;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->plugins = [];
    }
    public function registerPlugin(string $pluginName, string $className, string $entryMethodName=null){
        $class = new \ReflectionClass($className);
        $parentClassName = $class->getParentClass()->getName();
        if(!preg_match("/\\\\(tfplugin)$/", $parentClassName)){
            throw new \Exception("class '" . $className . "' is invalid", 660701);
        }
        $instance = $class->newInstance($this->tfphp);
        if($entryMethodName){
            $instance->$entryMethodName();
        }
        return $this->plugins[$pluginName] = $instance;
    }
    public function getPlugin(string $pluginName){
        if(isset($this->plugins[$pluginName])){
            return $this->plugins[$pluginName];
        }
        throw new \Exception("plugin '". $pluginName. "' is not found", 660702);
    }
    public function __get($name){
        if(isset($this->plugins[$name])){
            return $this->plugins[$name];
        }
        return null;
    }
}