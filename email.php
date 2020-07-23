<?php
require './sendgrid-php/vendor/autoload.php';

    $from = new SendGrid\Email("Gracias por contactarnos", "deivid921015@gmail.com");
    $subject = "Contacto por: deivid";
    $to = new SendGrid\Email("Gracias por contactarnos", "deivid921015@gmail.com");
    $content = new SendGrid\Content("text/html", "
    <h2>Datos de contacto: </h2>");
    $mail = new SendGrid\Mail($from, $subject, $to, $content);
    $apiKey = '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';
    $sg = new \SendGrid($apiKey);
    $response = $sg->client->mail()->send()->post($mail);
    $resp = $response->statusCode();
    if($resp==202){
        echo "ok";
    }else{
        setLog("Error al enviar el email ".$resp);
        echo "error";
    }
?>
