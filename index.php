<?php

namespace tfphp;

use tfphp\framework\tfphp;

define("TFPHP_DOCUMENT_ROOT", __DIR__);
require "./framework/tfphp.inc.php";
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
    public static function run(){
        tfphp::run(self::class);
    }
}
index::run();