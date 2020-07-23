<?php
require_once ('apiMasLogistica/base/connHelper.php');
connect();

$sql = "SELECT * FROM desarrollomas.galander";
$result = mysql_query($sql)or die(mysql_error());
while($row = mysql_fetch_array($result)){

    $sql1 = "SELECT * FROM desarrollomas.tracker WHERE nroPlanilla='".$row['manifiesto']."' group by retiro_id ";
    $result1 = mysql_query($sql1)or die(mysql_error());

    while($row1 = mysql_fetch_array($result1)){
        echo "MANIFIESTO: ".$row['manifiesto'].' RETIRO: '.$row1['retiro_id'].'</br>';

        $sql2 = "INSERT INTO desarrollomas.tracker(estado_id,retiro_id,user_id,detalles,receptor_fecha_hora,timestamp_modificacion,nroPlanilla)VALUES
        ('".$row['descripcion']."','".$row1['retiro_id']."','2','import','".$row['fecha2']."','".$row['fecha2']."','".$row1['nroPlanilla']."')";
        $result2 = mysql_query($sql2)or die(mysql_error());
        //die($sql2);
    }

}
?>