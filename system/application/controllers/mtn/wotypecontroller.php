<?php

/** @package    Controller
 *  @subpackage Wotypecontroller
 */
class Wotypecontroller extends APP_Controller
{

    function Wotypecontroller()
    {
	parent::APP_Controller();
    }

    /**
     * get
     * 
     * Lista el tipo de orden Correctiva y todas las demas cuyo id sea mayor a 3
     * @param integer $mtn_work_order_type_id
     * 
     */
    function get()
    {
	$workOrderTypeTable = Doctrine_Core::getTable('MtnWorkOrderType');
	$text_autocomplete = $this->input->post('query');
	$show_predictive_ot = $this->input->post('show_predictive_ot');
	$woTypes = $workOrderTypeTable->retrieveAll($text_autocomplete, (!empty($show_predictive_ot) ? true : false));

	if ($woTypes->count())
	{
	    echo '({"total":"' . $woTypes->count() . '", "results":' . $this->json->encode($woTypes->toArray()) . '})';
	}
	else
	{
	    echo '({"total":"0", "results":[]})';
	}
    }

    /**
     * get
     * 
     * Lista el tipo de orden  cuyo id sea mayor a 3
     * @param integer $mtn_work_order_type_id
     * 
     */
    //-----esto fue cambiado OJO ---
//    function getAll ()
//    {
//        $workOrderTypeTable = Doctrine_Core::getTable ( 'MtnWorkOrderType' );
//        $woTypes = $workOrderTypeTable->retrieveAllHigher ();
//
//        if ( $woTypes->count () )
//        {
//            echo '({"total":"' . $woTypes->count () . '", "results":' . $this->json->encode ( $woTypes->toArray () ) . '})';
//        }
//        else
//        {
//            echo '({"total":"0", "results":[]})';
//        }
//    }
    function getAll()
    {
        $text_autocomplete = $this->input->post ( 'query' );
	$workOrderTypeTable = Doctrine_Core::getTable('MtnWorkOrderType');
         $maintainer_type=1; //asset
	$woTypes = $workOrderTypeTable->retrieveTotal($text_autocomplete,$maintainer_type );

	if ($woTypes->count())
	{
	    echo '({"total":"' . $woTypes->count() . '", "results":' . $this->json->encode($woTypes->toArray()) . '})';
	}
	else
	{
	    echo '({"total":"0", "results":[]})';
	}
    }
    
    function getAllByNode()
    {
        $text_autocomplete = $this->input->post ( 'query' );
	$workOrderTypeTable = Doctrine_Core::getTable('MtnWorkOrderType');
         $maintainer_type=2;//node
	$woTypes = $workOrderTypeTable->retrieveTotal($text_autocomplete,$maintainer_type );

	if ($woTypes->count())
	{
	    echo '({"total":"' . $woTypes->count() . '", "results":' . $this->json->encode($woTypes->toArray()) . '})';
	}
	else
	{
	    echo '({"total":"0", "results":[]})';
	}
    }
    
    function getAllByNodeSolo()
    {
        $text_autocomplete = $this->input->post ( 'query' );
	$workOrderTypeTable = Doctrine_Core::getTable('MtnWorkOrderType');
        $maintainer_type=2;//node
	$woTypes = $workOrderTypeTable->retrieveTotalSolo($text_autocomplete,$maintainer_type );
        
        $type_ot_status_total = 0;
        $type_ot_status = array();
        foreach ($woTypes as $woTypesStates) {
            
            $workOrderTask = Doctrine_Core::getTable('MtnConfigState')->findOneBy('mtn_work_order_type_id', $woTypesStates->mtn_work_order_type_id);
            if ($workOrderTask){
                $workOrderType = Doctrine_Core::getTable('MtnWorkOrderType')->findOneBy('mtn_work_order_type_id', $workOrderTask->mtn_work_order_type_id);
                $type_ot_status_total++;
                 array_push($type_ot_status,$workOrderType->toArray());
            }
            
        }
        
	if ($type_ot_status_total > 0)
	{
	    echo '({"total":"' . $type_ot_status_total . '", "results":' . $this->json->encode($type_ot_status) . '})';
	}
	else
	{
	    echo '({"total":"0", "results":[]})';
	}
    }
    
    function getAllByAssetSolo()
    {
        $text_autocomplete = $this->input->post ( 'query' );
	$workOrderTypeTable = Doctrine_Core::getTable('MtnWorkOrderType');
        $maintainer_type=1;//node
	$woTypes = $workOrderTypeTable->retrieveTotalSoloAsset($text_autocomplete,$maintainer_type );
        
        $type_ot_status_total = 0;
        $type_ot_status = array();
        foreach ($woTypes as $woTypesStates) {
            
            $workOrderTask = Doctrine_Core::getTable('MtnConfigState')->findOneBy('mtn_work_order_type_id', $woTypesStates->mtn_work_order_type_id);
            if ($workOrderTask){
                $workOrderType = Doctrine_Core::getTable('MtnWorkOrderType')->findOneBy('mtn_work_order_type_id', $workOrderTask->mtn_work_order_type_id);
                $type_ot_status_total++;
                 array_push($type_ot_status,$workOrderType->toArray());
            }
            
        }
        
	if ($type_ot_status_total > 0)
	{
	    echo '({"total":"' . $type_ot_status_total . '", "results":' . $this->json->encode($type_ot_status) . '})';
	}
	else
	{
	    echo '({"total":"0", "results":[]})';
	}
    }

    function getPreventive()
    {
	$workOrderTypeTable = Doctrine_Core::getTable('MtnWorkOrderType');
	$text_autocomplete = $this->input->post('query');
	$show_predictive_ot = $this->input->post('show_predictive_ot');
	$woTypes = $workOrderTypeTable->retrievePreventive($text_autocomplete, (!empty($show_predictive_ot) ? true : false));

	if ($woTypes->count())
	{
	    echo '({"total":"' . $woTypes->count() . '", "results":' . $this->json->encode($woTypes->toArray()) . '})';
	}
	else
	{
	    echo '({"total":"0", "results":[]})';
	}
    }

    /**
     * add
     * 
     * Agrega un nuevo tipo de Orden de Trabajo
     * 
     * @post integer mtn_work_order_type_id
     * @post string mtn_work_order_type_name
     * @post string mtn_work_order_type_abbreviation 
     * @post integer mtn_work_order_type_duration
     */
    function add()
    {
	//Recibimos los Parametros
	//$mtn_work_order_type_id = (int) $this->input->post('mtn_work_order_type_id');
	$mtn_work_order_type_name = $this->input->post('mtn_work_order_type_name');
	//$mtn_work_order_type_duration = $this->input->post('mtn_work_order_type_duration');
	try
	{
	    $mtnWorkOrderType = new MtnWorkOrderType();
	  //  $mtnWorkOrderType->mtn_work_order_type_id = $mtn_work_order_type_id;
	    $mtnWorkOrderType->mtn_work_order_type_name = $mtn_work_order_type_name;
	//    $mtnWorkOrderType->mtn_work_order_type_duration = $mtn_work_order_type_duration;
	    $mtnWorkOrderType->mtn_maintainer_type_id = 1;
	    $mtnWorkOrderType->save();

//	    $mtnConfigState = new MtnConfigState();
//	    $mtnConfigState->mtn_work_order_type_id = $mtn_work_order_type_id;
//	    $mtnConfigState->mtn_system_work_order_status_id = 1;
//	    $mtnConfigState->mtn_config_state_access_user = 1;
//	    //$mtnConfigState->mtn_config_state_access_provider = 1;
//	    $mtnConfigState->mtn_config_state_order = 1;
//	    $mtnConfigState->save();

	    $success = 'true';
	    $msg = $this->translateTag('General', 'operation_successful');
	} catch (Exception $e)
	{

	    $mtn_work_order_task_id = NULL;
	    $success = 'false';
	    $msg = $e->getMessage();
	}

	$json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
	echo $json_data;
    }
    function addByNode()
    {
	//Recibimos los Parametros
	//$mtn_work_order_type_id = (int) $this->input->post('mtn_work_order_type_id');
	$mtn_work_order_type_name = $this->input->post('mtn_work_order_type_name');
	//$mtn_work_order_type_duration = $this->input->post('mtn_work_order_type_duration');
	try
	{
	    $mtnWorkOrderType = new MtnWorkOrderType();
	    //$mtnWorkOrderType->mtn_work_order_type_id = $mtn_work_order_type_id;
	    $mtnWorkOrderType->mtn_work_order_type_name = $mtn_work_order_type_name;
	   // $mtnWorkOrderType->mtn_work_order_type_duration = $mtn_work_order_type_duration;
	    $mtnWorkOrderType->mtn_maintainer_type_id = 2;
	    $mtnWorkOrderType->save();

//	    $mtnConfigState = new MtnConfigState();
//	    $mtnConfigState->mtn_work_order_type_id = $mtn_work_order_type_id;
//	    $mtnConfigState->mtn_system_work_order_status_id = 1;
//	    $mtnConfigState->mtn_config_state_access_user = 1;
//	    //$mtnConfigState->mtn_config_state_access_provider = 1;
//	    $mtnConfigState->mtn_config_state_order = 1;
//	    $mtnConfigState->save();

	    $success = 'true';
	    $msg = $this->translateTag('General', 'operation_successful');
	} catch (Exception $e)
	{

	    $mtn_work_order_task_id = NULL;
	    $success = 'false';
	    $msg = $e->getMessage();
	}

	$json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
	echo $json_data;
    }

    /**
     * update
     * 
     * Actualiza el  tipo de Orden de Trabajo
     * 
     * @post integer mtn_work_order_type_id
     * @post string mtn_work_order_type_name
     * @post integer mtn_work_order_type_duration
     */
    function update()
    {
	$mtnWorkOrderType = Doctrine_Core::getTable('MtnWorkOrderType')->find($this->input->post('mtn_work_order_type_id'));

	$mtnWorkOrderType['mtn_work_order_type_name'] = $this->input->post('mtn_work_order_type_name');
	$mtnWorkOrderType['mtn_work_order_type_duration'] = $this->input->post('mtn_work_order_type_duration');
	$mtnWorkOrderType->save();
	echo '{"success": true}';
    }

    /**
     * delete
     *
     * Elimina el Tipo de Orden de Trabajo
     *
     * @param integer $mtn_work_order_type_id
     */
    function delete()
    {
	$mtnWorkOrderType = Doctrine::getTable('MtnWorkOrderType')->find($this->input->post('mtn_work_order_type_id'));

	if ($mtnWorkOrderType->delete())
	{
	    $success = 'true';
	    $msg = $this->translateTag('General', 'operation_successful');
	}
	else
	{
	    $success = 'false';
	    $msg = $e->getMessage();
	}
	$json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
	echo $json_data;
    }

        function validatorDelete() {
        
        $mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');

        $MtnWorkOrder = Doctrine::getTable('MtnWorkOrderType')->find($mtn_work_order_type_id);
//SOLO LOS TIPOS EN NULL SE PUEDEN BORRAR LOS OTROS SON RESERVADOS PARA EL SISTEMA
        if ($MtnWorkOrder->mtn_work_order_type_abbreviation != null) {

            $success = 'false';
            $msg = $this->translateTag('Maintenance', 'you_can_not_delete_the_system_reserved_for_types');
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
            return;
        }

//SI SE PUEDEN BORRA LOS TIPOS DE OT CREADOS POR LOS USUARIOS 
        $MtnWorkOrder = Doctrine::getTable('MtnWorkOrder')->countToDelete($mtn_work_order_type_id);


            $success = 'true';
           // $msg = $this->translateTag('General', 'operation_successful');
            $msg = $MtnWorkOrder;

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
    
    
    
}