<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system\server\request;

use tfphp\framework\tfphp;

class tfsession extends tfvars {
    private array $SESSION;
    public function __construct(){
        $this->SESSION = [];
        if(isset($_SESSION)){
            parent::__construct($_SESSION);
        }
        else{
            parent::__construct($this->SESSION);
        }
    }
}