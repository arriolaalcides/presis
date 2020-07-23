<?php
/**
 * Created by PhpStorm.
 * User: picci
 * Date: 18/12/2017
 * Time: 17:48
 */
$smsusuario = "ranval"; //usuario de SMS MASIVOS
$smsclave 	 = "mrcce5"; //clave de SMS MASIVOS
$smsnumero = '1150127610';
$smstexto = "SE HA REGISTRADO UN NUEVO PEDIDO CORPORATIVO";
$smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($smsnumero) ."&TEXTO=". urlencode($smstexto) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=". urlencode($smsclave) );
echo "RESPUESTA: ".$smsrespuesta;
/*if (trim($smsrespuesta)=='OK'){
    $sqlu = "UPDATE viajes SET infAdmin=1 WHERE idViaje=".$row['idViaje'];
    if (!$resultado2 = $mysqli->query($sqlu)) {
        echo "Lo sentimos, este sitio web está experimentando problemas.";
        exit;
    }

}*/