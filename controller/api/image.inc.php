<?php 

namespace tfphp\controller\api;

use tfphp\framework\image\tfimage;
use tfphp\framework\system\tfrestfulAPI;

class image extends tfrestfulAPI{
    protected function onLoad(){
        $A = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/big.jpg");
        switch ($_SERVER["RESTFUL_RESOURCE_FUNCTION"]){
            case "origin":
                $A->output();die;
            case "top":
                $A->smartCrop(tfimage::T_SMART_FUNC_TOP, 100, 100)->output();die;
            case "top_right":
                $A->smartCrop(tfimage::T_SMART_FUNC_TOP_RIGHT, 100, 100)->output();die;
            case "right":
                $A->smartCrop(tfimage::T_SMART_FUNC_RIGHT, 100, 100)->output();die;
            case "bottom_right":
                $A->smartCrop(tfimage::T_SMART_FUNC_BOTTOM_RIGHT, 100, 100)->output();die;
            case "bottom":
                $A->smartCrop(tfimage::T_SMART_FUNC_BOTTOM, 100, 100)->output();die;
            case "bottom_left":
                $A->smartCrop(tfimage::T_SMART_FUNC_BOTTOM_LEFT, 100, 100)->output();die;
            case "left":
                $A->smartCrop(tfimage::T_SMART_FUNC_LEFT, 100, 100)->output();die;
            case "top_left":
                $A->smartCrop(tfimage::T_SMART_FUNC_TOP_LEFT, 100, 100)->output();die;
            case "centre":
                $A->smartCrop(tfimage::T_SMART_FUNC_CENTRE, 100, 100)->output();die;
            case "text":
                $A->writeText(date("Y-m-d H:i:s"), 12, 10, 12, 0, "000000");
                $A->writeText(date("Y-m-d H:i:s"), 11, 9, 12, 0, "ffffff");
                $A->output();die;
            case "watermark":
                $A3 = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/mark2.png");
                $A->addWatermark(TFPHP_DOCUMENT_ROOT. "/images/mark2.png", $A->getWidth()-$A3->getWidth()-10, $A3->getHeight()-10)->output();die;
            case "centerCropHorizontal":
                $A = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/horizontal.png");
                $A->centerCrop(200, 150)->output();die;
            case "centerCropHorizontalSM":
                $A = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/horizontal-sm.png");
                $A->centerCrop(200, 150)->output();die;
            case "centerCropVertical":
                $A = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/vertical.png");
                $A->centerCrop(200, 150)->output();die;
            case "centerCropVerticalSM":
                $A = new tfimage($this->tfphp, TFPHP_DOCUMENT_ROOT. "/images/vertical-sm.png");
                $A->centerCrop(200, 150)->output();die;

        }
    }
}