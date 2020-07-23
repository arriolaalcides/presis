<?php
require_once('connHelper.php');
connect();

header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=abc.xsl");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);

function fecha($fecha){
    $fecha = explode('/', $fecha);
    $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
    return $fecha;
}

$bandera = false;

$desde = $_REQUEST['fechaD'];
$hasta = $_REQUEST['fechaH'];
$fUltimoEstadoD = $_REQUEST['fUltimoEstadoD'];
$fUltimoEstadoH = $_REQUEST['fUltimoEstadoH'];
$fUltimaPlaD = $_REQUEST['fUltimaPlaD'];
$fUltimaPlaH = $_REQUEST['fUltimaPlaH'];
$planillaD = $_REQUEST['planillaD'];
$planillaH = $_REQUEST['planillaH'];
$fechaFactD = $_REQUEST['fechaFactD'];
$fechaFactH = $_REQUEST['fechaFactH'];
$nroGuiaD = $_REQUEST['nroGuiaD'];
$nroGuiaH = $_REQUEST['nroGuiaH'];
$cordonO = $_REQUEST['cordonO'];
$cordonD = $_REQUEST['cordonD'];
$zona = $_REQUEST['zona'];
$guiaManual = $_REQUEST['guiaManual'];
$guiaAgente = $_REQUEST['guiaAgente'];
$cliente = $_REQUEST['cliente'];
$nroCta = $_REQUEST['nroCta'];
$sucursal = $_REQUEST['sucursal'];
$distribuidor = $_REQUEST['distribuidor'];
$facturado = $_REQUEST['facturado'];
$conContrareembolso = $_REQUEST['conContrareembolso'];
$sinEstado = $_REQUEST['sinEstado'];
$sinFactura = $_REQUEST['sinFactura'];
$estado = $_REQUEST['estado'];
$user = $_REQUEST['nombreUsuario'];

$sql1 = "SELECT user_admin FROM fos_user WHERE username='".$user."' ";
$result1 = mysql_query($sql1)or die(mysql_error());
$reg = mysql_fetch_array($result1);
$tipoUser = $reg['user_admin'];

$sql = "SELECT r.id, c.empresa AS cliEmpresa, s.descripcion, de.guiaAgente, r.remito, e.codigo AS estado,
DATE_FORMAT(r.fecha_hora, '%d/%m/%Y') AS fecha, r.detalle_entrega, r.fecha_hora_entrega, r.distribuidor, de.bultos, de.peso,
de.alto, de.largo, de.ancho, de.valor_declarado, de.contrareembolso, de.cordonorigen, de.cordondestino, r.zona, r.nroPlanilla, 
r.fechaplanilla, de.observaciones, de.flete, de.costo_por_contrareembolso, de.seguro, de.costo_src, de.costo_despacho_a_expreso, de.custodia,
de.monto_guia_web, de.costo_por_monitoreo_activo, de.costo_adicional1, de.costo_adicional2, de.costo_adicional3, de.totalFlete, de.fecha_pactada,
sen.empresa AS senEmpresa, 
sen.remitente  AS senRemitente, 
CONCAT_WS(' ',sen.calle,' ',sen.altura,' ',sen.piso,' ',sen.dpto) AS senderDireccion, 
sen.localidad AS senLocalidad, 
sen.provincia AS senProvincia,
sen.cp AS senCp,
sen.celular AS senCelular, 
sen.other_info AS senOtherInfo, 
com.empresa, 
com.apellido_nombre, 
CONCAT_WS(' ',com.calle,' ',com.altura,' ',com.piso,' ',com.dpto) AS compradorDireccion,
com.localidad, 
com.provincia, 
com.cp, 
com.email, 
com.celular, 
com.other_info AS com_other_info, 
e.nombre AS estadoNombre, 
r.fechaUltimoEstado, 
r.receptor_nombre,
r.receptor_apellido, 
r.receptor_dni,
r.createGuia,
fu.user_admin
FROM retiro AS r 
LEFT JOIN cliente AS c ON(r.cliente_id=c.id)
LEFT JOIN datosenvios AS de ON(r.datosenvios_id=de.id)
LEFT JOIN servicio AS s ON(r.servicio_id=s.id)
LEFT JOIN estado AS e ON(r.estado_id=e.id) 
LEFT JOIN sender AS sen ON(r.sender_id=sen.id)
LEFT JOIN comprador AS com ON(r.comprador_id=com.id) 
LEFT JOIN fos_user AS fu ON(r.createGuia=fu.username)
WHERE ";

if ($desde != "") {
    $bandera ? $sql = $sql . "AND date_format(r.fecha_hora, '%Y-%m-%d') BETWEEN '" . fecha($desde) . "' AND '" . fecha($hasta) . "'  " : $sql = $sql . "date_format(r.fecha_hora, '%Y-%m-%d') BETWEEN '" . fecha($desde) . "' AND '" . fecha($hasta) . "' ";
    $bandera = true;
}
if ($fUltimoEstadoD != "") {
    $bandera ? $sql = $sql . "AND date_format(r.fecha_hora_entrega, '%Y-%m-%d') BETWEEN '" . fecha($fUltimoEstadoD) . "' AND '" . fecha($fUltimoEstadoH) . "'  " : $sql = $sql . "date_format(r.fecha_hora_entrega, '%Y-%m-%d') BETWEEN '" . fecha($fUltimoEstadoD) . "' AND '" . fecha($fUltimoEstadoH) . "' ";
    $bandera = true;
}
if ($fUltimaPlaD != "") {
    $bandera ? $sql = $sql . "AND date_format(r.fechaplanilla, '%Y-%m-%d') BETWEEN '" . fecha($fUltimaPlaD) . "' AND '" . fecha($fUltimaPlaH) . "'  " : $sql = $sql . "date_format(r.fechaplanilla, '%Y-%m-%d') BETWEEN '" . fecha($fUltimaPlaD) . "' AND '" . fecha($fUltimaPlaH) . "' ";
    $bandera = true;
}
if ($planillaD != "") {
    $bandera ? $sql = $sql . "AND r.nroPlanilla BETWEEN '" . $planillaD . "' AND '" . $planillaH . "'  " : $sql = $sql . "r.nroPlanilla BETWEEN '" . $planillaD . "' AND '" . $planillaH . "' ";
    $bandera = true;
}
if ($fechaFactD != "") {
    $bandera ? $sql = $sql . "AND date_format(de.fecha_factura, '%Y-%m-%d') BETWEEN '" . fecha($fechaFactD) . "' AND '" . fecha($fechaFactH) . "'  " : $sql = $sql . "date_format(de.fecha_factura, '%Y-%m-%d') BETWEEN '" . fecha($fechaFactD) . "' AND '" . fecha($fechaFactH) . "' ";
    $bandera = true;
}
if ($nroGuiaD != "") {
    $bandera ? $sql = $sql . "AND r.id BETWEEN '" . $nroGuiaD . "' AND '" . $nroGuiaH . "'  " : $sql = $sql . "r.id BETWEEN '" . $nroGuiaD . "' AND '" . $nroGuiaH . "' ";
    $bandera = true;
}
if ($cordonO != "") {
    $bandera ? $sql = $sql . "AND upper(de.cordonOrigen) = upper('" . $cordonO . "') " : $sql = $sql . "upper(de.cordonOrigen) = upper('" . $cordonO . "') ";
    $bandera = true;
}
if ($cordonD != "") {
    $bandera ? $sql = $sql . "AND upper(de.cordonDestino) = upper('" . $cordonD . "') " : $sql = $sql . "upper(de.cordonDestino) = upper('" . $cordonD . "') ";
    $bandera = true;
}
if ($zona != "") {
    $bandera ? $sql = $sql . "AND upper(r.zona) = upper('" . $zona . "') " : $sql = $sql . "upper(r.zona) = upper('" . $zona . "') ";
    $bandera = true;
}
if ($guiaManual != "") {
    $bandera ? $sql = $sql . "AND upper(r.remito) = upper('" . $guiaManual . "') " : $sql = $sql . "upper(r.remito) = upper('" . $guiaManual . "') ";
    $bandera = true;
}
if ($guiaAgente != "") {
    $bandera ? $sql = $sql . "AND upper(de.guiaAgente) = upper('" . $guiaAgente . "') " : $sql = $sql . "upper(de.guiaAgente) = upper('" . $guiaAgente . "') ";
    $bandera = true;
}
if ($nroCta != "") {
    $bandera ? $sql = $sql . "AND upper(de.nroCta) = upper('" . $nroCta . "') " : $sql = $sql . "upper(de.nroCta) = upper('" . $nroCta . "') ";
    $bandera = true;
}
if ($sucursal != "") {
    $bandera ? $sql = $sql . "AND upper(de.sucursalCabecera) = upper('" . $sucursal . "') " : $sql = $sql . "upper(de.sucursalCabecera) = upper('" . $sucursal . "') ";
    $bandera = true;
}
if ($distribuidor != "") {
    $bandera ? $sql = $sql . "AND upper(r.distribuidor) = upper('" . $distribuidor . "') " : $sql = $sql . "upper(r.distribuidor) = upper('" . $distribuidor . "') ";
    $bandera = true;
}
if ($facturado != "") {
    $bandera ? $sql = $sql . "AND upper(de.facturado) = upper('" . $facturado . "') " : $sql = $sql . "upper(de.facturado) = upper('" . $facturado . "') ";
    $bandera = true;
}
if ($conContrareembolso != "") {
    $bandera ? $sql = $sql . "AND upper(de.coontrareembolso) = upper('" . $conContrareembolso . "') " : $sql = $sql . "upper(de.coontrareembolso) = upper('" . $conContrareembolso . "') ";
    $bandera = true;
}
if($sinEstado=="true"){
    $bandera ? $sql = $sql . "AND upper(r.estado_id) IS NULL " : $sql = $sql . "upper(r.estado_id) IS NULL ";
    $bandera = true;
}
if($sinFactura=="true"){
    $bandera ? $sql = $sql . "AND upper(de.nro_factura) IS NULL " : $sql = $sql . "upper(de.nro_factura) IS NULL ";
    $bandera = true;
}
if($estado != "null"){
    $estado = explode(",", $estado);
    for($i=0; $i<count($estado); $i++){
        if($i==0){
            $bandera ? $sql = $sql . "AND (upper(r.estado_id) = upper('" . $estado[$i] . "') )" : $sql = $sql . " upper(r.estado_id) = upper('" . $estado[$i] . "') ";
            $bandera = true;
        }else{
            $bandera ? $sql = $sql . "OR (upper(r.estado_id) = upper('" . $estado[$i] . "') )" : $sql = $sql . " upper(r.estado_id) = upper('" . $estado[$i] . "') ";
            $bandera = true;
        }
    }
}

if($cliente!=""){
    $bandera ? $sql = $sql . "AND r.cliente_id = '".$cliente."' " : $sql = $sql . "r.cliente_id = '".$cliente."' ";
    $bandera = true;
}else{
    die("Ocurrio un error al exportar el archivo");
}

if($tipoUser=="1"){
    $sql = $sql . "AND (estado_id != 41 OR estado_id != 106) AND e.para_web=1";
}else{
    $sql = $sql . "AND (estado_id != 41 OR estado_id != 106) AND e.para_web=1 AND createGuia='".$user."' ";
}

//die($sql);

$result = mysql_query($sql)or die(mysql_error());
echo '<table cellpadding="0" cellspacing="0" border="1">';
echo '<tr>';
    echo '<td>Nro. Guia</td>';
    echo '<td>Cliente</td>';
    echo '<td>Servicio</td>';
    echo '<td>Guia Agente</td>';
    echo '<td>Guia Manual</td>';
    echo '<td>Fecha</td>';
    echo '<td>Estado</td>';
    echo '<td>Detalle Entrega</td>';
    echo '<td>F. Hora Estado</td>';

    echo '<td>Bultos</td>';
    echo '<td>Peso</td>';
    echo '<td>Alto</td>';
    echo '<td>Largo</td>';
    echo '<td>Ancho</td>';
    echo '<td>Valor declarado</td>';
    echo '<td>Contrareembolso</td>';
    echo '<td>C. Origen</td>';
    echo '<td>C. Destino</td>';
    echo '<td>Observaciones</td>';
    echo '<td>Rem. Empresa</td>';
    echo '<td>Rem. Nombre</td>';
    echo '<td>Rem. Dirección</td>';
    echo '<td>Rem. Localidad</td>';
    echo '<td>Rem. Provincia</td>';
    echo '<td>Rem. CP</td>';
    echo '<td>Rem. Celular</td>';
    echo '<td>Rem. Other Info</td>';
    echo '<td>Com. Empresa</td>';
    echo '<td>Com. Nombre</td>';
    echo '<td>Com. Dirección</td>';
    echo '<td>Com. Localidad</td>';
    echo '<td>Com. Provincia</td>';
    echo '<td>Com. CP</td>';
    echo '<td>Com. Email</td>';
    echo '<td>Com. Celular</td>';
    echo '<td>Com. Other Info</td>';
    echo '<td>Detalle Estado</td>';
    echo '<td>F. U. Estado</td>';
    echo '<td>Rec. Nombre</td>';
    echo '<td>Rec. Apellido</td>';
    echo '<td>Rec. DNI</td>';
    echo '<td>Usuario</td>';
echo '</tr>';
while($row = mysql_fetch_array($result)){
    echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['cliEmpresa'].'</td>';
        echo '<td>'.$row['descripcion'].'</td>';
        echo '<td>'.$row['guiaAgente'].'</td>';
        echo '<td>'.$row['remito'].'</td>';
        echo '<td>'.$row['fecha'].'</td>';
        echo '<td>'.$row['estado'].'</td>';
        echo '<td>'.$row['detalle_entrega'].'</td>';
        echo '<td>'.$row['fecha_hora_entrega'].'</td>';

        echo '<td>'.$row['bultos'].'</td>';
        echo '<td>'.$row['peso'].'</td>';
        echo '<td>'.$row['alto'].'</td>';
        echo '<td>'.$row['largo'].'</td>';
        echo '<td>'.$row['ancho'].'</td>';
        echo '<td>'.$row['valor_declarado'].'</td>';
        echo '<td>'.$row['contrareembolso'].'</td>';
        echo '<td>'.$row['cordonorigen'].'</td>';
        echo '<td>'.$row['cordondestino'].'</td>';

        echo '<td>'.$row['observaciones'].'</td>';
        echo '<td>'.$row['senEmpresa'].'</td>';
        echo '<td>'.$row['senRemitente'].'</td>';
        echo '<td>'.$row['senderDireccion'].'</td>';
        echo '<td>'.$row['senLocalidad'].'</td>';
        echo '<td>'.$row['senProvincia'].'</td>';
        echo '<td>'.$row['senCp'].'</td>';
        echo '<td>'.$row['senCelular'].'</td>';
        echo '<td>'.$row['senOther_info'].'</td>';
        echo '<td>'.$row['empresa'].'</td>';
        echo '<td>'.$row['apellido_nombre'].'</td>';
        echo '<td>'.$row['compradorDireccion'].'</td>';
        echo '<td>'.$row['localidad'].'</td>';
        echo '<td>'.$row['provincia'].'</td>';
        echo '<td>'.$row['cp'].'</td>';
        echo '<td>'.$row['email'].'</td>';
        echo '<td>'.$row['celular'].'</td>';
        echo '<td>'.$row['com_other_info'].'</td>';
        echo '<td>'.$row['estadoNombre'].'</td>';
        echo '<td>'.$row['fechaUltimoEstado'].'</td>';
        echo '<td>'.$row['receptor_nombre'].'</td>';
        echo '<td>'.$row['receptor_apellido'].'</td>';
        echo '<td>'.$row['receptor_dni'].'</td>';
        echo '<td>'.$row['createGuia'].'</td>';
    echo '</tr>';
}
echo '</table>';
?>