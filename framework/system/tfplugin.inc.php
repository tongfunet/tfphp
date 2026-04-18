<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\system;

use tfphp\framework\tfphp;

class tfplugin {
    protected tfphp $tfphp;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
    }
}