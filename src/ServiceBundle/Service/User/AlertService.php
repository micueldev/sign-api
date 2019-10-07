<?php

namespace ServiceBundle\Service\User;

use Doctrine\ORM\EntityManager;
use ServiceBundle\Service\Util\Sms;
use ServiceBundle\Service\Util\Socket;
use ServiceBundle\Service\Util\Constante;
use ServiceBundle\Service\CRUD\Create;

class AlertService{

    public $em;
    private $latitude;
    private $longitude;
    private $accuracy;
    private $profile;
    private $contacts=[];

    private $aUserAlert;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

	public function sendAlert($user){
        $location = $user->getLastLocation();
        if(!$location)
            return [
                'success' => false,
                'msg' => 'No se encontró su ubicacion'
            ];

        $this->latitude = $location->getLatitude();
        $this->longitude = $location->getLongitude();
        $this->accuracy = $user->getAccuracy();

        $this->profile = $this->em->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());

        if($isUserInAlert = $user->getIsAlert()){
            $alert = $this->em->getRepository('EntityBundle:Item\Alert')->findOneBy(['user'=>$user->getId(),'isActive'=>True]);
            if(!$alert) 
                return [
                    'success' => false,
                    'msg' => 'En alerta no existente'
                ];
        } else {
            $create = new Create($this->em);
            $cadena = 'user/User\User^'.$user->getId();
            $cadena .= '|f^'.date_create()->format('Y-m-d h:i:s A');
            $cadena .= '|aLocation^'.json_encode([['latitude'=>$this->latitude,
                                                    'longitude'=>$this->longitude,
                                                    'accuracy'=>$this->accuracy,
                                                    'date'=>date_create()]]);
            $alert = $create->create('Item\Alert',['cadena'=>$cadena]);
            if(!$alert['success']) return $this->json($alert,Constante::$enumCodigo);
            $alert = $alert['entity']; 
        }

        $this->aUserAlert = $alert->getAUserAlert();

        $respSms = $this->alertSms($user->getId());
        $respNot = $this->InitAlertNotification($user->getId());

        $user->setIsAlert(True);

        $lastAlert = new \DateTime();
        $lastAlert->modify('+'.(Constante::$timeAlert).' minutes');
        $alert->setFT($lastAlert);
        $alert->setAUserAlert($this->aUserAlert);
        
        $this->em->flush();

        if(!$respSms['success'] && !$respNot['success'])
            return $respNot;
        
        $isUserInAlert ? : proc_open ('php ../bin/console app:Notification_EndAlert '.$user->getId().' >> alert.log', Array (), $foo);

        return ['success' => true];
    }

    public function alertSms($uId,$sendSms=True){
        $contacts = $this->em->getRepository('EntityBundle:User\Contact')->findOneByUser($uId);
        if(!$contacts)
            return [
                'success' => false,
                'msg' => 'No ha agregado ningun contacto'
            ];

        $this->contacts = $contacts->getAContact();
        if(count($this->contacts)==0)
            return [
                'success' => false,
                'msg' => 'No tiene ningun contacto'
            ];

        if($sendSms){
            $sms = new Sms($this->em);

            $text = "Help Me!!\n";
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
        if(!is_array($users) || count($users)==0)
            return [
                'success' => false,
                'msg' => 'No se encontraron usuarios cercanos'
            ];

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
        if( count($this->contacts)!=0 ){
            $aUsername = [];
            foreach($this->contacts as $contact)
                $aUsername[]=$contact['number'];
        }
        $point = new \EntityBundle\Model\Object\Point($latitude,$longitude);

        $consulta = 'SELECT E.id, E.username, DISTANCE(E.lastLocation ,POINT_STR(\''.$point.'\')) AS distance_m
                FROM EntityBundle:User\User E
                WHERE E.id<>'.$uId.'
                AND E.isAlert<>'.True.'
                HAVING distance_m < 2000';

        if(isset($aUsername))
            $consulta .= 'OR E.username IN ('.implode(',', $aUsername).')';
        
        $query = $this->em->createQuery($consulta);

        return $query->getResult();
    }

    public function UpdateAlertLocation($user){

        $alert = $this->em->getRepository('EntityBundle:Item\Alert')->findOneBy(['user'=>$user->getId(),'isActive'=>True]);
        if(!$alert)
            return [
                'success' => false,
                'msg' => 'En alerta no existente'
            ];

        $this->aUserAlert = $alert->getAUserAlert();

        $this->profile = $this->em->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());

        $location = $user->getLastLocation();
        $this->latitude = $location->getLatitude();
        $this->longitude = $location->getLongitude();
        $this->accuracy = $user->getAccuracy();       

        $aLocation = $alert->getALocation();
        $aLocation[] = ['latitude'=>$this->latitude,
                        'longitude'=>$this->longitude,
                        'accuracy'=>$this->accuracy,
                        'date'=>date_create()];
        $alert->setALocation($aLocation);

        $this->em->flush();

        return $this->UpdateAlertNotification();
    }

    public function UpdateAlertNotification(){
        $msg = [
                'type'=>'alert_update',
                'user'=>[
                    'id'=>$this->profile->getUser()->getId(),
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

    public function EndAlertNotification($uId,$ids){        
        $msg = [
                'type'=>'alert_end',
                'id'=>(int)$uId
        ];

        $socket = new Socket($this->em);
        return $socket->send(['ids'=>$ids,'msg'=>$msg]);
    }
}