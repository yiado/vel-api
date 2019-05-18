<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td height="30">
    	<div align="center"><font size="12"><b><?=$mtn_node_plan_name; ?></b></font></div>
    </td>    
 </tr>
</table>

<br>

<br>

<table border="0" cellpadding="2" cellspacing="0" width="100%" style="border: 1px solid #cccccc">
<tr>
    <td width="100%">

		<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                <td width="15%"><strong>Creado Por</strong> </td>
                <td width="35%">: <?=$create_by; ?></td>
                <td width="15%"><strong>Folio</strong> </td>
                <td width="35%">: <?=$mtn_node_plan_id; ?></td>
              </tr>
		<tr>
		    <td width="15%"><strong>Fecha Inicial</strong> </td>
		    <td width="35%">: <?=mdate('%d/%m/%Y', mysql_to_unix($mtn_node_plan_date_begin)); ?></td>    
                    <td width="15%"><strong>Fecha Final</strong> </td>
		    <td width="35%">: <?=$mtn_node_plan_date_finish; ?></td> 
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
  <?php foreach ($mtn_node_plan_tasks as $task_list_reg):?>
<tr>
  <td><?php echo $task_list_reg['MtnNodeTask']['mtn_node_task_name'];?></td>
  <td><?php echo $task_list_reg['mtn_node_plan_task_time_job']?></td>  
  <td align="right"><?php echo number_format($task_list_reg['mtn_node_plan_task_price'], $currency_format->currency_number_of_decimal, $currency_format->currency_decimal_character, $currency_format->currency_thousands_character);?></td>
  <td><?php echo $task_list_reg['mtn_node_plan_task_comment'];?></td>
  </tr>
<?php endforeach;?>
</table>

<br><br>

<table border="0" cellpadding="2" cellspacing="0" width="100%" style="border: 1px solid #cccccc">
<tr>
    <td>

		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		    <td><strong>Descripción:</strong> </td>
		</tr>
		<tr>
		    <td height="60"><?=$mtn_node_plan_description; ?></td>
		</tr>
		</table>

	</td>    
  </tr>
</table>

<br>

<br>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td width="15%"></td>
    <td width="35%"></td>   
    <td width="15%"><strong>Total Plan</strong> </td>
    <td width="35%" colpsan="3">: <?=$mtn_node_plan_total; ?></td>
  </tr>
</table>
