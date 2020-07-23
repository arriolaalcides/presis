<?php

require_once('./base/modelo.php');

class Recibidos extends modelo{

    public function __construct(){
        parent::__construct();
    }

    public function updateRetiroRecibidor($retiro_id){
        $recibido = 1;
        $stmt = $this->_db->prepare("UPDATE constanciaretiro SET recibido=? WHERE id=? ");
        $rc = $stmt->bind_param("ii",$recibido,$retiro_id);
        $stmt->execute();
        $stmt->close();
    }

}

$retiro_id = $_POST['retiro_id'];

$retiro = new Recibidos();

$respuesta = $retiro->updateRetiroRecibidor($retiro_id);

echo $respuesta;

?>