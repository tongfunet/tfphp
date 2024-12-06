<?php

namespace tfphp\controller\api;

use tfphp\framework\image\tfimage;
use tfphp\framework\system\tfrestfulAPI;

class image extends tfrestfulAPI {
    protected function onLoad(){
        $image = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/big.jpg");
        switch ($_SERVER["RESTFUL_RESOURCE_FUNCTION"]){
            case "origin":
                $image->output();die;
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
            case "text":
                $image->writeText(date("Y-m-d H:i:s"), 12, 10, 12, 0, "000000");
                $image->writeText(date("Y-m-d H:i:s"), 11, 9, 12, 0, "ffffff");
                $image->output();die;
            case "watermark":
                $watermarkImage = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/mark2.png");
                $image->addWatermark(TFPHP_DOCUMENT_ROOT. "/images/mark2.png", $image->getWidth()-$watermarkImage->getWidth()-10, $watermarkImage->getHeight()-10)->output();die;
            case "centerCropHorizontal":
                $image = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/horizontal.png");
                $image->centerCrop(200, 150)->output();die;
            case "centerCropHorizontalSM":
                $image = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/horizontal-sm.png");
                $image->centerCrop(200, 150)->output();die;
            case "centerCropVertical":
                $image = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/vertical.png");
                $image->centerCrop(200, 150)->output();die;
            case "centerCropVerticalSM":
                $image = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/vertical-sm.png");
                $image->centerCrop(200, 150)->output();die;

        }
    }
}