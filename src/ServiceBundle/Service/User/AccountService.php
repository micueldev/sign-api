<?php

namespace ServiceBundle\Service\User;

use Doctrine\ORM\EntityManager;
use ServiceBundle\Service\Util\Constante;
use ServiceBundle\Service\Util\Util;
use ServiceBundle\Service\Util\Sms;
use ServiceBundle\Service\CRUD\Create;
use ServiceBundle\Service\CRUD\Update;


class AccountService{

    public $em;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

	public function sendCode($username) {

        $util = new Util();
        $code = $util->generateRandomNumber(Constante::$lenCode);
        $f = date('Y-m-d h:i:s A');

        $uCode = $this->em->getRepository('EntityBundle:User\Code')->findOneByUsername($username);
        if($uCode){
            $id=$uCode->getId();
        }

        $cadena = 'code^'.$code;
        $cadena.= '|f^'.$f;

        if( isset($id) ){
            $update = new Update($this->em);
            $uCode = $update->upEntity('User\Code',['id'=>$id,'cadena'=>$cadena]);
        }else{
            $cadena.= '|username^'.$username;
            $create = new Create($this->em);
            $uCode = $create->create('User\Code',['cadena'=>$cadena]);
        }

        if(!$uCode['success'])
            return $uCode;

        $text = 'Your '.(Constante::$name).' code is: '.$code."\n";
        $text.="_________________________";

        $sms = new Sms($this->em);
        $resp = $sms->send($username,$text,true);

        return $resp;
    }
}