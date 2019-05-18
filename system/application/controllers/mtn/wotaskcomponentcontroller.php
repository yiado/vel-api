<?php

/** @package    Controller
 *  @subpackage Wotaskcomponentcontroller
 */
class Wotaskcomponentcontroller extends APP_Controller
{

    function Wotaskcomponentcontroller()
    {
        parent::APP_Controller();
    }

    /**
     * Devuelve todos los insumos (components) asociados a una tarea
     * @param array post data
     */
    function get()
    {
        $mtn_work_order_task_id = (int) $this->input->post('mtn_work_order_task_id');
        $mtnWorkOrderTaskComponentTable = Doctrine_Core::getTable('MtnWorkOrderTaskComponent');
        $woTaskComponent = $mtnWorkOrderTaskComponentTable->retrieveAll($mtn_work_order_task_id);
        $json_data = $this->json->encode(array('total' => $woTaskComponent->count(), 'results' => $woTaskComponent->toArray()));
        echo $json_data;
    }

    /**
     * Agrega un insumo a una tarea de la ot
     * @param array post data
     */
    function add()
    {
        try
        {
            $mtn_work_order_task_id = (int) $this->input->post('mtn_work_order_task_id');
            $mtn_price_list_component_id = (int) $this->input->post('mtn_price_list_component_id');
            $mtn_work_order_component_amount = (int) $this->input->post('mtn_work_order_component_amount');
            $mtn_work_order_component_price = (int) $this->input->post('mtn_work_order_component_price');
            $mtnWorkOrderTaskComponent = new MtnWorkOrderTaskComponent();
            $mtnWorkOrderTaskComponent->mtn_work_order_task_id = $mtn_work_order_task_id;
            if ($mtn_work_order_component_price === 0)
            {
                $priceList = Doctrine_Core::getTable('MtnPriceListComponent')->find($mtn_price_list_component_id);
                $mtnWorkOrderTaskComponent->mtn_price_list_component_id = $mtn_price_list_component_id;
                $mtnWorkOrderTaskComponent->mtn_work_order_component_price = NULL;
                $mtnWorkOrderTaskComponent->mtn_component_id = $priceList->mtn_component_id;
                $mtnWorkOrderTaskComponent->mtn_work_order_component_price = $priceList->mtn_price_list_component_price;
            } else
            {
                $mtnWorkOrderTaskComponent->mtn_price_list_component_id = NULL;
                $mtnWorkOrderTaskComponent->mtn_component_id = $mtn_price_list_component_id; //Para este caso se envió el id del componente
                $mtnWorkOrderTaskComponent->mtn_work_order_component_price = $mtn_work_order_component_price;
            }


            $mtnWorkOrderTaskComponent->mtn_work_order_component_amount = $mtn_work_order_component_amount;
            $mtnWorkOrderTaskComponent->save();
            $success = 'true';

            $priceListSearch = Doctrine_Core :: getTable('MtnPriceListComponent')->find($mtn_price_list_component_id);
            $mtnComponent = Doctrine_Core :: getTable('MtnComponent')->find($priceListSearch->mtn_component_id);
            $mtnTask = Doctrine_Core::getTable('MtnWorkOrderTask')->find($mtn_work_order_task_id);
            $mtnTaskName = Doctrine_Core::getTable('MtnTask')->find($mtnTask->mtn_task_id);
            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtnTask->mtn_work_order_id);
            
            $asset = Doctrine_Core::getTable('Asset')->find($MtnWorkOrderDB->asset_id);

            $this->syslog->register('add_task_component_ot', array(
                $mtnComponent->mtn_component_name,
                $mtnTaskName->mtn_task_name,
                $MtnWorkOrderDB->mtn_work_order_folio,
                $asset->asset_name
            )); // registering log
            
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e)
        {
            $success = 'false';
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
    
    function addNode()
    {
        try
        {
            $mtn_work_order_task_id = (int) $this->input->post('mtn_work_order_task_id');
            $mtn_price_list_component_id = (int) $this->input->post('mtn_price_list_component_id');
            $mtn_work_order_component_amount = (int) $this->input->post('mtn_work_order_component_amount');
            $mtn_work_order_component_price = (int) $this->input->post('mtn_work_order_component_price');
            $mtnWorkOrderTaskComponent = new MtnWorkOrderTaskComponent();
            $mtnWorkOrderTaskComponent->mtn_work_order_task_id = $mtn_work_order_task_id;
            if ($mtn_work_order_component_price === 0)
            {
                $priceList = Doctrine_Core::getTable('MtnPriceListComponent')->find($mtn_price_list_component_id);
                $mtnWorkOrderTaskComponent->mtn_price_list_component_id = $mtn_price_list_component_id;
                $mtnWorkOrderTaskComponent->mtn_work_order_component_price = NULL;
                $mtnWorkOrderTaskComponent->mtn_component_id = $priceList->mtn_component_id;
                $mtnWorkOrderTaskComponent->mtn_work_order_component_price = $priceList->mtn_price_list_component_price;
            } else
            {
                $mtnWorkOrderTaskComponent->mtn_price_list_component_id = NULL;
                $mtnWorkOrderTaskComponent->mtn_component_id = $mtn_price_list_component_id; //Para este caso se envió el id del componente
                $mtnWorkOrderTaskComponent->mtn_work_order_component_price = $mtn_work_order_component_price;
            }


            $mtnWorkOrderTaskComponent->mtn_work_order_component_amount = $mtn_work_order_component_amount;
            $mtnWorkOrderTaskComponent->save();
            $success = 'true';

      
           
//            $MtnWorkOrderTask = Doctrine_Core :: getTable('MtnWorkOrderTask')->find($mtn_work_order_task_id);
//            echo $MtnWorkOrderTask->mtn_work_order_id;
            
//            $Wotaskcontroller->updateCostosDirectos($MtnWorkOrderTask->mtn_work_order_id);
//            
//            
//            
//                    $CI = & get_instance();
//        $CI->load->library('mtn/Wotaskcontroller');
             
            
          //  echo $MtnWorkOrderTask->mtn_work_order_id;
//       $this->load->controller('/mtn/wotaskcontroller','invoice_controller');
//
//$this->invoice_controller->updateCostosDirectos($MtnWorkOrderTask->mtn_work_order_id);
            
            $priceListSearch = Doctrine_Core :: getTable('MtnPriceListComponent')->find($mtn_price_list_component_id);
            $mtnComponent = Doctrine_Core :: getTable('MtnComponent')->find($priceListSearch->mtn_component_id);
            $mtnTask = Doctrine_Core::getTable('MtnWorkOrderTask')->find($mtn_work_order_task_id);
            $mtnTaskName = Doctrine_Core::getTable('MtnTask')->find($mtnTask->mtn_task_id);
            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtnTask->mtn_work_order_id);
            
            $node = Doctrine_Core::getTable('Node')->find($MtnWorkOrderDB->node_id);

            $this->syslog->register('add_task_component_ot_node', array(
                $mtnComponent->mtn_component_name,
                $mtnTaskName->mtn_task_name,
                $MtnWorkOrderDB->mtn_work_order_folio,
                $node->node_name
            )); // registering log
            
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e)
        {
            $success = 'false';
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * Actualiza un insumo de una tarea de la ot
     * @param array post data
     */
    function update()
    {
        try
        {
            $mtn_work_order_task_component_id = (int) $this->input->post('mtn_work_order_task_component_id');
            $mtn_work_order_component_amount = (int) $this->input->post('mtn_work_order_component_amount');
            $mtnWorkOrderTaskComponent = Doctrine_Core::getTable('MtnWorkOrderTaskComponent')->retrieveById($mtn_work_order_task_component_id);
            $amount_antes = $mtnWorkOrderTaskComponent->mtn_work_order_component_amount;
            $mtnWorkOrderTaskComponent->mtn_work_order_component_amount = $mtn_work_order_component_amount;
            $component_amount_despues = $mtnWorkOrderTaskComponent->mtn_work_order_component_amount;
            $mtnWorkOrderTaskComponent->save();
            
            
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
            
            $priceListSearch = Doctrine_Core :: getTable('MtnPriceListComponent')->find($this->input->post('mtn_work_order_task_component_id'));
            $mtnComponent = Doctrine_Core :: getTable('MtnComponent')->find($priceListSearch->mtn_component_id);
            $mtnTask = Doctrine_Core::getTable('MtnWorkOrderTask')->find($this->input->post('mtn_work_order_task_id'));
            $mtnTaskName = Doctrine_Core::getTable('MtnTask')->find($mtnTask->mtn_task_id);
            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtnTask->mtn_work_order_id);
            
            $node = Doctrine_Core::getTable('Node')->find($MtnWorkOrderDB->node_id);
            

            $log_id = $this->syslog->register('update_task_component_ot_node', array(
                $mtnComponent->mtn_component_name,
                $mtnTaskName->mtn_task_name,
                $MtnWorkOrderDB->mtn_work_order_folio,
                $node->node_name
            )); // registering log
            
            if ($amount_antes != $component_amount_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'quantity');
                    $logDetail->log_detail_value_old = $amount_antes;
                    $logDetail->log_detail_value_new = $component_amount_despues;
                    $logDetail->save();
                }
            }
            
        } catch (Exception $e)
        {
            $success = 'false';
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * Elimina el insumo usado en la tarea
     * @param integer  
     */
    function delete()
    {
        $mtn_work_order_task_component_id = $this->input->post('mtn_work_order_task_component_id');
        $mtnWorkOrderTaskComponentTable = Doctrine::getTable('MtnWorkOrderTaskComponent')->find($mtn_work_order_task_component_id);

        if ($mtnWorkOrderTaskComponentTable->delete())
        {
            $success = 'true';
        } else
        {
            $success = 'false';
        }
        $json_data = $this->json->encode(array('success' => $success));
        echo $json_data;
    }

}
