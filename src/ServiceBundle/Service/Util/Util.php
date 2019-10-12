<?php

namespace ServiceBundle\Service\Util;

class Util{

	public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i=0; $i<$length; $i++)
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        return $randomString;
    }

    public function generateRandomNumber($length = 10) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i=0; $i<$length; $i++)
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        return $randomString;
    }

    public function validateMobileNumber($number){
        if( strlen($number)==9 && $number[0]==9 && $number[1]!=0 && is_numeric($number) )
            return ['success'=>true];
        return ['success'=>false,'msg'=>'Numero no valido'];
    }    
}