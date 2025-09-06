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
    private tfphp $B9;
    private $BC;
    private array $C1;
    private int $C6;
    private int $C7;
    private string $CA;
    private string $CD;
    private function CE(string $CF, string $D1){
        switch ($D1){
            case "image/jpeg":
                return imagecreatefromjpeg($CF);
            case "image/png":
                return imagecreatefrompng($CF);
            case "image/gif":
                return imagecreatefromgif($CF);
            default:
                return imagecreatefromjpeg($CF);
        }
    }
    private function D3(?string $D9, string $D1, bool $DA=false): bool{
        switch ($D1){
            case "jpg":
            case "jpeg":
            case "image/jpeg":
                if(!$DA) header("Content-Type: image/jpeg");
                return imagejpeg($this->BC, $D9);
            case "png":
            case "image/png":
                if(!$DA) header("Content-Type: image/png");
                return imagepng($this->BC, $D9);
            case "gif":
            case "image/git":
                if(!$DA) header("Content-Type: image/git");
                return imagegif($this->BC, $D9);
            default:
                if(!$DA) header("Content-Type: image/jpeg");
                return imagejpeg($this->BC, $D9);
        }
    }
    public function __construct(tfphp $E6, string $CF=null, int $EA=null, int $EF=null, string $F0=null){
        $this->B9 = $E6;
        if($EA === null) $EA = 800;
        if($EF === null) $EF = 600;
        if($F0 === null) $F0 = "image/jpeg";
        if(!$CF){
            $this->C6 = intval($EA);
            $this->C7 = intval($EF);
            $this->CA = strval($F0);
            $this->CD = "jpg";
            $this->BC = imagecreatetruecolor($EA, $EF);
        }
        else{
            $this->C1 = getimagesize($CF);
            $this->C6 = intval($this->C1[0]);
            $this->C7 = intval($this->C1[1]);
            $this->CA = strval($this->C1["mime"]);
            $this->CD = str_replace("jpeg", "jpg", str_replace("image/", "", $this->CA));
            $this->BC = $this->CE($CF, $this->CA);
            if(!$this->BC){
                $this->BC = imagecreatetruecolor($EA, $EF);
            }
        }
    }
    public function save(string $D9): bool{
        if(!preg_match("/\.([^\.]+)$/", $D9, $F5)){
            return false;
        }
        switch ($F5[1]){
            case "jpg":
            case "jpeg":
                return imagejpeg($this->BC, $D9);
            case "png":
                return imagepng($this->BC, $D9);
            case "gif":
                return imagegif($this->BC, $D9);
            default:
                return imagejpeg($this->BC, $D9);
        }
    }
    public function output(): bool{
        switch ($this->CA){
            case "image/jpeg":
                header("Content-Type: image/jpeg");
                return imagejpeg($this->BC);
            case "image/png":
                header("Content-Type: image/png");
                return imagepng($this->BC);
            case "image/git":
                header("Content-Type: image/git");
                return imagegif($this->BC);
            default:
                header("Content-Type: image/jpeg");
                return imagejpeg($this->BC);
        }
    }
    public function isTransparent(): bool{
        $F6 = imagecolorat($this->BC, 1, 1);
        $F8 = imagecolorat($this->BC, $this->C6-1, 1);
        $FB = imagecolorat($this->BC, 1, $this->C7-1);
        $FE = imagecolorat($this->BC, $this->C6-1, $this->C7-1);
        $A02 = ($F6 >> 24) & 0xFF;
        $A07 = ($F8 >> 24) & 0xFF;
        $A0B = ($FB >> 24) & 0xFF;
        $A0F = ($FE >> 24) & 0xFF;
        return ($A02 > 0 || $A07 > 0 || $A0B > 0 || $A0F > 0);
    }
    public function zoom(float $A10): ?tfimage{
        $EA = intval($this->C6*$A10);
        $EF = intval($this->C7*$A10);
        $A15 = new tfimage($this->B9, null, $EA, $EF, $this->CA);
        if(!imagecopyresampled($A15->getImage(), $this->BC, 0, 0, 0, 0, $EA, $EF, $this->C6, $this->C7)){
            return null;
        }
        return $A15;
    }
    public function crop(int $A18, int $A1C, int $EA, int $EF): ?tfimage{
        if($A18+$EA > $this->C6 || $A1C+$EF > $this->C7){
            return null;
        }
        $A15 = new tfimage($this->B9, null, $EA, $EF, $this->CA);
        if(!imagecopyresampled($A15->getImage(), $this->BC, 0, 0, $A18, $A1C, $this->C6, $this->C7, $this->C6, $this->C7)){
            return null;
        }
        return $A15;
    }
    public function smartCrop(int $A1D, int $EA, int $EF): ?tfimage{
        $A15 = null;
        switch ($A1D){
            case tfimage::T_SMART_FUNC_TOP:
                $A18 = intval(($this->C6-$EA)/2);
                $A1C = 0;
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
            case tfimage::T_SMART_FUNC_TOP_RIGHT:
                $A18 = intval($this->C6-$EA);
                $A1C = 0;
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
            case tfimage::T_SMART_FUNC_RIGHT:
                $A18 = intval($this->C6-$EA);
                $A1C = intval(($this->C7-$EF)/2);
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
            case tfimage::T_SMART_FUNC_BOTTOM_RIGHT:
                $A18 = intval($this->C6-$EA);
                $A1C = intval($this->C7-$EF);
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
            case tfimage::T_SMART_FUNC_BOTTOM:
                $A18 = intval(($this->C6-$EA)/2);
                $A1C = intval($this->C7-$EF);
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
            case tfimage::T_SMART_FUNC_BOTTOM_LEFT:
                $A18 = 0;
                $A1C = intval($this->C7-$EF);
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
            case tfimage::T_SMART_FUNC_LEFT:
                $A18 = 0;
                $A1C = intval(($this->C7-$EF)/2);
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
            case tfimage::T_SMART_FUNC_TOP_LEFT:
                $A18 = 0;
                $A1C = 0;
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
            case tfimage::T_SMART_FUNC_CENTRE:
                $A18 = intval(($this->C6-$EA)/2);
                $A1C = intval(($this->C7-$EF)/2);
                $A15 = $this->crop($A18, $A1C, $EA, $EF);
                break;
        }
        return $A15;
    }
    public function centerCrop(int $EA, int $EF): ?tfimage{
        if($this->getWidth() <= $EA || $this->getHeight() <= $EF){
            $A15 = new tfimage($this->B9, null, $EA, $EF, $this->CA);
            if($this->getWidth() > $EA){
                $A18 = 0;
                $A21 = ($this->getWidth()-$EA)/2;
            }
            else{
                $A18 = ($EA-$this->getWidth())/2;
                $A21 = 0;
            }
            if($this->getHeight() > $EF){
                $A1C = 0;
                $A22 = ($this->getHeight()-$EF)/2;
            }
            else{
                $A1C = ($EF-$this->getHeight())/2;
                $A22 = 0;
            }
            if(!imagecopyresampled($A15->getImage(), $this->BC, $A18, $A1C, $A21, $A22, $this->C6, $this->C7, $this->C6, $this->C7)){
                return null;
            }
        }
        else{
            $A15 = ($EA/$this->getWidth() > $EF/$this->getHeight()) ? $this->zoom($EA/$this->getWidth()) : $this->zoom($EF/$this->getHeight());
            $A15 = $A15->centerCrop($EA, $EF);
        }
        return $A15;
    }
    public function fillColor(string $A27, int $A18, int $A1C): ?tfimage{
        $A2C = imagecolorallocate($this->BC, hexdec(substr($A27, 0, 2)), hexdec(substr($A27, 2, 2)), hexdec(substr($A27, 4, 2)));
        if(!imagefill($this->BC, $A18, $A1C, $A2C)){
            return null;
        }
        return $this;
    }
    public function writeText(string $A30, int $A18, int $A1C, int $A33=null, int $A36=null, string $A27=null, string $A3A=null): ?tfimage{
        if($A33 === null) $A33 = 16;
        if($A36 === null) $A36 = 0;
        if($A27 === null) $A27 = "000000";
        if($A3A === null) $A3A = TFPHP_ROOT. "/resource/fonts/simhei.ttf";
        $A3F = imagecolorallocate($this->BC, hexdec(substr($A27, 0, 2)), hexdec(substr($A27, 2, 2)), hexdec(substr($A27, 4, 2)));
        if(!imagettftext($this->BC, $A33, $A36, $A18, $A1C+$A33, $A3F, $A3A, $A30)){
            return null;
        }
        return $this;
    }
    public function addWatermark(string $A44, int $A18, int $A1C): ?tfimage{
        $A49 = new tfimage($this->B9, $A44);
        if(!imagecopyresampled($this->BC, $A49->getImage(), $A18, $A1C, 0, 0, $A49->getWidth(), $A49->getHeight(), $A49->getWidth(), $A49->getHeight())){
            return null;
        }
        return $this;
    }
    public function getImage(){
        return $this->BC;
    }
    public function getInfo(){
        $this->C1[0] = $this->C6;
        $this->C1[1] = $this->C7;
        $this->C1[3] = "width=\"". $this->C6. "\" height=\"". $this->C7. "\"";
        $this->C1["mime"] = $this->CA;
        return $this->C1;
    }
    public function getWidth(): int{
        return $this->C6;
    }
    public function getHeight(): int{
        return $this->C7;
    }
    public function getMIME(): string{
        return $this->CA;
    }
    public function getExtension(): string{
        return $this->CD;
    }
}