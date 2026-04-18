<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\controller;

use tfphp\framework\system\tfpage;

class classDoc extends tfpage {
    protected function onLoad(){
        $this->view->setVar("args", $this->tfphp->getRequest()->server()->pathArgs());
    }
}