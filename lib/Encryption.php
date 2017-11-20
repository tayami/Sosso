<?php

namespace Sosso\lib;

class Encryption
{
    const KEY = 'Taylor2017';

    public static function encrypt($str)
    {
        $data = openssl_encrypt($str, 'AES-128-ECB', self::KEY, OPENSSL_RAW_DATA);
        $data = base64_encode($data);
        return $data;
    }

    public static function decrypt($code)
    {
        $decrypted = openssl_decrypt(base64_decode($code), 'AES-128-ECB', self::KEY, OPENSSL_RAW_DATA);
        return $decrypted;
    }

    public static function randStr()
    {
        return md5(uniqid(rand(), true).time());
    }
}