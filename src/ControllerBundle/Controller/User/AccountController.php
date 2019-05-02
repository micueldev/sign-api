<?php

namespace ControllerBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ServiceBundle\Service\Util\Constante;

class AccountController extends Controller
{   
    public function loginAction(Request $request) {

        try{
            $cadena = 'username,password';
            $sentencia = $this->get('Read')->getData($cadena);  
            eval($sentencia);
            if(!$existen){
                return $this->json([
                                        'success'=>false,
                                        'msg'=>'No se encontro al parametro ('.$faltante.')'
                                        ],Constante::$enumParam);
            }

            $user = $this->getDoctrine()->getRepository('EntityBundle:User\User')->findOneByUsername($username);
            if($user && password_verify($password,$user->getPassword()) ){
                if( $user->getIsActive() ){
                    $profile = $this->getDoctrine()->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());
                    return $this->json([
                        'success' => true,
                        'authToken' => $this->get('Jwt')->getToken($user,'a'),
                        'profile' => $profile->asArray(FALSE,['apepat','nombres'])
                    ]);                    
                }
                return $this->json(['success'=>FALSE,'msg'=>'¡Usuario inactivo!'],Constante::$enumTock);                
            }
            return $this->json([
                                    'success'=>false,
                                    'msg'=>'¡phone or password incorrect!']);

        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }
    }
    
    public function existAction(Request $request) {

        try{
            $cadena = 'username';
            $sentencia = $this->get('Read')->getData($cadena);  
            eval($sentencia);
            if(!$existen){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'faltan parametros'
                                        ],Constante::$enumPerm);
            }

            $exist = false;
            $user = $this->getDoctrine()->getRepository('EntityBundle:User\User')->findOneByUsername($username);
            if($user) $exist = true;
            
            return  $this->json(['success'=>true,'exist'=>$exist]);

        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ]);
        }
    }

    public function sendCodeAction(Request $request) {

        try{
            $cadena = 'username';
            $sentencia = $this->get('Read')->getData($cadena);  
            eval($sentencia);
            if(!$existen){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'faltan parametros'
                                        ],Constante::$enumPerm);
            }

            $user = $this->getDoctrine()->getRepository('EntityBundle:User\User')->findOneByUsername($username);
            if($user){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'Usuario registrado'
                                        ],Constante::$enumNotExis);
            }

            $this->get('UserAccount')->sendCode($username);
            return  $this->json(['success'=>true]);

        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ]);
        }
    }

    public function checkCodeAction(Request $request) {

        try{
            $cadena = 'username,code';
            $sentencia = $this->get('Read')->getData($cadena);  
            eval($sentencia);
            if(!$existen){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'faltan parametros'
                                        ],Constante::$enumPerm);
            }

            $uCode = $this->getDoctrine()->getRepository('EntityBundle:User\Code')->findOneByUsername($username); 
            if(!$uCode){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'Code SMS invalid'
                                        ],Constante::$enumNotExis);
            }

            if( $code!=$uCode->getCode() ){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'Code SMS incorrect'
                                        ],Constante::$enumNotExis);
            }
            return  $this->json(['success'=>true]);

        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ]);
        }
    }

    public function createAction(Request $request) {

        try{           
            $cadena = 'username,code,password,email,apepat,apemat,nombres';
            $sentencia = $this->get('Read')->getData($cadena);  
            eval($sentencia);
            if(!$existen){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'faltan parametros'
                                        ],Constante::$enumPerm);
            }

            $oUser = $this->getDoctrine()->getRepository('EntityBundle:User\User')->findOneByUsername($username); 
            if($oUser){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'El numero de telefono ya esta siendo usado'
                                        ],Constante::$enumNotExis);
            }
            /*
            $oUser = $this->getDoctrine()->getRepository('EntityBundle:User\Profile')->findOneByEmail($email);
            if($oUser){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'El email ya esta siendo usado.'
                                        ],Constante::$enumCodigo);
            }
            */
            $uCode = $this->getDoctrine()->getRepository('EntityBundle:User\Code')->findOneByUsername($username); 
            if(!$uCode){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'Code SMS invalid'
                                        ],Constante::$enumNotExis);
            }

            if( $code!=$uCode->getCode() ){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'Code SMS incorrect'
                                        ],Constante::$enumNotExis);
            }

            $begin;
            $this->getDoctrine()->getConnection()->beginTransaction();

            $cadena = 'username^'.$username;
            $cadena.= '|password^'.password_hash($password,PASSWORD_DEFAULT);
            $nUser = $this->get('Create')->create('User\User',['cadena'=>$cadena]);
            if(!$nUser['success']){
                return  $this->json($nUser,Constante::$enumCodigo);
            }
            $nUser = $nUser['entity'];
  
            $cadena = 'user/User\User^'.$nUser->getId();
            $cadena.= '|apepat^'.$apepat;
            $cadena.= '|apemat^'.$apemat;
            $cadena.= '|nombres^'.$nombres;
            $cadena.= '|email^'.$email;
            $profile = $this->get('Create')->create('User\Profile',['cadena'=>$cadena]);
            if(!$profile['success']){
                $this->getDoctrine()->getConnection()->rollBack();              
                return  $this->json($profile,Constante::$enumCodigo);
            }

            $this->getDoctrine()->getConnection()->commit();
            return  $this->json(['success'=>true]);

        }catch (\Exception $e){
            if( isset($begin) ){
                $this->getDoctrine()->getConnection()->rollBack();    
            }
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }
    }

    public function sendCodeUserAction(Request $request) {

        try{
            $cadena = 'username';
            $sentencia = $this->get('Read')->getData($cadena);  
            eval($sentencia);
            if(!$existen){
                return  $this->json([
                                        'success'=>false,
                                        'msg'=>'faltan parametros'
                                        ],Constante::$enumPerm);
            }

            $user = $this->getDoctrine()->getRepository('EntityBundle:User\User')->findOneByUsername($username); 
            if(!$user){
                return  $this->json([
                                        'success'=>false,
                                        'msg'=>'Usuario no registrado'
                                        ],Constante::$enumNotExis);
            }

            $resp = $this->get('UserAccount')->sendCode($username);
            return $this->json($resp);
            
        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ]);
        }
    }
    
    public function setNewPasswordAction(Request $request){

        try{           
            $cadena = 'username,code,password';
            $sentencia = $this->get('Read')->getData($cadena);  
            eval($sentencia);
            if(!$existen){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'faltan parametros'
                                        ],Constante::$enumPerm);
            }

            $user = $this->getDoctrine()->getRepository('EntityBundle:User\User')->findOneByUsername($username); 
            if(!$user){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'El numero de telefono no esta registrado'
                                        ],Constante::$enumNotExis);
            }

            $uCode = $this->getDoctrine()->getRepository('EntityBundle:User\Code')->findOneByUsername($username); 
            if(!$uCode){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'Code SMS invalid'
                                        ],Constante::$enumNotExis);
            }

            if( $code!=$uCode->getCode() ){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'Code SMS incorrect'
                                        ],Constante::$enumNotExis);
            }

            $begin;
            $this->getDoctrine()->getConnection()->beginTransaction();

            $cadena = 'username^'.$username;
            $cadena.= '|password^'.password_hash($password,PASSWORD_DEFAULT);
            $user = $this->get('Update')->upEntity('User\User',['id'=>$user->getId(),'cadena'=>$cadena]);
            if(!$user['success']){
                return  $this->json($nUser,Constante::$enumCodigo);
            }

            $this->getDoctrine()->getConnection()->commit();
            return  $this->json(['success'=>true]);

        }catch (\Exception $e){
            if( isset($begin) ){
                $this->getDoctrine()->getConnection()->rollBack();    
            }
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }
    } 
    /*
    public function sendSmsGetAction(Request $request) {
        try{
            $number = $_GET['number'];
            $text = $_GET['text'];
            $resp = $this->get('Sms')->validateSms($number,$text);
            if(!$resp['success'])
                return $this->json($resp);

            $resp = $this->get('Sms')->send($number,$text);
            return $this->json($resp);

        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ]);
        }
    }

    public function sendSmsPostAction(Request $request) {
        try{
            $cadena = 'number,text';
            $sentencia = $this->get('Read')->getData($cadena);  
            eval($sentencia);
            if(!$existen){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'Parametros erroneos'
                                        ]);
            }

            $resp = $this->get('Sms')->validateSms($number,$text);
            if(!$resp['success'])
                return $this->json($resp);

            $resp = $this->get('Sms')->send($number,$text);
            return $this->json($resp);

        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ]);
        }
    }
    */
}