<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system\server\request;

use tfphp\framework\tfphp;

class tfvars{
    private array $items;
    public function __construct(array &$vars){
        $this->items = $vars;
    }
    public function get(string $key){
        return (isset($this->items[$key])) ? $this->items[$key] : null;
    }
    public function set(string $key, $value){
        $this->items[$key] = $value;
    }
    public function items(): array{
        return $this->items;
    }
    public function __get($name){
        if(isset($this->items[$name])){
            return $this->items[$name];
        }
        return null;
    }
}