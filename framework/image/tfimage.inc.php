<?php

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
    private function createImageByMIME(string $BA, string $B2){
        switch ($B2){
            case "image/jpeg":
                return imagecreatefromjpeg($BA);
            case "image/png":
                return imagecreatefrompng($BA);
            case "image/gif":
                return imagecreatefromgif($BA);
            default:
                return imagecreatefromjpeg($BA);
        }
    }
    private function saveImage(?string $BD, string $B2, bool $BE=false): bool{
        switch ($B2){
            case "jpg":
            case "jpeg":
            case "image/jpeg":
                if(!$BE) header("Content-Type: image/jpeg");
                return imagejpeg($this->image, $BD);
            case "png":
            case "image/png":
                if(!$BE) header("Content-Type: image/png");
                return imagepng($this->image, $BD);
            case "gif":
            case "image/git":
                if(!$BE) header("Content-Type: image/git");
                return imagegif($this->image, $BD);
            default:
                if(!$BE) header("Content-Type: image/jpeg");
                return imagejpeg($this->image, $BD);
        }
    }
    public function __construct(tfphp $A, string $BA=null, int $C1=null, int $C7=null, string $C9=null){
        $this->tfphp = $A;
        if($C1 === null) $C1 = 800;
        if($C7 === null) $C7 = 600;
        if($C9 === null) $C9 = "image/jpeg";
        if(!$BA){
            $this->imageWidth = intval($C1);
            $this->imageHeight = intval($C7);
            $this->imageMIME = strval($C9);
            $this->imageExtension = "jpg";
            $this->image = imagecreatetruecolor($C1, $C7);
        }
        else{
            $this->imageInfo = getimagesize($BA);
            $this->imageWidth = intval($this->imageInfo[0]);
            $this->imageHeight = intval($this->imageInfo[1]);
            $this->imageMIME = strval($this->imageInfo["mime"]);
            $this->imageExtension = str_replace("jpeg", "jpg", str_replace("image/", "", $this->imageMIME));
            $this->image = $this->createImageByMIME($BA, $this->imageMIME);
            if(!$this->image){
                $this->image = imagecreatetruecolor($C1, $C7);
            }
        }
    }
    public function save(string $BD): bool{
        if(!preg_match("/\.([^\.]+)$/", $BD, $rg)){
            return false;
        }
        switch ($rg[1]){
            case "jpg":
            case "jpeg":
                return imagejpeg($this->image, $BD);
            case "png":
                return imagepng($this->image, $BD);
            case "gif":
                return imagegif($this->image, $BD);
            default:
                return imagejpeg($this->image, $BD);
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
        $CF = imagecolorat($this->image, 1, 1);
        $D0 = imagecolorat($this->image, $this->imageWidth-1, 1) ;
        $D6 = imagecolorat($this->image, 1, $this->imageHeight-1) ;
        $DC = imagecolorat($this->image, $this->imageWidth-1, $this->imageHeight-1) ;
        $E0 = ($CF >> 24) & 0xFF ;
        $E5 = ($D0 >> 24) & 0xFF ;
        $E9 = ($D6 >> 24) & 0xFF ;
        $EB = ($DC >> 24) & 0xFF ;
        return ($E0 > 0 || $E5 > 0 || $E9 > 0 || $EB > 0);
    }
    public function zoom(float $EF): ?tfimage{
        $C1 = intval($this->imageWidth*$EF);
        $C7 = intval($this->imageHeight*$EF) ;
        $F0 = new tfimage($this->tfphp, null, $C1, $C7, $this->imageMIME) ;
        if(!imagecopyresampled($F0->getImage(), $this->image, 0, 0, 0, 0, $C1, $C7, $this->imageWidth, $this->imageHeight)){
            return null;
        }
        return $F0;
    }
    public function crop(int $F2, int $F7, int $C1, int $C7): ?tfimage{
        if($F2+$C1 > $this->imageWidth || $F7+$C7 > $this->imageHeight){
            return null;
        }
        $F0 = new tfimage($this->tfphp, null, $C1, $C7, $this->imageMIME);
        if(!imagecopyresampled($F0->getImage(), $this->image, 0, 0, $F2, $F7, $this->imageWidth, $this->imageHeight, $this->imageWidth, $this->imageHeight)){
            return null;
        }
        return $F0;
    }
    public function smartCrop(int $FD, int $C1, int $C7): ?tfimage{
        $F0 = null;
        switch ($FD){
            case tfimage::T_SMART_FUNC_TOP:
                $F2 = intval(($this->imageWidth-$C1)/2);
                $F7 = 0 ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
            case tfimage::T_SMART_FUNC_TOP_RIGHT:
                $F2 = intval($this->imageWidth-$C1) ;
                $F7 = 0 ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
            case tfimage::T_SMART_FUNC_RIGHT:
                $F2 = intval($this->imageWidth-$C1) ;
                $F7 = intval(($this->imageHeight-$C7)/2) ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
            case tfimage::T_SMART_FUNC_BOTTOM_RIGHT:
                $F2 = intval($this->imageWidth-$C1) ;
                $F7 = intval($this->imageHeight-$C7) ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
            case tfimage::T_SMART_FUNC_BOTTOM:
                $F2 = intval(($this->imageWidth-$C1)/2) ;
                $F7 = intval($this->imageHeight-$C7) ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
            case tfimage::T_SMART_FUNC_BOTTOM_LEFT:
                $F2 = 0 ;
                $F7 = intval($this->imageHeight-$C7) ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
            case tfimage::T_SMART_FUNC_LEFT:
                $F2 = 0 ;
                $F7 = intval(($this->imageHeight-$C7)/2) ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
            case tfimage::T_SMART_FUNC_TOP_LEFT:
                $F2 = 0 ;
                $F7 = 0 ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
            case tfimage::T_SMART_FUNC_CENTRE:
                $F2 = intval(($this->imageWidth-$C1)/2) ;
                $F7 = intval(($this->imageHeight-$C7)/2) ;
                $F0 = $this->crop($F2, $F7, $C1, $C7) ;
                break;
        }
        return $F0;
    }
    public function centerCrop(int $C1, int $C7): ?tfimage{
        if($this->getWidth() <= $C1 || $this->getHeight() <= $C7){
            $F0 = new tfimage($this->tfphp, null, $C1, $C7, $this->imageMIME);
            if($this->getWidth() > $C1){
                $F2 = 0;
                $A01 = ($this->getWidth()-$C1)/2 ;
            }
            else{
                $F2 = ($C1-$this->getWidth())/2;
                $A01 = 0 ;
            }
            if($this->getHeight() > $C7){
                $F7 = 0;
                $A07 = ($this->getHeight()-$C7)/2 ;
            }
            else{
                $F7 = ($C7-$this->getHeight())/2;
                $A07 = 0 ;
            }
            if(!imagecopyresampled($F0->getImage(), $this->image, $F2, $F7, $A01, $A07, $this->imageWidth, $this->imageHeight, $this->imageWidth, $this->imageHeight)){
                return null;
            }
        }
        else{
            $F0 = ($C1/$this->getWidth() > $C7/$this->getHeight()) ? $this->zoom($C1/$this->getWidth()) : $this->zoom($C7/$this->getHeight());
            $F0 = $F0->centerCrop($C1, $C7) ;
        }
        return $F0;
    }
    public function fillColor(string $A0D, int $F2, int $F7): ?tfimage{
        $A10 = imagecolorallocate($this->image, hexdec(substr($A0D, 0, 2)), hexdec(substr($A0D, 2, 2)), hexdec(substr($A0D, 4, 2)));
        if(!imagefill($this->image, $F2, $F7, $A10)){
            return null;
        }
        return $this;
    }
    public function writeText(string $A14, int $F2, int $F7, int $A19=null, int $A1B=null, string $A0D=null, string $A1E=null): ?tfimage{
        if($A19 === null) $A19 = 16;
        if($A1B === null) $A1B = 0;
        if($A0D === null) $A0D = "000000";
        if($A1E === null) $A1E = TFPHP_ROOT. "/resource/fonts/simhei.ttf";
        $A24 = imagecolorallocate($this->image, hexdec(substr($A0D, 0, 2)), hexdec(substr($A0D, 2, 2)), hexdec(substr($A0D, 4, 2))) ;
        if(!imagettftext($this->image, $A19, $A1B, $F2, $F7+$A19, $A24, $A1E, $A14)){
            return null;
        }
        return $this;
    }
    public function addWatermark(string $A29, int $F2, int $F7): ?tfimage{
        $A2C = new tfimage($this->tfphp, $A29);
        if(!imagecopyresampled($this->image, $A2C->getImage(), $F2, $F7, 0, 0, $A2C->getWidth(), $A2C->getHeight(), $A2C->getWidth(), $A2C->getHeight())){
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