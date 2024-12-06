<?php

namespace tfphp\framework\text\encoding;

use tfphp\framework\tfphp;

class tfaes{
    protected tfphp $tfphp;
    private string $method;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $this->method = "AES-256-CBC";
    }
    public function setMethod(string $F){
        if(!in_array(strtolower($F), openssl_get_cipher_methods())){
            throw new \Exception("invalid method '". $F. "' of aes");
        }
        $this->method = $F;
    }
    public function testIVLength(string $F): int{
        return openssl_cipher_iv_length(strtolower($F));
    }
    public function PKCS7Padding(string $A0, int $A4): string{
        $A7 = $A4 - (strlen($A0) % $A4);
        return $A0. str_repeat(chr($A7), $A7);
    }
    public function encrypt(string $A0, string $AA, string $AF): string{
        $AA = str_pad($AA, 32, '0', STR_PAD_RIGHT);
        $AF = str_pad($AF, openssl_cipher_iv_length($this->method), '0', STR_PAD_RIGHT) ;
        $B2 = openssl_encrypt($A0, $this->method, $AA, OPENSSL_RAW_DATA, $AF) ;
        if(!$B2){
            throw new \Exception(openssl_error_string());
        }
        $B3 = base64_encode($B2) ;
        return $B3;
    }
    public function decrypt(string $B7, string $AA, string $AF): string{
        $AA = str_pad($AA, 32, '0', STR_PAD_RIGHT);
        $AF = str_pad($AF, openssl_cipher_iv_length($this->method), '0', STR_PAD_RIGHT) ;
        $BA = base64_decode($B7) ;
        $C0 = openssl_decrypt($BA, $this->method, $AA, OPENSSL_RAW_DATA, $AF) ;
        if(!$C0){
            throw new \Exception(openssl_error_string());
        }
        return $C0;
    }
}