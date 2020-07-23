<?php
ini_set('memory_limit', '-1');
require_once('modelo.php');

class getInfo extends modelo{

	public function __construct(){
		parent::__construct();
	}

	public function getData($id){
		if($stmt = $this->_db->prepare("SELECT 
		t.retiro_id, 
		c.empresa, 
		c.localidad, 
		c.provincia, 
		de.fecha_pactada,	
		receptor_fecha_hora, 
		e.codigo,
		e.nombre AS estado_nombre, 
		t.estado_id, 
		t.receptor_nombre, 
		t.detalles,
		t.timestamp_modificacion
		FROM 
		retiro AS r 
		LEFT JOIN datosEnvios AS de ON(r.datosEnvios_id=de.id)
		LEFT JOIN tracker AS t ON(r.id=t.retiro_id) 
		LEFT JOIN estado AS e ON(t.estado_id=e.id) 
		LEFT JOIN comprador AS c ON(r.comprador_id=c.id)
		WHERE t.retiro_id=? OR r.remito=? ")){
			$rc = $stmt->bind_param("ss",$id,$id);
			if ( false===$rc ) {
				die('bind_param() failed: ' . htmlspecialchars($stmt->error));
			}
			$rc = $stmt->execute();
			if ( false===$rc ) {
				die('execute() failed: ' . htmlspecialchars($stmt->error));
			}

			/*$stmt->bind_result($retiro_id,$estado_id,$receptor_nombre,$detalles,$receptor_fecha_hora,$empresa,
				$localidad,$provincia,$fecha_pactada,$estado_codigo,$nombre);*/
			$stmt->bind_result($retiro_id,$empresa,$localidad,$provincia,$fecha_pactada,$receptor_fecha_hora,$codigo,$estado_nombre,$estado_id,$receptor_nombre,$detalles,$timestamp_modificacion);
			while($stmt->fetch()){
				$cabecera = array(
					"id" => $retiro_id,
					"comprador_empresa"=>utf8_encode($empresa),
					"localidad"=>utf8_encode($localidad),
					"provincia"=>utf8_encode($provincia),
					"fecha_pactada"=>$fecha_pactada
				);
				$array[] = array(
					"receptor_fecha_hora"=>$receptor_fecha_hora,
					"estado_codigo"=>$codigo,
					"estado_nombre"=>utf8_encode($estado_nombre),
					"estado_id" => $estado_id,
					"receptor_nombre" =>utf8_encode($receptor_nombre),
					"detalles"=>utf8_encode($detalles),
					"timestamp_modificacion"=>$timestamp_modificacion
				);
			}
			$final = array("cabecera"=>$cabecera, "detalle"=>$array);
			//$final = array("detalle"=>$cabecera);
			$stmt->free_result();
			$stmt->close();
		}else{
			printf("Errormessage: %s\n", $this->_db->error);
		}
		return $final;
	}

}

header('Content-Type: application/json');
$getInfo = new getInfo();

$id = trim($_GET['tracking']);

$data = $getInfo->getData($id);

echo json_encode($data);
?>

