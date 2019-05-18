<?php

/** @package    Controller
 *  @subpackage Wotaskcontroller
 */
class Wotaskcontroller extends APP_Controller {

    function Wotaskcontroller() {
        parent::APP_Controller();
    }

    /**
     * Devuelve todas las tareas asociadas a una ot
     * @param array post data
     */
    function get() {
        $mtn_work_order_id = (int) $this->input->post('mtn_work_order_id');
        $mtnWorkOrderTaskTable = Doctrine_Core::getTable('MtnWorkOrderTask');
        $woTask = $mtnWorkOrderTaskTable->retrieveAll($mtn_work_order_id);
        $json_data = $this->json->encode(array('total' => count($woTask), 'results' => $woTask));
        echo $json_data;
    }

    /**
     * Agrega una tarea a una ot
     * @param array post data
     */
    function add() {
        try {
            $mtn_task_id = (int) $this->input->post('mtn_task_id');
            $mtn_work_order_task_price = (int) $this->input->post('mtn_work_order_task_price');
            $mtn_work_order_id = (int) $this->input->post('mtn_work_order_id');
            $mtn_work_order_task_time_job = (int) $this->input->post('mtn_work_order_task_time_job');
            $mtn_work_order_task_comment = $this->input->post('mtn_work_order_task_comment');
            $mtn_work_order_status = (int) $this->input->post('mtn_work_order_status');
            $mtnWorkOrderTask = new MtnWorkOrderTask();
            $mtnWorkOrderTask->mtn_task_id = $mtn_task_id;
            $mtnWorkOrderTask->mtn_work_order_id = $mtn_work_order_id;
            $mtnWorkOrderTask->mtn_work_order_task_price = $mtn_work_order_task_price;
            $mtnWorkOrderTask->mtn_work_order_task_time_job = $mtn_work_order_task_time_job;
            $mtnWorkOrderTask->mtn_work_order_task_comment = $mtn_work_order_task_comment;
//            $workOrderTask->mtn_work_order_status = $mtn_work_order_status;
            $mtnWorkOrderTask->save();

            $mtn_work_order_task_id = $mtnWorkOrderTask->mtn_work_order_task_id;

            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');

            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
            $mtnTask = Doctrine_Core::getTable('MtnTask')->find($mtn_task_id);
            $asset = Doctrine_Core::getTable('Asset')->find($MtnWorkOrderDB->asset_id);

            $this->syslog->register('add_task_ot', array(
                $mtnTask->mtn_task_name,
                $MtnWorkOrderDB->mtn_work_order_folio,
                $asset->asset_name
            )); // registering log
        } catch (Exception $e) {
            $mtn_work_order_task_id = NULL;
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'mtn_work_order_task_id' => $mtn_work_order_task_id));
        echo $json_data;
    }

    /**
     * Agrega una tarea a una ot
     * @param array post data
     */
    function addNode() {
        try {
            $mtn_task_id = (int) $this->input->post('mtn_task_id');
            $currency_id = (int) $this->input->post('currency_id');
            $mtn_work_order_task_price = (int) $this->input->post('mtn_work_order_task_price');
            $mtn_work_order_id = (int) $this->input->post('mtn_work_order_id');
            $mtn_work_order_task_time_job = (int) $this->input->post('mtn_work_order_task_time_job');
            $mtn_work_order_task_comment = $this->input->post('mtn_work_order_task_comment');
            $mtn_work_order_status = (int) $this->input->post('mtn_work_order_status');

            //VALIDACION DE LA UF
            $MtnWorkOrder = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
            list($mtn_work_order_created, $hora) = explode(' ', $MtnWorkOrder['mtn_work_order_created']);

            //BUSCA LA UF DE FECA DE LA CREACION DE LA OT 
            $Uf = Doctrine_Core :: getTable('Uf')->retrieveByFecha($mtn_work_order_created);
            $UF_array = $Uf->toArray();
            if (!$UF_array) {
          
                $json_data = $this->json->encode(array('success' => 'false_uf', 'msg' => $this->translateTag('Maintenance', 'there_is_no_value_Uf')));
                echo $json_data;
                exit;
            }

            $mtnWorkOrderTask = new MtnWorkOrderTask();
            $mtnWorkOrderTask->mtn_task_id = $mtn_task_id;
            $mtnWorkOrderTask->currency_id = $currency_id;
            $mtnWorkOrderTask->mtn_work_order_id = $mtn_work_order_id;
            $mtnWorkOrderTask->mtn_work_order_task_price = $mtn_work_order_task_price;
            $mtnWorkOrderTask->mtn_work_order_task_time_job = $mtn_work_order_task_time_job;
            $mtnWorkOrderTask->mtn_work_order_task_comment = $mtn_work_order_task_comment;
            $mtnWorkOrderTask->save();
//echo $mtn_work_order_id;
            //CREA LOS COSTOS DIRECTOS
            $this->updateCostosDirectos($mtn_work_order_id);

            $mtn_work_order_task_id = $mtnWorkOrderTask->mtn_work_order_task_id;

            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');

            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
            $mtnTask = Doctrine_Core::getTable('MtnTask')->find($mtn_task_id);
            $node = Doctrine_Core::getTable('Node')->find($MtnWorkOrderDB->node_id);

            $this->syslog->register('add_task_ot_node', array(
                $mtnTask->mtn_task_name,
                $MtnWorkOrderDB->mtn_work_order_folio,
                $node->node_name
            )); // registering log
            
        } catch (Exception $e) {
            $mtn_work_order_task_id = NULL;
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'mtn_work_order_task_id' => $mtn_work_order_task_id));
        echo $json_data;
    }

    function updateCostosDirectos($mtn_work_order_id) {

        //BUSCA LA FECHA DE LA OT
        $MtnWorkOrder = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
        list($mtn_work_order_created, $hora) = explode(' ', $MtnWorkOrder['mtn_work_order_created']);

        //BUSCA LA UF DE FECA DE LA CREACION DE LA OT 
        $Uf = Doctrine_Core :: getTable('Uf')->retrieveByFecha($mtn_work_order_created);
        $UF_array = $Uf->toArray();
        if ($UF_array) {

            // echo  $UF_array[0]['uf_value'];              
            //SE DIVIDE PARA SABER EL VALOR EN UF DE TODAS LAS TAREAS DE LA OT 
            if ($MtnWorkOrder->total_task > 0) {
//                
                $valor_en_uf1 = $MtnWorkOrder->total_task / $UF_array[0]['uf_value'];
                $valor_en_uf = round($valor_en_uf1);
            } else {
                $valor_en_uf = 0;
            }
        } else {
            //OJO TIRA OTRO MENSAJE
            $json_data = $this->json->encode(array('success' => 'false_uf', 'msg' => $this->translateTag('Maintenance', 'there_is_no_value_Uf')));
            echo $json_data;
            exit;
        }

        //SE BUSCA LA REGION AL QUE PERTENECE LA OT DENTRO DE LA RAMA POR NODE ID
        $nodePadre = Doctrine_Core::getTable('Node')->find($MtnWorkOrder->node_id);
        $node = $nodePadre->getNode();
        $nodesCantity = $node->getAncestors();

        $nodo_region = array(535, 189, 640, 643, 362, 672, 675, 676, 391, 705, 710, 321, 2, 713, 714);
        $node_id_region = 0;
        if ($nodesCantity) {
            // Con Ancestros 
            $ancestor = $nodesCantity->toArray();

            foreach ($ancestor as $item) {

                if (in_array($item['node_id'], $nodo_region)) {
                    $node_id_region = $item['node_id'];
                    break;
                } else {
                    $node_id_region = $MtnWorkOrder->node_id;
                }
            }
        }
//SI EL VALOR EN UF ES MAYOR A 3000 USE LOS PORCENTAJES EN RANGO DE 3000
        if ($valor_en_uf > 3000) {
            $valor_en_uf = 3000;
        }

// SE BUSCA EL RANGO AL QUE PERTENECE EL VALOR_EN_UF PARA AGREGAR LOS PORCENTAJES CORRESPONDIENTES
        $MtnPercentages = Doctrine_Core :: getTable('MtnPercentages')->retrievePercentages($node_id_region, $valor_en_uf);

        $MtnWorkOrderOtherCosts = Doctrine_Core :: getTable('MtnWorkOrderOtherCosts')->findBy('mtn_work_order_id', $mtn_work_order_id);
        if ($MtnWorkOrderOtherCosts->count()) {
            //SI ENCUENTRA ACTUALIZA
            $MtnWorkOrderOtherCosts = Doctrine_Core :: getTable('MtnWorkOrderOtherCosts')->retrieveByWoAndPercentage($mtn_work_order_id, 100);
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = ($MtnWorkOrder->total_task * $MtnPercentages->mtn_percentages_viatico) / 100;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $MtnPercentages->mtn_percentages_viatico;
            $MtnWorkOrderOtherCosts->save();
            $MtnWorkOrderOtherCosts = Doctrine_Core :: getTable('MtnWorkOrderOtherCosts')->retrieveByWoAndPercentage($mtn_work_order_id, 200);
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = ($MtnWorkOrder->total_task * $MtnPercentages->mtn_percentages_general_expenses) / 100;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $MtnPercentages->mtn_percentages_general_expenses;
            $MtnWorkOrderOtherCosts->save();
            $MtnWorkOrderOtherCosts = Doctrine_Core :: getTable('MtnWorkOrderOtherCosts')->retrieveByWoAndPercentage($mtn_work_order_id, 300);
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = ($MtnWorkOrder->total_task * $MtnPercentages->mtn_percentages_utility) / 100;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $MtnPercentages->mtn_percentages_utility;
            $MtnWorkOrderOtherCosts->save();
        } else {
            //ACA ENTRA LA PRIMERA VEZ
            $MtnWorkOrderOtherCosts = new MtnWorkOrderOtherCosts();
            $MtnWorkOrderOtherCosts->mtn_work_order_id = $mtn_work_order_id;
            $MtnWorkOrderOtherCosts->mtn_other_costs_id = 100;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = ($MtnWorkOrder->total_task * $MtnPercentages->mtn_percentages_viatico) / 100;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $MtnPercentages->mtn_percentages_viatico;
            $MtnWorkOrderOtherCosts->save();

            $MtnWorkOrderOtherCosts = new MtnWorkOrderOtherCosts();
            $MtnWorkOrderOtherCosts->mtn_work_order_id = $mtn_work_order_id;
            $MtnWorkOrderOtherCosts->mtn_other_costs_id = 200;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = ($MtnWorkOrder->total_task * $MtnPercentages->mtn_percentages_general_expenses) / 100;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $MtnPercentages->mtn_percentages_general_expenses;
            $MtnWorkOrderOtherCosts->save();

            $MtnWorkOrderOtherCosts = new MtnWorkOrderOtherCosts();
            $MtnWorkOrderOtherCosts->mtn_work_order_id = $mtn_work_order_id;
            $MtnWorkOrderOtherCosts->mtn_other_costs_id = 300;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = ($MtnWorkOrder->total_task * $MtnPercentages->mtn_percentages_utility) / 100;
            $MtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $MtnPercentages->mtn_percentages_utility;
            $MtnWorkOrderOtherCosts->save();
        }
        return;
    }

    /**
     * Actualiza los datos de una tarea
     * @param post data
     */
    function update() {
        try {
            $mtn_work_order_task_id = (int) $this->input->post('mtn_work_order_task_id');
            $mtn_task_id = (int) $this->input->post('mtn_task_id');
            $mtn_work_order_task_time_job = (int) $this->input->post('mtn_work_order_task_time_job');
            $mtn_work_order_task_price = (int) $this->input->post('mtn_work_order_task_price');
            $mtn_work_order_task_comment = $this->input->post('mtn_work_order_task_comment');
            $mtn_work_order_status = (int) $this->input->post('mtn_work_order_status');

            $workOrderTask = Doctrine_Core::getTable('MtnWorkOrderTask')->find($mtn_work_order_task_id);

            $workOrderTaskAntes = $workOrderTask->mtn_task_id;
            $workOrderTask->mtn_task_id = $mtn_task_id;
            $task_time_job_antes = $workOrderTask->mtn_work_order_task_time_job;
            $workOrderTask->mtn_work_order_task_time_job = $mtn_work_order_task_time_job;
            $task_time_job_despues = $workOrderTask->mtn_work_order_task_time_job;
            $task_price_antes = $workOrderTask->mtn_work_order_task_price;
            $workOrderTask->mtn_work_order_task_price = $mtn_work_order_task_price;
            $task_price_despues = $workOrderTask->mtn_work_order_task_price;
            $task_comment_antes = $workOrderTask->mtn_work_order_task_comment;
            $workOrderTask->mtn_work_order_task_comment = $mtn_work_order_task_comment;
            $task_comment_despues = $workOrderTask->mtn_work_order_task_comment;

            //Update de la tarea
            $workOrderTask->save();
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');

            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($workOrderTask->mtn_work_order_id);
            $mtnTaskDespues = Doctrine_Core::getTable('MtnTask')->find($mtn_task_id);
            $mtnTaskAntes = Doctrine_Core::getTable('MtnTask')->find($workOrderTaskAntes);
            $asset = Doctrine_Core::getTable('Asset')->find($MtnWorkOrderDB->asset_id);

            $log_id = $this->syslog->register('update_task_ot', array(
                $mtnTaskDespues->mtn_task_name,
                $MtnWorkOrderDB->mtn_work_order_folio,
                $asset->asset_name
            )); // registering log

            if ($mtnTaskAntes->mtn_task_name != $mtnTaskDespues->mtn_task_name) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'task');
                    $logDetail->log_detail_value_old = $mtnTaskAntes->mtn_task_name;
                    $logDetail->log_detail_value_new = $mtnTaskDespues->mtn_task_name;
                    $logDetail->save();
                }
            }

            if ($task_price_antes != $task_price_despues) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'value');
                    $logDetail->log_detail_value_old = $task_price_antes;
                    $logDetail->log_detail_value_new = $task_price_despues;
                    $logDetail->save();
                }
            }

            if ($task_time_job_antes != $task_time_job_despues) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'time');
                    $logDetail->log_detail_value_old = $task_time_job_antes;
                    $logDetail->log_detail_value_new = $task_time_job_despues;
                    $logDetail->save();
                }
            }

            if ($task_comment_antes != $task_comment_despues) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                    $logDetail->log_detail_value_old = $task_comment_antes;
                    $logDetail->log_detail_value_new = $task_comment_despues;
                    $logDetail->save();
                }
            }
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'mtn_work_order_task_id' => $mtn_work_order_task_id));
        echo $json_data;
    }

    /**
     * Actualiza los datos de una tarea
     * @param post data
     */
    function updateNode() {
        try {
            $mtn_work_order_task_id = (int) $this->input->post('mtn_work_order_task_id');
            $mtn_task_id = (int) $this->input->post('mtn_task_id');
            $currency_id = (int) $this->input->post('currency_id');
            $mtn_work_order_task_time_job = (int) $this->input->post('mtn_work_order_task_time_job');
            $mtn_work_order_task_price = (int) $this->input->post('mtn_work_order_task_price');
            $mtn_work_order_task_comment = $this->input->post('mtn_work_order_task_comment');
            $mtn_work_order_status = (int) $this->input->post('mtn_work_order_status');

            $workOrderTask = Doctrine_Core::getTable('MtnWorkOrderTask')->find($mtn_work_order_task_id);

            $workOrderTaskAntes = $workOrderTask->mtn_task_id;
            $workOrderTask->mtn_task_id = $mtn_task_id;

            $workOrderCurrencyAntes = Doctrine_Core::getTable('Currency')->find($workOrderTask->currency_id);
            $workOrderTask->currency_id = $currency_id;

            $task_time_job_antes = $workOrderTask->mtn_work_order_task_time_job;
            $workOrderTask->mtn_work_order_task_time_job = $mtn_work_order_task_time_job;
            $task_time_job_despues = $workOrderTask->mtn_work_order_task_time_job;
            $task_price_antes = $workOrderTask->mtn_work_order_task_price;
            $workOrderTask->mtn_work_order_task_price = $mtn_work_order_task_price;
            $task_price_despues = $workOrderTask->mtn_work_order_task_price;
            $task_comment_antes = $workOrderTask->mtn_work_order_task_comment;
            $workOrderTask->mtn_work_order_task_comment = $mtn_work_order_task_comment;
            $task_comment_despues = $workOrderTask->mtn_work_order_task_comment;
            
            
            //Update de la tarea
            $workOrderTask->save();
            $resp = $this->updateCostosDirectos($workOrderTask->mtn_work_order_id);



            if ($resp === false) {
                return;
            }

            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');

            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($workOrderTask->mtn_work_order_id);
            $mtnTaskDespues = Doctrine_Core::getTable('MtnTask')->find($mtn_task_id);
            
            $mtnTaskAntes = Doctrine_Core::getTable('MtnTask')->find($workOrderTaskAntes);
            $node = Doctrine_Core::getTable('Node')->find($MtnWorkOrderDB->node_id);
            
            $mtnCurrencyDespues = Doctrine_Core::getTable('Currency')->find($currency_id);

            $log_id = $this->syslog->register('update_task_ot_node', array(
                $mtnTaskDespues->mtn_task_name,
                $MtnWorkOrderDB->mtn_work_order_folio,
                $node->node_name
            )); // registering log
            
            if ($mtnCurrencyDespues->currency_name != $workOrderCurrencyAntes->currency_name)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'currency');
                    $logDetail->log_detail_value_old = $workOrderCurrencyAntes->currency_name;
                    $logDetail->log_detail_value_new = $mtnCurrencyDespues->currency_name;
                    $logDetail->save();
                }
            }
            
            if ($mtnTaskAntes->mtn_task_name != $mtnTaskDespues->mtn_task_name)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'task');
                    $logDetail->log_detail_value_old = $mtnTaskAntes->mtn_task_name;
                    $logDetail->log_detail_value_new = $mtnTaskDespues->mtn_task_name;
                    $logDetail->save();
                }
            }
            
            if ($task_price_antes != $task_price_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'value');
                    $logDetail->log_detail_value_old = $task_price_antes;
                    $logDetail->log_detail_value_new = $task_price_despues;
                    $logDetail->save();
                }
            }
            
            if ($task_time_job_antes != $task_time_job_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'time');
                    $logDetail->log_detail_value_old = $task_time_job_antes;
                    $logDetail->log_detail_value_new = $task_time_job_despues;
                    $logDetail->save();
                }
            }
            
            if ($task_comment_antes != $task_comment_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                    $logDetail->log_detail_value_old = $task_comment_antes;
                    $logDetail->log_detail_value_new = $task_comment_despues;
                    $logDetail->save();
                }
            }
            
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
            return;
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'mtn_work_order_task_id' => $mtn_work_order_task_id));
        echo $json_data;
    }

    /**
     * Elimina las tareas asociadas a la OT y los insumos usados por la tarea.
     * Delete en cascada
     * @param integer $mtn_work_order_task_id 
     */
    function delete() {
        $mtn_work_order_task_id = $this->input->post('mtn_work_order_task_id');
        $mtnWorkOrderTaskTable = Doctrine::getTable('mtnWorkOrderTask')->find($mtn_work_order_task_id);
        $mtn_work_order_id = $mtnWorkOrderTaskTable->mtn_work_order_id;
        if ($mtnWorkOrderTaskTable->delete()) {
            $this->updateCostosDirectos($mtn_work_order_id);
            $success = 'true';
        } else {
            $success = 'false';
        }

        $json_data = $this->json->encode(array('success' => $success));
        echo $json_data;
    }

}
