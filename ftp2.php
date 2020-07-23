<?php
echo 'http://'.$_SERVER['HTTP_HOST'];

die();
$start = microtime(true);

$ftp_server = "181.30.37.12";
$ftp_user_name = "GalanderTest";
$ftp_user_pass = "Galander2017!";

if ($handle = opendir('web/procesados'))
{
    // Add all files inside the directory
    while (false !== ($entry = readdir($handle)))
    {
        if ($entry != "." && $entry != ".." && !is_dir('web/procesados/' . $entry))
        {
            $destination_file = $entry;
            $source_file = 'web/procesados/'.$entry;

            echo 'ORIGEN: '.$source_file.'</br>';
            echo 'DESTINO: '.$destination_file.'</br>';
            echo '=============================================================</br>';
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


            $fecha = (string) date("Y-m-d H:i:s");
            $totalTimexDestino = (string) ((microtime(true) - $start) / 60); // Tiempo en minutos.
            $totalTime = number_format($totalTimexDestino, 2, '.', '');
            $reporte = $fecha . ' Time: ' . $totalTime . " min. - ".$accion."\r\n----------\r\n" ;
            file_put_contents("log_runTime.txt", $reporte, FILE_APPEND);


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
        }
    }
    closedir($handle);
}