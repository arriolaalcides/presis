<?php
require_once('./base/modelo.php');

class PostInformes extends modelo{

    public function __construct(){
        parent::__construct();
    }

    public function saveInforme($retiro_id,$guia,$codigo,$descripcion,$fecha,$movil_id){
        $stmt = $this->_db->prepare("INSERT INTO ftmapa.retirosapi(retiro_id,guia,codigoEstado,estado,fecha,movil)VALUES(?,?,?,?,?,?) ");
        $rc = $stmt->bind_param("ssssss",$retiro_id,$guia,$codigo,$descripcion,$fecha,$movil_id);
        $stmt->execute();
        $stmt->close();
    }

}

$foo = file_get_contents("php://input");

$r=json_decode($foo,true);

$informe = new PostInformes();

$informe->saveInforme($r['retiro_id'],$r['guia'],$r['codigo'],$r['descripcion'],$r['fecha'],$r['movil_id']);
//{"retiro_id":105,"guia":"100006626","codigo":"28","descripcion":"Recolección cancelada por cliente","fecha":"2017-07-11 16:26:49"}
?>