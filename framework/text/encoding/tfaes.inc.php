<?php 

namespace tfphp\framework\text\encoding;

use tfphp\framework\tfphp;

class tfaes{
    protected tfphp $tfphp;
    private string $C;
    public function __construct(tfphp $A){
        $this->tfphp = $A;
        $this->C = "AES-256-CBC";
    }
    public function setMethod(string $A1){
        if(!in_array(strtolower($A1), openssl_get_cipher_methods())){
            throw new \Exception("invalid method '". $A1. "' of aes", 666111);
        }
        $this->C = $A1;
    }
    public function testIVLength(string $A1): int{
        return openssl_cipher_iv_length(strtolower($A1));
    }
    public function PKCS7Padding(string $A4, int $A6): string{
        $AB = $A6 - (strlen($A4) % $A6);
        return $A4. str_repeat(chr($AB), $AB);
    }
    public function encrypt(string $A4, string $AE, string $B2): string{
        $AE = str_pad($AE, 32, '0', STR_PAD_RIGHT);
        $B2 = str_pad($B2, openssl_cipher_iv_length($this->C), '0', STR_PAD_RIGHT);
        $B6 = openssl_encrypt($A4, $this->C, $AE, OPENSSL_RAW_DATA, $B2);
        if(!$B6){
            throw new \Exception(openssl_error_string(), 666112);
        }
        $BB = base64_encode($B6);
        return $BB;
    }
    public function decrypt(string $BC, string $AE, string $B2): string{
        $AE = str_pad($AE, 32, '0', STR_PAD_RIGHT);
        $B2 = str_pad($B2, openssl_cipher_iv_length($this->C), '0', STR_PAD_RIGHT);
        $BD = base64_decode($BC);
        $BE = openssl_decrypt($BD, $this->C, $AE, OPENSSL_RAW_DATA, $B2);
        if(!$BE){
            throw new \Exception(openssl_error_string(), 666113);
        }
        return $BE;
    }
}