<?php

require_once('./base/modelo.php');

class Viaje extends modelo{

    public function __construct(){
        parent::__construct();
    }

    public function getDistribuidor($mail){
        $stmt = $this->_db->prepare("SELECT CONCAT(codigo,' - ',apellido,', ',nombre) AS distribuidor 
        FROM distribuidor WHERE email=?");
        $stmt->bind_param("s",$mail);
        $stmt->execute();
        $stmt->bind_result($distribuidor);
        $stmt->store_result();
        $stmt->fetch();
        return $distribuidor;
    }

    public function getViajesJson($distribuidor){

        $result = $this->_db->query("SELECT id, fecha, CONCAT_WS(' ',calle,' ',altura,' ',piso,' ',dpto) AS domicilio, localidad, cp, franja,
        bultos, peso, contacto, observaciones, retiro AS guia, telefono, estado
        FROM constanciaretiro WHERE habilitado=true AND recibido=false AND distribuidor='".$distribuidor."' ORDER BY id DESC ");

        while ($row = $result->fetch_assoc()) {
            $viajes[] = array_map('utf8_encode', $row);
        }
        $result->free();
        $this->disconnect();
        return json_encode($viajes);
    }

}
//"matias@presisconsultores.com";

$email = $_GET['email'];

$viajes = new Viaje();

$distribuidor = $viajes->getDistribuidor($email);


$lista = $viajes->getViajesJson($distribuidor);

echo $lista;

?>