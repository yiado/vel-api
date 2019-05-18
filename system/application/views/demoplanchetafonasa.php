<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" height="30">
    	<font size="15"><b>PLANCHETA INVENTARIO</b></font>
    </td>    
 </tr>
<tr>
    <td align="center">
    	<img src="temp/barcode_node.jpg" wwidth="370">
    </td>    
 </tr>
</table>

<br>

<table border="0" cellpadding="2" cellspacing="0">
<tr>
    <td width="30%"><strong>Fecha control</strong> </td>
    <td width="70%">:</td>    
  </tr>
</table>

<br>

<table border="0" cellpadding="2" cellspacing="0" style="border: 1px solid #cccccc">
<tr>
    <td>

		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		    <td><strong>Nombre recinto </strong></td>
		    <td>: <?=$nombre_recinto; ?></td>
		</tr>
		</table>

	</td>    
  </tr>
</table>

<br>

<table border="0" cellpadding="3" cellspacing="0">
<tr>
    <td width="30%"><strong>Responsable Asignado</strong> </td>
    <td width="70%">:</td>    
  </tr>
</table>

<br>

<table width="100%" border="1" cellpadding="2" cellspacing="0">
  <tr>
    <td bgcolor="#cccccc"><div align="center"><strong>Nombre</strong></div></td>
    <td bgcolor="#cccccc"><div align="center"><strong>Nro. Serie </strong></div></td>
    <td bgcolor="#cccccc"><div align="center"><strong>&Iacute;tem</strong></div></td>
    <td bgcolor="#cccccc"><div align="center"><strong>Marca</strong></div></td>
    <td bgcolor="#cccccc"><div align="center"><strong>Tipolog&iacute;a</strong></div></td>
  </tr>
  <?php foreach ($asset_list as $asset_af):?>
<tr>
  <td><?php echo $asset_af['asset_name'];?></td>
  <td><?php echo $asset_af['asset_num_serie_intern'];?></td>
  <td><?php echo $asset_af['asset_type_name'];?></td>
  <td><?php echo $asset_af['brand_name'];?></td>
  <td><?php echo $asset_af['tipologia'];?></td>
</tr>
<?php endforeach;?>
</table>

<br></br>

<table border="0" cellpadding="2" cellspacing="0" style="border: 1px solid #cccccc">
<tr>
    <td>

		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		    <td><strong>Observaci&oacute;n:</strong> </td>
		</tr>
		<tr>
		    <td height="60"></td>
		</tr>
		</table>

	</td>    
  </tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td>Nota : Ser&aacute; obligaci&oacute;n del funcionario responsable, comunicar al responsable de inventario, de todo pr&eacute;stamo, 
    	traslado o deterioro de los bienes inventariables que est&aacute;n a su cargo.
    </td>    
</tr>
</table>

<br><br><br><br>

<table border="0" cellpadding="3" cellspacing="0">
  <tr>
    <td width="30%" style="border-bottom: 1px solid black"></td>
    <td width="40%"></td>
    <td width="30%" style="border-bottom: 1px solid black"></td>    
  </tr>
  <tr>
    <td align="center">Firma Responsable Recinto</td>
    <td width="40%"></td>
    <td align="center">Responsable de Inventario</td>    
  </tr>
</table>


