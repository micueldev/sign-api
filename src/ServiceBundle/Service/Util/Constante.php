<?php

namespace ServiceBundle\Service\Util;

class Constante {

    public static $name;
    public static $codConfig;

    public static $enumAceptado;
    public static $enumEmpty;    
    public static $enumParam;
	public static $enumTock;
    public static $enumPerm;    
    public static $enumCodigo;
    public static $enumNotExis;

    public static $lenCode;

    public static function init(){

        self::$name ='VITAL SIGN'; //Nombre del projecto
        self::$codConfig ='VITSIGN'; //Codigo de configuracion

        self::$enumAceptado = '202'; //Aceptado, pero el procesamiento no se ha completado
        self::$enumEmpty = '204'; //Se procesó correctamente la solicitud y no devuelve ningún contenido
    	self::$enumParam = '400'; //Petición Incorrecta
    	self::$enumTock = '401'; //No autorizado
        self::$enumPerm = '403'; //Sin permiso
        self::$enumCodigo = '409'; //conflicto en la edicion simultanea
        self::$enumNotExis = '501'; //No reconoce solicitud o no puede completar la solicitud  

        self::$lenCode = 6; //longitud del codigo de verificacion
    }
}
Constante::init();