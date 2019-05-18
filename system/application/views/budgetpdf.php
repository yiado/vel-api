<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td height="30">
    	<div align="center"><font size="12"><b>PRESUPUESTO</b></font></div>
    </td>    
 </tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td width="15%"><strong>Folio</strong> </td>
    <td width="35%">: <?=str_pad($MtnNodeBudget->mtn_node_budget_id, 10, "0", STR_PAD_LEFT); ?></td>
  </tr>
<tr>
    <td width="15%"><strong>Nodo</strong> </td>
    <td width="85%" colpsan="3">: <?=$node->getPath(); ?></td>
  </tr>
<tr>
    <td width="15%"><strong>Comentario</strong> </td>
    <td width="85%" colpsan="3">: <?=$MtnNodeBudget->mtn_node_budget_description; ?></td>
  </tr>
</table>


<br><br>

<table width="100%" border="1" cellpadding="2" cellspacing="0">
  <tr>
    <td colspan="5" bgcolor="#cccccc"><div align="center"><strong>Tarea</strong></div></td>
  </tr>
  

<tr>
  <td width="65%"><div align="center"><strong>Nombre Tarea</strong></div></td>
  <td width="10%"><div align="center"><strong>Cantidad</strong></div></td>
  <td width="10%"><div align="center"><strong>Precio</strong></div></td>
  <td width="15%"><div align="center"><strong>Total</strong></div></td>
  </tr>
  <?php foreach ($MtnNodeBudgetTask as $task):?>
<tr>
  <td><?php echo $task->MtnNodeTask->mtn_node_task_name;?></td>
  <td><?php echo $task->mtn_node_budget_task_amount?></td>
  <td><?php echo $task->mtn_node_budget_task_value?></td>    
  <td><?php echo ($task->mtn_node_budget_task_value *  $task->mtn_node_budget_task_amount) ?></td>
  </tr>
<?php endforeach;?>
</table>

<br><br>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
    <td width="15%"><strong>Total</strong> </td>
    <td width="85%" colpsan="3">: <?=$MtnNodeBudget->mtn_node_budget_total; ?></td>
  </tr>
</table>


<br>

<br>
