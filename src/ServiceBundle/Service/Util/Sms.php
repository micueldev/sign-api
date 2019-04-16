<?php

namespace ServiceBundle\Service\Util;

use Doctrine\ORM\EntityManager;
use ServiceBundle\Service\Util\Constante;

class Sms{

	public $em;
	public $host;
	public $port;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
        $sms = $this->em->getRepository('EntityBundle:Config\Sms')->findOneByCod(Constante::$codConfig);
        if($sms){
        	$this->host = $sms->getHost();
        	$this->port = $sms->getPort();
            $this->appHash = $sms->getHash();
        }
    }

    public function validateSms($number,$text){
        $resp = $this->validateNumber($number);
        if(!$resp['success']) return $resp;
        $resp = $this->validateText($text);
        if(!$resp['success']) return $resp;
        return $resp;
    }
    public function validateNumber($number){
        if( strlen($number)==9 && $number[0]==9 && $number[1]!=0 && is_numeric($number) )
            return ['success'=>true];
        return ['success'=>false,'msg'=>'Numero no valido'];
    }
    public function validateText($text){
        $lenText = strlen($text);
        if( $lenText>160 || $lenText==0)
            return ['success'=>false,'msg'=>'Longitud del mensaje no valido.'];
        if ( preg_match('/^[a-zA-Z0-9\s\n\!@#$%^&*()-_+={\[\]}|\\`<>,.\?\/]*$/u', $text) )
            return ['success'=>true];
        return ['success'=>false,'msg'=>'Caracteres del mensaje no validos.'];
    }

	public function send($number,$text,$flagApp=false){
		//try{
            $socket = stream_socket_client('tcp://'.$this->host.':'.$this->port, $errno, $errstr, 30);
            if(!$socket){
            	return ['success'=>FALSE,'msg'=>$errstr.' ('.$errno.')'];
            }else{
                if($flagApp)
                    $text = '<#> '.$text."\n".$this->appHash;
                
                $sms=json_encode(['number'=>$number,'text'=>$text]);
                fwrite($socket, $sms);
                $resp = fread($socket, 26);
                fclose($socket);
                if( substr($resp, 0,5)!='Error' )
                    return ['success'=>TRUE];
                
                return ['success'=>TRUE,'msg'=>"No hay mensajero conectado."];
            }

        /*}catch (\Exception $e){
            echo($e->getMessage());
            return ['success'=>FALSE,'msg'=>$e->getMessage()];
        }*/
    }
}