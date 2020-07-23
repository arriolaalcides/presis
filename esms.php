<?php
require('./sendgrid-php/sendgrid-php.php');
require_once 'plivo.php';

if (!function_exists('http_response_code')) {
        function http_response_code($code = NULL) {

            if ($code !== NULL) {

                switch ($code) {
                    case 100: $text = 'Continue'; break;
                    case 101: $text = 'Switching Protocols'; break;
                    case 200: $text = 'OK'; break;
                    case 201: $text = 'Created'; break;
                    case 202: $text = 'Accepted'; break;
                    case 203: $text = 'Non-Authoritative Information'; break;
                    case 204: $text = 'No Content'; break;
                    case 205: $text = 'Reset Content'; break;
                    case 206: $text = 'Partial Content'; break;
                    case 300: $text = 'Multiple Choices'; break;
                    case 301: $text = 'Moved Permanently'; break;
                    case 302: $text = 'Moved Temporarily'; break;
                    case 303: $text = 'See Other'; break;
                    case 304: $text = 'Not Modified'; break;
                    case 305: $text = 'Use Proxy'; break;
                    case 400: $text = 'Bad Request'; break;
                    case 401: $text = 'Unauthorized'; break;
                    case 402: $text = 'Payment Required'; break;
                    case 403: $text = 'Forbidden'; break;
                    case 404: $text = 'Not Found'; break;
                    case 405: $text = 'Method Not Allowed'; break;
                    case 406: $text = 'Not Acceptable'; break;
                    case 407: $text = 'Proxy Authentication Required'; break;
                    case 408: $text = 'Request Time-out'; break;
                    case 409: $text = 'Conflict'; break;
                    case 410: $text = 'Gone'; break;
                    case 411: $text = 'Length Required'; break;
                    case 412: $text = 'Precondition Failed'; break;
                    case 413: $text = 'Request Entity Too Large'; break;
                    case 414: $text = 'Request-URI Too Large'; break;
                    case 415: $text = 'Unsupported Media Type'; break;
                    case 500: $text = 'Internal Server Error'; break;
                    case 501: $text = 'Not Implemented'; break;
                    case 502: $text = 'Bad Gateway'; break;
                    case 503: $text = 'Service Unavailable'; break;
                    case 504: $text = 'Gateway Time-out'; break;
                    case 505: $text = 'HTTP Version not supported'; break;
                    default:
                        exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
                }

                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

                header($protocol . ' ' . $code . ' ' . $text);

                $GLOBALS['http_response_code'] = $code;

            } else {

                $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

            }

            return $code;

        }
    }

//var_dump(json_decode($foo, true));
	

	
	$mysqli = new mysqli("testsp.cobv1qysbncr.sa-east-1.rds.amazonaws.com", "presis", "Presis10241024", "presisml");
	$sql="select * from smss where enviado=0";
	if(!$result = $mysqli->query($sql)){
		die('There was an error running the query [' . $mysqli->error . ']');
	}

	while($row = $result->fetch_assoc()){
	
     $tipo=$row['tipo'];
	 if ($tipo=="email"){
		$sendgrid = new SendGrid(arieldeanna, ariel123);
		$email = new SendGrid\Email();
		
		$email->addTo($row['email'])
				->setFrom("info@webpack.com.ar")
				->setSubject('[Notificacion WebPack]')
				->setText($row['mensaje'])
				->setHtml("<h1>".$row['mensaje']."</h1>")
	
;

		$result2 = $sendgrid->send($email);
		if ($result2->code==200){
			$sql="update smss set enviado=1 where idsmss=".$row['idsmss'];
			if(!$result3 = $mysqli->query($sql)){
				die('There was an error running the query [' . $mysqli->error . ']');
			}
	}
	 }
	 if ($tipo=="sms"){
	
	 if (!($row["telefono"]===NULL)){
	
				$auth_id = "MAOTA2MTJLNZDMODFMM2";
    $auth_token = "MGI3ZTExYjIwYzE0ZWEwYTlmZDk5YmQ0MjExMTFl";
    $p = new RestAPI($auth_id, $auth_token);
	$mensaje=$row['mensaje'];
	
	$encoded = utf8_encode($mensaje);
	
	// Send a message
    $params = array(
			'src' => '+111111',
            'dst' => $row['telefono'],
            'text' => $encoded,
            'type' => 'sms',
        );
		//'text' => 'Motonorte retirará sus paquetes bajo el código de retiro: 602376solicítelo al chofer  antes de entregárselos.',
    
    $response = $p->send_message($params);


	if ($response['status']==202){
			$sql="update smss set enviado=1 where idsmss=".$row['idsmss'];
			if(!$result3 = $mysqli->query($sql)){
				$mysqli->close();

				die('There was an error running the query [' . $mysqli->error . ']');
			}
	}
	}
	 }
	}
	

	
	
			

			
	
	
	
	
	
	
	$mysqli->close();
?>
