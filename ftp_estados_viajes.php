<?php
error_reporting(E_ALL);
$host = '181.30.37.12';
$port = 21;
$user = 'GalanderViajes';
$pass = 'V14jG4lUn1r';
        
            // Add all files inside the directory
            
                $source_file = 'EST_GALAN_180206190000.txt';
                $destination_file = 'web/backup_estados_viajes/';


                echo 'ORIGEN: '.$source_file.'</br>';
                echo 'DESTINO: '.$destination_file.'</br>';
                echo '=============================================================</br>';

                //conectarse al host
                $conn = @ftp_connect($host, $port);

                //Comprobar que la conexión ha tenido éxito
                if (!$conn) {
                echo 'Error al tratar de conectar con ' . $host . "\n";
                exit();
                }
                echo 'Conectado con ' . $host . "\n";

                //Iniciamos sesión
                $login = @ftp_login($conn, $user, $pass);
                if (!$login) {
                echo 'Error al intentar acceder con el usuario ' . $user;
                ftp_quit($conn);
                exit();
                }
                //Si queremos obtener una lista con los archivos del servidor
                $files = ftp_nlist($conn, '.');
                foreach ($files as $file) {
                echo  $file . "\n";
                }

                //if (file_exists($verificacion)&&file_exists($enlace))

                // intenta descargar un $remote_file y guardarlo en $handle
                // abrir un archivo para escribir

                //$página_inicio = file_get_contents($source_file);
                //echo $página_inicio;

                 //$handle = fopen($local_file, 'w');
                //if (ftp_fget($conn, $local_file, $remote_file, FTP_ASCII, 0)) {
               /* if (ftp_get($conn, $destination_file, $source_file, FTP_BINARY)){
                 echo "Se ha escrito satisfactoriamente sobre $destination_file\n";
                } else {
                 echo   "Ha habido un problema durante la descarga de  $source_file en $destination_file\n";
                }*/

                //echo $local_file;
                //echo $remote_file;
                       
                //Cerramos la conexion
                ftp_close($conn);
            
        

?>
