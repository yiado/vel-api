<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td height="30">
    	<div align="center"><font size="12"><b>ORDEN DE TRABAJO</b></font></div>
    </td>    
 </tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td width="15%"><strong>Creado Por</strong> </td>
    <td width="35%">: <?=$create_by; ?></td>
    <td width="15%"><strong>Folio</strong> </td>
    <td width="35%">: <?=$mtn_node_work_order_folio; ?></td>
  </tr>
<tr>
    <td width="15%"><strong>Nodo</strong> </td>
    <td width="85%" colpsan="3">: <?=$node->getPath(); ?></td>
  </tr>
</table>


<br>

<table border="0" cellpadding="2" cellspacing="0" width="100%" style="border: 1px solid #cccccc">
<tr>
    <td width="100%">

		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		    <td width="15%"><strong>Fecha Inicial</strong> </td>
		    <td width="35%">: <?=mdate('%d/%m/%Y', mysql_to_unix($mtn_node_work_order_date_begin)); ?></td>    
            <td width="15%"><strong>Hora Inicio</strong> </td>
		    <td width="35%">: <?=$mtn_node_work_order_time_begin; ?></td> 
		</tr>
		<tr>
		    <td width="15%"><strong>Fecha Final</strong> </td>
		    <td width="35%">: <?=mdate('%d/%m/%Y', mysql_to_unix($mtn_node_work_order_date_finish)); ?></td>  
		    <td width="15%"><strong>Hora Final</strong></td>
		    <td width="35%">: <?=$mtn_node_work_order_time_finish; ?></td>
		</tr>
		<tr>
		    <td width="15%"><strong>Proveedor</strong> </td>
		    <td width="35%">: <?=$provider_name; ?></td>    
		    <td width="15%"><strong>Fecha Creacion</strong></td>
		    <td width="35%">: <?=mdate('%d/%m/%Y %H:%i', mysql_to_unix($mtn_node_work_order_created)); ?></td>  
		</tr>
		<tr>
			<td width="15%"><strong>Tipo de O.T.</strong> </td>
		    <td width="35%">: <?=$mtn_node_work_order_type_name; ?></td>    
		    <td width="15%"><strong>Estado</strong></td>
		    <td width="35%">: <?=$mtn_node_status_name; ?></td>
		</tr>
                <tr>
			<td width="15%"><strong>Solicitante</strong> </td>
		    <td width="35%">: <?=$applicant_order; ?></td>    
		    <td width="15%"><strong>Responsable</strong></td>
		    <td width="35%">: <?=$responsible_order; ?></td>
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
  <td width="10%"><div align="center"><strong>Tiempo</strong></div></td>
  <td width="10%"><div align="center"><strong>Precio</strong></div></td>
  <td width="65%"><div align="center"><strong>Comentario</strong></div></td>
  </tr>
  <?php foreach ($mtn_node_work_order_tasks as $task_list_reg):?>
<tr>
  <td><?php echo $task_list_reg['MtnNodeTask']['mtn_node_task_name'];?></td>
  <td><?php echo $task_list_reg['mtn_node_work_order_task_time_job']?></td>  
  <td align="right"><?php echo number_format($task_list_reg['mtn_node_work_order_task_price'], $currency_format->currency_number_of_decimal, $currency_format->currency_decimal_character, $currency_format->currency_thousands_character);?></td>
  <td><?php echo $task_list_reg['mtn_node_work_order_task_comment'];?></td>
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
		    <td height="60"><?=$mtn_node_work_order_comment; ?></td>
		</tr>
		</table>

	</td>    
  </tr>
</table>

<br>

<br>
