<?php
$start = microtime(true);
// variables
$ftp_server = "181.30.37.12";
$ftp_user_name = "GalanderTest";
$ftp_user_pass = "Galander2017!";

//ABRE EL DIRECTORIO Y MUEVE LOS ARCHIVOS DE "WEB/UNIR" A "WEB/PROCESADOS"
if ($handle = opendir('web/unir'))
{
    // Add all files inside the directory
    while (false !== ($entry = readdir($handle)))
    {
        if ($entry != "." && $entry != ".." && !is_dir('web/unir/' . $entry))
        {
            rename ('web/unir/' .$entry,"web/procesados/".$entry);
        }
    }
    closedir($handle);
}
//EN LA CARPETA "WEB/PROCESADOS" COMPRIMO LOS ARCHIVOS
$zip = new ZipArchive;
$fecha = date('Ymd');
$hora = date('His');
$archivo = 'archivo'.$fecha.$hora.'.zip';
if ($zip->open($archivo, ZipArchive::OVERWRITE) === TRUE)
{
    if ($handle = opendir('web/procesados'))
    {
        // Add all files inside the directory
        while (false !== ($entry = readdir($handle)))
        {
            if ($entry != "." && $entry != ".." && !is_dir('web/procesados/' . $entry))
            {
                $zip->addFile('web/procesados/' . $entry, $entry);
                $data[] = $entry;
            }
        }
        closedir($handle);
    }
    $zip->close();
}

foreach ($data as $archivos){
    rename ('web/procesados/'.$archivos,"web/backunir/".$archivos);
}

$destination_file = $archivo;

$source_file = $archivo;

// conexión
$conn_id = ftp_connect($ftp_server);

// logeo
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// conexión
if ((!$conn_id) || (!$login_result)) {
    $accion = "Conexión al FTP con errores! - ";
    $accion .= "Intentando conectar a $ftp_server for user $ftp_user_name";
    exit;
} else {
    $accion =  "Conectado a $ftp_server, for user $ftp_user_name";
}
/*
 * LOG DE ACCIONES
 */

$fecha = (string) date("Y-m-d H:i:s");
$totalTimexDestino = (string) ((microtime(true) - $start) / 60); // Tiempo en minutos.
$totalTime = number_format($totalTimexDestino, 2, '.', '');
$reporte = $fecha . ' Time: ' . $totalTime . " min. - ".$accion."\r\n----------\r\n" ;
file_put_contents("log_runTime.txt", $reporte, FILE_APPEND);
/*
 *
 */

// archivo a copiar/subir
$upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

// estado de subida/copiado
if (!$upload) {
    echo "Error al subir el archivo!";
} else {
    echo "Archivo $source_file se ha subido exitosamente a $ftp_server en $destination_file";
}

// cerramos
ftp_close($conn_id);
?>
