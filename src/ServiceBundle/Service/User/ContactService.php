<?php

namespace ServiceBundle\Service\User;

use Doctrine\ORM\EntityManager;
use ServiceBundle\Service\Util\Sms;

class ContactService{

    public $em;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

	public function sendAlert($user) { 

        $location = $user->getLastLocation();
        if(!$location){
            return [
                'success' => false,
                'msg' => 'No se encontró su ubicacion'
            ];
        }        
        $latitude = $location->getLatitude();
        $longitude = $location->getLongitude();

        $contacts = $this->em->getRepository('EntityBundle:User\Contact')->findOneByUser($user->getId());
        if(!$contacts){
            return [
                'success' => false,
                'msg' => 'No ha agregado ningun contacto'
            ];
        }

        $aContact = $contacts->getAContact();
        if(count($aContact)==0){
            return [
                'success' => false,
                'msg' => 'No tiene ningun contacto'
            ];
        }

        $profile = $this->em->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());

        $sms = new Sms($this->em);

        $text = "help me!!\n";
        $text .= ("I am ".$profile->getNombres()." ".$profile->getApepat()."\n");
        $text .= ("http://maps.google.com/?q=".$latitude.",".$longitude);        
        foreach($aContact as $contact){
            $resp = $sms->send($contact['number'],$contact['name']." ".$text);
            if(!$resp['success'])
                return $resp;
        }
        return ['success'=>true];
    }
}