<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\image;

use tfphp\framework\tfphp;

class tfimage{
    const T_SMART_FUNC_TOP = 1;
    const T_SMART_FUNC_TOP_RIGHT = 2;
    const T_SMART_FUNC_RIGHT = 3;
    const T_SMART_FUNC_BOTTOM_RIGHT = 4;
    const T_SMART_FUNC_BOTTOM = 5;
    const T_SMART_FUNC_BOTTOM_LEFT = 6;
    const T_SMART_FUNC_LEFT = 7;
    const T_SMART_FUNC_TOP_LEFT = 8;
    const T_SMART_FUNC_CENTRE = 9;
    const T_SMART_FUNC_MAXSIZE_CENTRE = 10;
    private tfphp $tfphp;
    private $image;
    private array $imageInfo;
    private int $imageWidth;
    private int $imageHeight;
    private string $imageMIME;
    private string $imageExtension;
    private function createImageByMIME(string $imageFilepath, string $imageMIME){
        switch ($imageMIME){
            case "image/jpeg":
                return imagecreatefromjpeg($imageFilepath);
            case "image/png":
                return imagecreatefrompng($imageFilepath);
            case "image/gif":
                return imagecreatefromgif($imageFilepath);
            default:
                return imagecreatefromjpeg($imageFilepath);
        }
    }
    private function saveImage(?string $saveFilepath, string $imageMIME, bool $saveImageWithoutHeader=false): bool{
        switch ($imageMIME){
            case "jpg":
            case "jpeg":
            case "image/jpeg":
                if(!$saveImageWithoutHeader) header("Content-Type: image/jpeg");
                return imagejpeg($this->image, $saveFilepath);
            case "png":
            case "image/png":
                if(!$saveImageWithoutHeader) header("Content-Type: image/png");
                return imagepng($this->image, $saveFilepath);
            case "gif":
            case "image/git":
                if(!$saveImageWithoutHeader) header("Content-Type: image/git");
                return imagegif($this->image, $saveFilepath);
            default:
                if(!$saveImageWithoutHeader) header("Content-Type: image/jpeg");
                return imagejpeg($this->image, $saveFilepath);
        }
    }
    public function __construct(tfphp $tfphp, string $imageFilepath=null, int $width=null, int $height=null, string $mime=null){
        $this->tfphp = $tfphp;
        if($width === null) $width = 800;
        if($height === null) $height = 600;
        if($mime === null) $mime = "image/jpeg";
        if(!$imageFilepath){
            $this->imageWidth = intval($width);
            $this->imageHeight = intval($height);
            $this->imageMIME = strval($mime);
            $this->imageExtension = "jpg";
            $this->image = imagecreatetruecolor($width, $height);
        }
        else{
            $this->imageInfo = getimagesize($imageFilepath);
            $this->imageWidth = intval($this->imageInfo[0]);
            $this->imageHeight = intval($this->imageInfo[1]);
            $this->imageMIME = strval($this->imageInfo["mime"]);
            $this->imageExtension = str_replace("jpeg", "jpg", str_replace("image/", "", $this->imageMIME));
            $this->image = $this->createImageByMIME($imageFilepath, $this->imageMIME);
            if(!$this->image){
                $this->image = imagecreatetruecolor($width, $height);
            }
        }
    }
    public function save(string $saveFilepath): bool{
        if(!preg_match("/\.([^\.]+)$/", $saveFilepath, $rg)){
            return false;
        }
        switch ($rg[1]){
            case "jpg":
            case "jpeg":
                return imagejpeg($this->image, $saveFilepath);
            case "png":
                return imagepng($this->image, $saveFilepath);
            case "gif":
                return imagegif($this->image, $saveFilepath);
            default:
                return imagejpeg($this->image, $saveFilepath);
        }
    }
    public function output(): bool{
        switch ($this->imageMIME){
            case "image/jpeg":
                header("Content-Type: image/jpeg");
                return imagejpeg($this->image);
            case "image/png":
                header("Content-Type: image/png");
                return imagepng($this->image);
            case "image/git":
                header("Content-Type: image/git");
                return imagegif($this->image);
            default:
                header("Content-Type: image/jpeg");
                return imagejpeg($this->image);
        }
    }
    public function isTransparent(): bool{
        $lt = imagecolorat($this->image, 1, 1);
        $rt = imagecolorat($this->image, $this->imageWidth-1, 1);
        $lb = imagecolorat($this->image, 1, $this->imageHeight-1);
        $rb = imagecolorat($this->image, $this->imageWidth-1, $this->imageHeight-1);
        $ltAlpha = ($lt >> 24) & 0xFF;
        $rtAlpha = ($rt >> 24) & 0xFF;
        $lbAlpha = ($lb >> 24) & 0xFF;
        $rbAlpha = ($rb >> 24) & 0xFF;
        return ($ltAlpha > 0 || $rtAlpha > 0 || $lbAlpha > 0 || $rbAlpha > 0);
    }
    public function zoom(float $rate): ?tfimage{
        $width = intval($this->imageWidth*$rate);
        $height = intval($this->imageHeight*$rate);
        $newImage = new tfimage($this->tfphp, null, $width, $height, $this->imageMIME);
        if(!imagecopyresampled($newImage->getImage(), $this->image, 0, 0, 0, 0, $width, $height, $this->imageWidth, $this->imageHeight)){
            return null;
        }
        return $newImage;
    }
    public function crop(int $x, int $y, int $width, int $height): ?tfimage{
        if($x+$width > $this->imageWidth || $y+$height > $this->imageHeight){
            return null;
        }
        $newImage = new tfimage($this->tfphp, null, $width, $height, $this->imageMIME);
        if(!imagecopyresampled($newImage->getImage(), $this->image, 0, 0, $x, $y, $this->imageWidth, $this->imageHeight, $this->imageWidth, $this->imageHeight)){
            return null;
        }
        return $newImage;
    }
    public function smartCrop(int $funcType, int $width, int $height): ?tfimage{
        $newImage = null;
        switch ($funcType){
            case tfimage::T_SMART_FUNC_TOP:
                $x = intval(($this->imageWidth-$width)/2);
                $y = 0;
                $newImage = $this->crop($x, $y, $width, $height);
                break;
            case tfimage::T_SMART_FUNC_TOP_RIGHT:
                $x = intval($this->imageWidth-$width);
                $y = 0;
                $newImage = $this->crop($x, $y, $width, $height);
                break;
            case tfimage::T_SMART_FUNC_RIGHT:
                $x = intval($this->imageWidth-$width);
                $y = intval(($this->imageHeight-$height)/2);
                $newImage = $this->crop($x, $y, $width, $height);
                break;
            case tfimage::T_SMART_FUNC_BOTTOM_RIGHT:
                $x = intval($this->imageWidth-$width);
                $y = intval($this->imageHeight-$height);
                $newImage = $this->crop($x, $y, $width, $height);
                break;
            case tfimage::T_SMART_FUNC_BOTTOM:
                $x = intval(($this->imageWidth-$width)/2);
                $y = intval($this->imageHeight-$height);
                $newImage = $this->crop($x, $y, $width, $height);
                break;
            case tfimage::T_SMART_FUNC_BOTTOM_LEFT:
                $x = 0;
                $y = intval($this->imageHeight-$height);
                $newImage = $this->crop($x, $y, $width, $height);
                break;
            case tfimage::T_SMART_FUNC_LEFT:
                $x = 0;
                $y = intval(($this->imageHeight-$height)/2);
                $newImage = $this->crop($x, $y, $width, $height);
                break;
            case tfimage::T_SMART_FUNC_TOP_LEFT:
                $x = 0;
                $y = 0;
                $newImage = $this->crop($x, $y, $width, $height);
                break;
            case tfimage::T_SMART_FUNC_CENTRE:
                $x = intval(($this->imageWidth-$width)/2);
                $y = intval(($this->imageHeight-$height)/2);
                $newImage = $this->crop($x, $y, $width, $height);
                break;
        }
        return $newImage;
    }
    public function centerCrop(int $width, int $height): ?tfimage{
        if($this->getWidth() <= $width || $this->getHeight() <= $height){
            $newImage = new tfimage($this->tfphp, null, $width, $height, $this->imageMIME);
            if($this->getWidth() > $width){
                $x = 0;
                $x2 = ($this->getWidth()-$width)/2;
            }
            else{
                $x = ($width-$this->getWidth())/2;
                $x2 = 0;
            }
            if($this->getHeight() > $height){
                $y = 0;
                $y2 = ($this->getHeight()-$height)/2;
            }
            else{
                $y = ($height-$this->getHeight())/2;
                $y2 = 0;
            }
            if(!imagecopyresampled($newImage->getImage(), $this->image, $x, $y, $x2, $y2, $this->imageWidth, $this->imageHeight, $this->imageWidth, $this->imageHeight)){
                return null;
            }
        }
        else{
            $newImage = ($width/$this->getWidth() > $height/$this->getHeight()) ? $this->zoom($width/$this->getWidth()) : $this->zoom($height/$this->getHeight());
            $newImage = $newImage->centerCrop($width, $height);
        }
        return $newImage;
    }
    public function fillColor(string $color, int $x, int $y): ?tfimage{
        $iColor = imagecolorallocate($this->image, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
        if(!imagefill($this->image, $x, $y, $iColor)){
            return null;
        }
        return $this;
    }
    public function writeText(string $text, int $x, int $y, int $size=null, int $angle=null, string $color=null, string $fontFile=null): ?tfimage{
        if($size === null) $size = 16;
        if($angle === null) $angle = 0;
        if($color === null) $color = "000000";
        if($fontFile === null) $fontFile = TFPHP_ROOT. "/resource/fonts/simhei.ttf";
        $txtColor = imagecolorallocate($this->image, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
        if(!imagettftext($this->image, $size, $angle, $x, $y+$size, $txtColor, $fontFile, $text)){
            return null;
        }
        return $this;
    }
    public function addWatermark(string $watermarkFilepath, int $x, int $y): ?tfimage{
        $wmImage = new tfimage($this->tfphp, $watermarkFilepath);
        if(!imagecopyresampled($this->image, $wmImage->getImage(), $x, $y, 0, 0, $wmImage->getWidth(), $wmImage->getHeight(), $wmImage->getWidth(), $wmImage->getHeight())){
            return null;
        }
        return $this;
    }
    public function getImage(){
        return $this->image;
    }
    public function getInfo(){
        $this->imageInfo[0] = $this->imageWidth;
        $this->imageInfo[1] = $this->imageHeight;
        $this->imageInfo[3] = "width=\"". $this->imageWidth. "\" height=\"". $this->imageHeight. "\"";
        $this->imageInfo["mime"] = $this->imageMIME;
        return $this->imageInfo;
    }
    public function getWidth(): int{
        return $this->imageWidth;
    }
    public function getHeight(): int{
        return $this->imageHeight;
    }
    public function getMIME(): string{
        return $this->imageMIME;
    }
    public function getExtension(): string{
        return $this->imageExtension;
    }
}