<?php

namespace ServiceBundle\Service\User;

use Doctrine\ORM\EntityManager;
use ServiceBundle\Service\Util\Sms;
use ServiceBundle\Service\Util\Socket;

class AlertService{

    public $em;
    private $latitude;
    private $longitude;
    private $profile;
    private $contacts;

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
        $this->latitude = $location->getLatitude();
        $this->longitude = $location->getLongitude();

        $contacts = $this->em->getRepository('EntityBundle:User\Contact')->findOneByUser($user->getId());
        if(!$contacts){
            return [
                'success' => false,
                'msg' => 'No ha agregado ningun contacto'
            ];
        }

        $this->contacts = $contacts->getAContact();
        if(count($this->contacts)==0){
            return [
                'success' => false,
                'msg' => 'No tiene ningun contacto'
            ];
        }

        $this->profile = $this->em->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());

        $resp = $this->alertNotification($user->getId());
        if(!$resp['success']){
            $msg = $resp['msg'];
        }

        $resp = $this->alertSms();
        if(!$resp['success'] && isset($msg)){
            return [
                'success' => false,
                'msg' => $msg
            ];
        }
        return ['success' => true];           
    }

    public function alertSms(){
        $sms = new Sms($this->em);

        $text = "help me!!\n";
        $text .= ("I am ".$this->profile->getNombres()." ".$this->profile->getApepat()."\n");
        $text .= ("http://maps.google.com/?q=".$this->latitude.",".$this->longitude);        
        foreach($this->contacts as $contact){
            $resp = $sms->send($contact['number'],$contact['name']." ".$text);
            if(!$resp['success'])
                return $resp;
        }
        return ['success'=>true];
    }

    public function alertNotification($uId){
        $users = $this->getNearUsers($this->latitude,$this->longitude,$uId);
        if(!is_array($users) || count($users)==0){
            return [
                'success' => false,
                'msg' => 'No se encontraron usuarios cercanos'
            ];
        }

        $ids=[];
        foreach ($users as $user) {
            $ids[]=$user['id'];
        }

        $msg = [
                'type'=>'alert',
                'user'=>[
                    'id'=>$uId,
                    'username'=>$this->profile->getUser()->getUsername(),
                    'nombres'=>$this->profile->getNombres(),
                    'apePat'=>$this->profile->getApepat(),
                    'apeMat'=>$this->profile->getApemat(),
                    'location'=>[
                        'latitude'=>$this->latitude,
                        'longitude'=>$this->longitude
                    ]
                ]
        ];

        $socket = new Socket($this->em);
        return $socket->send(['ids'=>$ids,'msg'=>$msg]);
    }

    public function getNearUsers($latitude,$longitude,$uId){
        $point = new \EntityBundle\Model\Object\Point($latitude,$longitude);

        $consulta= 'SELECT E.id, DISTANCE(E.lastLocation ,POINT_STR(\''.$point.'\')) AS distance_m
                FROM EntityBundle:User\User E
                WHERE E.id<>'.$uId.'
                HAVING distance_m < 2000';
        $query = $this->em->createQuery($consulta);

        return $query->getResult();
    }
}