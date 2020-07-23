<?php

require_once('./base/modelo.php');

class getManifiesto extends modelo{

    public function __construct(){
        parent::__construct();
    }


    public function getManifiestos($nroManifiesto){

        $result = $this->_db->query("
        SELECT * FROM manifiestocarga WHERE id=".$nroManifiesto." ");
        while ($row = $result->fetch_assoc()) {
            $viajes[] = array_map('utf8_encode', $row);
        }
        $result->free();
        $this->disconnect();
        return json_encode($viajes);
    }

}

$nroManifiesto = $_GET['nroManifiesto'];

$search = new getManifiesto();

$lista = $search->getManifiestos($nroManifiesto);

echo $lista;
?>

