<?php

namespace ServiceBundle\Service\User;

use Doctrine\ORM\EntityManager;
use ServiceBundle\Service\Util\Sms;
use ServiceBundle\Service\Util\Socket;
use ServiceBundle\Service\Util\Constante;

class AlertService{

    public $em;
    private $latitude;
    private $longitude;
    private $profile;
    private $contacts=null;

    private $aUserAlert;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

	public function sendAlert($user){
        $location = $user->getLastLocation();
        if(!$location){
            return [
                'success' => false,
                'msg' => 'No se encontró su ubicacion'
            ];
        }
        $this->latitude = $location->getLatitude();
        $this->longitude = $location->getLongitude();

        $this->profile = $this->em->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());

        $isUserInAlert = $user->getIsAlert();
        if(!$isUserInAlert){
            $this->aUserAlert = [];
        }else{
            $this->aUserAlert = $user->getAUserAlert();
        }
        
        $respSms = $this->alertSms($user->getId());
        $respNot = $this->InitAlertNotification($user->getId());

        $lastAlert = new \DateTime();
        $lastAlert->modify('+'.(Constante::$timeAlert).' minutes');

        $user->setIsAlert(True);
        $user->setFLastAlert($lastAlert);
        $user->setAUserAlert($this->aUserAlert);
        $this->em->flush();

        if(!$respSms['success'] && !$respNot['success'])
            return $respNot;

        if(!$isUserInAlert){
            proc_open ('php ../bin/console app:Notification_EndAlert '.$user->getId().' >> alert.log', Array (), $foo);
        }

        return ['success' => true];
    }

    public function alertSms($uId,$sendSms=True){
        $contacts = $this->em->getRepository('EntityBundle:User\Contact')->findOneByUser($uId);
        if(!$contacts){
            return [
                'success' => false,
                'msg' => 'No ha agregado ningun contacto'
            ];
        }

        $contacts = $contacts->getAContact();
        if(count($contacts)==0){
            return [
                'success' => false,
                'msg' => 'No tiene ningun contacto'
            ];
        }
        $this->contacts = $contacts;

        if($sendSms){
            $sms = new Sms($this->em);

            $text = "help me!!\n";
            $text .= ("I am ".$this->profile->getNombres()." ".$this->profile->getApepat()."\n");
            $text .= ("http://maps.google.com/?q=".$this->latitude.",".$this->longitude);        
            foreach($this->contacts as $contact){
                $resp = $sms->send($contact['number'],$contact['name']." ".$text);
                if(!$resp['success'])
                    return $resp;
            }
        }        
        return ['success'=>true];
    }

    public function InitAlertNotification($uId){
        $users = $this->getNearUsers($this->latitude,$this->longitude,$uId);
        if(!is_array($users) || count($users)==0){
            return [
                'success' => false,
                'msg' => 'No se encontraron usuarios cercanos'
            ];
        }

        $ids=[];
        foreach ($users as $user)
            $ids[]=$user['id'];
        $this->aUserAlert = array_values(array_unique(array_merge($this->aUserAlert,$ids)));

        $msg = [
                'type'=>'alert_init',
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
        return $socket->send(['ids'=>$this->aUserAlert,'msg'=>$msg]);
    }

    public function getNearUsers($latitude,$longitude,$uId){
        if( !is_null($this->contacts) ){
            $aUsername = [];
            foreach($this->contacts as $contact){
                $aUsername[]=$contact['number'];
            }
        }
        $point = new \EntityBundle\Model\Object\Point($latitude,$longitude);

        $consulta = 'SELECT E.id, E.username, DISTANCE(E.lastLocation ,POINT_STR(\''.$point.'\')) AS distance_m
                FROM EntityBundle:User\User E
                WHERE E.id<>'.$uId.'
                HAVING distance_m < 2000';

        if(isset($aUsername)){
            $consulta .= 'OR E.username IN ('.implode(',', $aUsername).')';
        }

        $query = $this->em->createQuery($consulta);

        return $query->getResult();
    }

    public function UpdateAlertNotification($user){

        $location = $user->getLastLocation();
        $latitude = $location->getLatitude();
        $longitude = $location->getLongitude();

        $profile = $this->em->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());

        $msg = [
                'type'=>'alert_update',
                'user'=>[
                    'id'=>$user->getId(),
                    'username'=>$profile->getUser()->getUsername(),
                    'nombres'=>$profile->getNombres(),
                    'apePat'=>$profile->getApepat(),
                    'apeMat'=>$profile->getApemat(),
                    'location'=>[
                        'latitude'=>$latitude,
                        'longitude'=>$longitude
                    ]
                ]
        ];

        $socket = new Socket($this->em);
        return $socket->send(['ids'=>$user->getAUserAlert(),'msg'=>$msg]);
    }

    public function EndAlertNotification($uId,$ids){        
        $msg = [
                'type'=>'alert_end',
                'id'=>(int)$uId
        ];

        $socket = new Socket($this->em);
        return $socket->send(['ids'=>$ids,'msg'=>$msg]);
    }
}