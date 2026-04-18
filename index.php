<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp;

use tfphp\framework\tfphp;
use tfphp\plugin\security;

define("TFPHP_DOCUMENT_ROOT", __DIR__);
define("TFPHP_DEBUG", true);
require "framework/tfphp.inc.php";
class index{
    public function getStaticRoutes(): array{
        return [
            "/css/"=>"/css/",
            "/js/"=>"/js/",
            "/images/"=>"/images/",
        ];
    }
    public function getRERoutes(): array{
        return [
            "/class/{class}/doc/{doc}.html"=>"/classDoc",
        ];
    }
    public function getPluginsConfig(): array{
        return [
            "security"=>["className"=>security::class, "entryMethodName"=>"test"],
        ];
    }
    public static function run(){
        tfphp::run(self::class);
    }
}
index::run();