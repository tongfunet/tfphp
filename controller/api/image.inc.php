<?php

namespace tfphp\controller\api;

use tfphp\framework\image\tfimage;
use tfphp\framework\system\tfrestfulAPI;

class image extends tfrestfulAPI {
    protected function onLoad(){
        $image = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/bg.jpg");
        switch ($_SERVER["RESTFUL_RESOURCE_FUNCTION"]){
            case "top":
                $image->smartCrop(tfimage::T_SMART_FUNC_TOP, 100, 100)->output();die;
            case "top_right":
                $image->smartCrop(tfimage::T_SMART_FUNC_TOP_RIGHT, 100, 100)->output();die;
            case "right":
                $image->smartCrop(tfimage::T_SMART_FUNC_RIGHT, 100, 100)->output();die;
            case "bottom_right":
                $image->smartCrop(tfimage::T_SMART_FUNC_BOTTOM_RIGHT, 100, 100)->output();die;
            case "bottom":
                $image->smartCrop(tfimage::T_SMART_FUNC_BOTTOM, 100, 100)->output();die;
            case "bottom_left":
                $image->smartCrop(tfimage::T_SMART_FUNC_BOTTOM_LEFT, 100, 100)->output();die;
            case "left":
                $image->smartCrop(tfimage::T_SMART_FUNC_LEFT, 100, 100)->output();die;
            case "top_left":
                $image->smartCrop(tfimage::T_SMART_FUNC_TOP_LEFT, 100, 100)->output();die;
            case "centre":
                $image->smartCrop(tfimage::T_SMART_FUNC_CENTRE, 100, 100)->output();die;
            case "watermark":
                $watermarkImage = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/mark2.png");
                $image->addWatermark(TFPHP_DOCUMENT_ROOT. "/images/mark2.png", $image->getWidth()-$watermarkImage->getWidth()-10, $image->getHeight()-$watermarkImage->getHeight()-10)->output();die;
        }
    }
}