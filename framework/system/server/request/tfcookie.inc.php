<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system\server\request;

use tfphp\framework\tfphp;

class tfcookie extends tfvars {
    public function __construct(){
        parent::__construct($_COOKIE);
    }
    public function set(string $key, $value){
        setcookie($key, $value);
    }
    public function setCookie(string $key, $value, int $expires=0, string $path='', string $domain='', bool $secure=false, bool $httponly=false){
        setcookie($key, $value, $expires, $path, $domain, $secure, $httponly);
    }
}