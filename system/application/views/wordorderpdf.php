<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td height="30">
    	<div align="center"><font size="10"><b>ORDEN DE TRABAJO</b></font></div>
    </td>    
 </tr>
</table>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td width="15%"><strong>Creado Por</strong> </td>
    <td width="35%">: <?=$createdBy['user_name']; ?></td>
    <td width="15%"><strong>Folio</strong> </td>
    <td width="35%">: <?=$wo['mtn_work_order_folio']; ?></td>
  </tr>
</table>

<br>

<table border="0" cellpadding="2" cellspacing="0" width="100%" style="border: 1px solid #cccccc">
<tr>
    <td width="100%">

		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		    <td width="15%"><strong>Ruta</strong> </td>
		    <td width="85%" colspan="3">: <?=$wo['Asset']['asset_path']; ?></td>    
		</tr>
		<tr>
		    <td width="15%"><strong>Activo</strong> </td>
		    <td width="35%">: <?=$wo['Asset']['asset_name']; ?></td>    
		    <td width="15%"><strong>Numero Interno</strong></td>
		    <td width="35%">: <?=$wo['Asset']['asset_num_serie_intern']; ?></td>
		</tr>
		<tr>
		    <td width="15%"><strong>Proveedor</strong> </td>
		    <td width="35%">: <?=$wo['Provider']['provider_name']; ?></td>    
		    <td width="15%"><strong>Fecha</strong></td>
		    <td width="35%">: <?=$wo['mtn_work_order_date']; ?></td>
		</tr>
		<tr>
			<td width="15%"><strong>Tipo de O.T.</strong> </td>
		    <td width="35%">: <?=$wo['MtnConfigState']['MtnWorkOrderType']['mtn_work_order_type_name']; ?></td>    
		    <td width="15%"><strong>Estado</strong></td>
		    <td width="35%">: <?=$wo['MtnConfigState']['MtnSystemWorkOrderStatus']['mtn_system_work_order_status_name']; ?></td>
		</tr>
		</table>

	</td>    
  </tr>
</table>

<br><br>

<table width="100%" border="1" cellpadding="2" cellspacing="0">
  <tr>
    <td colspan="5" bgcolor="#cccccc"><div align="center"><strong>Tarea</strong></div></td>
  </tr>
  

<tr>
  <td width="15%"><div align="center"><strong>Nombre Tarea</strong></div></td>
  <td width="10%"><div align="center"><strong>Precio</strong></div></td>
  <td width="10%"><div align="center"><strong>Insumo</strong></div></td>
  <td width="65%"><div align="center"><strong>Comentario</strong></div></td>
  </tr>
  <?php foreach ($task_list as $task_list_reg):?>
<tr>
  <td><?php echo $task_list_reg['MtnTask']['mtn_task_name'];?></td>
  <td align="right"><?php echo number_format($task_list_reg['mtn_work_order_task_price'], $currency_format->currency_number_of_decimal, $currency_format->currency_decimal_character, $currency_format->currency_thousands_character);?></td>
  <td align="right"><?php echo number_format($task_list_reg['mtn_costos_component_in_task'], $currency_format->currency_number_of_decimal, $currency_format->currency_decimal_character, $currency_format->currency_thousands_character);?></td>
  <td><?php echo $task_list_reg['mtn_work_order_task_comment'];?></td>
  </tr>
<?php endforeach;?>
</table>

<br><br>

<table width="100%" border="1" cellpadding="2" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#cccccc"><div align="center"><strong>Otros Costos</strong></div></td>
  </tr>
	<tr>
	  <td width="25%"><div align="center"><strong>Nombre Costo</strong></div></td>
	  <td width="10%"><div align="center"><strong>Valor</strong></div></td>
	  <td width="65%"><div align="center"><strong>Comentario</strong></div></td>
  </tr>
  <?php foreach ($other_list as $other_list_reg):?>
<tr>
  <td><?php echo $other_list_reg['MtnOtherCosts']['mtn_other_costs_name'];?></td>
  <td align="right"><?php echo number_format($other_list_reg['mtn_work_order_other_costs_costs'], $currency_format->currency_number_of_decimal, $currency_format->currency_decimal_character, $currency_format->currency_thousands_character);?></td>
  <td><?php echo $other_list_reg['mtn_work_order_other_costs_comment'];?></td>
  </tr>
<?php endforeach;?>
</table>

<br><br>

<table border="0" cellpadding="2" cellspacing="0" width="100%" style="border: 1px solid #cccccc">
<tr>
    <td>

		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		    <td><strong>Comentarios:</strong> </td>
		</tr>
		<tr>
		    <td height="60"><?=$wo['mtn_work_order_comment']; ?></td>
		</tr>
		</table>

	</td>    
  </tr>
</table>

<br>

<table border="0" cellpadding="2" cellspacing="0">
<tr>
    <td width="30%"><strong>RESUMEN TOTALES</strong> </td>
    <td width="70%"></td>    
  </tr>
</table>

<table border="0" cellpadding="2" cellspacing="0" width="100%" style="border: 1px solid #cccccc">
<tr>
    <td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
		    <td align="right"><strong>Total Tareas</strong> &nbsp;&nbsp;: &nbsp;&nbsp;<?=number_format($wo['total_task'], $currency_format->currency_number_of_decimal, $currency_format->currency_decimal_character, $currency_format->currency_thousands_character); ?></td>
		</tr>
		<tr>
		    <td align="right"><strong>Total Otros Costos</strong> &nbsp;&nbsp;: &nbsp;&nbsp;<?=number_format($wo['total_other_costs'], $currency_format->currency_number_of_decimal, $currency_format->currency_decimal_character, $currency_format->currency_thousands_character); ?></td>
		</tr>
		<tr>
		    <td align="right"><strong>Total Orden Trabajo</strong> &nbsp;&nbsp;: &nbsp;&nbsp;<?=number_format($wo['total_work_order'], $currency_format->currency_number_of_decimal, $currency_format->currency_decimal_character, $currency_format->currency_thousands_character); ?></td>
		  </tr>
		</table>
	</td>    
  </tr>
</table>
