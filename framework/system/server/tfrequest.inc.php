<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

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
    private ?tfget $get;
    private ?tfpost $post;
    private ?tffiles $files;
    private ?tfcookie $cookie;
    private ?tfserver $server;
    private ?tfsession $session;
    private ?tfglobals $globals;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->get = $this->post = $this->files = $this->cookie = $this->server = $this->session = $this->globals = null;
    }
    public function get(): tfget{
        return ($this->get === null) ? new tfget() : $this->get;
    }
    public function post(): tfpost{
        return ($this->post === null) ? new tfpost() : $this->post;
    }
    public function files(): tffiles{
        return ($this->files === null) ? new tffiles() : $this->files;
    }
    public function cookie(): tfcookie{
        return ($this->cookie === null) ? new tfcookie() : $this->cookie;
    }
    public function server(): tfserver{
        return ($this->server === null) ? new tfserver() : $this->server;
    }
    public function session(): tfsession{
        return ($this->session === null) ? new tfsession() : $this->session;
    }
    public function globals(): tfglobals{
        return ($this->globals === null) ? new tfglobals() : $this->globals;
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
    public function __get($name){
        return $this->get()->get($name)
            ?? $this->post()->get($name)
            ?? $this->files()->get($name)
            ?? $this->cookie()->get($name)
            ?? $this->server()->get($name)
            ?? $this->session()->get($name)
            ?? $this->globals()->get($name);
    }
}