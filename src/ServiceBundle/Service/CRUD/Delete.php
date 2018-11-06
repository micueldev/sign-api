<?php

namespace ServiceBundle\Service\CRUD;

use Doctrine\ORM\EntityManager;

class Delete
{   
    public $em;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

    public function delete($entidad,$id,$flush=true){
        //try{
            if(is_numeric($id) && $id!=''){ 
                eval('$entidad = $this->em->getRepository(\'EntityBundle:'.$entidad.'\')->findOneById('.$id.');');
                if($entidad){
                    $this->em->remove($entidad);
                    if($flush){
                        $this->em->flush();
                    }
                }
                return ['success'=>true];
            }
            else{
                return ['success'=>false,'msg'=>'variable incorrecta'];
            }
        /*}
        catch (\Exception $e){
            $error = $e->getMessage();
            return ['success'=>false,'msg'=>$error];
        }*/
    }

    public function delEntitys($entidad,$cadena,$flush=true){
        //try{
            $cadena = explode('|',$cadena);
            $parametros = [];
            for($i=0;$i<count($cadena);$i++){
                $parametros[] = explode('^',$cadena[$i]);
            }

            $codigo='$entidades = $this->em->getRepository(\'EntityBundle:'.$entidad.'\')->findBy(array(';
            for($i=0;$i<count($parametros);$i++){
                if($i>0){
                    $codigo.=',';
                }          
                $codigo.='\''.$parametros[$i][0].'\'=> \''.$parametros[$i][1].'\''; 
            } 
            $codigo.='));';

            eval($codigo);
            foreach ($entidades as $entidad){
               $this->em->remove($entidad);
            }
            if($flush){
                $this->em->flush();
            }
            return ['success'=>true];
        /*}
        catch (\Exception $e){
            $error  = $e->getMessage();
            return ['success'=>false,'msg'=>$error];
        }*/ 
    }    
}