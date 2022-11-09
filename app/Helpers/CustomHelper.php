<?php
namespace App\Helpers;

class CustomHelper
{
    public static function getEncrypted($id){
        $encrypted_string=openssl_encrypt($id,config('services.encryption.type'),config('services.encryption.secret'));
        return base64_encode($encrypted_string);
    }
    public static function getDecrypted($id){
        $string=openssl_decrypt(base64_decode($id),config('services.encryption.type'),config('services.encryption.secret'));
        return $string;
    }


    public static function encrypt($dataToEncrypt){
        $aesKey = config('services.phonenumber.phonenumber_encryption_key');
        $output = false;
        $iv = '25c6c7ff35b99788';
        $output = openssl_encrypt($dataToEncrypt, 'AES-256-CBC', $aesKey,
        OPENSSL_RAW_DATA, $iv);
        $output = base64_encode($output);
        return $output;
    }

    public static function decrypt($dataTodecrypt){
        $aesKey = config('services.phonenumber.phonenumber_encryption_key');
        $output = false;
        $iv = '25c6c7ff35b99788';
        $dataTodecrypt = base64_decode ($dataTodecrypt);
        $dataTodecrypt = $output = openssl_decrypt($dataTodecrypt, 'AES-256-CBC',
        $aesKey, OPENSSL_RAW_DATA, $iv);
        return $output;
    }

}
