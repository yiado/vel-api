<?php echo $this->load->view('mails/header', array(), true); ?>

<tr style="border-collapse:collapse;"> 
    <td align="left" style="padding:0;Margin:0;padding-left:5px;padding-bottom:10px;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;color:#555555;"><strong>UBICACIÓN RECINTO:</strong></p> 
        <ul> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;"><strong></strong><?= implode(' > ', $nodos_ancestros); ?></li> 
        </ul></td> 
</tr> 
<tr style="border-collapse:collapse;"> 
    <td align="left" style="padding:0;Margin:0;padding-left:5px;padding-top:10px;padding-bottom:10px;"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;color:#555555;"><strong>DETALLE:</strong></p>
        <ul> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Tipo de Servicio: <?= $service->ServiceType->service_type_name; ?></li> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Estado del Servicio: <?= $serviceStatus->service_status_name; ?></li> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Nombre de Usuario: <?= $service->User->user_username; ?></li> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Fecha: <?= $fecha; ?></li> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Teléfono: <?= $service->service_phone; ?></li> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Organismo: <?= $service->service_organism; ?></li> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Requerimiento: <?= $service->service_commentary; ?></li> 
        </ul></td> 
</tr> 

<?php echo $this->load->view('mails/footer', array(), true); ?>