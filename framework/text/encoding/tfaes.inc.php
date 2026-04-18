<?php

/*
 * SPDX-FileCopyrightText: 2026 Tongfu from Tongfu.net
 * SPDX-License-Identifier: Apache-2.0
 */

namespace tfphp\framework\text\encoding;

use tfphp\framework\tfphp;

class tfaes{
    protected tfphp $tfphp;
    private string $method;
    public function __construct(tfphp $tfphp){
        $this->tfphp = $tfphp;
        $this->method = "AES-256-CBC";
    }
    public function setMethod(string $method){
        if(!in_array(strtolower($method), openssl_get_cipher_methods())){
            throw new \Exception("invalid method '". $method. "' of aes", 660901);
        }
        $this->method = $method;
    }
    public function testIVLength(string $method): int{
        return openssl_cipher_iv_length(strtolower($method));
    }
    public function PKCS7Padding(string $data, int $blockSize): string{
        $padSize = $blockSize - (strlen($data) % $blockSize);
        return $data. str_repeat(chr($padSize), $padSize);
    }
    public function encrypt(string $data, string $key, string $iv): string{
        $key = str_pad($key, 32, '0', STR_PAD_RIGHT);
        $iv = str_pad($iv, openssl_cipher_iv_length($this->method), '0', STR_PAD_RIGHT);
        $encrypted = openssl_encrypt($data, $this->method, $key, OPENSSL_RAW_DATA, $iv);
        if(!$encrypted){
            throw new \Exception(openssl_error_string(), 660902);
        }
        $encoded = base64_encode($encrypted);
        return $encoded;
    }
    public function decrypt(string $encodedData, string $key, string $iv): string{
        $key = str_pad($key, 32, '0', STR_PAD_RIGHT);
        $iv = str_pad($iv, openssl_cipher_iv_length($this->method), '0', STR_PAD_RIGHT);
        $decoded = base64_decode($encodedData);
        $decrypted = openssl_decrypt($decoded, $this->method, $key, OPENSSL_RAW_DATA, $iv);
        if(!$decrypted){
            throw new \Exception(openssl_error_string(), 660903);
        }
        return $decrypted;
    }
}