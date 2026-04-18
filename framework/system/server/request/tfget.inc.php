<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system\server\request;

use tfphp\framework\tfphp;

class tfget extends tfvars {
    public function __construct(){
        parent::__construct($_GET);
    }
}