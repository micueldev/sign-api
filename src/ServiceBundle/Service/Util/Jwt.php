<?php

namespace ServiceBundle\Service\Util;

use Doctrine\ORM\EntityManager;
use \Firebase\JWT\JWT as firejwt;

class Jwt
{   
    private $key = 'LlavePrivada';

    public $em;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

	public function getToken($usuario){

        $token = [
            "id" => $usuario->getId(),
            "username" => $usuario->getUsername(),
            "iat" => time(),
            //"exp" => time() + (7*24*60*60),
        ];
        
        $conexion = $this->em->getRepository('EntityBundle:Config\Platform')->findOneByCod(Constante::$codConfig);
        if($conexion){
            $token['domain'] = $conexion->getDomain();
            $token['subdomain'] = $conexion->getSubdomain();
        }
                
		return firejwt::encode($token, $this->key, 'HS256');
	}

    public function decodeToken($token,$objeto=TRUE){

        try{
            $array = (array) firejwt::decode($token, $this->key, array('HS256'));
            if(!$objeto){
                return ['success'=>TRUE,'user'=>$array];
            }

            $user = $this->em->getRepository('EntityBundle:User\User')->findOneById($array['id']);
                      
            if( $user ){
                if( $user->getIsActive() ){
                    return ['success'=>TRUE,'user'=>$user];
                }
                return ['success'=>FALSE,'msg'=>'¡Usuario inactivo!'];
            }
            return ['success'=>FALSE,'msg'=>'logout'];               
        }
        catch (\Exception $e){
            return ['success'=>FALSE,'msg'=>'logout'];
        }
    }
}