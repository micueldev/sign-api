<?php

namespace ControllerBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ServiceBundle\Service\Util\Constante;

class AccountController extends Controller
{   
    public function pruebaAction(Request $request) {
        return  $this->json(['success'=>true,'user'=>['username'=>'mcueva','password'=>'mcueva']]);
    }

    public function createAction(Request $request) {

        try{           
            $cadena = 'username,password,email,apepat,apemat,nombres';
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
                                        'msg'=>'El username ya esta siendo usado'
                                        ],Constante::$enumNotExis);
            }
            $oUser = $this->getDoctrine()->getRepository('EntityBundle:User\Profile')->findOneByEmail($email);
            if($oUser){
                return  $this->json ([
                                        'success'=>false,
                                        'msg'=>'El email ya esta siendo usado.'
                                        ],Constante::$enumCodigo);
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
                                    'msg'=>'¡username or password incorrect!']);

        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }
    }

    public function tokingAction(Request $request)
    {   
        try{        
            $decoded = $this->get('Jwt')->decodeToken($request->headers->get('authToken'));
            if(!$decoded['success']) return $this->json($decoded,Constante::$enumTock);
            $user = $decoded['user'];

            $profile = $this->getDoctrine()->getRepository('EntityBundle:User\Profile')->findOneByUser($user->getId());
            return $this->json([
                'success' => true,
                //'authToken' => $request->headers->get('authToken'),
                'profile' => $profile->asArray(FALSE,['apepat','nombres'])
            ]);            
            
        }catch (\Exception $e){
            return $this->json([
                                    'success'=>false,
                                    'msg'=>$e->getMessage()
                                    ],Constante::$enumCodigo);
        }
    }
/*
    // Change Password - POST
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

    }

    // Change Password - POST
    public function changeNewPasswordAction(Request $request){

        $decoded = $this->get('Jwt')->decodeToken($request->headers->get('authToken'),'c');
        if(!$decoded['success']) return new JsonResponse($decoded,Constante::$enumTock);
        $user = $decoded['user'];


        $cadena = 'codcheck,newpwd,rnewpwd';

        $sentencia = $this->get('Read')->getData($cadena);
        eval($sentencia);
        if(!$existen){
            return New JsonResponse ([
                                    'success'=>false,
                                    'msg'=>'No se encontro al parametro ('.$faltante.')'
                                    ],Constante::$enumParam);
        }

        if (strcmp($newpwd, $rnewpwd) !== 0) {
            return New JsonResponse (['success'=>false,'msg'=>'La nueva clave no coincide con la confirmacion.'],Constante::$enumAceptado);
        }
                
        $verifica = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Users')->findOneByIdCl($user->getId());
        if(!$verifica){
            return New JsonResponse (['success'=>false,'msg'=>'No se encontro codigo de verificacion, contactarse con soporte.'],Constante::$enumAceptado);
        }

        if (strcmp($codcheck, $verifica->getCodcheck()) !== 0){
            return New JsonResponse (['success'=>false,'msg'=>'Codigo incorrecto.'],Constante::$enumAceptado);
        }

        if( strlen($newpwd)<8){
            return New JsonResponse (['success'=>false,'msg'=>'La nueva clave debe tener como minimo 8 caracteres.'],Constante::$enumAceptado);
        }
        
        $encriptado = password_hash($newpwd,PASSWORD_DEFAULT);
        //$option = 'password^'.$newpwd.'|pwd^0';
        $cadena = 'password^'.$encriptado;
        $resp = $this->get('Update')->upEntity('Cliente\Cliente',['id'=>$user->getId(),'cadena'=>$cadena],false);
        if (!$resp['success']){
            return New JsonResponse ($resp,Constante::$enumCodigo);
        }

        $user_cliente = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Users')->findOneBy(array('idCl'=>$user->getId()));
        $cadena = 'password^'.$encriptado . '|codcheck^' ;
        $resp = $this->get('Update')->upEntity('Cliente\Users',['id'=>$user_cliente->getId(),'cadena'=>$cadena],false);
        return New JsonResponse ($resp);
    }


    // Recover Password - POST
    public function recoverPasswordAction(Request $request){
        $cadena = 'usermail';

        $sentencia = $this->get('Read')->getData($cadena);
        eval($sentencia);
        if(!$existen){
            return New JsonResponse ([
                                    'success'=>false,
                                    'msg'=>'No se encontro al parametro ('.$faltante.')'
                                    ],Constante::$enumParam);
        }
        
        $user = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Cliente')->findOneByUsername($usermail);
        if(!$user){
            //si no buscaremos al usuario por el correo
            $condicion['where'] = 'aEmail^' . $usermail . '%%LIKE%%';
            $entidad_correo = $this->get('Read')->findEntitys('Cliente\Entidad',$condicion,['filtro'=>'id']);

            if (!$entidad_correo['success']){
                return New JsonResponse ($entidad_correo,Constante::$enumAceptado);
            }

            foreach ($entidad_correo['entidad'] as $j => $elemento){
                $idEtd =  $elemento['id'];
                $user = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Cliente')->findOneByIdEtd($idEtd);
                if ($user){
                    //Encontrol al cliente
                    break;
                }
            }
        }


        if($user){
            if ($user->getI()=='p'){
                $aEmail= $user->getIdEtd()->getAEmail();
            }else{
                $aEmail= $user->getIdCto()->getAEmail();
            }

            $email='';
            foreach($aEmail as $obj){
                if ($obj['estado']=='1'){
                    $email = $obj['email'];
                }
            }

            if( is_null($email) || $email == '' )
                return New JsonResponse (['success'=>false,'msg'=>'El usuario no presenta correo, ponganse al contacto con soporte.'],Constante::$enumAceptado);
        }
        else{
            return New JsonResponse (['success'=>false,'msg'=>'Usuario no encontrado.'],Constante::$enumAceptado);
        }


        $codigo = $this->get('Util')->generateRandomString(10);

        $user_cliente = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Users')->findOneBy(array('idCl'=>$user->getId()));
        $cadena = 'codcheck^' . $codigo ;
        $resp = $this->get('Update')->upEntity('Cliente\Users',['id'=>$user_cliente->getId(),'cadena'=>$cadena],false);
        if (!$resp['success']){
            return New JsonResponse ($resp,Constante::$enumCodigo);
        }

        $nomcliente="";
        if ($user->getIdEtd()->getIdPn()){
            $apepat = $user->getIdEtd()->getIdPn()->getApepat();
            $apemat = $user->getIdEtd()->getIdPn()->getApemat();
            $nombres = $user->getIdEtd()->getIdPn()->getNombres();
            $nomcliente = $apepat . ' ' . $apemat . ' ' . $nombres;
        }else{
            //Persona Juridica
            $nomcliente = $user->getIdEtd()->getIdPj()->getRazon();
        }

        $correo = [
                'asunto'=>'Recuperacion de Contraseña',
                'destino'=>[$email]
                ];

        $data = [

                'variables'=>['backend'=>$_SERVER['SERVER_NAME'],'receptor'=>ltrim($nomcliente) . ' - usuario: ' . $user->getUsername() . ' - Codigo: '. $codigo,'numDoc'=>'','password'=>''],
                'plantilla'=>'GodivisaServiceBundle:Correo:correoBienvenida.html.twig'
                ];

        $resp = $this->get('Correo')->send($correo,$data);
        return New JsonResponse ($resp);

    }

    // New Password - POST
    public function newPasswordAction(Request $request){

        $cadena = 'username,codcheck,newpwd,rnewpwd';
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

        $user = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Cliente')->findOneByUsername($username);
        if(is_null($user)){
            return New JsonResponse (['success'=>false,'msg'=>'Usuario incorrecto'],Constante::$enumAceptado);
        }
    
        $entidad = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Users')->findOneByIdCl($user->getId());
        if(is_null($entidad->getCodcheck() || $entidad->getCodcheck() =='')){
            return New JsonResponse (['success'=>false,'msg'=>'El usuario no ha solicitado recuperacion de contraseña'],Constante::$enumAceptado);
        }

        if (strcmp($codcheck,$entidad->getCodcheck()) !== 0) {
            return New JsonResponse (['success'=>false,'msg'=>'Usuario o codigo incorrecto.'],Constante::$enumNotExis);
        }
        if( strlen($newpwd)<8 ){
            return New JsonResponse (['success'=>false,'msg'=>'La nueva clave debe tener como minimo 8 caracteres.'],Constante::$enumAceptado);            
        }

        $encriptado = password_hash($newpwd,PASSWORD_DEFAULT);
        //$option = 'password^'.$newpwd.'|pwd^0';
        $cadena = 'password^'.$encriptado;
        $resp = $this->get('Update')->upEntity('Cliente\Cliente',['id'=>$user->getId(),'cadena'=>$cadena],false);
        if (!$resp['success']){
            return New JsonResponse ($resp,Constante::$enumCodigo);
        }

        $user_cliente = $this->getDoctrine()->getRepository('GodivisaEntityBundle:Cliente\Users')->findOneBy(array('idCl'=>$user->getId()));
        $cadena = 'password^'.$encriptado . '|codcheck^' ;
        $resp = $this->get('Update')->upEntity('Cliente\Users',['id'=>$user_cliente->getId(),'cadena'=>$cadena],false);
        return New JsonResponse ($resp);

    }
    */
}