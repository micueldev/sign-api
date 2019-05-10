<?php

namespace ControllerBundle\Controller\MyAccount;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ServiceBundle\Service\Util\Constante;

use EntityBundle\Model\Object\Point;

class InfoController extends Controller
{   
    public function tokingAction(Request $request) {

        try{        
            $decoded = $this->get('Jwt')->decodeToken($request->headers->get('authToken'));
            if(!$decoded['success']) return $this->json($decoded,Constante::$enumTock);
            $user = $decoded['user'];

            $profile = $this->getDoctrine()->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());
            return $this->json([
                'success' => true,
                'authToken' => $this->get('Jwt')->getToken($user,'a'),
                'profile' => $profile->asArray(FALSE,['apepat','nombres'])
            ]);  
            
        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }
    }

    public function setLocationAction(Request $request) {

        try{
            $decoded = $this->get('Jwt')->decodeToken($request->headers->get('authToken'));
            if(!$decoded['success']) return $this->json($decoded,Constante::$enumTock);
            $user = $decoded['user'];

            $cadena = 'latitude,longitude';
            $sentencia = $this->get('Read')->getData($cadena,'PATCH');  
            eval($sentencia);
            if(!$existen){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'faltan parametros'
                                        ],Constante::$enumPerm);
            }

            $user->setLastLocation(new Point($latitude, $longitude));
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'success' => true
            ]);
            
        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }
    }

    public function sendAlertAction(Request $request) {

        //try{
            $decoded = $this->get('Jwt')->decodeToken($request->headers->get('authToken'));
            if(!$decoded['success']) return $this->json($decoded,Constante::$enumTock);
            $user = $decoded['user'];

            $resp = $this->get('MyAccountContact')->sendAlert($user);
            return $this->json($resp);
        /*  
        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }*/
    }

    /*
    public function changePasswordAction(Request $request){

        try{
            $decoded = $this->get('Jwt')->decodeToken($request->headers->get('authToken'),'c');
            if(!$decoded['success']) return new JsonResponse($decoded,Constante::$enumTock);
            $user = $decoded['user'];

            $cadena = 'oldpwd,newpwd,rnewpwd';
            $sentencia = $this->get('Read')->getData($cadena);
            eval($sentencia);
            if(!$existen){
                return New JsonResponse ([
                                        'success'=>false,
                                        'msg'=>'No se encontro al parametro ('.$faltante.')'
                                        ],Constante::$enumParam);
            }

            if (strcmp($newpwd, $rnewpwd) !== 0){
                return New JsonResponse (['success'=>false,'msg'=>'La nueva clave no coincide con la confirmacion.'],Constante::$enumAceptado);
            }

            if (password_verify($oldpwd, $user->getPassword())){
                if( strlen($newpwd)<8 ){
                    return New JsonResponse (['success'=>false,'msg'=>'La nueva clave debe tener como minimo 8 caracteres.'],Constante::$enumAceptado);
                }
            }
            else {
                return New JsonResponse (['success'=>false,'msg'=>'La clave es incorecta.'],Constante::$enumAceptado);
            }

            $encriptado = password_hash($newpwd,PASSWORD_DEFAULT);
            //$option = 'password^'.$newpwd.'|pwd^0';
            $cadena = 'password^'.$encriptado;

            $resp = $this->get('Update')->upEntity('Cliente\Cliente',['id'=>$user->getId(),'cadena'=>$cadena],false);
            if (!$resp['success']){
                return New JsonResponse ($resp,Constante::$enumCodigo);
            }

            $user_cliente = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Users')->findOneBy(array('idCl'=>$user->getId()));

            $cadena = 'password^'.$encriptado;
            $resp = $this->get('Update')->upEntity('Cliente\Users',['id'=>$user_cliente->getId(),'cadena'=>$cadena],false);
            return New JsonResponse ($resp);

         }catch (\Exception $e){
            return New JsonResponse([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }
    }*/
}