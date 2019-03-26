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
        }
    }

	public function send($number,$text){
		try{
            $socket = stream_socket_client('tcp://'.$this->host.':'.$this->port, $errno, $errstr, 30);
            if(!$socket){
            	return ['success'=>FALSE,'msg'=>$errstr.' ('.$errno.')'];
            }else{
                $sms=json_encode(['number'=>$number,'text'=>$text]);
                fwrite($socket, $sms);
                fclose($socket);
                return ['success'=>TRUE];
            }

        }catch (\Exception $e){
            return ['success'=>FALSE,'msg'=>$e->getMessage()];
        }
    }
}