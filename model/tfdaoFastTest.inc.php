<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\model;

use tfphp\framework\tfphp;
use tfphp\framework\model\tfmodel;

class tfdaoFastTest extends tfmodel{
    public function __construct(tfphp $tfphp){
        parent::__construct($tfphp);
        $this->setO2O("fullUser", ["user", "user_detail"], [
            ["mapping"=>["uId"=>"uId"]] // user.uId == user_detail.uId
        ]);
        $this->setM2M("subscribeUser", ["user", "subscribe_user", "user"], [
            ["mapping"=>["uId"=>"uId"]], // user.uId == subscribe_user.uId
            ["mapping"=>["subscribeUId"=>"uId"]] // subscribe_user.subscribeUId == user.uId
        ]);
    }
}