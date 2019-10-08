<?php

/**
 * @package    Controller
 * @subpackage RequestProblemController
 */
class RequestController extends APP_Controller {

    function RequestController() {
        parent::APP_Controller();
    }

    /**
     * get
     * 
     * Lista todos los tipos de equipos existentes
     */
    function getProvider() {
        $request_status_id = $this->input->post('request_status_id');
        $request_problem_id = $this->input->post('request_problem_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $user_id = $this->session->userdata('user_id');
        $UserProvider = Doctrine_Core::getTable('UserProvider')->findOneBy($user_id);

        $filters = array(
            'request_status_id = ?' => (!empty($request_status_id) ? $request_status_id : NULL),
            'request_problem_id = ?' => (!empty($request_problem_id) ? $request_problem_id : NULL),
            'request_date_creation >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'request_date_creation <= ?' => (!empty($end_date) ? $end_date : NULL )
        );

        $request = Doctrine_Core::getTable('Request')->retriveProvider($filters, $UserProvider->provider_id);

        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getSolicitud() {

        $request = Doctrine_Core::getTable('Solicitud')->retrieveAll();
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }
    
    function getSolicitudEstado() {

        $request = Doctrine_Core::getTable('SolicitudEstado')->findAll();
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }
    
    function getSolicitudTipo() {

        $request = Doctrine_Core::getTable('SolicitudType')->findAll();
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }
//    function get() {
//        $request_status_id = $this->input->post('request_status_id');
//        $request_problem_id = $this->input->post('request_problem_id');
//        $node_id = $this->input->post('node_id');
//        $search_branch = $this->input->post('search_branch');
//        $start_date = $this->input->post('start_date');
//        $end_date = $this->input->post('end_date');
//
//        $filters = array(
//            'request_status_id = ?' => (!empty($request_status_id) ? $request_status_id : NULL),
//            'request_problem_id = ?' => (!empty($request_problem_id) ? $request_problem_id : NULL),
//            'request_date_creation >= ?' => (!empty($start_date) ? $start_date : NULL ),
//            'request_date_creation <= ?' => (!empty($end_date) ? $end_date : NULL )
//        );
//
//        $request = Doctrine_Core::getTable('Request')->retriveRequest($filters, $node_id, $search_branch);
//        if ($request->count()) {
//            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
//        } else {
//            echo '({"total":"0", "results":[]})';
//        }
//    }
    	function getLayers ( $obj ) {
	 		
			$buf = array();
			foreach ($obj as $k => $el) {
				 if ($k == "CADConverterDwgEntity" && $el->Layer != "") { $buf[] = (string) $el->Layer; }
	 		}
	 		
	 		$buf2 = array_unique($buf);
	 		return $buf2;
	 		
	 	}
    function getgraph() {

//VALIDACIONES
        $dir = str_replace("\\", "/", $this->config->item('temp_dir'));
        
        $plan = str_replace("\\", "/", $this->config->item('plan_dir'));
        
            //RESCATA LAS SECCIONES
         $this->load->helper('file');
          $svg_xml = simplexml_load_file($plan . 'base_493.svg');
//          $svg_xml = simplexml_load_file($plan . 'base_' . $plan_id . '.svg');
          
         foreach ($this->svg->getLayers($svg_xml) as $layer)
        {
            echo $layer;
           echo '-';
//            $planSection = new PlanSection();
//            $planSection->plan_id = $plan_id;
//            $planSection->plan_section_name = $layer;
//            $planSection->plan_section_color = $this->config->config['plan_default_layer_color'];
//            $planSection->save();
        }
//        $svg_xml = simplexml_load_file($plan . 'base_493.svg');

//        foreach ($this->getLayers($svg_xml) as $layer)
//        {
//           
//           echo $svg_xml;
//           echo '-';
//            $planSection->plan_section_color = $this->config->config['plan_default_layer_color'];
//            $planSection->save();
//        }
        
        exit
        ;

        if ($dir . 'grafico.png') {
            unlink($dir . 'grafico.png');
        }

        //CONFIGURACIONES
        $alto = 350;
        $ancho = 250;
        $this->load->library('graph');
        $data = array(40, 10, 10, 40);
        $leyenda = array("Morenas", "Rubias", "Pelirrojas", "Otras");
        $this->graph = new PieGraph($alto, $ancho);

        $theme_class = "DefaultTheme";
        
	//Definimos el titulo 
	$this->graph->title->Set("Mi primer grafico de tarta");
	$this->graph->title->SetFont(FF_FONT1,FS_BOLD);
        
        $p1 = new PiePlot($data);
	$p1->SetLegends($leyenda);
	$p1->SetCenter(0.4);
        $p1->SetSliceColors(array('#1E90FF', '#2E8B57', '#ADFF2F', '#DC143C'));
        $p1->ShowBorder();
        $p1->SetColor('black');
        $this->graph->Add($p1);
        
        //ESTO CREA UN FOTO
        $this->graph->Stroke($dir . 'grafico.png');
        $this->load->library('pdf');
        $this->pdf->SetFont('helvetica', '', 8);
        
        // add a page
        $this->pdf->AddPage();        
        $this->pdf->Image($dir . 'grafico.png');
        $this->pdf->ImageSVG($file= $plan . 'base_493.svg', $x=5, $y=20, $w='', $h='', $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->pdf->Output('grafico' . '.pdf', 'D');
    }
    
    function getgraphCompleto() {

        //VALIDACIONES
        $dir = str_replace("\\", "/", $this->config->item('temp_dir'));
        $plan = str_replace("\\", "/", $this->config->item('plan_dir'));
        
        //BORRA Y CREA EL NUEVO GRAFICO EN LA CARPETA TEMP
        if ($dir . 'grafico.png') {
            unlink($dir . 'grafico.png');
        }

        //CONFIGURACIONES
        $alto = 350;
        $ancho = 250;
        $this->load->library('graph');
        $data = array(40, 10, 10, 40);
        $leyenda = array("Morenas", "Rubias", "Pelirrojas", "Otras");
        $this->graph = new PieGraph($alto, $ancho);

        $theme_class = "DefaultTheme";

	//Definimos el titulo 
	$this->graph->title->Set("Mi primer grafico de tarta");
	$this->graph->title->SetFont(FF_FONT1,FS_BOLD);
        
        $p1 = new PiePlot($data);
	$p1->SetLegends($leyenda);
	$p1->SetCenter(0.4);
        
        $p1->SetSliceColors(array('#1E90FF', '#2E8B57', '#ADFF2F', '#DC143C'));
        $p1->ShowBorder();
        $p1->SetColor('black');
        $this->graph->Add($p1);
        
        //ESTO CREA UN FOTO
        $this->graph->Stroke($dir . 'grafico.png');
        
        $this->load->library('pdf');
        $this->pdf->SetFont('helvetica', '', 8);
        
        // add a page
        $this->pdf->AddPage();        
        $this->pdf->Image($dir . 'grafico.png');
        $this->pdf->ImageSVG($file= $plan . 'base_493.svg', $x=5, $y=20, $w='', $h='', $link='', $align='', $palign='', $border=0, $fitonpage=false);
        $this->pdf->Output('grafico' . '.pdf', 'D');
    }

    function getByNode() {
        $request_status_id = $this->input->post('request_status_id');
        $request_problem_id = $this->input->post('request_problem_id');
        $node_id = $this->input->post('node_id');
        $search_branch = $this->input->post('search_branch');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $request_mail = $this->input->post('request_mail');

        $filters = array(
            'request_status_id = ?' => (!empty($request_status_id) ? $request_status_id : NULL),
            'request_problem_id = ?' => (!empty($request_problem_id) ? $request_problem_id : NULL),
            'request_date_creation >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'request_date_creation <= ?' => (!empty($end_date) ? $end_date : NULL ),
            'request_mail LIKE ?' => (!empty($request_mail) ? '%'.$request_mail . '%' : NULL )
        );

        $requests = Doctrine_Core::getTable('Request')->retriveRequestByNode($filters, $node_id, $search_branch);

        //$requests = $request->toArray();
        foreach ($requests->toArray() as $key => $value) {

            $final[] = $value;

            $Node = Doctrine_Core::getTable('Node')->find($value['node_id']);
            $AuxNode = $Node->getPath();

            $final[$key]['node_name'] = $Node->node_name;
            $final[$key]['node_ruta'] = $AuxNode;
        }



        if ($requests->count()) {
            echo '({"total":"' . $requests->count() . '", "results":' . $this->json->encode($final) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() {
        try {
            $nuevo_folio = Doctrine_Core :: getTable('Request')->lastFolioWo() + 1;
            $nuevo_folio_conceros = $this->generateFolioLocal($nuevo_folio);

            $request = new Request();
            $request->fromArray($this->input->postall());
            $request->user_id = $this->session->userdata('user_id');

            $asset = Doctrine_Core::getTable('Asset')->find($request->asset_id);
            $node_asset = Doctrine_Core::getTable('Node')->find($asset->node_id);


            $this->syslog->register('add_request', array(
                $nuevo_folio_conceros,
                $asset->asset_name,
                $node_asset->getPath()
            )); // registering log

            $request->request_date_creation = now();
            $request->request_mail = $this->input->post('request_mail');
            $request->request_fono = $this->input->post('request_fono');

            $request->request_folio = $nuevo_folio_conceros;
            $request->save();
            $this->sendNotification($request->request_id);
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function addByNode() {
        try {
            $nuevo_folio = Doctrine_Core :: getTable('Request')->lastFolioWo() + 1;
            $nuevo_folio_conceros = $this->generateFolioLocal($nuevo_folio);

            $request = new Request();
            $request->fromArray($this->input->postall());
            $request->user_id = $this->session->userdata('user_id');

            $node = Doctrine_Core::getTable('Node')->find($request->node_id);
            $this->syslog->register('add_request_node', array(
                $nuevo_folio_conceros,
                $node->node_name,
                $node->getPath()
            )); // registering log

            $request->request_date_creation = now();
            $request->request_mail = $this->input->post('request_mail');
            $request->request_fono = $this->input->post('request_fono');
            $request->node_id = $this->input->post('node_id');

            $request->request_folio = $nuevo_folio_conceros;
            $request->save();

            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }


        // ESTO ESTA PENDIENTE
        $this->sendNotificationByNode($request->request_id);



        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function update() {
        $request_status_id = $this->input->post('request_status_id');

        //--(APROVADA)-- CREA OT Y ACTUALIZA ESTADO 
        if ($request_status_id == 2) {
            try {
                $request = Doctrine_Core::getTable('Request')->find($this->input->post('request_id'));
                if ($request->request_status_id == 1) {
                    $AssetTable = Doctrine_Core :: getTable('Asset')->find($request->asset_id);
                    $provider_id = $AssetTable['provider_id'];
                    if ($provider_id) {
                        $workOrder = new MtnWorkOrder();
                        $CI = & get_instance();
                        $nuevo_folio = Doctrine_Core :: getTable('MtnWorkOrder')->lastFolioWo() + 1;
                        $workOrder->mtn_work_order_folio = $CI->app->generateFolio($nuevo_folio);
                        $workOrder->mtn_work_order_date = $request->request_date_creation;
                        $workOrder->mtn_work_order_requested_by = $request->request_requested_by;
//                        $workOrder->node_id = $AssetTable->node_id;
                        $workOrder->provider_id = $provider_id;
                        $workOrder->asset_id = $request->asset_id;

                        $user_id = $this->session->userdata('user_id');
                        $User = Doctrine_Core :: getTable('User')->findOneBy('user_id', $user_id);
                        $user_type = $User['user_type'];

                        if ($user_type == 'N' || $user_type == 'A' || $user_type == 'S') {
                            //ACA ENTRA SI ES USUARIO NORMAL O ADMINISTRADOR O SYSTEM (ACCESOS)
                            $ConfigStateTable = Doctrine_Core :: getTable('MtnConfigState')->retrieveByMenorUser(1); // 1 = CORRECTIVA
                            $mtn_config_state_id = $ConfigStateTable['mtn_config_state_id'];
                        } else {
                            //ACA ENTRA SOLO SI ES PROVEDOR (ACCESOS)
                            $ConfigStateTable = Doctrine_Core :: getTable('MtnConfigState')->retrieveByMenorProvider(1); // 1 = CORRECTIVA
                            $mtn_config_state_id = $ConfigStateTable['mtn_config_state_id'];
                        }

                        $workOrder->mtn_config_state_id = $mtn_config_state_id;
                        $workOrder->mtn_work_order_creator_id = $this->session->userdata('user_id');
//                        $workOrder->mtn_system_work_order_status_id = 5; //aprovar trabajo
                        $workOrder->mtn_work_order_comment = $request->request_description; //aprovar trabajo

                        $asset = Doctrine_Core::getTable('Asset')->find($workOrder->asset_id);
                        $provider = Doctrine_Core::getTable('Provider')->find($workOrder->provider_id);
                        $node = Doctrine_Core::getTable('Node')->find($asset->node_id);


                        $this->syslog->register('update_request', array(
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
                        $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id;
                        $MtnStatusLog->mtn_status_log_comments = 'Creacion';
                        $MtnStatusLog->save();
                    } else {
                        $success = 'false';
                        $msg = $this->translateTag('Asset', 'need_provider_asset');
                    }
                    $request = Doctrine_Core::getTable('Request')->find($this->input->post('request_id'));
                    $request->request_status_id = $this->input->post('request_status_id');
                    $request->mtn_work_order_id = $workOrder->mtn_work_order_id; //SE ASIGA EL ID DE LA OT CREADA              
                    $request->request_requested_by_comment = $this->translateTag('Request', 'work_order_number') . $workOrder->mtn_work_order_folio;
                    $request->save();

                    $success = 'true';
                    $msg = $this->translateTag('General', 'operation_successful');
                } else {
                    $success = 'false';
                    $msg = $this->translateTag('Request', 'only_the_state_can_change_applications_issued');
                }
            } catch (Exception $e) {
                $success = 'false';
                $msg = $e->getMessage();
            }
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
        }

        //--(RECHAZADA)---- SOLO ACTUALIZA ESTADO   
        if ($request_status_id == 3) {
            try {
                $request = Doctrine_Core::getTable('Request')->find($this->input->post('request_id'));
                if ($request->request_status_id == 1) {

                    $request->request_status_id = $this->input->post('request_status_id');
                    //              $this->generateFolioLocal($request->mtn_work_order_id);

                    $request->request_requested_by_comment = $this->input->post('request_requested_by_comment');
                    //  $asset = Doctrine_Core::getTable('Asset')->find($request->asset_id);
                    //  $node = Doctrine_Core::getTable('Node')->find($asset->node_id);
                    //$nuevo_folio = Doctrine_Core::getTable('Request')->find($this->input->post('request_id'));
//                    $this->syslog->register('rejected_request', array(
//                        $request->request_folio,
//                        $asset->asset_name,
//                        $node->getPath()
//                    )); // registering log

                    $request->save();

                    $success = 'true';
                    $msg = $this->translateTag('General', 'operation_successful');
                } else {
                    $success = 'false';
                    $msg = $this->translateTag('Request', 'only_the_state_can_change_applications_issued');
                }
            } catch (Exception $e) {
                $success = 'false';
                $msg = $e->getMessage();
            }
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
        }
    }

    function updateByNode() {
        $request_status_id = $this->input->post('request_status_id');

        //--(APROVADA)-- CREA OT Y ACTUALIZA ESTADO 
        if ($request_status_id == 2) {
            try {
                $request = Doctrine_Core::getTable('Request')->find($this->input->post('request_id'));
                if ($request->request_status_id == 1) {
//                    $AssetTable = Doctrine_Core :: getTable('Asset')->find($request->asset_id);
//                    $provider_id = $AssetTable['provider_id'];
//                    if ($provider_id) {
                    $workOrder = new MtnWorkOrder();
                    $CI = & get_instance();
                    $nuevo_folio = Doctrine_Core :: getTable('MtnWorkOrder')->lastFolioWo() + 1;
                    $workOrder->mtn_work_order_folio = $CI->app->generateFolio($nuevo_folio);
                    $workOrder->mtn_work_order_date = $request->request_date_creation;
                    $workOrder->mtn_work_order_requested_by = $request->request_requested_by;
                    $workOrder->node_id = $request->node_id;
                    $workOrder->mtn_maintainer_type_id = 2;
                    $workOrder->provider_id = $this->input->post('provider_id');
                    //    $workOrder->asset_id = $request->asset_id;

                    $user_id = $this->session->userdata('user_id');
                    $User = Doctrine_Core :: getTable('User')->findOneBy('user_id', $user_id);
                    $user_type = $User['user_type'];

                    if ($user_type == 'N' || $user_type == 'A' || $user_type == 'S') {
                        //ACA ENTRA SI ES USUARIO NORMAL O ADMINISTRADOR O SYSTEM (ACCESOS)
                        $ConfigStateTable = Doctrine_Core :: getTable('MtnConfigState')->retrieveByMenorUser(1); // 1 = CORRECTIVA
                        $mtn_config_state_id = $ConfigStateTable['mtn_config_state_id'];
                    } else {
                        //ACA ENTRA SOLO SI ES PROVEDOR (ACCESOS)
                        $ConfigStateTable = Doctrine_Core :: getTable('MtnConfigState')->retrieveByMenorProvider(1); // 1 = CORRECTIVA
                        $mtn_config_state_id = $ConfigStateTable['mtn_config_state_id'];
                    }

                    $workOrder->mtn_config_state_id = $mtn_config_state_id;
                    $workOrder->mtn_work_order_creator_id = $this->session->userdata('user_id');
//                        $workOrder->mtn_system_work_order_status_id = 5; //aprovar trabajo
                    $workOrder->mtn_work_order_comment = $request->request_description; //aprovar trabajo
                    $workOrder->save();


                        $node = Doctrine_Core::getTable('Node')->find($workOrder->node_id);
                        $provider = Doctrine_Core::getTable('Provider')->find($workOrder->provider_id);


                        $this->syslog->register('update_request_node', array(
                            $workOrder->mtn_work_order_folio,
                            $node->node_name,
                            $provider->provider_name,
                            $node->getPath()
                        )); // registering log
                        //
                    //AGREGA LOG A LA OT
                    $MtnStatusLog = new MtnStatusLog();
                    $MtnStatusLog->user_id = $this->session->userdata('user_id');
                    $MtnStatusLog->mtn_work_order_id = $workOrder->mtn_work_order_id;
                    $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id;
                    $MtnStatusLog->mtn_status_log_comments = 'Creacion';
                    $MtnStatusLog->save();
//                    } else {
//                        $success = 'false';
//                        $msg = $this->translateTag('Asset', 'need_provider_asset');
//                    }
                    //SE ACTUALIZA LA SOLICITUD CON LA WORK ORDER CREADA
                    $request = Doctrine_Core::getTable('Request')->find($this->input->post('request_id'));
                    $request->request_status_id = $this->input->post('request_status_id');
                    $request->mtn_work_order_id = $workOrder->mtn_work_order_id; //SE ASIGA EL ID DE LA OT CREADA              
                    $request->request_requested_by_comment = $this->translateTag('Request', 'work_order_number') . $workOrder->mtn_work_order_folio;
                    $request->save();

                    $success = 'true';
                    $msg = $this->translateTag('General', 'operation_successful');
                } else {
                    $success = 'false';
                    $msg = $this->translateTag('Request', 'only_the_state_can_change_applications_issued');
                }
            } catch (Exception $e) {
                $success = 'false';
                $msg = $e->getMessage();
            }
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
        }

        //--(RECHAZADA)---- SOLO ACTUALIZA ESTADO   
        if ($request_status_id == 3) {
            try {
                $request = Doctrine_Core::getTable('Request')->find($this->input->post('request_id'));
                if ($request->request_status_id == 1) {

                    $request->request_status_id = $this->input->post('request_status_id');
                    $request->request_requested_by_comment = $this->input->post('request_requested_by_comment');
                    
                    $node = Doctrine_Core::getTable('Node')->find($request->node_id);
//                    $nuevo_folio = Doctrine_Core::getTable('Request')->find($this->input->post('request_id'));
                    
                    $this->syslog->register('rejected_request_node', array(
                        $request->request_folio,
                        $node->node_name,
                        $node->getPath()
                    )); // registering log

                    $request->save();

                    $success = 'true';
                    $msg = $this->translateTag('General', 'operation_successful');
                } else {
                    $success = 'false';
                    $msg = $this->translateTag('Request', 'only_the_state_can_change_applications_issued');
                }
            } catch (Exception $e) {
                $success = 'false';
                $msg = $e->getMessage();
            }
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
        }
    }

    function export() {
        //--- HEADER EXCEL--
        $node_id = $this->input->post('node_id');
        $search_branch = $this->input->post('search_branch');
        $this->load->library('PHPExcel');
        $sheetIndex = 0;
        $sheet = $this->phpexcel->setActiveSheetIndex($sheetIndex);
        $sheet->setTitle($this->translateTag('Request', 'requests'));
        $sheet->setCellValue('A1', $this->translateTag('Request', 'request_export_folio'))
                ->setCellValue('B1', $this->translateTag('Request', 'request_export_asset'))
                ->setCellValue('C1', $this->translateTag('Request', 'request_export_location'))
                ->setCellValue('D1', $this->translateTag('Request', 'request_export_problem'))
                ->setCellValue('E1', $this->translateTag('Request', 'request_export_subject'))
                ->setCellValue('F1', $this->translateTag('Request', 'request_export_creation_date'))
                ->setCellValue('G1', $this->translateTag('Request', 'request_export_state'));

        //-----FIN HEADER--------
        //-----BODY EXCEL----
        $request_status_id = $this->input->post('request_status_id');
        $request_problem_id = $this->input->post('request_problem_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $filters = array(
            'request_status_id = ?' => (!empty($request_status_id) ? $request_status_id : NULL),
            'request_problem_id = ?' => (!empty($request_problem_id) ? $request_problem_id : NULL),
            'request_date_creation >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'request_date_creation <= ?' => (!empty($end_date) ? $end_date : NULL )
        );
        $requests = Doctrine_Core::getTable('Request')->retriveRequest($filters, $node_id, $search_branch);

        $rcount = 1;
        foreach ($requests as $request) {

            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $request->request_folio, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('B' . $rcount, $request->Asset->asset_name)
                    ->setCellValue('C' . $rcount, $request->Asset->asset_path)
                    ->setCellValue('D' . $rcount, $request->RequestProblem->request_problem_name)
                    ->setCellValue('E' . $rcount, $request->request_subject)
                    ->setCellValue('F' . $rcount, $request->request_date_creation)
                    ->setCellValue('G' . $rcount, $request->RequestStatus->request_status_name);
        }

        //---FOOTER DEL EXCEL--
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);

        $sheet->getStyle('A1:G1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:G1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:G' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $this->phpexcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');

        $objWriter->save('./temp/' . $this->input->post('file_name') . '.xls');
        echo '{"success": true, "file": "temp/' . $this->input->post('file_name') . '.xls"}';

        $this->syslog->register('export_list_request', array(
            $this->input->post('file_name') . '.xls'
        )); // registering log
    }

    function exportByNode() {
        //--- HEADER EXCEL--
        $node_id = $this->input->post('node_id');
        $search_branch = $this->input->post('search_branch');
        $this->load->library('PHPExcel');
        $sheetIndex = 0;
        $sheet = $this->phpexcel->setActiveSheetIndex($sheetIndex);
        $sheet->setTitle($this->translateTag('Request', 'requests'));
        $sheet->setCellValue('A1', $this->translateTag('Request', 'request_export_folio'))
                ->setCellValue('B1', 'Nombre Recinto')
                ->setCellValue('C1', $this->translateTag('Request', 'request_export_location'))
                ->setCellValue('D1', 'FALLA')
                ->setCellValue('E1', $this->translateTag('Request', 'request_export_subject'))
                ->setCellValue('F1', $this->translateTag('Request', 'request_export_creation_date'))
                ->setCellValue('G1', $this->translateTag('Request', 'request_export_state'));

        //-----FIN HEADER--------
        //-----BODY EXCEL----
        $request_status_id = $this->input->post('request_status_id');
        $request_problem_id = $this->input->post('request_problem_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $request_mail = $this->input->post('request_mail');

        $filters = array(
            'request_status_id = ?' => (!empty($request_status_id) ? $request_status_id : NULL),
            'request_problem_id = ?' => (!empty($request_problem_id) ? $request_problem_id : NULL),
            'request_date_creation >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'request_date_creation <= ?' => (!empty($end_date) ? $end_date : NULL ),
            'request_mail LIKE ?' => (!empty($request_mail) ? '%'.$request_mail . '%' : NULL )
        );
        //  $requests = Doctrine_Core::getTable('Request')->retriveRequest($filters, $node_id, $search_branch);

        $rcount = 1;

        $requests = Doctrine_Core::getTable('Request')->retriveRequestByNode($filters, $node_id, $search_branch);

        //$requests = $request->toArray();
        foreach ($requests as  $request) {

            $Node = Doctrine_Core::getTable('Node')->find($request['node_id']);

            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $request->request_folio, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('B' . $rcount, $Node->node_name)
                    ->setCellValue('C' . $rcount, $Node->getPath())
                    ->setCellValue('D' . $rcount, $request->RequestProblem->request_problem_name)
                    ->setCellValue('E' . $rcount, $request->request_subject)
                    ->setCellValue('F' . $rcount, $request->request_date_creation)
                    ->setCellValue('G' . $rcount, $request->RequestStatus->request_status_name);
        }





        //---FOOTER DEL EXCEL--
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);

        $sheet->getStyle('A1:G1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:G1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:G' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $this->phpexcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');

        $objWriter->save('./temp/' . $this->input->post('file_name') . '.xls');
        echo '{"success": true, "file": "temp/' . $this->input->post('file_name') . '.xls"}';

        $this->syslog->register('export_list_request', array(
            $this->input->post('file_name') . '.xls'
        )); // registering log
    }

    function exportProvider() {
        //--- HEADER EXCEL--
        $this->load->library('PHPExcel');
        $sheetIndex = 0;
        $sheet = $this->phpexcel->setActiveSheetIndex($sheetIndex);
        $sheet->setTitle($this->translateTag('Request', 'requests'));
        $sheet->setCellValue('A1', $this->translateTag('Request', 'request_export_folio'))
                ->setCellValue('B1', $this->translateTag('Request', 'request_export_asset'))
                ->setCellValue('C1', $this->translateTag('Request', 'request_export_location'))
                ->setCellValue('D1', $this->translateTag('Request', 'request_export_problem'))
                ->setCellValue('E1', $this->translateTag('Request', 'request_export_subject'))
                ->setCellValue('F1', $this->translateTag('Request', 'request_export_creation_date'))
                ->setCellValue('G1', $this->translateTag('Request', 'request_export_state'));

        //-----FIN HEADER--------
        //-----BODY EXCEL----
        $request_status_id = $this->input->post('request_status_id');
        $request_problem_id = $this->input->post('request_problem_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $user_id = $this->session->userdata('user_id');
        $UserProvider = Doctrine_Core::getTable('UserProvider')->findOneBy($user_id);

        $filters = array(
            'request_status_id = ?' => (!empty($request_status_id) ? $request_status_id : NULL),
            'request_problem_id = ?' => (!empty($request_problem_id) ? $request_problem_id : NULL),
            'request_date_creation >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'request_date_creation <= ?' => (!empty($end_date) ? $end_date : NULL )
        );
        $requests = Doctrine_Core::getTable('Request')->retriveProvider($filters, $UserProvider->provider_id);

        $rcount = 1;
        foreach ($requests as $request) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $request->request_folio, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('B' . $rcount, $request->Asset->asset_name)
                    ->setCellValue('C' . $rcount, $request->Asset->asset_path)
                    ->setCellValue('D' . $rcount, $request->RequestProblem->request_problem_name)
                    ->setCellValue('E' . $rcount, $request->request_subject)
                    ->setCellValue('F' . $rcount, $request->request_date_creation)
                    ->setCellValue('G' . $rcount, $request->RequestStatus->request_status_name);
        }

        //---FOOTER DEL EXCEL--
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);

        $sheet->getStyle('A1:G1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:G1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:G' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $this->phpexcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');

        $objWriter->save('./temp/' . $this->input->post('file_name') . '.xls');
        echo '{"success": true, "file": "temp/' . $this->input->post('file_name') . '.xls"}';
    }

    function generateFolioLocal($number, $number_zeros = 11) {
        $string_folio = str_pad($number, $number_zeros, '0', STR_PAD_LEFT);
        return $string_folio;
    }

    function sendNotification($request_id) {
        $q = Doctrine_Query::create()
                ->select('r.*,a.*, rs.*, rp.*')
                ->from('Request r')
                ->innerJoin('r.RequestProblem rp')
                ->innerJoin('r.Asset a')
                ->innerJoin('a.Node n')
                ->leftJoin('r.RequestStatus rs')
                ->where('r.request_id = ?', $request_id);


        $results = $q->fetchOne();

        $asset = Doctrine_Core::getTable('Asset')->find($results->Asset->asset_id);
        $node_asset = Doctrine_Core::getTable('Node')->find($asset->node_id);

        $CI = & get_instance();
        $CI->load->library('NotificationUser');

        $to = trim($results['request_mail']); //CORREO DESTINATARIO

        $subject = $this->translateTag('Request', 'request_problem'); //ASUNTO

        $body = $this->translateTag('General', 'requested_by') . $results['request_requested_by'] . "\n"; //CUERPO DEL MENSAJE
        $body .= $this->translateTag('General', 'phone') . $results['request_fono'] . "\n";
        $body .= $this->translateTag('Request', 'team') . $results['Asset']['asset_name'] . "\n";
        $body .= $this->translateTag('Request', 'subject') . $results['request_subject'] . "\n";
        $body .= $this->translateTag('Request', 'failure') . $results['RequestProblem']['request_problem_name'] . "\r\n";
        $body .= $this->translateTag('Core', 'location') . $node_asset->getPath() . "\r\n";

        $CI->notificationuser->mail($to, $subject, $body);
        $body = '';
    }

    function sendNotificationByNode($request_id) {


        $q = Doctrine_Query::create()
                ->select('r.*, rs.*, rp.*')
                ->from('Request r')
                ->innerJoin('r.RequestProblem rp')
//               // ->innerJoin('r.Asset a')
                ->innerJoin('r.Node n')
//                ->leftJoin('r.RequestStatus rs')
                ->where('r.request_id = ?', $request_id);


        $results = $q->fetchOne();

        $Node = Doctrine_Core::getTable('Node')->find($results->node_id);


        $CI = & get_instance();
        $CI->load->library('NotificationUser');

        $to = trim($results['request_mail']); //CORREO DESTINATARIO

        $subject = $this->translateTag('Request', 'request_problem'); //ASUNTO

        $body = $this->translateTag('General', 'requested_by') . ' : ' . $results['request_requested_by'] . "\n"; //CUERPO DEL MENSAJE
        $body .= $this->translateTag('General', 'phone') . ' : ' . $results['request_fono'] . "\n";
        $body .= 'Nombre Recinto' . ' : ' . $Node->node_name . "\n";
        $body .= $this->translateTag('Request', 'subject') . ' : ' . $results['request_subject'] . "\n";
        $body .= $this->translateTag('Request', 'failure') . ' : ' . $results['RequestProblem']['request_problem_name'] . "\r\n";
        $body .= $this->translateTag('Core', 'location') . ' : ' . $Node->getPath() . "\r\n";


        $CI->notificationuser->mail($to, $subject, $body);
        $body = '';
    }

}
