<?php

require_once('./base/modelo.php');

class Estado extends modelo{

    public function __construct(){
        parent::__construct();
    }

    public function getEstadosJson($idMovil){
        $result = $this->_db->query("SELECT codigo, nombre, paraEntrega, paraRetiro FROM estado 
        WHERE para_chofer=1 ORDER BY codigo ASC");
        while ($row = $result->fetch_assoc()) {
            $viajes[] = array_map('utf8_encode', $row);
        }
        $result->free();
        $this->disconnect();
        return json_encode($viajes);
    }

}

$estado = new Estado();

$lista = $estado->getEstadosJson();

echo $lista;

?>