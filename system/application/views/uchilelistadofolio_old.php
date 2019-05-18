<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="left" >
            <font  ><b>UNIVERSIDAD DE CHILE</b></font>
        </td>    
          <td align="right" >
            <font><b>R-DSI-DEAC-21</b></font>
        </td>  
    </tr>
    <tr>
        <td align="left"  >
            <font  ><b>VICERRECTORÍA DE ASUNTOS ECONÓMICOS Y GESTIÓN INSTITUCIONAL</b></font>
        </td>  
                 <td align="right" >
            <font><b></b></font>
        </td>  
    </tr>
    <tr>
        <td align="left"  >
            <font  ><b>DIRECCIÓN DE SERVICIOS E INFRAESTRUCTURA</b></font>
        </td>  
                 <td align="right" >
            <font><b></b></font>
        </td>  
    </tr>
    
    <br>
    <br>
    <tr>
        <td align="center"  >
            <font size="10"><b>COMPROBANTE DE ALTA FÍSICA</b></font>
        </td>    
    </tr>

</table>

<br>

 

<br>

<table border="0" cellpadding="2" cellspacing="0" style="border: 1px solid #cccccc">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="20%"><strong>Folio</strong> </td>
                    <td width="80%">: <?=$folio; ?> </td>    
                </tr>
                <tr>
                    <td><strong>Fecha Carga</strong> </td>
                    <td>: <?=$fecha_carga; ?></td>    
                </tr>
                <tr>
                    <td><strong>Usuario Cargador</strong> </td>
                    <td>: <?=$usuario_cargador; ?> </td>    
                </tr>
                <tr>
                    <td><strong>Organismo / Servicio</strong> </td>
                    <td>: <?=$comentario; ?> </td>    
                </tr>
            </table>
        </td>    
    </tr>
</table>


<br></br>
<br></br>

<table width="100%" border="1" cellpadding="2" cellspacing="0">
  <tr>
    <td rowspan="2" width ="9%"><div align="center"><strong>C&oacute;digo inventario </strong></div></td>
    <td colspan="3" bgcolor="#cccccc" width ="45%"><div align="center"><strong>Bien de Registro</strong></div></td>
    <td rowspan="2" width ="10%"><div align="center"><strong>Nro. Serie </strong></div></td>
    <td rowspan="2" width ="8%"><div align="center" ><strong>Nro. Factura </strong></div></td>
    <td rowspan="2" width ="15%"><div align="center" ><strong>Ubicaci&oacute;n </strong></div></td>
    <td rowspan="2" width ="10%"><div align="center" ><strong>Encargado de Sala</strong></div></td>
  </tr>
  

<tr>
  <td width ="12%"><div align="center"><strong>Nombre</strong></div></td>
<!--  <td><div align="center"><strong>Tipo</strong></div></td>-->
  <td width ="9%"><div align="center"><strong>Marca</strong></div></td>
  <td width ="9%"><div align="center"><strong>Modelo</strong></div></td>
  <td width ="15%"><div align="center"><strong>Descripci&oacute;n</strong></div></td>
  </tr>
  <?php foreach ($br_list as $asset_br):?>
<tr>
  <td width ="9%" align="center"><?php echo $asset_br['asset_num_serie_intern'];?></td>
  <td width ="12%"><?php echo $asset_br['asset_name'];?></td>
<!--  <td><?php //echo $asset_br['asset_type_name'];?></td>-->
  <td width ="9%"><?php echo $asset_br['brand_name'];?></td>
  <td width ="9%"><?php echo $asset_br['modelo'];?></td>
  <td width ="15%"><?php echo $asset_br['asset_description'];?></td>
  <td width ="10%"><?php echo $asset_br['asset_num_serie'];?></td>
  <td width ="8%"><?php echo $asset_br['asset_num_factura'];?></td>
  <td width ="15%"><?php echo $asset_br['asset_path_3_niveles'];?></td>
  <td width ="10%"><?php echo $asset_br['encargado_sala'];?></td>
</tr>
<?php endforeach;?>
</table>








