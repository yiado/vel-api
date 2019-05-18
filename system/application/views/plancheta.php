<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" height="30">
    	<font size="15"><b>PLANCHETA INVENTARIO</b></font>
    </td>    
 </tr>
<tr>
    <td align="center">
    	<img src="<?=base_url(); ?>temp/barcode_plancheta.jpg" wwidth="370">
    </td>    
 </tr>
</table>

<br>

<table border="0" cellpadding="2" cellspacing="0">
<tr>
    <td width="30%"><strong>Fecha control</strong> </td>
    <td width="70%">:<?=date('d/m/Y'); ?></td>    
  </tr>
</table>

<br>

<table border="0" cellpadding="2" cellspacing="0" style="border: 1px solid #cccccc">
<tr>
    <td>
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		    <td width="30%"><strong>Organismo</strong> </td>
		    <td width="70%">: <?=$organismo; ?></td>    
		  </tr>
		<tr>
		    <td><strong>Departamento</strong> </td>
		    <td>: <?=$departamento; ?></td>    
		  </tr>
		<tr>
		    <td><strong>Unidad</strong> </td>
		    <td>: <?=$unidad; ?></td>    
		  </tr>
		</table>
	</td>    
  </tr>
</table>


<br></br>

<table border="0" cellpadding="2" cellspacing="0" style="border: 1px solid #cccccc">
<tr>
    <td>

		<table border="0" cellpadding="0" cellspacing="0">
<!--		<tr>
		    <td width="30%"><strong>Direcci&oacute;n</strong> </td>
		    <td width="70%">: <?=@$direccion; ?></td>    
		</tr>
		<tr>
		    <td><strong>Edificio</strong> </td>
		    <td>: <?=@$edificio; ?></td>    
		</tr>-->
		<tr>
		    <td width="30%"><strong>C&oacute;digo Recinto </strong></td>
		    <td width="70%">: <?=$codigo_recinto; ?></td>    
		</tr>
		<tr>
		  <td><strong>C&oacute;digo Subrecinto </strong></td>
		  <td>: <?=$codigo_subrecinto; ?></td>
		</tr>
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
    <td width="70%">: <?=$usuario; ?></td>    
  </tr>
</table>


<br></br>
<table width="100%" border="1" cellpadding="2" cellspacing="0">
  <tr>
    <td rowspan="2"><div align="center"><strong>C&oacute;digo inventario </strong></div></td>
    <td colspan="4" bgcolor="#cccccc"><div align="center"><strong>Bien de Registro</strong></div></td>
    <td rowspan="2"><div align="center"><strong>Nro. Serie </strong></div></td>
  </tr>
  

<tr>
  <td><div align="center"><strong>Nombre</strong></div></td>
  <td><div align="center"><strong>Tipo</strong></div></td>
  <td><div align="center"><strong>Marca</strong></div></td>
  <td><div align="center"><strong>Descripci&oacute;n</strong></div></td>
  </tr>
  <?php foreach ($br_list as $asset_br):?>
<tr>
  <td><?php echo $asset_br['asset_num_serie_intern'];?></td>
  <td><?php echo $asset_br['asset_name'];?></td>
  <td><?php echo $asset_br['asset_type_name'];?></td>
  <td><?php echo $asset_br['brand_name'];?></td>
  <td><?php echo $asset_br['asset_description'];?></td>
  <td><?php echo $asset_br['asset_num_serie'];?></td>
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
    <td>Nota : Ser&aacute; obligaci&oacute;n del funcionario responsable, comunicar al conservador de inventario, de todo pr&eacute;stamo, 
    	traslado o deterioro de los bienes inventariables que est&aacute;n a su cargo (Decreto No 5857 art. 2)
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
    <td align="center"><?=$usuario; ?><br>Firma Responsable</td>
    <td width="40%"></td>
    <td align="center"><?=@$conservador; ?><br>Conservador de Inventarios</td>    
  </tr>
</table>


