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
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Estado del Servicio: <?= $serviceStatus->rdi_status_name; ?></li> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Fecha: <?= $fecha; ?></li> 
            <li style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;Margin-bottom:15px;color:#555555;">Requerimiento: <?= $rdi->rdi_description; ?></li> 
        </ul></td> 
</tr>
<tr style="border-collapse:collapse;"> 
    <td align="left" style="padding:0;Margin:0;padding-left:5px;padding-top:10px;padding-bottom:10px;">
        <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-align: center;font-size:15px;font-family:helvetica, \'helvetica neue\', arial, verdana, sans-serif;line-height:23px;color:#555555;">
            <strong>¿Está conforme con el resultado del requerimiento?</strong>
        </p>
    </td> 
</tr>
<tr style="border-collapse:collapse;"> 
    <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-left:40px;padding-right:40px;"> 
        <table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left;max-width: 170px;"> 
            <tr style="border-collapse:collapse;"> 
                <td class="es-m-p20b" width="250" align="left" style="padding:0;Margin:0;"> 
                    <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                        <tr style="border-collapse:collapse;"> 
                            <td style="padding:0;Margin:0;font-size:0px;" align="center">
                                <a href="<?= base_url() . "index.php/request/evaluation/rdi/{$rdi->rdi_token}/2/{$rdi->rdi_id}"; ?>">
                                <img class="adapt-img" src="<?= base_url(); ?>style/emails/no_conforme.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;" width="80">
                                </a>
                            </td> 
                        </tr> 
                    </table></td> 
            </tr> 
        </table> 
        <table class="es-right" cellspacing="0" cellpadding="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right;max-width: 170px;"> 
            <tr style="border-collapse:collapse;"> 
                <td width="250" align="left" style="padding:0;Margin:0;"> 
                    <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                        <tr style="border-collapse:collapse;"> 
                            <td style="padding:0;Margin:0;font-size:0px;" align="center">
                                <a href="<?= base_url() . "index.php/request/evaluation/rdi/{$rdi->rdi_token}/3/{$rdi->rdi_id}"; ?>">
                                <img class="adapt-img" src="<?= base_url(); ?>style/emails/conforme.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;" width="80">
                                </a>
                            </td> 
                        </tr> 
                    </table></td> 
            </tr> 
        </table> 
    </td> 
</tr>

<?php echo $this->load->view('mails/footer', array(), true); ?>