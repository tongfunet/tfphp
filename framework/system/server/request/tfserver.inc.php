<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system\server\request;

use tfphp\framework\tfphp;

class tfserver extends tfvars {
    private array $_pathArgs;
    public function __construct(){
        parent::__construct($_SERVER);
        $this->_pathArgs = (isset($_SERVER["PATH_ARGV"]) && is_array($_SERVER["PATH_ARGV"])) ? $_SERVER["PATH_ARGV"] : [];
    }
    public function pathArgv(string $key){
        return (isset($this->_pathArgs[$key])) ? $this->_pathArgs[$key] : null;
    }
    public function pathArgs(){
        return $this->_pathArgs;
    }
    public function remoteAddr(): ?string{
        if(isset($_SERVER["REMOTE_ADDR"])) return $_SERVER["REMOTE_ADDR"];
        return null;
    }
    public function realRemoteAddr(): ?string{
        if(isset($_SERVER["HTTP_X_REAL_IP"])) return $_SERVER["HTTP_X_REAL_IP"];
        else if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) return $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if(isset($_SERVER["REMOTE_ADDR"])) return $_SERVER["REMOTE_ADDR"];
        return null;
    }
}