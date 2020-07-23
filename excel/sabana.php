<?php
require_once('connHelper.php');
connect();

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=pickImpo.xls");
header("Pragma: no-cache");
header("Expires: 0");

function fecha($fecha){
    $fecha = explode('/', $fecha);
    $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
    return $fecha;
}

$bandera = false;

$desde = $_REQUEST['fechaD'];
$hasta = $_REQUEST['fechaH'];
$codSuc = $_REQUEST['codSuc'];
$ani = $_REQUEST['ani'];
$nroserie = $_REQUEST['nroserie'];
$nomyape = $_REQUEST['nomyape'];
$nrosst = $_REQUEST['sst'];
$fabricante = $_REQUEST['fabricante'];
$modelo = $_REQUEST['modelo'];
$estadointervencion = $_REQUEST['estadointervencion'];
$nroimei = $_REQUEST['nroimei'];
$origendelequipo = $_REQUEST['origendelequipo'];
$sva = $_REQUEST['sva'];
$rotura = $_REQUEST['rotura'];
$tipocliente = $_REQUEST['tipocliente'];
$tiposervicio = $_REQUEST['tiposervicio'];
$claimassurant = $_REQUEST['claimassurant'];
$certificadoreparador = $_REQUEST['certificadoreparador'];
$placaswap = $_REQUEST['placaswap'];
$estado = $_REQUEST['estado'];

$sql = "SELECT 
r.id AS guia, e.nombre AS estadoFinal, fab.descricion, mo.descripcion,
t.retiro_id AS tracker,  
gc.usuario, gc.ani, gc.nroserie, gc.nomyape, gc.nrosst, gc.aceptacargos, gc.nivelderep, gc.muleto, gc.imeimuleto, 
gc.fechaactivacion, gc.fechafabricacion, gc.origendelequipo, gc.sva, gc.falla, gc.rotura, gc.completitud,
gc.estadointervencion, gc.certificadoreparador, gc.placaswap, gc.nroimei, gc.tipocliente, gc.tiposervicio,
gc.claimassurant, gc.precinto, gc.trayecto, gc.valordeclaradocel, gc.fecha_base, t.estado_id, 
(SELECT nombre FROM estado WHERE id=t.estado_id) AS estadoTracker,
t.receptor_fecha_hora, t.timestamp_modificacion, t.nroPlanilla, t.fecha_planilla
FROM retiro AS r 
LEFT JOIN cliente AS c ON(r.cliente_id=c.id) 
LEFT JOIN datosenvios AS de ON(r.datosenvios_id=de.id) 
LEFT JOIN servicio AS s ON(r.servicio_id=s.id) 
LEFT JOIN estado AS e ON(r.estado_id=e.id) 
LEFT JOIN sender AS sen ON(r.sender_id=sen.id) 
LEFT JOIN comprador AS com ON(r.comprador_id=com.id) 
LEFT JOIN gestioncel AS gc ON(r.gestioncel_id=gc.id) 
LEFT JOIN tracker AS t ON(r.id=t.retiro_id)
LEFT JOIN fabricante AS fab ON(gc.fabricante_id=fab.id)
LEFT JOIN modelo AS mo ON(gc.modelo_id=mo.id)
WHERE ";

if($modelo=='SELECCIONAR'){
    $modelo='';
}

if ($desde != "") {
    $bandera ? $sql = $sql . "AND date_format(gc.fecha_base, '%Y-%m-%d') BETWEEN '" . fecha($desde) . "' AND '" . fecha($hasta) . "'  " : $sql = $sql . "date_format(gc.fecha_base, '%Y-%m-%d') BETWEEN '" . fecha($desde) . "' AND '" . fecha($hasta) . "' ";
    $bandera = true;
}

if ($codSuc != "") {
    $bandera ? $sql = $sql . "AND upper(gc.sucursal) = upper('" . $codSuc . "') " : $sql = $sql . "upper(gc.sucursal) = upper('" . $codSuc . "') ";
    $bandera = true;
}
if ($ani != "") {
    $bandera ? $sql = $sql . "AND upper(gc.ani) = upper('" . $ani . "') " : $sql = $sql . "upper(gc.ani) = upper('" . $ani . "') ";
    $bandera = true;
}
if ($nroserie != "") {
    $bandera ? $sql = $sql . "AND upper(gc.nroserie) = upper('" . $nroserie . "') " : $sql = $sql . "upper(gc.nroserie) = upper('" . $nroserie . "') ";
    $bandera = true;
}
if ($nomyape != "") {
    $bandera ? $sql = $sql . "AND upper(gc.nomyape) = upper('" . $nomyape . "') " : $sql = $sql . "upper(gc.nomyape) = upper('" . $nomyape . "') ";
    $bandera = true;
}
if ($nrosst != "") {
    $bandera ? $sql = $sql . "AND upper(gc.nrosst) = upper('" . $nrosst . "') " : $sql = $sql . "upper(gc.nrosst) = upper('" . $nrosst . "') ";
    $bandera = true;
}
if ($fabricante != "") {
    $bandera ? $sql = $sql . "AND upper(gc.fabricante_id) = upper('" . $fabricante . "') " : $sql = $sql . "upper(gc.fabricante_id) = upper('" . $fabricante . "') ";
    $bandera = true;
}
if ($modelo != "") {
    $bandera ? $sql = $sql . "AND upper(gc.modelo_id) = upper('" . $modelo . "') " : $sql = $sql . "upper(gc.modelo_id) = upper('" . $modelo . "') ";
    $bandera = true;
}
if ($estadointervencion != "") {
    $bandera ? $sql = $sql . "AND upper(gc.estadointervencion) = upper('" . $estadointervencion . "') " : $sql = $sql . "upper(gc.estadointervencion) = upper('" . $estadointervencion . "') ";
    $bandera = true;
}
if ($nroimei != "") {
    $bandera ? $sql = $sql . "AND upper(gc.nroimei) = upper('" . $nroimei . "') " : $sql = $sql . "upper(gc.nroimei) = upper('" . $nroimei . "') ";
    $bandera = true;
}
if ($origendelequipo != "") {
    $bandera ? $sql = $sql . "AND upper(gc.origendelequipo) = upper('" . $origendelequipo . "') " : $sql = $sql . "upper(gc.origendelequipo) = upper('" . $origendelequipo . "') ";
    $bandera = true;
}

if ($sva != "") {
    $bandera ? $sql = $sql . "AND upper(gc.sva) = upper('" . $sva . "') " : $sql = $sql . "upper(gc.sva) = upper('" . $sva . "') ";
    $bandera = true;
}
if ($rotura != "") {
    $bandera ? $sql = $sql . "AND upper(gc.rotura) = upper('" . $rotura . "') " : $sql = $sql . "upper(gc.rotura) = upper('" . $rotura . "') ";
    $bandera = true;
}
if ($tipocliente != "") {
    $bandera ? $sql = $sql . "AND upper(gc.tipocliente) = upper('" . $tipocliente . "') " : $sql = $sql . "upper(gc.tipocliente) = upper('" . $tipocliente . "') ";
    $bandera = true;
}
if ($tiposervicio != "") {
    $bandera ? $sql = $sql . "AND upper(gc.tiposervicio) = upper('" . $tiposervicio . "') " : $sql = $sql . "upper(gc.tiposervicio) = upper('" . $tiposervicio . "') ";
    $bandera = true;
}
if ($claimassurant != "") {
    $bandera ? $sql = $sql . "AND upper(gc.claimassurant) = upper('" . $claimassurant . "') " : $sql = $sql . "upper(gc.claimassurant) = upper('" . $claimassurant . "') ";
    $bandera = true;
}
if ($certificadoreparador != "") {
    $bandera ? $sql = $sql . "AND upper(gc.certificadoreparador) = upper('" . $certificadoreparador . "') " : $sql = $sql . "upper(gc.certificadoreparador) = upper('" . $certificadoreparador . "') ";
    $bandera = true;
}
if ($placaswap != "") {
    $bandera ? $sql = $sql . "AND upper(gc.placaswap) = upper('" . $placaswap . "') " : $sql = $sql . "upper(gc.placaswap) = upper('" . $placaswap . "') ";
    $bandera = true;
}
if ($estado != "") {
    $bandera ? $sql = $sql . "AND upper(gc.estado_id) = upper('" . $estado . "') " : $sql = $sql . "upper(gc.estado_id) = upper('" . $estado . "') ";
    $bandera = true;
}
if(!$bandera){
    die("DEBE SELECCIONAR UN FILTRO.");
}

$result = mysql_query($sql)or die(mysql_error());

echo '<table cellpadding="0" cellspacing="0" border="1">';
echo '<tr>';
    echo '<td>GUIA</td>';
    echo '<td>ESTADO FINAL</td>';
    echo '<td>FABRICANTE</td>';
    echo '<td>MODELO</td>';
    echo '<td>TRACKER</td>';
    echo '<td>USUARIO</td>';
    echo '<td>ANI</td>';
    echo '<td>NRO SERIE</td>';
    echo '<td>NOMBRE Y APELLIDO</td>';
    echo '<td>NRO SST</td>';
    echo '<td>ACEPTA CARGOS</td>';
    echo '<td>NIVEL REPARACION</td>';
    echo '<td>MULETO</td>';
    echo '<td>IMEI MULETO</td>';
    echo '<td>FECHA ACTIVACION</td>';
    echo '<td>FECHA FABRICACION</td>';
    echo '<td>ORIGEN DEL EQUIPO</td>';
    echo '<td>SVA</td>';
    echo '<td>FALLA</td>';
    echo '<td>ROTURA</td>';
    echo '<td>COMPLETITUD</td>';
    echo '<td>ESTADO INTERVENCION</td>';
    echo '<td>CERTIFICADO</td>';
    echo '<td>PLACA SWAP</td>';
    echo '<td>NRO IMEI</td>';
    echo '<td>TIPO CLIENTE</td>';
    echo '<td>TIPO SERVICIO</td>';
    echo '<td>CLAIM ASSURANT</td>';
    echo '<td>PRECINTO</td>';
    echo '<td>TRAYECTO</td>';
    echo '<td>VALOR DECLARADO</td>';
    echo '<td>FECHA BASE</td>';
    echo '<td>ESTADO ID</td>';
    echo '<td>SEGUIMIENTO</td>';
    echo '<td>FECHA HORA</td>';
    echo '<td>FECHA HORA SISTEMA</td>';
    echo '<td>NRO PLANILLA</td>';
    echo '<td>FECHA PLANILLA</td>';
echo '</tr>';
while($row = mysql_fetch_array($result)){
    echo '<tr>';
        echo '<td>'.$row['guia'].'</td>';
        echo '<td>'.$row['estadoFinal'].'</td>';
        echo '<td>'.$row['descricion'].'</td>';
        echo '<td>'.$row['descripcion'].'</td>';
        echo '<td>'.$row['tracker'].'</td>';
        echo '<td>'.$row['usuario'].'</td>';
        echo '<td>'.$row['ani'].'</td>';
        echo '<td>'.$row['nroserie'].'</td>';
        echo '<td>'.$row['nomyape'].'</td>';
        echo '<td>'.$row['nrosst'].'</td>';
        echo '<td>'.$row['aceptacargos'].'</td>';
        echo '<td>'.$row['nivelderep'].'</td>';
        echo '<td>'.$row['muleto'].'</td>';
        echo '<td>'.$row['imeimuleto'].'</td>';
        echo '<td>'.$row['fechaactivacion'].'</td>';
        echo '<td>'.$row['fechafabricacion'].'</td>';
        echo '<td>'.$row['origendelequipo'].'</td>';
        echo '<td>'.$row['sva'].'</td>';
        echo '<td>';
        foreach(unserialize($row['falla']) as $clave=>$valor){
            echo $valor.'-';
        }
        echo '</td>';
        echo '<td>'.$row['rotura'].'</td>';
        echo '<td>';
        foreach(unserialize($row['completitud']) as $clave2=>$valor2){
            echo $valor2.'-';
        }
        echo '</td>';
        echo '<td>'.$row['estadointervencion'].'</td>';
        echo '<td>'.$row['certificadoreparador'].'</td>';
        echo '<td>'.$row['placaswap'].'</td>';
        echo '<td>'.$row['nroimei'].'</td>';
        echo '<td>'.$row['tipocliente'].'</td>';
        echo '<td>'.$row['tiposervicio'].'</td>';
        echo '<td>'.$row['claimassurant'].'</td>';
        echo '<td>'.$row['precinto'].'</td>';
        echo '<td>'.$row['trayecto'].'</td>';
        echo '<td>'.$row['valordeclaradocel'].'</td>';
        echo '<td>'.$row['fecha_base'].'</td>';
        echo '<td>'.$row['estado_id'].'</td>';
        echo '<td>'.$row['estadoTracker'].'</td>';
        echo '<td>'.$row['receptor_fecha_hora'].'</td>';
        echo '<td>'.$row['timestamp_modificacion'].'</td>';
        echo '<td>'.$row['nroPlanilla'].'</td>';
        echo '<td>'.$row['fecha_planilla'].'</td>';
    echo '</tr>';
}
echo '</table>';
?>