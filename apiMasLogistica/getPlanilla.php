<?php

require_once('./base/modelo.php');

class getPlanilla extends modelo{

    public function __construct(){
        parent::__construct();
    }


    public function getPlanillas($nroPlanilla){

        $result = $this->_db->query("
        SELECT 
retiro.id 'nro_carta',
cliente.empresa reminom,
CONCAT_WS('',sender.calle,' ',sender.altura,' ',sender.piso,' ',sender.dpto,' (',sender.cp,')') remidir,
recorridos_retiros.recorrido_id nro_planil, 
recorrido.distribuidor_id distribuidor, 
'WP' socio,
recorridos_retiros.orden nro_orden,
'' telefono,
'' email,
CONCAT_WS('',comprador.calle,' ',comprador.altura,' ',comprador.piso,' ',comprador.dpto,' (',comprador.cp,')') domDestino,
comprador.localidad,
comprador.provincia,
comprador.apellido_nombre destinatario,
cliente.codigo codigoCli,
datosenvios.cecoDesc ceco,
comprador.celular,
comprador.other_info,
comprador.horario,
comprador.documento,
comprador.localidad,
comprador.obs1,
comprador.obs2,
comprador.obs3,
comprador.obs4,
comprador.obsEstado,
datosenvios.bultos,
datosenvios.contrareembolso,
datosenvios.observaciones,
retiro.remito
FROM recorridos_retiros,recorrido,retiro,sender,estado,cliente,comprador,datosenvios  
WHERE recorridos_retiros.retiro_id=retiro.id 
AND retiro.datosenvios_id=datosenvios.id
AND retiro.comprador_id=comprador.id
AND retiro.cliente_id=cliente.id
AND sender.id=retiro.sender_id 
AND estado.id=retiro.estado_id 
AND recorridos_retiros.recorrido_id=recorrido.id
AND recorrido_id=".$nroPlanilla." 
AND (estado.id='70' OR estado.id='96')
ORDER BY nro_orden ASC
        ");

        while ($row = $result->fetch_assoc()) {
            $viajes[] = array_map('utf8_encode', $row);
        }
        $result->free();
        $this->disconnect();
        return json_encode($viajes);
    }

}

$nroPlanilla = $_GET['nroPlanilla'];

$search = new getPlanilla();

$lista = $search->getPlanillas($nroPlanilla);

echo $lista;
?>

