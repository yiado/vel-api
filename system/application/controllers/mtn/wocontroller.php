<?php

/**
 * @package    Controller
 * @subpackage WoController (work order)
 */
class WoController extends APP_Controller {

    function WoController() {
        parent :: APP_Controller();
    }

    /**
     * Retorna todas las WO que cumplan con los filtros enviados
     *
     * @param post data
     */
    function getOne() {
        $MtnWorkOrder = Doctrine_Core :: getTable('MtnWorkOrder')->retrieveById($this->input->post('mtn_work_order_id'));

        if ($this->input->post('mtn_work_order_id') && $MtnWorkOrder->count()) {
            echo '({"success": true, "data":' . $this->json->encode($MtnWorkOrder->toArray()) . '})';
        } else {
            echo '({"success": false, "data":{}})';
        }
    }

    function getOneNode() {

        if ($this->input->post('mtn_work_order_id')) {
            $MtnWorkOrder = Doctrine_Core :: getTable('MtnWorkOrder')->retrieveByIdNode($this->input->post('mtn_work_order_id'));

            $final = $MtnWorkOrder->toArray();

            $Node = Doctrine_Core::getTable('Node')->find($final['node_id']);
            $ruta = $Node->getPath();

            $AuxNode = $Node->toArray();
            $NodeType = Doctrine_Core::getTable('NodeType')->find($Node['node_type_id']);
            $AuxNodeType = $NodeType->toArray();

            $final['node_name'] = $AuxNode['node_name'];
            $final['node_type_name'] = $AuxNodeType['node_type_name'];
            $final['node_ruta'] = $ruta;
        }


        if ($this->input->post('mtn_work_order_id') && $MtnWorkOrder->count()) {
            echo '({"success": true, "data":' . $this->json->encode($final) . '})';
        } else {
            echo '({"success": false, "data":{}})';
        }
    }

    function get() {
        $mtn_work_order_id = $this->input->post('mtn_work_order_id');
        $node_id = $this->input->post('node_id');
        $search_branch = $this->input->post('search_branch');
        $assetFolio = $this->input->post('mtn_work_order_folio');
        $mtn_work_order_requested_by = $this->input->post('mtn_work_order_requested_by');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
        $mtn_system_work_order_status_id = $this->input->post('mtn_system_work_order_status_id');

        if (isset($start_date)) {
            list ($dia, $mes, $anio) = explode("/", $start_date);
            $start_date = $anio . "-" . $mes . "-" . $dia;
        }
        if (isset($end_date)) {
            list ($dia, $mes, $anio) = explode("/", $end_date);
            $end_date = $anio . "-" . $mes . "-" . $dia;
        }



        $filters = array(
            'wo.mtn_work_order_id = ?' => $mtn_work_order_id,
            'p.provider_id = ?' => $this->input->post('provider_id'),
            'mswos.mtn_system_work_order_status_id = ?' => (!empty($mtn_system_work_order_status_id) ? $mtn_system_work_order_status_id : NULL),
            'wot.mtn_work_order_type_id = ?' => (!empty($mtn_work_order_type_id) ? $mtn_work_order_type_id : NULL),
            'wo.mtn_work_order_folio LIKE ?' => (!empty($assetFolio) ? '%' . $assetFolio . '%' : NULL),
            'a.asset_type_id = ?' => $this->input->post('asset_type_id'),
            'wo.mtn_work_order_date >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL),
            'wo.mtn_work_order_date <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL),
            'wo.mtn_work_order_requested_by LIKE ?' => (!empty($mtn_work_order_requested_by) ? '%' . $mtn_work_order_requested_by . '%' : NULL));

        if (!$this->input->post('include_closed_wo')) {
            $filters['mtn_work_order_closed = ?'] = '0';
        }


        $woTable = Doctrine_Core :: getTable('MtnWorkOrder');

        if ($search_branch) {

            $workOrders = $woTable->retrieveAll($filters, $node_id, $search_branch);
        } else {

            $workOrders = $woTable->retrieveAllId($filters, $node_id);
        }


        if ($workOrders->count()) {
            echo '({"total":"' . $workOrders->count() . '", "results":' . $this->json->encode($workOrders->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getNode() {
        $mtn_work_order_id = $this->input->post('mtn_work_order_id');
        $nodeFolio = $this->input->post('mtn_work_order_folio');
        $mtn_work_order_requested_by = $this->input->post('mtn_work_order_requested_by');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
        $mtn_system_work_order_status_id = $this->input->post('mtn_system_work_order_status_id');

        if (isset($start_date)) {
            list ($dia, $mes, $anio) = explode("/", $start_date);
            $start_date = $anio . "-" . $mes . "-" . $dia;
        }
        if (isset($end_date)) {
            list ($dia, $mes, $anio) = explode("/", $end_date);
            $end_date = $anio . "-" . $mes . "-" . $dia;
        }



        $filters = array(
            'wo.mtn_work_order_id = ?' => $mtn_work_order_id,
            'p.provider_id = ?' => $this->input->post('provider_id'),
            'mswos.mtn_system_work_order_status_id = ?' => (!empty($mtn_system_work_order_status_id) ? $mtn_system_work_order_status_id : NULL),
            'wot.mtn_work_order_type_id = ?' => (!empty($mtn_work_order_type_id) ? $mtn_work_order_type_id : NULL),
            'wo.mtn_work_order_folio LIKE ?' => (!empty($nodeFolio) ? '%' . $nodeFolio . '%' : NULL),
            'wo.mtn_work_order_date >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL),
            'wo.mtn_work_order_date <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL),
            'wo.mtn_work_order_requested_by LIKE ?' => (!empty($mtn_work_order_requested_by) ? '%' . $mtn_work_order_requested_by . '%' : NULL));

        if (!$this->input->post('include_closed_wo')) {
            $filters['mtn_work_order_closed = ?'] = '0';
        }
        $node_id = 'root';
        
        $MtnWorkOrder = Doctrine_Core::getTable('MtnWorkOrder');

        $woTable = Doctrine_Core :: getTable('MtnWorkOrder')->findAllNode($filters, $node_id, $this->input->post('start'), $this->input->post('limit'));
        foreach ($woTable->toArray() as $key => $wo) {

            $final[] = $wo;

            $Node = Doctrine_Core::getTable('Node')->find($wo['node_id']);
            $AuxNode = $Node->getPath();

            $final[$key]['node_ruta'] = $AuxNode;
        }

        if ($woTable->count()) {
            echo '({"total":"' . $MtnWorkOrder->findAllNode($filters, $node_id, null, null, true) . '", "results":' . $this->json->encode($final) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
        
    }

    function getProvider() {
        $mtn_work_order_id = $this->input->post('mtn_work_order_id');
        $mtn_work_order_status = $this->input->post('mtn_work_order_status');
        $assetFolio = $this->input->post('mtn_work_order_folio');
        $mtn_work_order_requested_by = $this->input->post('mtn_work_order_requested_by');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $user_id = $this->session->userdata('user_id');

        $UserProvider = Doctrine_Core :: getTable('UserProvider');
        $User = $UserProvider->findOneBy($user_id);
        
        if (isset($start_date)) {
            list ($dia, $mes, $anio) = explode("/", $start_date);
            $start_date = $anio . "-" . $mes . "-" . $dia;
        }

        if (isset($end_date)) {
            list ($dia, $mes, $anio) = explode("/", $end_date);
            $end_date = $anio . "-" . $mes . "-" . $dia;
        }

        $filters = array(
            'wo.mtn_work_order_id = ?' => $mtn_work_order_id,
            'mswos.mtn_system_work_order_status_id = ?' => $this->input->post('mtn_system_work_order_status_id'),
            'p.provider_id = ?' => $this->input->post('provider_id'),
            'wot.mtn_work_order_type_id = ?' => $this->input->post('mtn_work_order_type_id'),
            'wo.mtn_work_order_folio LIKE ?' => (!empty($assetFolio) ? '%' . $assetFolio . '%' : NULL),
//            'a.asset_type_id = ?' => $this->input->post('asset_type_id'),
            'wo.mtn_work_order_date >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL),
            'wo.mtn_work_order_date <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL),
            'wo.mtn_work_order_status = ?' => (!empty($mtn_work_order_status) ? $mtn_work_order_status : 0),
            'wo.mtn_work_order_requested_by LIKE ?' => (!empty($mtn_work_order_requested_by) ? '%' . $mtn_work_order_requested_by . '%' : NULL));

        if (!$this->input->post('include_closed_wo')) {
            $filters['mtn_work_order_closed = ?'] = '0';
        }


        $woTable = Doctrine_Core :: getTable('MtnWorkOrder');
        $workOrders = $woTable->retrieveByProvider($filters, $User->provider_id);
        
        foreach ($workOrders->toArray() as $key => $wo) {

            $final[] = $wo;

            $Node = Doctrine_Core::getTable('Node')->find($wo['node_id']);
            $AuxNode = $Node->getPath();

            $final[$key]['node_ruta'] = $AuxNode;
        }

        if ($workOrders->count()) {
            echo '({"total":"' . $workOrders->count() . '", "results":' . $this->json->encode($final) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getPreventive() {
        $node_id = $this->input->post('node_id');
        $asset_type_id = $this->input->post('asset_type_id');
        $brand_id = $this->input->post('brand_id');
        $provider_id = $this->input->post('provider_id');

        $filters = array(
            'a.asset_type_id = ?' => (!empty($asset_type_id) ? $asset_type_id : NULL),
            'a.brand_id = ?' => (!empty($brand_id) ? $brand_id : NULL));

        $contractAssetTable = Doctrine_Core :: getTable('MtnWorkOrder');
        $contractAsset = $contractAssetTable->retrieveByFilter($filters, $node_id, $provider_id);

        if ($contractAsset->count()) {
            echo '({"total":"' . $contractAsset->count() . '", "results":' . $this->json->encode($contractAsset->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getPreventiveByNode() {
        $node_id = $this->input->post('node_id');
        $node_type_id = $this->input->post('node_type_id');

        if ($node_id) {
            $nodePadre = Doctrine_Core::getTable('Node')->find($node_id);
            $node = $nodePadre->getNode();
            $nodesCantity = $node->getAncestors();

            $resultado = 0;

            $ContractSolo = Doctrine_Core::getTable('ContractNode')->findOneBy('node_id', $this->input->post('node_id'));
            if ($ContractSolo) {

                if ($ContractSolo) {
                    $resultado = $ContractSolo->contract_id;
                }
            } else {

                if ($nodesCantity) {
                    foreach ($nodesCantity as $nodes) {
                        $ContractNode = Doctrine_Core::getTable('ContractNode')->findOneBy('node_id', $nodes->node_id);
                        if ($ContractNode) {
                            $resultado = $ContractNode->contract_id;
                        }
                    }
                }
            }

            if ($resultado != 0) {

                $filters = array('n.node_type_id = ?' => (!empty($node_type_id) ? $node_type_id : NULL));

                $MtnWorkOrders = Doctrine_Core::getTable('MtnWorkOrder')->retrieveByFilterByNode($filters, $node_id);

                foreach ($MtnWorkOrders as $key => $value) {

                    $final[] = $value;
                    $Node = Doctrine_Core::getTable('Node')->find($value['node_id']);
                    $AuxNode = $Node->getPath();

                    $final[$key]['node_ruta'] = $AuxNode;
                }
            } else {
                $msg = "Debe agregar un Proveedor mediante un contrato";
                $success = false;
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
                return;
            }
        } else {
            echo '({"total":"0", "results":[]})';
            return;
        }



        if (count($final)) {
            echo '({"total":"' . count($final) . '", "results":' . $this->json->encode($final) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function addCorrective() {
        try {
            $asset_id = $this->input->post('asset_id');
            $node_id = $this->input->post('node_id');
            $mtn_config_state_id = $this->input->post('mtn_config_state_id');
            $mtn_work_order_creator_id = $this->session->userdata('user_id');
            $mtn_work_order_requested_by = $this->input->post('mtn_work_order_requested_by');
            $mtn_work_order_date = $this->input->post('mtn_work_order_date');

            $MtnConfigState = Doctrine_Core :: getTable('MtnConfigState')->find($mtn_config_state_id);
            $mtn_work_order_type_id = $MtnConfigState['mtn_work_order_type_id'];
            $mtn_system_work_order_status_id = $MtnConfigState['mtn_system_work_order_status_id'];

            //--BUSCA EL PROVEDOR	    
            $AssetTable = Doctrine_Core :: getTable('Asset')->find($asset_id);
            $provider_id = $AssetTable['provider_id'];

            if ($provider_id) {
                //CREA OT
                $workOrder = new MtnWorkOrder();
                $CI = & get_instance();
                $nuevo_folio = Doctrine_Core :: getTable('MtnWorkOrder')->lastFolioWo() + 1;
                $workOrder->mtn_work_order_folio = $CI->app->generateFolio($nuevo_folio);
                $workOrder->provider_id = $provider_id;
                $workOrder->asset_id = $asset_id;
                $workOrder->node_id = $node_id;
                $workOrder->mtn_config_state_id = $mtn_config_state_id;
                $workOrder->mtn_work_order_date = $mtn_work_order_date;
                $workOrder->mtn_work_order_requested_by = $mtn_work_order_requested_by;
                $workOrder->mtn_work_order_creator_id = $mtn_work_order_creator_id;

                $asset = Doctrine_Core::getTable('Asset')->find($workOrder->asset_id);
                $provider = Doctrine_Core::getTable('Provider')->find($workOrder->provider_id);
                $node = Doctrine_Core::getTable('Node')->find($asset->node_id);


                $this->syslog->register('add_corrective_ot', array(
                    $workOrder->mtn_work_order_folio,
                    $asset->asset_name,
                    $provider->provider_name,
                    $node->getPath()
                )); // registering log

                $workOrder->save();

                //AGREGA LOG A LA OT
                $MtnStatusLog = new MtnStatusLog();
                $MtnStatusLog->user_id = $this->session->userdata('user_id');
                $MtnStatusLog->mtn_work_order_id = $workOrder->mtn_work_order_id;
                $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id; //CORRECTIVA			
                $MtnStatusLog->mtn_status_log_comments = $this->translateTag('General', 'creation');
                $MtnStatusLog->save();


                $mtn_work_order_id = $workOrder->mtn_work_order_id;
                $mtn_work_order_folio = $workOrder->mtn_work_order_folio;
                $mtn_config_state_id = $workOrder->mtn_config_state_id;
                $msg = $this->translateTag('General', 'operation_successful');
                $success = 'true';
            } else {

                $msg = $this->translateTag('Asset', 'need_provider_asset');
                $success = false;
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
                return;
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $success = 'false';
            $mtn_work_order_id = NULL;
            $mtn_work_order_folio = NULL;
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg,
            'mtn_work_order_id' => $mtn_work_order_id,
            'mtn_work_order_folio' => $mtn_work_order_folio,
            // 'mtn_system_work_order_status_id' => $mtn_system_work_order_status_id,
            'mtn_config_state_id' => $mtn_config_state_id
        ));
        echo $json_data;
    }

    function addCorrectiveNode() {
        try {

            $node_id = $this->input->post('node_id');
            $mtn_config_state_id = $this->input->post('mtn_config_state_id');
            $mtn_work_order_date = $this->input->post('mtn_work_order_date');
            $mtn_work_order_requested_by = $this->input->post('mtn_work_order_requested_by');
            $mtn_work_order_creator_id = $this->session->userdata('user_id');

            $node = Doctrine_Core::getTable('Node')->find($node_id)->getNode();
            $nodesCantity = $node->getAncestors();

            $resultado = 0;

            $ContractSolo = Doctrine_Core::getTable('ContractNode')->findOneBy('node_id', $node_id);
            if ($ContractSolo) {

                if ($ContractSolo) {
                    $resultado = $ContractSolo->contract_id;
                }
            } else {

                if ($nodesCantity) {
                    foreach ($nodesCantity as $nodes) {
                        $ContractNode = Doctrine_Core::getTable('ContractNode')->findOneBy('node_id', $nodes->node_id);
                        if ($ContractNode) {
                            $resultado = $ContractNode->contract_id;
                        }
                    }
                }
            }

            if ($resultado != 0) {
                $contract = Doctrine_Core::getTable('Contract')->findOneBy('contract_id', $resultado);
                $provider = Doctrine_Core::getTable('Provider')->findOneBy('provider_id', $contract->provider_id);

                //CREA OT
                $workOrder = new MtnWorkOrder();
                $CI = & get_instance();
                $nuevo_folio = Doctrine_Core :: getTable('MtnWorkOrder')->lastFolioWo() + 1;
                $workOrder->mtn_work_order_folio = $CI->app->generateFolio($nuevo_folio);
                $workOrder->provider_id = $provider->provider_id;
                $workOrder->node_id = $node_id;
                $workOrder->mtn_maintainer_type_id = 2;
                $workOrder->mtn_config_state_id = $mtn_config_state_id;
                $workOrder->mtn_work_order_date = $mtn_work_order_date;
                $workOrder->mtn_work_order_requested_by = $mtn_work_order_requested_by;
                $workOrder->mtn_work_order_creator_id = $mtn_work_order_creator_id;
                
//                $asset = Doctrine_Core::getTable('Asset')->find($workOrder->asset_id);
                $provider = Doctrine_Core::getTable('Provider')->find($workOrder->provider_id);
                $node = Doctrine_Core::getTable('Node')->find($node_id);
                
                $this->syslog->register('add_corrective_ot_node', array(
                    $workOrder->mtn_work_order_folio,
                    $node->node_name,
                    $provider->provider_name,
                    $node->getPath()
                )); // registering log
                
                $workOrder->save();

                //AGREGA LOG A LA OT
                $MtnStatusLog = new MtnStatusLog();
                $MtnStatusLog->user_id = $this->session->userdata('user_id');
                $MtnStatusLog->mtn_work_order_id = $workOrder->mtn_work_order_id;
                $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id; //CORRECTIVA			
                $MtnStatusLog->mtn_status_log_comments = $this->translateTag('General', 'creation');
                $MtnStatusLog->save();


                $mtn_work_order_id = $workOrder->mtn_work_order_id;
                $mtn_work_order_folio = $workOrder->mtn_work_order_folio;
                $mtn_config_state_id = $workOrder->mtn_config_state_id;
                $msg = $this->translateTag('General', 'operation_successful');
                $success = 'true';
            } else {

                $msg = $this->translateTag('Maintenance', 'no_supplier');
                $success = false;
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
                return;
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $success = 'false';
            $mtn_work_order_id = NULL;
            $mtn_work_order_folio = NULL;
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg,
            'mtn_work_order_id' => $mtn_work_order_id,
            'mtn_work_order_folio' => $mtn_work_order_folio,
            // 'mtn_system_work_order_status_id' => $mtn_system_work_order_status_id,
            'mtn_config_state_id' => $mtn_config_state_id
        ));
        echo $json_data;
    }

    function addPreventive() {
        try {
            $fecha_start = $this->input->post('mtn_work_order_date');
            $mtn_date_finish = $this->input->post('mtn_date_finish');
            $mtn_plan_id = $this->input->post('mtn_plan_id');
            $selected_asset_ids = $this->input->post('asset_id');
            $arregloAsset = explode(",", $selected_asset_ids);
            $fecha_start = strtotime($fecha_start);
            $mtn_date_finish = strtotime($mtn_date_finish);


            $MtnConfigState = Doctrine_Core :: getTable('MtnConfigState')->findByBefore(2, 1); //2-> PREVENTIVO 1-> DE ASIGNADO
            // VALIDAR QUE EXISTA CONFIG ESTADO PARA EL TIPO DE OT PREVENTIVA
            if (!$MtnConfigState) {


                return;
            }

            $mtn_config_state_id = $MtnConfigState['mtn_config_state_id'];

            if (!empty($mtn_config_state_id)) {//SOLO ENTRA SI EXISTE UNA CONFIGURACION DE ESTADO VALIDA
                foreach ($arregloAsset as $asset_id) { //RECORRE LOS ASSET
                    $asset = Doctrine_Core::getTable('Asset')->find($asset_id);

                    $MtnWorkOrderTable = Doctrine_Core :: getTable('MtnWorkOrder')->retrieveProvider($asset_id);
                    $MtnPlanTaskTable = Doctrine_Core :: getTable('MtnWorkOrder')->retrievePlanTask($mtn_plan_id);
                    $provider_id = $asset->provider_id;

                    foreach ($MtnPlanTaskTable as $MtnPlanTask) { //RECORRE LAS TAREAS
                        $mtn_task_id = $MtnPlanTask['mtn_task_id'];
                        $interval = $MtnPlanTask['mtn_plan_task_interval'];
                        $fecha_aux = strtotime("+" . $interval . "days", $fecha_start);

                        while ($fecha_aux < $mtn_date_finish) { //RECORRE INTERVALO POR CADA TAREA
                            $fecha_insert = date('Y-m-d', $fecha_aux);
                            $MtnWOTable = Doctrine_Core :: getTable('MtnWorkOrder')->retrieveWO($fecha_insert, $asset_id);

                            if (isset($MtnWOTable[0]['mtn_work_order_id']) && $MtnWOTable[0]['mtn_work_order_closed'] == 0) {
                                $MtnWorkOrderTask = new MtnWorkOrderTask();
                                $MtnWorkOrderTask->mtn_work_order_id = $MtnWOTable[0]['mtn_work_order_id'];
                                $MtnWorkOrderTask->mtn_task_id = $mtn_task_id;
                                $MtnWorkOrderTask->save();
                            } else {
                                $MtnWorkOrderDB = new MtnWorkOrder();
                                $CI = & get_instance();
                                $nuevo_folio = Doctrine_Core :: getTable('MtnWorkOrder')->lastFolioWo() + 1;
                                $MtnWorkOrderDB->mtn_work_order_folio = $CI->app->generateFolio($nuevo_folio);
                                $MtnWorkOrderDB->asset_id = $asset_id;
                                $MtnWorkOrderDB->mtn_work_order_creator_id = $this->session->userdata('user_id');
                                //$MtnWorkOrderDB->mtn_work_order_requested_by = $this->session->userdata('user_id');
                                // $MtnWorkOrderDB->mtn_work_order_type_id = 2;esto era antes
                                $MtnWorkOrderDB->provider_id = $provider_id;
                                //$MtnWorkOrderDB->mtn_system_work_order_status_id = 1; //asignado esto era antes 

                                $MtnWorkOrderDB->mtn_config_state_id = $mtn_config_state_id;
                                $MtnWorkOrderDB->mtn_work_order_date = $fecha_insert; //fecha de inicio de la OT
                                $MtnWorkOrderDB->mtn_work_order_comment = $this->input->post('mtn_work_order_comment');
                                $MtnWorkOrderDB->save();

                                $MtnWorkOrderTask = new MtnWorkOrderTask();
                                $MtnWorkOrderTask->mtn_work_order_id = $MtnWorkOrderDB->mtn_work_order_id;
                                $MtnWorkOrderTask->mtn_task_id = $mtn_task_id;
                                $MtnWorkOrderTask->save();

                                //AGREGA LOG A LA OT
                                $MtnStatusLog = new MtnStatusLog();
                                $MtnStatusLog->user_id = $this->session->userdata('user_id');
                                $MtnStatusLog->mtn_work_order_id = $MtnWorkOrderDB->mtn_work_order_id;
                                $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id;
                                $MtnStatusLog->mtn_status_log_comments = $this->translateTag('General', 'creation');
                                $MtnStatusLog->save();

                                $node = Doctrine_Core::getTable('Node')->find($asset->node_id);

                                $this->syslog->register('add_preventive_ot', array(
                                    $MtnWorkOrderDB->mtn_work_order_folio,
                                    $asset->asset_name,
                                    $provider->provider_name,
                                    $node->getPath()
                                )); // registering log
                            }

                            //INCREMENTA EL INTERVALO NUEVAMENTE
                            $fecha_aux = strtotime("+" . $interval . "days", $fecha_aux);
                        }
                    }
                }
            } //FIN CONFIG ESTADO VALIDO

            $msg = $this->translateTag('General', 'operation_successful');
            $success = 'true';
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    function addPreventiveByNode() {
        try {
            $fecha_start = $this->input->post('mtn_work_order_date');
            $mtn_date_finish = $this->input->post('mtn_date_finish');
            $mtn_plan_id = $this->input->post('mtn_plan_id');
            $node_ids = $this->input->post('node_id');
            $arregloNode = explode(",", $node_ids);
            $fecha_start = strtotime($fecha_start);
            $mtn_date_finish = strtotime($mtn_date_finish);


            $MtnConfigState = Doctrine_Core :: getTable('MtnConfigState')->findByBefore(8, 1); //2-> PREVENTIVO 1-> DE ASIGNADO
            // VALIDAR QUE EXISTA CONFIG ESTADO PARA EL TIPO DE OT PREVENTIVA
            if (!$MtnConfigState) {


                return;
            }

            $mtn_config_state_id = $MtnConfigState['mtn_config_state_id'];



            if (!empty($mtn_config_state_id)) {//SOLO ENTRA SI EXISTE UNA CONFIGURACION DE ESTADO VALIDA
                foreach ($arregloNode as $node_id) { //RECORRE LOS ASSET
                    //
             //   echo $node_id;
                    // $asset = Doctrine_Core::getTable('Asset')->find($asset_id);
                    //   $ContractNode = Doctrine_Core::getTable('ContractNode')->retrieveContractProviderByNode($node_id);
                    //---AQUI BUSCA EL PROVEDOR DEL NODO
                    $nodePadre = Doctrine_Core::getTable('Node')->find($node_id);

                    $node = $nodePadre->getNode();
                    $NodeType = Doctrine_Core::getTable('NodeType')->find($nodePadre->node_type_id);
                    $ruta = $nodePadre->getPath();
                    $node_type_name = $NodeType->node_type_name;

                    $nodesCantity = $node->getAncestors();

                    $resultado = 0;

                    $ContractSolo = Doctrine_Core::getTable('ContractNode')->findOneBy('node_id', $node_id);
                    if ($ContractSolo) {

                        if ($ContractSolo) {
                            $resultado = $ContractSolo->contract_id;
                        }
                    } else {

                        if ($nodesCantity) {
                            foreach ($nodesCantity as $nodes) {
                                $ContractNode = Doctrine_Core::getTable('ContractNode')->findOneBy('node_id', $nodes->node_id);
                                if ($ContractNode) {
                                    $resultado = $ContractNode->contract_id;
                                }
                            }
                        }
                    }

                    if ($resultado != 0) {

                        $contract = Doctrine_Core::getTable('Contract')->findOneBy('contract_id', $resultado);
                        $provider_id = $contract->provider_id;
                    } else {
                        $msg = "No existe proveedor";
                        $success = false;
                        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                        echo $json_data;
                        return;
                    }


                    //----------FIN EL PROVEDOR DEL NODO



                    $MtnPlanTaskTable = Doctrine_Core :: getTable('MtnWorkOrder')->retrievePlanTask($mtn_plan_id);


                    foreach ($MtnPlanTaskTable as $MtnPlanTask) { //RECORRE LAS TAREAS
                        $mtn_task_id = $MtnPlanTask['mtn_task_id'];
                        $interval = $MtnPlanTask['mtn_plan_task_interval'];
                        $fecha_aux = strtotime("+" . $interval . "days", $fecha_start);

                        while ($fecha_aux < $mtn_date_finish) { //RECORRE INTERVALO POR CADA TAREA
                            $fecha_insert = date('Y-m-d', $fecha_aux);
                            //$MtnWOTable = Doctrine_Core :: getTable('MtnWorkOrder')->retrieveWO($fecha_insert, $asset_id);
                            $MtnWOTable = Doctrine_Core :: getTable('MtnWorkOrder')->retrieveWOByNode($fecha_insert, $node_id);

                            if (isset($MtnWOTable[0]['mtn_work_order_id']) && $MtnWOTable[0]['mtn_work_order_closed'] == 0) {
                                $MtnWorkOrderTask = new MtnWorkOrderTask();
                                $MtnWorkOrderTask->mtn_work_order_id = $MtnWOTable[0]['mtn_work_order_id'];
                                $MtnWorkOrderTask->mtn_task_id = $mtn_task_id;
                                $MtnWorkOrderTask->save();
                            } else {
                                $MtnWorkOrderDB = new MtnWorkOrder();
                                $CI = & get_instance();
                                $nuevo_folio = Doctrine_Core :: getTable('MtnWorkOrder')->lastFolioWo() + 1;
                                $MtnWorkOrderDB->mtn_work_order_folio = $CI->app->generateFolio($nuevo_folio);
                                //   $MtnWorkOrderDB->asset_id = $asset_id;
                                $MtnWorkOrderDB->node_id = $node_id;
                                $MtnWorkOrderDB->mtn_work_order_creator_id = $this->session->userdata('user_id');
                                //$MtnWorkOrderDB->mtn_work_order_requested_by = $this->session->userdata('user_id');
                                // $MtnWorkOrderDB->mtn_work_order_type_id = 2;esto era antes
                                $MtnWorkOrderDB->provider_id = $provider_id;
                                $MtnWorkOrderDB->mtn_maintainer_type_id = 2;
                                //$MtnWorkOrderDB->mtn_system_work_order_status_id = 1; //asignado esto era antes 

                                $MtnWorkOrderDB->mtn_config_state_id = $mtn_config_state_id;
                                $MtnWorkOrderDB->mtn_work_order_date = $fecha_insert; //fecha de inicio de la OT
                                $MtnWorkOrderDB->mtn_work_order_comment = $this->input->post('mtn_work_order_comment');
                                $MtnWorkOrderDB->save();

                                $MtnWorkOrderTask = new MtnWorkOrderTask();
                                $MtnWorkOrderTask->mtn_work_order_id = $MtnWorkOrderDB->mtn_work_order_id;
                                $MtnWorkOrderTask->mtn_task_id = $mtn_task_id;
                                $MtnWorkOrderTask->save();

                                //AGREGA LOG A LA OT
                                $MtnStatusLog = new MtnStatusLog();
                                $MtnStatusLog->user_id = $this->session->userdata('user_id');
                                $MtnStatusLog->mtn_work_order_id = $MtnWorkOrderDB->mtn_work_order_id;
                                $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id;
                                $MtnStatusLog->mtn_status_log_comments = $this->translateTag('General', 'creation');
                                $MtnStatusLog->save();

//                                $node = Doctrine_Core::getTable('Node')->find($node_id);
//                                $this->syslog->register('add_preventive_ot', array(
//                                    $MtnWorkOrderDB->mtn_work_order_folio,
//                                    $node->node_name,
//                                    $provider->provider_name,
//                                    $node->getPath()
//                                )); // registering log
                            }

                            //INCREMENTA EL INTERVALO NUEVAMENTE
                            $fecha_aux = strtotime("+" . $interval . "days", $fecha_aux);
                        }
                    }
                }
            } //FIN CONFIG ESTADO VALIDO

            $msg = $this->translateTag('General', 'operation_successful');
            $success = 'true';
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    function update() {
        try {
            $mtn_work_order_id = $this->input->post('mtn_work_order_id');
            $request_by = $this->session->userdata('user_id');
            $mtn_work_order_requested_by = $this->input->post('mtn_work_order_requested_by');
            $mtn_work_order_comment = $this->input->post('mtn_work_order_comment');
            $work_order_status = $this->input->post('mtn_work_order_status');
            $mtn_work_order_closed = $this->input->post('mtn_work_order_closed');

            $workOrder = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
//VALORES ANTERIORES
            $mtn_work_order_requested_by_DB = $workOrder->mtn_work_order_requested_by;
            $mtn_work_order_comment_DB = $workOrder->mtn_work_order_comment;
            $mtn_work_order_status_DB = $workOrder->mtn_work_order_status;

//VALORES NUEVOS
            $workOrder->mtn_work_order_status = ($work_order_status == 'true' ? '1' : '0');
            $workOrder->mtn_work_order_closed = (($mtn_work_order_closed == 'true' || $work_order_status == 'true') ? '1' : '0');
            $antes_mtn_work_order_comment = $workOrder->mtn_work_order_comment;
            $workOrder->mtn_work_order_comment = $mtn_work_order_comment;
            $antes_mtn_work_order_requested_by = $workOrder->mtn_work_order_requested_by;
            $workOrder->mtn_work_order_requested_by = $mtn_work_order_requested_by;
            $workOrder->save();

            $asset = Doctrine_Core::getTable('Asset')->find($workOrder->asset_id);

            $log_id = $this->syslog->register('update_ot', array(
                $workOrder->mtn_work_order_folio,
                $asset->asset_name
            )); // registering log

            if ($antes_mtn_work_order_requested_by != $workOrder->mtn_work_order_requested_by) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'requested_by');
                    $logDetail->log_detail_value_old = $antes_mtn_work_order_requested_by;
                    $logDetail->log_detail_value_new = $workOrder->mtn_work_order_requested_by;
                    $logDetail->save();
                }
            }

            if ($antes_mtn_work_order_comment != $workOrder->mtn_work_order_comment) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                    $logDetail->log_detail_value_old = $antes_mtn_work_order_comment;
                    $logDetail->log_detail_value_new = $workOrder->mtn_work_order_comment;
                    $logDetail->save();
                }
            }

            if ($work_order_status == 'true') {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'ot_cancelled');
                    $logDetail->log_detail_value_old = "Falso";
                    $logDetail->log_detail_value_new = "True";
                    $logDetail->save();
                }
            }

            if ($mtn_work_order_closed == 'true') {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'closed_order');
                    $logDetail->log_detail_value_old = "Falso";
                    $logDetail->log_detail_value_new = "True";
                    $logDetail->save();
                }
            }


            $msg = $this->translateTag('General', 'operation_successful');
            $success = 'true';
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg,
            'mtn_work_order_id' => $mtn_work_order_id
        ));
        echo $json_data;
    }

    function updateNode() {
        try {
            $mtn_work_order_id = $this->input->post('mtn_work_order_id');
            $request_by = $this->session->userdata('user_id');
            $mtn_work_order_requested_by = $this->input->post('mtn_work_order_requested_by');
            $mtn_work_order_comment = $this->input->post('mtn_work_order_comment');
            $work_order_status = $this->input->post('mtn_work_order_status');
            $mtn_work_order_closed = $this->input->post('mtn_work_order_closed');

            $workOrder = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
//VALORES ANTERIORES
            $mtn_work_order_requested_by_DB = $workOrder->mtn_work_order_requested_by;
            $mtn_work_order_comment_DB = $workOrder->mtn_work_order_comment;
            $mtn_work_order_status_DB = $workOrder->mtn_work_order_status;

//VALORES NUEVOS
            $mtn_work_order_date = $this->input->post('mtn_work_order_date');
            $workOrder->mtn_work_order_status = ($work_order_status == 'true' ? '1' : '0');
            $workOrder->mtn_work_order_closed = (($mtn_work_order_closed == 'true' || $work_order_status == 'true') ? '1' : '0');
            $antes_mtn_work_order_comment = $workOrder->mtn_work_order_comment;
            $workOrder->mtn_work_order_comment = $mtn_work_order_comment;
            $antes_mtn_work_order_requested_by = $workOrder->mtn_work_order_requested_by;
            $workOrder->mtn_work_order_date = $mtn_work_order_date;
            $workOrder->mtn_work_order_requested_by = $mtn_work_order_requested_by;
            $workOrder->save();

            $node = Doctrine_Core::getTable('Node')->find($workOrder->node_id);

            $log_id = $this->syslog->register('update_ot_node', array(
                $workOrder->mtn_work_order_folio,
                $node->node_name
            )); // registering log

            if ($antes_mtn_work_order_requested_by != $workOrder->mtn_work_order_requested_by) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'requested_by');
                    $logDetail->log_detail_value_old = $antes_mtn_work_order_requested_by;
                    $logDetail->log_detail_value_new = $workOrder->mtn_work_order_requested_by;
                    $logDetail->save();
                }
            }

            if ($antes_mtn_work_order_comment != $workOrder->mtn_work_order_comment) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                    $logDetail->log_detail_value_old = $antes_mtn_work_order_comment;
                    $logDetail->log_detail_value_new = $workOrder->mtn_work_order_comment;
                    $logDetail->save();
                }
            }

            if ($work_order_status == 'true') {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'ot_cancelled');
                    $logDetail->log_detail_value_old = "Falso";
                    $logDetail->log_detail_value_new = "True";
                    $logDetail->save();
                }
            }

            if ($mtn_work_order_closed == 'true') {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'closed_order');
                    $logDetail->log_detail_value_old = "Falso";
                    $logDetail->log_detail_value_new = "True";
                    $logDetail->save();
                }
            }


            $msg = $this->translateTag('General', 'operation_successful');
            $success = 'true';
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg,
            'mtn_work_order_id' => $mtn_work_order_id
        ));
        echo $json_data;
    }

    function updateState() {
        try {
            $mtn_work_order_id = $this->input->post('mtn_work_order_id');
            $mtn_config_state_id = $this->input->post('mtn_config_state_id');
            $mtn_status_log_comments = $this->input->post('mtn_status_log_comments');

            $workOrder = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
//VALORES ANTERIORES
            $mtn_config_state_id_DB = $workOrder->mtn_config_state_id;
//VALORES NUEVOS
            $workOrder->mtn_config_state_id = $mtn_config_state_id;
            $ahora = $workOrder->mtn_config_state_id;
            $workOrder->save();

            $mtnConfigState = Doctrine_Core :: getTable('MtnConfigState')->find($ahora);
            $mtnSystemWorkOrderStatus = Doctrine_Core :: getTable('MtnSystemWorkOrderStatus')->find($mtnConfigState->mtn_system_work_order_status_id);


//            $asset = Doctrine_Core::getTable('Asset')->find($workOrder->asset_id);
//
//            $log_id = $this->syslog->register('update_ot_state', array(
//                $workOrder->mtn_work_order_folio,
//                $asset->asset_name
//            )); // registering log

//            if ($workOrder->mtn_config_state_id != $mtn_config_state_id) {
//                if ($log_id) {
//                    $logDetail = new LogDetail();
//                    $logDetail->log_id = $log_id;
//                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'change_of_status');
//                    $logDetail->log_detail_value_old = $antes_MtnSystemWorkOrderStatus->mtn_system_work_order_status_name;
//                    $logDetail->log_detail_value_new = $mtnSystemWorkOrderStatus->mtn_system_work_order_status_name;
//                    $logDetail->save();
//                }
//            }



            $MtnStatusLog = new MtnStatusLog();
            $antesMtnStatusLog = $MtnStatusLog->mtn_status_log_comments;

            $antes_MtnConfigState = Doctrine_Core :: getTable('MtnConfigState')->find($mtn_config_state_id_DB);
            $antes_MtnSystemWorkOrderStatus = Doctrine_Core :: getTable('MtnSystemWorkOrderStatus')->find($antes_MtnConfigState->mtn_system_work_order_status_id);
//
//            if ($MtnStatusLog->mtn_config_state_id != $mtn_config_state_id) {
//                if ($log_id) {
//                    $logDetail = new LogDetail();
//                    $logDetail->log_id = $log_id;
//                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'change_of_status');
//                    $logDetail->log_detail_value_old = $antes_MtnSystemWorkOrderStatus->mtn_system_work_order_status_name;
//                    $logDetail->log_detail_value_new = $mtnSystemWorkOrderStatus->mtn_system_work_order_status_name;
//                    $logDetail->save();
//                }
//            }

            if ($mtn_config_state_id_DB != $mtn_config_state_id) {
                //UPDATE LOG A LA OT

                $MtnStatusLog->user_id = $this->session->userdata('user_id');
                $MtnStatusLog->mtn_work_order_id = $workOrder->mtn_work_order_id;
                $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id;
                $MtnStatusLog->mtn_status_log_comments = $mtn_status_log_comments;
                $MtnStatusLog->save();
            }
//
//            if ($antesMtnStatusLog != $mtn_status_log_comments) {
//                if ($log_id) {
//                    $logDetail = new LogDetail();
//                    $logDetail->log_id = $log_id;
//                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
//                    $logDetail->log_detail_value_old = $antesMtnStatusLog;
//                    $logDetail->log_detail_value_new = $mtn_status_log_comments;
//                    $logDetail->save();
//                }
//            }

            $msg = $this->translateTag('General', 'operation_successful');
            $success = 'true';
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg,
            'mtn_work_order_id' => $mtn_work_order_id
        ));
        echo $json_data;
    }
    
    function updateStateNode() {
        try {
            $mtn_work_order_id = $this->input->post('mtn_work_order_id');
            $mtn_config_state_id = $this->input->post('mtn_config_state_id');
            $mtn_status_log_comments = $this->input->post('mtn_status_log_comments');

            $workOrder = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
//VALORES ANTERIORES
            $mtn_config_state_id_DB = $workOrder->mtn_config_state_id;
//VALORES NUEVOS
            $workOrder->mtn_config_state_id = $mtn_config_state_id;
            $ahora = $workOrder->mtn_config_state_id;
            $workOrder->save();

            $mtnConfigState = Doctrine_Core :: getTable('MtnConfigState')->find($ahora);
            $mtnSystemWorkOrderStatus = Doctrine_Core :: getTable('MtnSystemWorkOrderStatus')->find($mtnConfigState->mtn_system_work_order_status_id);
            
            $node = Doctrine_Core::getTable('Node')->find($workOrder->node_id);

            $log_id = $this->syslog->register('update_ot_state_node', array(
                $workOrder->mtn_work_order_folio,
                $node->node_name
            )); // registering log

            if ($workOrder->mtn_config_state_id != $mtn_config_state_id) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'change_of_status');
                    $logDetail->log_detail_value_old = $antes_MtnSystemWorkOrderStatus->mtn_system_work_order_status_name;
                    $logDetail->log_detail_value_new = $mtnSystemWorkOrderStatus->mtn_system_work_order_status_name;
                    $logDetail->save();
                }
            }

            $MtnStatusLog = new MtnStatusLog();
            $antesMtnStatusLog = $MtnStatusLog->mtn_status_log_comments;

            $antes_MtnConfigState = Doctrine_Core :: getTable('MtnConfigState')->find($mtn_config_state_id_DB);
            $antes_MtnSystemWorkOrderStatus = Doctrine_Core :: getTable('MtnSystemWorkOrderStatus')->find($antes_MtnConfigState->mtn_system_work_order_status_id);
            if ($mtn_config_state_id_DB != $mtn_config_state_id) {
                //UPDATE LOG A LA OT

                $MtnStatusLog->user_id = $this->session->userdata('user_id');
                $MtnStatusLog->mtn_work_order_id = $workOrder->mtn_work_order_id;
                $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id;
                $MtnStatusLog->mtn_status_log_comments = $mtn_status_log_comments;
                $MtnStatusLog->save();
            }
            
            
            if ($antesMtnStatusLog != $mtn_status_log_comments) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                    $logDetail->log_detail_value_old = $antesMtnStatusLog;
                    $logDetail->log_detail_value_new = $mtn_status_log_comments;
                    $logDetail->save();
                }
            }

            $msg = $this->translateTag('General', 'operation_successful');
            $success = 'true';
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg,
            'mtn_work_order_id' => $mtn_work_order_id
        ));
        echo $json_data;
    }

    /**
     * Actualiza un conjunto de fechas de la WO 
     * @param post data
     * @return json_data
     */
    function updateDate() {
        try {
            $mtn_work_order = $this->input->post('mtn_work_order_id');
            $mtn_work_order_date = $this->input->post('mtn_work_order_date');
            $arregloWO = explode(",", $mtn_work_order);

            foreach ($arregloWO as $mtn_work_order_id) { //RECORRE LAS Word Order
                $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
                $fecha_antes = $MtnWorkOrderDB->mtn_work_order_date;
                $MtnWorkOrderDB->mtn_work_order_date = $mtn_work_order_date;
                $fecha_despues = $MtnWorkOrderDB->mtn_work_order_date;
                $MtnWorkOrderDB->save();

                $fecha = $this->input->post('asset_purchase_date');
                list($fecha) = explode("T", $fecha);

                $fecha1 = $fecha_despues;
                $fecha2 = date("d/m/Y", strtotime($fecha1));

                $fecha3 = $fecha_antes;
                $fecha4 = date("d/m/Y", strtotime($fecha3));


                $node = Doctrine_Core::getTable('Node')->find($MtnWorkOrderDB->node_id);

                $this->syslog->register('update_ot_date_node', array(
                    $MtnWorkOrderDB->mtn_work_order_folio,
                    $node->node_name,
                    $fecha4,
                    $fecha2
                )); // registering log
            }
            $msg = $this->translateTag('General', 'operation_successful');
            $success = true;
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    function delete() {
        
    }

    function getLogWorkOrder() {

        $mtn_work_order_id = $this->input->post('mtn_work_order_id');
        $MtnStatusLogTable = Doctrine_Core::getTable('MtnStatusLog');
        $MtnStatusLog = $MtnStatusLogTable->retrieveLogByWorkOrderId($mtn_work_order_id);

        if ($MtnStatusLog->count()) {
            echo '({"total":"' . $MtnStatusLog->count() . '", "results":' . $this->json->encode($MtnStatusLog->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

}
