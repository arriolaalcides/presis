<?php
require_once ('./base/connHelper.php');
connect();

$sql = "SELECT * FROM tracker WHERE retiro_id='108' ";
$result = mysql_query($sql)or die(mysql_error());
while($row = mysql_fetch_array($result)){
    $array[] = $row;
}

function filasacolumnas($rs=null) {

    /*$rs[0][0] = 'a';
    $rs[0][1] = 'b';
    $rs[0][2] = 'c';
    $rs[1][0] = 'd';
    $rs[1][1] = 'e';
    $rs[1][2] = 'f';
    $rs[1][3] = 'g';
    $rs[2][0] = 'h';
    $rs[2][1] = 'i';*/

    echo "<pre>";
    print_r($rs);

    echo "<hr>";

    foreach ($rs as $key => $reg) {
        $a++;
        echo 'Longitud registro ' . $a . ': ' . count($reg) . '<br>';
        $maxc = count($reg) > $maxc ? count($reg) : $maxc ;
    }

    echo "<hr>";

    echo '<table border=1 cellpadding=10>';
    for ($i=0; $i < $maxc ; $i++) {
        echo '<tr>';
        for ($ii=0; $ii < count($rs) ; $ii++) {
            echo '<td>' . $rs[$ii][$i] . '</td>';
        }
        echo '</tr>';
    }
    echo '<table>';

    return;
}
filasacolumnas($array);
?>