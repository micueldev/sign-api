<?php

namespace ServiceBundle\Service\Util;

use Doctrine\ORM\EntityManager;
use \GuzzleHttp\Client;

use ServiceBundle\Service\Util\Constante;

class Socket
{
    public $em;
    private $parameter=[];

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
        $conexion = $this->em->getRepository('EntityBundle:Config\Platform')->findOneByCod(Constante::$codConfig);
        if($conexion){
            $this->parameter['domain'] = $conexion->getDomain();
            $this->parameter['subdomain'] = $conexion->getSubdomain();
        }
    }

	public function send($body)
    {   
        try{
            if(count($this->parameter)<2){
                return ['success'=>true,'msg'=>'No existe configuracion de la plataforma'];
            }

            if( !isset($body['i']) ){
                $body['i']='user';
            }
            
            $conexion = $this->em->getRepository('EntityBundle:Config\Socket')->findOneByCod(Constante::$codConfig);
            if( $conexion && $conexion->getDomain() ){
                $ruta = $conexion->getDomain().'/socket';

                $client = new Client();
                $response = $client->request('POST',$ruta,[
                                                'http_errors' => false,
                                                'headers' => [
                                                                'Content-Type' => 'application/json',
                                                            ],
                                                'body' => json_encode(array_merge($body,$this->parameter))
                                        ]);

                $statuscode = $response->getStatusCode();
                if($statuscode == 200){
                    return ['success'=>true,'msg'=>'Exito en el envio de socket'];
                }
                else{
                    return ['success'=>false,'msg'=>'Error status code ('.$statuscode.').'];
                }
            }

        }catch (\Exception $e){
            return [
                    'success'=>false,
                    'msg'=>$e->getMessage()
                    ];
        }
    }
}