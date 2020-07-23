<?php
require_once('./base/modelo.php');

class PostManifiesto extends modelo {

    public function __construct(){
        parent::__construct();
    }

    public function saveFirmaManifiesto($nro_manifiesto,$path,$fechaCel,$fechaBase,$recibio,$documento,$obs,$estado,$distribuidor_id){
        //echo $nro_manifiesto;
        if($nro_manifiesto!=0 || $nro_manifiesto!=""){
        $stmt = $this->_db->prepare("INSERT INTO firmasmanifiesto(nro_manifiesto,img,estado,recibio,documento,
        obs,fechaCel,fechaBase,distribuidor_id)VALUES(?,?,?,?,?,?,?,?,?) ");
        $rc = $stmt->bind_param("sssssssss",$nro_manifiesto,$path,$estado,$recibio,$documento,$obs,$fechaCel,$fechaBase,
        	$distribuidor_id);
        if(false===$rc){
            die('bind_param() failed: ' . htmlspecialchars($this->_db->error));
        }
        $rc = $stmt->execute();
        if(false===$rc){
            die('execute() failed: ' . htmlspecialchars($this->_db->error));
        }
        	$stmt1 = $this->_db->prepare("select gestioncel.estado_id as estado_original from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  
                                            ");
                $stmt1->execute(); 
                $stmt1->store_result(); 
                if ($stmt1->num_rows >= "1"){ 
                      $stmt1->bind_result($estado_original);
                      while ($stmt1->fetch()) {
                             //var_dump($estado_original); 
                             $stmt1->free_result();
                             $stmt1->close();
                            $this->actualizarRetiro($nro_manifiesto, $fechaCel, $fechaBase, $estado,$documento,$recibio,
                            	$obs, $estado_original, $distribuidor_id);         
                        }
                }

        $stmt->close();
        }
    }

    /*public function estadoActual($nro_manifiesto,$estado,$documento,$recibio,$obs){
    	$stmt1 = $this->_db->prepare("select gestioncel.estado_id as estado_original from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  
                                            ");
                $stmt1->execute(); 
                $stmt1->store_result(); 
                if ($stmt1->num_rows >= "1"){ 
                      $stmt1->bind_result($estado_original);
                      while ($stmt1->fetch()) {
                             //var_dump($estado_original); 
                             $stmt1->free_result();
                             $stmt1->close();
                            $this->actualizarRetiro($nro_manifiesto, $fechaCel, $fechaBase, $estado, $documento,$recibio,
                            	$obs, $estado_original, $distribuidor_id);         
                        }
                }
    }*/

    public function actualizarRetiro($nro_manifiesto, $fechaCel, $fechaBase, $estado, $documento,$recibio,$obs, 
    	$estado_original, $distribuidor_id){

        switch($estado){
            case 'ENTREGADO GALANDER':
            	switch ($estado_original) {
            		case 157:
            			$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 157 
                            "); 
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                if ($stmt1->num_rows >= "1") { 
		                    $stmt1->bind_result($guia);        
		                    while ($stmt1->fetch()) {
		                         	//$stmt1->free_result();
		                         	//$stmt1->close();
		                    	//printf ($guia . "\n");
		                
		                            //$stmt1->free_result();
		                            $estado=158;//EQUIPO EN TRASLADO AL HUB REPARADOR 
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
			                                	///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                die('execute() failed: ' . htmlspecialchars($rc->error));
		                                }	
		                    }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
		                    exit();
		                }
            			break;
            		case 158:
            			
            				$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 158 
                                            ");
                
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                 if ($stmt1->num_rows >= "1") { 
		                      $stmt1->bind_result($guia);
		                      while ($stmt1->fetch()) {
		                            // $stmt1->free_result();
		                           //  $stmt1->close();
		                            $estado=159;//INGRESO AL HUB  
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                
		                                    die('execute() failed: ' . htmlspecialchars($rc->error));
		                                }
		                                /*else{
		                                     /////////////////////////////////////////////////////////////////////
		                                    $smsusuario = "ranval"; //usuario de SMS MASIVOS
		                                    $smsclave = "mrcce5"; //clave de SMS MASIVOS
		                                    $smsnumero = $ani;
		                                    $smstexto = "Un tecnico especialista ya esta trabajando en la reparacion de tu equipo. Vamos a ofrecerte la mejor solucion.";
		                                    $smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($smsnumero) ."&TEXTO=". urlencode($smstexto) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=". urlencode($smsclave) );
		                                    exit;
		                                }*/
		                        }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);    
		                    exit();
		                }
            			break;

        			case 166:
        					$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 166 
                                            "); 
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                if ($stmt1->num_rows >= "1") { 
		                    $stmt1->bind_result($guia);
		                    while ($stmt1->fetch()) {
		                        //$stmt1->free_result();
		                             //$stmt1->close();
		                            //$stmt1->free_result();
		                            $estado=161;//EQUIPO EN TRASLADO AL CEC DE ORIGEN
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                
		                                    die('execute() failed: ' . htmlspecialchars($rc->error));
		                                }
		                    }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
		                    exit();
		                }
        				break;
        			case 161:
        					$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 161 
                                            "); 
			                $stmt1->execute(); 
			                $stmt1->store_result(); 
			                if ($stmt1->num_rows >= "1") { 
			                    $stmt1->bind_result($guia);
			                    while ($stmt1->fetch()) {
			                        	//$stmt1->free_result();
			                            //$stmt1->close();
			                            //$stmt1->free_result();
			                            $estado=162;//EQUIPO RECEPCIONADO EN CEC
			                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
			                            SET retiro.estado_id=?, gestioncel.estado_id=? 
			                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

			                            if ( false===$stmt2 ) {
			                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
			                                }
			                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
			                                if(false===$rc){
			                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
			                                }
			                                $rc = $stmt2->execute();
			                                if(false===$rc) {
			                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                
			                                die('execute() failed: ' . htmlspecialchars($rc->error));
			                                }
			                                ///////////////////////////////RESPALDO TXT////////////////////////
		                                
			                    }

			                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
			                    exit();
			                }
        				break;
        			default:
						///////////////////////////////RESPALDO TXT////////////////////////
                        $file = fopen("../apiMasLogisticaDesarrollo/default_apk/manifiesto"."_".
                        $nro_manifiesto."_".$fechaBase.".txt","a");
                    	$txt = $estado.";guia;".$fechaBase.";".$nro_manifiesto.";".$documento.";".
                    	$recibio.";".$obs.";".$distribuidor_id;
                    	fwrite($file, $txt);
                    	fclose($file);
            	}

                break;
            case 'ENTREGADO MOVISTAR':
            	switch ($estado_original) {
            		case 157:
            			$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 157 
                            "); 
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                if ($stmt1->num_rows >= "1") { 
		                    $stmt1->bind_result($guia);
		                    while ($stmt1->fetch()) {
		                         //$stmt1->free_result();
		                           //  $stmt1->close();
		                            //$stmt1->free_result();
		                            $estado=158;//EQUIPO EN TRASLADO AL HUB REPARADOR 
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                
		                                    die('execute() failed: ' . htmlspecialchars($rc->error));
		                                }
		                                
		                    }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
		                    exit();
		                }
            			break;
            		case 158:
            				$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 158 
                                            ");
                
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                 if ($stmt1->num_rows >= "1") { 
		                      $stmt1->bind_result($guia);
		                      while ($stmt1->fetch()) {
		                            //die($ani);
		                             //$stmt1->free_result();
		                            // $stmt1->close();
		                            //$stmt1->free_result();
		                            $estado=159;//INGRESO AL HUB  
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                
		                                    die('execute() failed: ' . htmlspecialchars($rc->error));
		                                }
		                                
		                                /*else{
		                                     /////////////////////////////////////////////////////////////////////
		                                    $smsusuario = "ranval"; //usuario de SMS MASIVOS
		                                    $smsclave = "mrcce5"; //clave de SMS MASIVOS
		                                    $smsnumero = $ani;
		                                    $smstexto = "Un tecnico especialista ya esta trabajando en la reparacion de tu equipo. Vamos a ofrecerte la mejor solucion.";
		                                    $smsrespuesta = file_get_contents("http://servicio.smsmasivos.com.ar/enviar_sms.asp?API=1&TOS=". urlencode($smsnumero) ."&TEXTO=". urlencode($smstexto) ."&USUARIO=". urlencode($smsusuario) ."&CLAVE=". urlencode($smsclave) );
		                                    exit;
		                                }*/
		                        }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);    
		                    exit();
		                }
            			break;

        			case 166:
        					$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 166 
                                            "); 
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                if ($stmt1->num_rows >= "1") { 
		                    $stmt1->bind_result($guia);
		                    while ($stmt1->fetch()) {
		                        //$stmt1->free_result();
		                          //   $stmt1->close();
		                            //$stmt1->free_result();
		                            $estado=161;//EQUIPO EN TRASLADO AL CEC DE ORIGEN
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                            
		                                    die('execute() failed: ' . htmlspecialchars($rc->error));
		                                }
		                                
		                    }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
		                    exit();
		                }
        				break;
        			case 161:
        					$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 161 
                                            "); 
			                $stmt1->execute(); 
			                $stmt1->store_result(); 
			                if ($stmt1->num_rows >= "1") { 
			                    $stmt1->bind_result($guia);
			                    while ($stmt1->fetch()) {
			                        //$stmt1->free_result();
			                          //   $stmt1->close();
			                            //$stmt1->free_result();
			                            $estado=162;//EQUIPO RECEPCIONADO EN CEC
			                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
			                            SET retiro.estado_id=?, gestioncel.estado_id=? 
			                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

			                            if ( false===$stmt2 ) {
			                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
			                                }
			                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
			                                if(false===$rc){
			                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
			                                }
			                                $rc = $stmt2->execute();
			                                if(false===$rc) {
			                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                die('execute() failed: ' . htmlspecialchars($rc->error));
			                                
			                                }
			                            
			                    }
			                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
			                    exit();
			                }
        				break;
    				default:
						///////////////////////////////RESPALDO TXT////////////////////////
                       
                        $file = fopen("../apiMasLogisticaDesarrollo/default_apk/manifiesto"."_".
                        $nro_manifiesto."_".$fechaBase.".txt","a");
                    	$txt = $estado.";guia;".$fechaBase.";".$nro_manifiesto.";".$documento.";".
                    	$recibio.";".$obs.";".$distribuidor_id;
                    	fwrite($file, $txt);
                    	fclose($file);
            	}
                
                break;
            case 'RETIRADO GALANDER':
            	switch ($estado_original) {
            		case 157:
            			$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 157 
                            "); 
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                if ($stmt1->num_rows >= "1") { 
		                    $stmt1->bind_result($guia);
		                    while ($stmt1->fetch()) {
		                         //$stmt1->free_result();
		                           //  $stmt1->close();
		                            //$stmt1->free_result();
		                            $estado=158;//EQUIPO EN TRASLADO AL HUB REPARADOR 
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                die('execute() failed: ' . htmlspecialchars($rc->error));
		                                    
		                                }
		                                
		                    }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
		                    exit();
		                }
            			break;
            		
        			case 166:
        					$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 166 
                                            "); 
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                if ($stmt1->num_rows >= "1") { 
		                    $stmt1->bind_result($guia);
		                    while ($stmt1->fetch()) {
		                        //$stmt1->free_result();
		                          //   $stmt1->close();
		                            //$stmt1->free_result();
		                            $estado=161;//EQUIPO EN TRASLADO AL CEC DE ORIGEN
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                
		                                    die('execute() failed: ' . htmlspecialchars($rc->error));
		                                }
		                                
		                    }

		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
		                    exit();
		                }
        				break;
    				default:
						///////////////////////////////RESPALDO TXT////////////////////////
                        $file = fopen("../apiMasLogisticaDesarrollo/default_apk/manifiesto"."_".
                        $nro_manifiesto."_".$fechaBase.".txt","a");
                    	$txt = $estado.";guia;".$fechaBase.";".$nro_manifiesto.";".$documento.";".
                    	$recibio.";".$obs.";".$distribuidor_id;
                    	fwrite($file, $txt);
                    	fclose($file);
        			
            	}
                break;
            case 'RETIRADO MOVISTAR':
                switch ($estado_original) {
            		case 157:
            			$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 157 
                            "); 
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                if ($stmt1->num_rows >= "1") { 
		                    $stmt1->bind_result($guia);
		                    while ($stmt1->fetch()) {
		                         //$stmt1->free_result();
		                           //  $stmt1->close();
		                            //$stmt1->free_result();
		                            $estado=158;//EQUIPO EN TRASLADO AL HUB REPARADOR 
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                die('execute() failed: ' . htmlspecialchars($rc->error));
		                                 
		                                }
		                                
		                    }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
		                    exit();
		                }
            			break;
            		
            		
        			case 166:
        					$stmt1 = $this->_db->prepare("select retiro.id as guia from retiro 
                                              join gestioncel  ON (retiro.gestioncel_id = gestioncel.id)
                                              where retiro.nro_constancia = '".$nro_manifiesto."'  and gestioncel.estado_id = 166 
                                            "); 
		                $stmt1->execute(); 
		                $stmt1->store_result(); 
		                if ($stmt1->num_rows >= "1") { 
		                    $stmt1->bind_result($guia);
		                    while ($stmt1->fetch()) {
		                        //$stmt1->free_result();
		                          //   $stmt1->close();
		                            //$stmt1->free_result();
		                            $estado=161;//EQUIPO EN TRASLADO AL CEC DE ORIGEN
		                            $stmt2 = $this->_db->prepare("UPDATE retiro, gestioncel 
		                            SET retiro.estado_id=?, gestioncel.estado_id=? 
		                            WHERE retiro.gestioncel_id=gestioncel.id AND retiro.nro_constancia=?");

		                            if ( false===$stmt2 ) {
		                                    die('prepare() failed: AAAA ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->bind_param('sss',$estado, $estado,  $nro_manifiesto);
		                                if(false===$rc){
		                                    die('bind_param() failed: ' . htmlspecialchars($stmt2->error));
		                                }
		                                $rc = $stmt2->execute();
		                                if(false===$rc) {
		                                		///////////////////////////////RESPALDO TXT////////////////////////
			                                $file = fopen("../apiMasLogisticaDesarrollo/update_gcel_retiro/manifiesto"."_".
		                                    $nro_manifiesto."_".$fechaBase.".txt","a");
			                            	$txt = $estado.";".$guia.";".$fechaBase.";".$nro_manifiesto.";".$documento.";".
			                            	$recibio.";".$obs.";".$distribuidor_id;
			                            	fwrite($file, $txt);
			                            	fclose($file);	                                
			                                die('execute() failed: ' . htmlspecialchars($rc->error));
		                                    
		                                }
		                                
		                    }
		                    $this->actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id);
		                    exit();
		                }
        				break;
    				default:
						///////////////////////////////RESPALDO TXT////////////////////////
                        $file = fopen("../apiMasLogisticaDesarrollo/default_apk/manifiesto"."_".
                        $nro_manifiesto."_".$fechaBase.".txt","a");
                    	$txt = $estado.";guia;".$fechaBase.";".$nro_manifiesto.";".$documento.";".
                    	$recibio.";".$obs.";".$distribuidor_id;
                    	fwrite($file, $txt);
                    	fclose($file);

        			
            	}
                break;
            case 'NO RETIRADO':
                $estado=12;//NO RETIRADO
                break;
            case 'NO ENTREGADO':
                $estado=166;//NO ENTREGADO
                break;
        }
        
    } 

    public function actualizarTracker($nro_manifiesto,$estado,$documento,$recibio,$obs, $distribuidor_id){
        $fecha = date('Y-m-d H:i:s');
        $result = $this->_db->query("SELECT id FROM retiro WHERE nro_constancia=".$nro_manifiesto." ");
        while ($row = $result->fetch_assoc()) {
            $stmt = $this->_db->prepare("INSERT INTO tracker(estado_id,retiro_id,receptor_fecha_hora,timestamp_modificacion,nroPlanilla,dni,receptor_nombre,obs, distribuidor_id)
            VALUES(?,?,?,?,?,?,?,?,?) ");
            $rc = $stmt->bind_param("sssssssss",$estado,$row['id'],$fecha,$fecha,$nro_manifiesto,$documento,$recibio,$obs, $distribuidor_id);
            //$stmt->execute();
            $rc = $stmt->execute();
            if(false===$rc) {
    		///////////////////////////////RESPALDO TXT////////////////////////
                $file = fopen("../apiMasLogisticaDesarrollo/insert_tracker/manifiesto"."_".
                $nro_manifiesto."_".$fecha.".txt","a");
            	$txt = $estado.";".$row['id'].";".$fecha.";".$nro_manifiesto.";".$documento.";".
            	$recibio.";".$obs.";".$distribuidor_id;
            	fwrite($file, $txt);
            	fclose($file);	                                
            }
            $stmt->close();
        }
    }
             

}

$foo = file_get_contents("php://input");

$r=json_decode($foo,true);

$manifiesto = new PostManifiesto();

$nro_manifiesto=$r['nro_manifiesto'];
//die($nro_manifiesto);

if(!empty($r['imagen'])){
    $ima=base64_decode($r['imagen']);
    $filename_path = $nro_manifiesto.'-'.time().uniqid().".jpg";
    file_put_contents("manifiestos/".$filename_path,$ima);
    $path = "manifiestos/".$filename_path;
}else{
    $path = "";
}
    $fechaCel=$r['fecha'];
    $fechaBase = date('Y-m-d H:i:s');
    $recibio = $r['apenom'];
    $documento = $r['documento'];
    $obs = $r['obs'];
    $estado = $r['estado'];
    $distribuidor_id = $r['distribuidor_id'];


$manifiesto->saveFirmaManifiesto($nro_manifiesto,$path,$fechaCel,$fechaBase,$recibio,$documento,$obs,$estado,$distribuidor_id);

?>