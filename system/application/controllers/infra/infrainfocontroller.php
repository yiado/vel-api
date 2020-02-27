<?php

/**
 * @package    Controller
 * @subpackage infrainfoController
 */
class infraInfoController extends APP_Controller {

    function infrainfoController() {
        parent::APP_Controller();
    }

    /** get
     * 
     * Obtiene la informaci�n de un nodo.
     * Recibe como parametro el node_id o el node_type_id
     * 
     * @param integer $node_id
     * @param integer $node_type_id
     * @method POST 
     */
    function get() {

        $node_id = trim($this->input->post('node_id'));
        $node_type_id = $this->input->post('node_type_id');
        $lat = trim($this->input->post('lat'));
        $lng = trim($this->input->post('lng'));
        //se carga la libreria y se captura el nodo padre
        $this->load->library('TreeNodes');
        $treeObject = Doctrine_Core::getTable('Node')->getTree();
        $nodes = $treeObject->fetchRoots();
        $nodeParent = $nodes[0]->node_id;

        //si el nodo padre es igual al nodo suministrado se procede a obtener los hijos
        if ( !empty($lat) && !empty($lng)) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $nodes = $node->getChildren();
            if ($nodes) {

                $nodesChild = '';
                foreach ($nodes as $node) {

                    $nodesChild .= $node->node_id . ',';
                }
                $nodesChild .= $node_id;
            } else {
                $nodesChild = $node_id;
            }

            //Se trae el nodo id de acuerdo a la latitud y longitud suministrada
            $real_node = Doctrine_Core::getTable('InfraCoordinate')->findByCoordinates($nodesChild, $lat, $lng);

            $node_id = $real_node['node_id'];
        }



        if (is_numeric($node_id) || !empty($node_type_id)) {

            if ($node_id == $nodeParent) {

                $data = Doctrine_Core::getTable('InfraCoordinate')->getById($node_id);
            

                $result[0] = array();
                $result[0]['field'] = 'infra_info_terrain_area_total';
                $result[0]['value'] = ($data) ? $data->infra_info_terrain_area_total : 0.00;
                $result[0]['label'] = 'SUPERFICIE TERRENO TOTAL: ';

                $result[1] = array();
                $result[1]['field'] = 'infra_info_area_total';
                $result[1]['value'] = ($data) ? $data->infra_info_area_total : 0.00;
                $result[1]['label'] = 'SUPERFICIE CONSTRIDA TOTAL: ';
            } else {
                if (empty($node_type_id)) {
                    $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
                    $node_type_id = $nodeType->node_type_id;
                    $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
                } else {
                    $info = NULL;
                }
                $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeId($node_type_id);

                $result = array();
                $cont = 0;
                foreach ($infraConfig as $config) {
                    $result[$cont] = array();
                    $result[$cont]['field'] = $config->infra_attribute;
                    $result[$cont]['value'] = ($info) ? $info->{$config->infra_attribute} : '';
                    $result[$cont]['label'] = $this->translateTag('Infrastructure', $config->infra_attribute);
                    $cont++;
                }
            }
            $node = Doctrine_Core::getTable('Node')->findById($node_id);
        
            $infraGrupo = Doctrine_Core::getTable('InfraGrupo')->retrieveAllGrupos($node_id, $node->node_type_id);

          
            if ($infraGrupo->count()) {
               
                $infraGrupoAux = $infraGrupo->toArray();
            } else {
            
                $infraGrupoAux = array();
            }
            
         
            $total = ($node_id == $nodeParent) ? (count($result) + $infraGrupo->count()) : ($infraConfig->count() + $infraGrupo->count());

           
            $output = array('total' => $total, 'resultsInfraInfo' => $result, 'resultsInfraOtherData' => $infraGrupoAux);
        } else {

            $output = array('total' => 0, 'results' => array());
        }
        echo $this->json->encode($output);
    }

    function getFichaResumen() {
//        $node_id = $this->input->post('node_id');
//        $node_id = 1474; //

        $node_id = 2; //
//        $node_type_id = $this->input->post('node_type_id');

        if (is_numeric($node_id)) {
            if (empty($node_type_id)) {
                $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
                $node_type_id = $nodeType->node_type_id;
                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
            } else {
                $info = NULL;
            }
            $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeIdConfig($node_type_id);



            $result = array();
            $cont = 0;
            if ($infraConfig->count() >= 1) {
                foreach ($infraConfig as $config) {
                    $result[$cont] = array();
                    $result[$cont]['field'] = $config->infra_attribute;
                    $result[$cont]['value'] = ($info) ? $info->{$config->infra_attribute} : NULL;
                    $result[$cont]['label'] = $this->translateTag('Infrastructure', $config->infra_attribute);
                    $cont++;
                }

                if (empty($node_type_id)) {
                    $result[$cont] = array();
                    $result[$cont]['field'] = 'node_id';
                    $result[$cont]['value'] = $node_id;
                    $result[$cont]['label'] = $this->translateTag('Infrastructure', $config->infra_attribute);
                }
            } else {
                $result = null;
            }


            $node = Doctrine_Core::getTable('Node')->findById($node_id);

            $infraCoordinate = Doctrine_Core::getTable('InfraCoordinate')->retrieveByNode($node_id, null);

            $infraFotoPortada = $node->node_document_id_default;

            $infraPlanCategoryNodeType = Doctrine_Core::getTable('NodeType')->retriveById($node->node_type_id);
            $infraPlano = $infraPlanCategoryNodeType->plan_category_id;

            $infraGrupo = Doctrine_Core::getTable('InfraGrupo')->retrieveAllGruposConfig($node_id, $node->node_type_id);


            if ($infraGrupo->count()) {
                $infraGrupoAux = $infraGrupo->toArray();
            } else {
                $infraGrupoAux = null;
            }

            $output = array('total' => ($infraConfig->count() + $infraGrupo->count()), 'resultsInfraInfo' => $result, 'resultsInfraOtherData' => $infraGrupoAux, 'resultsFotoPortada' => $infraFotoPortada, 'resultsPlano' => $infraPlano, 'resultsMapa' => $infraCoordinate);
        } else {

            $output = array('total' => 0, 'results' => array());
        }
        echo $this->json->encode($output);
    }

    function getConfi() {

        $node_type_id = $this->input->post('node_type_id');

        if (!empty($node_type_id)) {

            $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeId($node_type_id);

            $result = array();
            $cont = 0;
            foreach ($infraConfig as $config) {
                $result[$cont] = array();
                $result[$cont]['field'] = $config->infra_attribute;
                $result[$cont]['value'] = $config->infra_attribute;
                $result[$cont]['sumary'] = $config->infra_the_sumary;
                $result[$cont]['label'] = $this->translateTag('Infrastructure', $config->infra_attribute);
                $cont++;
            }



            $output = array('total' => ($infraConfig->count()), 'resultsInfraInfo' => $result);
        } else {

            $output = array('total' => 0, 'results' => array());
        }
        echo $this->json->encode($output);
    }

    function updateSumary() {

        $node_type_id = $this->input->post('node_type_id');
        $infra_attribute = $this->input->post('infra_attribute');

        $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->retrieveSumary($node_type_id, $infra_attribute);

        if ($infraConfig->infra_the_sumary == 1) {
            $infraConfig->infra_the_sumary = null;
        } else {
            $infraConfig->infra_the_sumary = 1;
        }

        $infraConfig->save();
    }

// ESTO ES LO QUE ESTABA ANTES DE LA FUNCION "getConfi" AL PARECER YA NO FUNCIONA    
//    function getConfi()
//    {
//        $node_id = $this->input->post('node_id');
//        $node_type_id = $this->input->post('node_type_id');
//
//        if (is_numeric($node_id) || !empty($node_type_id))
//        {
//            if (empty($node_type_id))
//            {
//                $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
//                $node_type_id = $nodeType->node_type_id;
//                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
//            } else
//            {
//                $info = NULL;
//            }
//            $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeId($node_type_id);
//
//            $result = array();
//            $cont = 0;
//            foreach ($infraConfig as $config)
//            {
//                $result[$cont] = array();
//                $result[$cont]['field'] = $config->infra_attribute;
//                $result[$cont]['value'] = ($info) ? $info->{$config->infra_attribute} : NULL;
//                $result[$cont]['label'] = $this->translateTag('Infrastructure', $config->infra_attribute);
//                $cont++;
//            }
//
//            if (empty($node_type_id))
//            {
//                $result[$cont] = array();
//                $result[$cont]['field'] = 'node_id';
//                $result[$cont]['value'] = $node_id;
//                $result[$cont]['label'] = $this->translateTag('Infrastructure', $config->infra_attribute);
//            }
//            
//	        $node = Doctrine_Core::getTable ( 'Node' )->findByNodeId ($node_id);
//	
//	        $infraGrupo = Doctrine_Core::getTable ( 'InfraGrupo' )->retrieveAllGrupos ($node_id, $node->node_type_id);
//	        
//	
//	        if ( $infraGrupo->count () )
//	        {
//	            $infraGrupoAux = $infraGrupo->toArray ();
//	        }
//	        else
//	        {
//	            $infraGrupoAux = array();
//	        }
//
//            $output = array('total' => ($infraConfig->count() + $infraGrupo->count()), 'resultsInfraInfo' => $result, 'resultsInfraOtherData' => $infraGrupoAux);
//        } else
//        {
//
//            $output = array('total' => 0, 'results' => array());
//        }
//        echo $this->json->encode($output);
//    }

    function add() {
        $node_id = $this->input->post('node_id');

//        if (!$this->input->post('46')) {
//
//            $json_data = $this->json->encode(array(
//                'success' => false,
//                'msg' => 'Debe Introducir el Código de Recinto '
//            ));
//            echo $json_data;
//            exit;
//            ;
//        }
        //VALIDA ANTES DE REALIZAR ALGUNA ACCION QUE EL CODIGO DE RECINTO SEA UNICO

        $valueExiste = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, @$this->input->post('46'));

        $valueMismo = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 46);
        if ($valueExiste) {

            if ($valueExiste->infra_other_data_value_id != @$valueMismo->infra_other_data_value_id) {

                $json_data = $this->json->encode(array(
                    'success' => false,
                    'msg' => 'El Código de Recinto ' . $this->input->post('46') . ' ya Existe'
                ));
                echo $json_data;
                exit;
                ;
            }
        }




        ################
        ## infra info ##
        ################
        $infoAntes = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
        $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);

//        echo 'info: '; 
//        print_r($info); exit();
        if ($info === false) {
            $info = new InfraInfo();
            $info->node_id = $node_id;
        }
        $info->allowListener = true;

        foreach ($this->input->postall() as $att => $val) {
            if (!is_numeric($att)) {
                $info->{$att} = $val;
            }
        }

        $info->save();


        $node = Doctrine::getTable('Node')->find($node_id);
        $log_id = $this->syslog->register('add_info_structural_data', array(
            $node->node_name,
            $node->getPath()
        )); // registering log

        if (@$infoAntes->infra_info_option_id_1 != $info->infra_info_option_id_1) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_option_id_1');
            $logDetail->log_detail_value_old = Doctrine_Core::getTable('InfraInfoOption')->find($infoAntes->infra_info_option_id_1)->infra_other_data_option_name;
            $logDetail->log_detail_value_new = Doctrine_Core::getTable('InfraInfoOption')->find($info->infra_info_option_id_1)->infra_other_data_option_name;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_option_id_2 != $info->infra_info_option_id_2) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_option_id_2');
            $logDetail->log_detail_value_old = Doctrine_Core::getTable('InfraInfoOption')->find($infoAntes->infra_info_option_id_2)->infra_other_data_option_name;
            $logDetail->log_detail_value_new = Doctrine_Core::getTable('InfraInfoOption')->find($info->infra_info_option_id_2)->infra_other_data_option_name;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_option_id_3 != $info->infra_info_option_id_3) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_option_id_3');
            $logDetail->log_detail_value_old = Doctrine_Core::getTable('InfraInfoOption')->find($infoAntes->infra_info_option_id_3)->infra_other_data_option_name;
            $logDetail->log_detail_value_new = Doctrine_Core::getTable('InfraInfoOption')->find($info->infra_info_option_id_3)->infra_other_data_option_name;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_option_id_4 != $info->infra_info_option_id_4) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_option_id_4');
            $logDetail->log_detail_value_old = Doctrine_Core::getTable('InfraInfoOption')->find($infoAntes->infra_info_option_id_4)->infra_other_data_option_name;
            $logDetail->log_detail_value_new = Doctrine_Core::getTable('InfraInfoOption')->find($info->infra_info_option_id_4)->infra_other_data_option_name;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_usable_area != $info->infra_info_usable_area) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_usable_area');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_usable_area;
            $logDetail->log_detail_value_new = $info->infra_info_usable_area;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_usable_area_total != $info->infra_info_usable_area_total) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_usable_area_total');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_usable_area_total;
            $logDetail->log_detail_value_new = $info->infra_info_usable_area_total;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_area != $info->infra_info_area) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_area');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_area;
            $logDetail->log_detail_value_new = $info->infra_info_area;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_area_total != $info->infra_info_area_total) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_area_total');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_area;
            $logDetail->log_detail_value_new = $info->infra_info_area;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_volume != $info->infra_info_volume) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_volume');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_volume;
            $logDetail->log_detail_value_new = $info->infra_info_volume;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_volume_total != $info->infra_info_volume_total) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_volume_total');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_volume_total;
            $logDetail->log_detail_value_new = $info->infra_info_volume_total;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_length != $info->infra_info_length) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_length');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_length;
            $logDetail->log_detail_value_new = $info->infra_info_length;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_width != $info->infra_info_width) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_width');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_width;
            $logDetail->log_detail_value_new = $info->infra_info_width;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_height != $info->infra_info_height) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_height');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_height;
            $logDetail->log_detail_value_new = $info->infra_info_height;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_capacity != $info->infra_info_capacity) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_capacity');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_capacity;
            $logDetail->log_detail_value_new = $info->infra_info_capacity;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_capacity_total != $info->infra_info_capacity_total) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_capacity_total');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_capacity_total;
            $logDetail->log_detail_value_new = $info->infra_info_width;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_terrain_area != $info->infra_info_terrain_area) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_terrain_area');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_terrain_area;
            $logDetail->log_detail_value_new = $info->infra_info_terrain_area;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_terrain_area_total != $info->infra_info_terrain_area_total) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_terrain_area_total');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_terrain_area_total;
            $logDetail->log_detail_value_new = $info->infra_info_terrain_area_total;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_additional_1 != $info->infra_info_additional_1) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_additional_1');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_additional_1;
            $logDetail->log_detail_value_new = $info->infra_info_additional_1;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_additional_2 != $info->infra_info_additional_2) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_additional_2');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_additional_2;
            $logDetail->log_detail_value_new = $info->infra_info_additional_2;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_additional_3 != $info->infra_info_additional_3) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_additional_3');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_additional_3;
            $logDetail->log_detail_value_new = $info->infra_info_additional_3;
            $logDetail->save();
        }

        if (@$infoAntes->infra_info_additional_4 != $info->infra_info_additional_4) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_additional_4');
            $logDetail->log_detail_value_old = @$infoAntes->infra_info_additional_4;
            $logDetail->log_detail_value_new = $info->infra_info_additional_4;
            $logDetail->save();
        }
        ################
        ## infra info ##
        ################
        ############################
        ## infra other data value ##
        ############################
        $cont = 0;
        foreach ($this->input->postall() as $att => $val) {
            if (is_numeric($att)) {
                $value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, $att);

                if ($value) {
                    $infra_value = $value->infra_other_data_value_value;
                    $infra_value_option = $value->infra_other_data_option_id;
                } else {
                    $infra_other = new InfraOtherDataValue();
                    $infra_other->infra_other_data_attribute_id = $att;
                    $infra_other->node_id = $node_id;
                    $infra_other->save();

                    $value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, $att);
                    $infra_value = $value->infra_other_data_value_value;
                    $infra_value_option = $value->infra_other_data_option_id;
                }

                $attr = Doctrine_Core::getTable('InfraOtherDataAttribute')->find($att);


                if ($value === false) {
                    $value = new InfraOtherDataValue();
                }
//                echo 'aqui';
//                print_r($value); exit();

                $value->infra_other_data_attribute_id = $att;
                $value->node_id = $node_id;

                if ($attr->infra_other_data_attribute_type == 5) {
                    $value->infra_other_data_option_id = $val;

                    $other_data_attribute = Doctrine_Core::getTable('InfraOtherDataAttribute')->find($value->infra_other_data_attribute_id);
                    $infra_other_data_attribute_name = $other_data_attribute->infra_other_data_attribute_name;

                    $other_antes = Doctrine_Core::getTable('InfraOtherDataOption')->find($infra_value_option);
                    $other_ahora = Doctrine_Core::getTable('InfraOtherDataOption')->find($value->infra_other_data_option_id);

                    $infra_other_data_attribute_name = $other_data_attribute->infra_other_data_attribute_name;

                    if ($infra_value_option != $value->infra_other_data_option_id) {
                        if ($log_id) {
                            if ($infra_value_option === NULL) {
                                $logDetail = new LogDetail();
                                $logDetail->log_id = $log_id;
                                $logDetail->log_detail_param = $this->translateTag('General', 'other_data');
                                $logDetail->log_detail_value_old = $infra_other_data_attribute_name . " ";
                                $logDetail->log_detail_value_new = $infra_other_data_attribute_name . " " . $other_ahora->infra_other_data_option_name;
                                $logDetail->save();
                            } else {
                                $logDetail = new LogDetail();
                                $logDetail->log_id = $log_id;
                                $logDetail->log_detail_param = $this->translateTag('General', 'other_data');
                                $logDetail->log_detail_value_old = @$infra_other_data_attribute_name . " " . @$other_antes->infra_other_data_option_name;
                                $logDetail->log_detail_value_new = @$infra_other_data_attribute_name . " " . @$other_ahora->infra_other_data_option_name;
                                $logDetail->save();
                            }
                        }
                    }
                } else {
                    //CUANDO ES CHECKBOX
                    if ($attr->infra_other_data_attribute_type == 7) {

                        if (trim($val) == 'true') {
                            $value->infra_other_data_value_value = 1;
                        } else {
                            $value->infra_other_data_value_value = NULL;
                        }
                    } else {
                        $value->infra_other_data_value_value = $val;
                    }

                    $other_data_attribute = Doctrine_Core::getTable('InfraOtherDataAttribute')->find($value->infra_other_data_attribute_id);
                    $infra_other_data_attribute_name = $other_data_attribute->infra_other_data_attribute_name;


                    $infra_other_data_attribute_name = $other_data_attribute->infra_other_data_attribute_name;
                    if ($infra_value != $value->infra_other_data_value_value) {
                        if ($log_id) {
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('General', 'other_data');
                            $logDetail->log_detail_value_old = $infra_other_data_attribute_name . " " . $infra_value;
                            $logDetail->log_detail_value_new = $infra_other_data_attribute_name . " " . $value->infra_other_data_value_value;
                            $logDetail->save();
                        }
                    }
                }

//                print_r($value); 

                $valueCodigoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 46);
                if ($valueCodigoRecinto) {
                    $valueCodigoRecinto->infra_other_data_value_value = $this->input->post('46');
                } else {

                    $valueCodigoRecinto = new InfraOtherDataValue();
                    $valueCodigoRecinto->infra_other_data_attribute_id = 46;
                    $valueCodigoRecinto->infra_other_data_value_value = $this->input->post('46');
                    $valueCodigoRecinto->node_id = $node_id;
                }

                $valueCodigoRecinto->save();
                $cont++;

                $value->save();
            }
        }

        echo '{"success": true}';
    }

    function exportarFormato($node_id = null) {
        ini_set('memory_limit', '2048M');
        set_time_limit('60000000');
        $this->load->library('PHPExcel');

        ############        
        ## HOJA 2 ##
        ############

        $sheet2 = $this->phpexcel->createSheet(); //CREA LA NUEVA HOJA
        $sheet2 = $this->phpexcel->setActiveSheetIndex(1); //SEGUNDA HOJA
        $sheet2->setTitle('BASE DE DATOS');

        //TITULOS
        $sheet2->setCellValue('A1', 'ORGANISMO')
                ->setCellValue('B1', 'DEPARTAMENTO')
                ->setCellValue('C1', 'UNIDAD')
                ->setCellValue('D1', 'ACTIVIDAD')
                ->setCellValue('E1', 'USO')
                ->setCellValue('F1', 'USO PARTICULAR')
                ->setCellValue('G1', 'PROPIEDAD RECINTO')
                ->setCellValue('H1', 'ESTATUS RECINTO')
                ->setCellValue('I1', 'ESTADO RECINTO')
                ->setCellValue('J1', 'VENTANAS')
                ->setCellValue('K1', 'AIRE ACONDICIONADO')
                ->setCellValue('L1', 'CALEFACCION')
                ->setCellValue('M1', 'PROYECTOR')
                ->setCellValue('N1', 'WIFI');

        //ORGANISMO
        $InfraOtherDataOptionOrganismo = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(6);
        $rcount = 1;
        foreach ($InfraOtherDataOptionOrganismo as $Organismo) {
            $rcount++;
            $sheet2->setCellValueExplicit('A' . $rcount, $Organismo->infra_other_data_option_name);
        }

        //DEPARTAMENTO
        $InfraOtherDataOptionDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(8);
        $rcount = 1;
        foreach ($InfraOtherDataOptionDepartamento as $Departamento) {
            $rcount++;
            $sheet2->setCellValueExplicit('B' . $rcount, $Departamento->infra_other_data_option_name);
        }

        //UNIDAD
        $InfraOtherDataOptionUnidad = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(10);
        $rcount = 1;
        foreach ($InfraOtherDataOptionUnidad as $Unidad) {
            $rcount++;
            $sheet2->setCellValueExplicit('C' . $rcount, $Unidad->infra_other_data_option_name);
        }

        //ACTIVIDAD
        $InfraOtherDataOptionActividad = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(13);
        $rcount = 1;
        foreach ($InfraOtherDataOptionActividad as $Actividad) {
            $rcount++;
            $sheet2->setCellValueExplicit('D' . $rcount, $Actividad->infra_other_data_option_name);
        }

        //USO
        $InfraOtherDataOptionUso = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(15);
        $rcount = 1;
        foreach ($InfraOtherDataOptionUso as $Uso) {
            $rcount++;
            $sheet2->setCellValueExplicit('E' . $rcount, $Uso->infra_other_data_option_name);
        }

        //USO PARTICULAR
        $InfraOtherDataOptionUsoParticular = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(17);
        $rcount = 1;
        foreach ($InfraOtherDataOptionUsoParticular as $UsoParticular) {
            $rcount++;
            $sheet2->setCellValueExplicit('F' . $rcount, $UsoParticular->infra_other_data_option_name);
        }

        //PROPIEDAD RECINTO
        $InfraOtherDataOptionPropiedadRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(19);
        $rcount = 1;
        foreach ($InfraOtherDataOptionPropiedadRecinto as $PropiedadRecinto) {
            $rcount++;
            $sheet2->setCellValueExplicit('G' . $rcount, $PropiedadRecinto->infra_other_data_option_name);
        }

        //ESTATUS RECINTO
        $InfraOtherDataOptionEstatusRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(21);
        $rcount = 1;
        foreach ($InfraOtherDataOptionEstatusRecinto as $EstatusRecinto) {
            $rcount++;
            $sheet2->setCellValueExplicit('H' . $rcount, $EstatusRecinto->infra_other_data_option_name);
        }

        //ESTADO RECINTO
        $InfraOtherDataOptionEstadoRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(23);
        $rcount = 1;
        foreach ($InfraOtherDataOptionEstadoRecinto as $EstadoRecinto) {
            $rcount++;
            $sheet2->setCellValueExplicit('I' . $rcount, $EstadoRecinto->infra_other_data_option_name);
        }

        //VENTANAS
        $InfraOtherDataOptionVentanas = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(26);
        $rcount = 1;
        foreach ($InfraOtherDataOptionVentanas as $Ventanas) {
            $rcount++;
            $sheet2->setCellValueExplicit('J' . $rcount, $Ventanas->infra_other_data_option_name);
        }

        //AIRE ACONDICIONADO
        $InfraOtherDataOptionAireAcondicionado = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(27);
        $rcount = 1;
        foreach ($InfraOtherDataOptionAireAcondicionado as $AireAcondicionado) {
            $rcount++;
            $sheet2->setCellValueExplicit('K' . $rcount, $AireAcondicionado->infra_other_data_option_name);
        }

        //CALEFACCION
        $InfraOtherDataOptionCalefaccion = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(28);
        $rcount = 1;
        foreach ($InfraOtherDataOptionCalefaccion as $Calefaccion) {
            $rcount++;
            $sheet2->setCellValueExplicit('L' . $rcount, $Calefaccion->infra_other_data_option_name);
        }

        //PROYECTOR
        $InfraOtherDataOptionProyector = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(32);
        $rcount = 1;
        foreach ($InfraOtherDataOptionProyector as $Proyector) {
            $rcount++;
            $sheet2->setCellValueExplicit('M' . $rcount, $Proyector->infra_other_data_option_name);
        }

        //WIFI
        $InfraOtherDataOptionWifi = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttribute(33);
        $rcount = 1;
        foreach ($InfraOtherDataOptionWifi as $Wifi) {
            $rcount++;
            $sheet2->setCellValueExplicit('N' . $rcount, $Wifi->infra_other_data_option_name);
        }

        //ESTILOS

        $sheet2->getStyle('A1:N1')->getFont()->applyFromArray(array(
            'bold' => true
        ));
        $sheet2->getStyle('A1:N1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));
        $sheet2->getStyle('A1:N1')->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $sheet2->getColumnDimension('A')->setAutoSize(true);
        $sheet2->getColumnDimension('B')->setAutoSize(true);
        $sheet2->getColumnDimension('C')->setAutoSize(true);
        $sheet2->getColumnDimension('D')->setAutoSize(true);
        $sheet2->getColumnDimension('E')->setAutoSize(true);
        $sheet2->getColumnDimension('F')->setAutoSize(true);
        $sheet2->getColumnDimension('G')->setAutoSize(true);
        $sheet2->getColumnDimension('H')->setAutoSize(true);
        $sheet2->getColumnDimension('I')->setAutoSize(true);
        $sheet2->getColumnDimension('J')->setAutoSize(true);
        $sheet2->getColumnDimension('K')->setAutoSize(true);
        $sheet2->getColumnDimension('L')->setAutoSize(true);
        $sheet2->getColumnDimension('M')->setAutoSize(true);
        $sheet2->getColumnDimension('N')->setAutoSize(true);

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('FORMATO ACTUALIZACION DE DATOS');


        $sheet->setCellValueExplicit('A1', 'CODIGO RECINTO(CAMPO OBLIGATORIO)')
                ->setCellValue('B1', 'NOMBRE RECINTO')
                ->setCellValue('C1', 'CODIGO SUBRECINTO')
                ->setCellValue('D1', 'NOMBRE SUBRECINTO')
                ->setCellValue('E1', 'ORGANISMO')
                ->setCellValue('F1', 'DEPARTAMENTO')
                ->setCellValue('G1', 'UNIDAD')
                ->setCellValue('H1', 'ACTIVIDAD')
                ->setCellValue('I1', 'USO')
                ->setCellValue('J1', 'USO PARTICULAR')
                ->setCellValue('K1', 'PROPIEDAD RECINTO')
                ->setCellValue('L1', 'ESTATUS RECINTO')
                ->setCellValue('M1', 'ESTADO RECINTO')
                ->setCellValue('N1', 'CANTIDAD USUARIOS')
                ->setCellValue('O1', 'VENTANAS')
                ->setCellValue('P1', 'AIRE ACONDICIONADO')
                ->setCellValue('Q1', 'CALEFACCION')
                ->setCellValue('R1', 'LUMINARIAS')
                ->setCellValue('S1', 'ENCHUFES')
                ->setCellValue('T1', 'PUNTOS DE RED')
                ->setCellValue('U1', 'PROYECTOR')
                ->setCellValue('V1', 'WIFI')
                ->setCellValue('W1', 'OBSERVACION RECINTO')
                ->setCellValue('X1', 'ULTIMA ACTUALIZACION')
                ->setCellValue('Y1', 'USUARIO');


        $node = Doctrine_Core::getTable('Node')->find($node_id);

//        if ($node->node_type_id == 3){//3  ES SOLO PARA EL NIVEL


        $q = Doctrine_Query :: create()
                ->from('Node n')
                ->where('node_parent_id = ?', $node->node_parent_id)
                ->where('n.lft >= ?', $node->lft)
                ->andWhere('n.rgt <= ?', $node->rgt);

        $results = $q->execute();


        $rcount = 1;
        foreach ($results as $result) {
            $nodeType = Doctrine_Core::getTable('NodeType')->find($result->node_type_id);
            if ($nodeType->node_type_category_id == 2) { //QUE SEA DE CATEGORIA 2 ES DECIR TIPO RECINTO
                //CODIGO RECINTO
                $InfraOtherDataOptionCodigoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 46);
                if ($InfraOtherDataOptionCodigoRecinto) {
                    $codigo_recinto = $InfraOtherDataOptionCodigoRecinto->infra_other_data_value_value;
                } else {
                    $codigo_recinto = '';
                }

                //NOMBRE RECINTO
                $InfraOtherDataOptionNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 2);
                if ($InfraOtherDataOptionNombreRecinto) {
                    $nombre_recinto = $InfraOtherDataOptionNombreRecinto->infra_other_data_value_value;
                } else {
                    $nombre_recinto = '';
                }

                //CODIGO SUBRECINTO
                $InfraOtherDataOptionCodigoSubrecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 3);
                if ($InfraOtherDataOptionCodigoSubrecinto) {
                    $codigo_subrecinto = $InfraOtherDataOptionCodigoSubrecinto->infra_other_data_value_value;
                } else {
                    $codigo_subrecinto = '';
                }

                //NOMBRE SUBRECINTO
                $InfraOtherDataOptionNombreSubrecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 4);
                if ($InfraOtherDataOptionNombreSubrecinto) {
                    $nombre_subrecinto = $InfraOtherDataOptionNombreSubrecinto->infra_other_data_value_value;
                } else {
                    $nombre_subrecinto = '';
                }

                //ORGANISMO
                $InfraOtherDataOptionOrganismo = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 6);
                if ($InfraOtherDataOptionOrganismo) {
                    $InfraOtherDataOptionOrganismoTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionOrganismo->infra_other_data_option_id);
                    if ($InfraOtherDataOptionOrganismoTable) {
                        $organismo = $InfraOtherDataOptionOrganismoTable->infra_other_data_option_name;
                    } else {
                        $organismo = '';
                    }
                } else {
                    $organismo = '';
                }

                //DEPARTAMENTO
                $InfraOtherDataOptionDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 8);
                if ($InfraOtherDataOptionDepartamento) {
                    $InfraOtherDataOptionDepartamentoTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionDepartamento->infra_other_data_option_id);
                    if ($InfraOtherDataOptionDepartamentoTable) {
                        $departamento = $InfraOtherDataOptionDepartamentoTable->infra_other_data_option_name;
                    } else {
                        $departamento = '';
                    }
                } else {
                    $departamento = '';
                }

                //UNIDAD
                $InfraOtherDataOptionUnidad = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 10);
                if ($InfraOtherDataOptionUnidad) {
                    $InfraOtherDataOptionUnidadTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionUnidad->infra_other_data_option_id);
                    if ($InfraOtherDataOptionUnidadTable) {
                        $unidad = $InfraOtherDataOptionUnidadTable->infra_other_data_option_name;
                    } else {
                        $unidad = '';
                    }
                } else {
                    $unidad = '';
                }

                //ACTIVIDAD
                $InfraOtherDataOptionActividad = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 13);
                if ($InfraOtherDataOptionActividad) {
                    $InfraOtherDataOptionActividadTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionActividad->infra_other_data_option_id);
                    if ($InfraOtherDataOptionActividadTable) {
                        $actividad = $InfraOtherDataOptionActividadTable->infra_other_data_option_name;
                    } else {
                        $actividad = '';
                    }
                } else {
                    $actividad = '';
                }

                //USO
                $InfraOtherDataOptionUso = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 15);
                if ($InfraOtherDataOptionUso) {
                    $InfraOtherDataOptionUsoTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionUso->infra_other_data_option_id);
                    if ($InfraOtherDataOptionUsoTable) {
                        $uso = $InfraOtherDataOptionUsoTable->infra_other_data_option_name;
                    } else {
                        $uso = '';
                    }
                } else {
                    $uso = '';
                }

                //USO PARTICULAR
                $InfraOtherDataOptionUsoParticular = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 17);
                if ($InfraOtherDataOptionUsoParticular) {
                    $InfraOtherDataOptionUsoParticularTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionUsoParticular->infra_other_data_option_id);
                    if ($InfraOtherDataOptionUsoParticularTable) {
                        $uso_particular = $InfraOtherDataOptionUsoParticularTable->infra_other_data_option_name;
                    } else {
                        $uso_particular = '';
                    }
                } else {
                    $uso_particular = '';
                }

                //PROPIEDAD RECINTO
                $InfraOtherDataOptionPropiedadRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 19);
                if ($InfraOtherDataOptionPropiedadRecinto) {
                    $InfraOtherDataOptionPropiedadRecintoTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionPropiedadRecinto->infra_other_data_option_id);
                    $propiedad_recinto = $InfraOtherDataOptionPropiedadRecintoTable->infra_other_data_option_name;
                } else {
                    $propiedad_recinto = '';
                }

                //ESTATUS RECINTO
                $InfraOtherDataOptionEstatusRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 21);
                if ($InfraOtherDataOptionEstatusRecinto) {
                    $InfraOtherDataOptionEstatusRecintoTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionEstatusRecinto->infra_other_data_option_id);
                    if ($InfraOtherDataOptionEstatusRecintoTable) {
                        $estatus_recinto = $InfraOtherDataOptionEstatusRecintoTable->infra_other_data_option_name;
                    } else {
                        $estatus_recinto = '';
                    }
                } else {
                    $estatus_recinto = '';
                }

                //ESTADO RECINTO
                $InfraOtherDataOptionEstadoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 23);
                if ($InfraOtherDataOptionEstadoRecinto) {
                    $InfraOtherDataOptionEstadoRecintoTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionEstadoRecinto->infra_other_data_option_id);
                    if ($InfraOtherDataOptionEstadoRecintoTable) {
                        $estado_recinto = $InfraOtherDataOptionEstadoRecintoTable->infra_other_data_option_name;
                    } else {
                        $estado_recinto = '';
                    }
                } else {
                    $estado_recinto = '';
                }

                //CANTIDAD USUARIOS
                $InfraOtherDataOptionCantidadUsuarios = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 25);
                if ($InfraOtherDataOptionCantidadUsuarios) {
                    $cantidad_usuarios = $InfraOtherDataOptionCantidadUsuarios->infra_other_data_value_value;
                } else {
                    $cantidad_usuarios = '';
                }

                //VENTANAS
                $InfraOtherDataOptionVentanas = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 26);
                if ($InfraOtherDataOptionVentanas) {
                    $InfraOtherDataOptionVentanasTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionVentanas->infra_other_data_option_id);
                    if ($InfraOtherDataOptionVentanasTable) {
                        $ventanas = $InfraOtherDataOptionVentanasTable->infra_other_data_option_name;
                    } else {
                        $ventanas = '';
                    }
                } else {
                    $ventanas = '';
                }

                //AIRE ACONDICIONADO
                $InfraOtherDataOptionAire = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 27);
                if ($InfraOtherDataOptionAire) {
                    $InfraOtherDataOptionAireTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionAire->infra_other_data_option_id);
                    if ($InfraOtherDataOptionAireTable) {
                        $aire_acondicionado = $InfraOtherDataOptionAireTable->infra_other_data_option_name;
                    } else {
                        $aire_acondicionado = '';
                    }
                } else {
                    $aire_acondicionado = '';
                }

                //CALEFACCION
                $InfraOtherDataOptionCalefaccion = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 28);
                if ($InfraOtherDataOptionCalefaccion) {
                    $InfraOtherDataOptionCalefaccionTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionCalefaccion->infra_other_data_option_id);
                    if ($InfraOtherDataOptionCalefaccionTable) {
                        $calefaccion = $InfraOtherDataOptionCalefaccionTable->infra_other_data_option_name;
                    } else {
                        $calefaccion = '';
                    }
                } else {
                    $calefaccion = '';
                }

                //LUMINARIAS
                $InfraOtherDataOptionLuminaria = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 29);
                if ($InfraOtherDataOptionLuminaria) {
                    $luminarias = $InfraOtherDataOptionLuminaria->infra_other_data_value_value;
                } else {
                    $luminarias = '';
                }

                //ENCHUFES
                $InfraOtherDataOptionEnchufes = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 30);
                if ($InfraOtherDataOptionEnchufes) {
                    $enchufes = $InfraOtherDataOptionEnchufes->infra_other_data_value_value;
                } else {
                    $enchufes = '';
                }

                //PUNTOS DE RED
                $InfraOtherDataOptionPuntoDeRed = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 31);
                if ($InfraOtherDataOptionPuntoDeRed) {
                    $puntos_de_red = $InfraOtherDataOptionPuntoDeRed->infra_other_data_value_value;
                } else {
                    $puntos_de_red = '';
                }

                //PROYECTOR
                $InfraOtherDataOptionProyector = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 32);
                if ($InfraOtherDataOptionProyector) {
                    $InfraOtherDataOptionProyectorTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionProyector->infra_other_data_option_id);
                    $proyector = $InfraOtherDataOptionProyectorTable ? $InfraOtherDataOptionProyectorTable->infra_other_data_option_name : '';
                } else {
                    $proyector = '';
                }


                //WIFI
                $InfraOtherDataOptionWifi = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 33);
                if ($InfraOtherDataOptionWifi) {
                    $InfraOtherDataOptionWifiTable = Doctrine_Core::getTable('InfraOtherDataOption')->find($InfraOtherDataOptionWifi->infra_other_data_option_id);
                    if ($InfraOtherDataOptionWifiTable) {
                        $wifi = $InfraOtherDataOptionWifiTable->infra_other_data_option_name;
                    } else {
                        $wifi = '';
                    }
                } else {
                    $wifi = '';
                }

                //OBSERVACION RECINTO
                $InfraOtherDataOptionObservacionRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 34);
                if ($InfraOtherDataOptionObservacionRecinto) {
                    $observacion_recinto = $InfraOtherDataOptionObservacionRecinto->infra_other_data_value_value;
                } else {
                    $observacion_recinto = '';
                }


                //ULTIMA ACTUALIZACION
                $InfraOtherDataOptionUltimaActualizacion = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 38);
                if ($InfraOtherDataOptionUltimaActualizacion) {
                    $ultima_actualizacion = $InfraOtherDataOptionUltimaActualizacion->infra_other_data_value_value;
                } else {
                    $ultima_actualizacion = '';
                }

                //USUARIO
                $InfraOtherDataOptionUsuario = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 398);
                if ($InfraOtherDataOptionUsuario) {
                    $usuario = $InfraOtherDataOptionUsuario->infra_other_data_value_value;
                } else {
                    $usuario = '';
                }

                $rcount++;
                $sheet->setCellValueExplicit('A' . $rcount, $codigo_recinto, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('B' . $rcount, $nombre_recinto, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('C' . $rcount, $codigo_subrecinto, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('D' . $rcount, $nombre_subrecinto, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('E' . $rcount, $organismo, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('F' . $rcount, $departamento, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('G' . $rcount, $unidad, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('H' . $rcount, $actividad, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('I' . $rcount, $uso, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('J' . $rcount, $uso_particular, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('K' . $rcount, $propiedad_recinto, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('L' . $rcount, $estatus_recinto, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('M' . $rcount, $estado_recinto, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('N' . $rcount, $cantidad_usuarios, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('O' . $rcount, $ventanas, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('P' . $rcount, $aire_acondicionado, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('Q' . $rcount, $calefaccion, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('R' . $rcount, $luminarias, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('S' . $rcount, $enchufes, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('T' . $rcount, $puntos_de_red, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('U' . $rcount, $proyector, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('V' . $rcount, $wifi, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('W' . $rcount, $observacion_recinto, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('X' . $rcount, $ultima_actualizacion, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('Y' . $rcount, $usuario, PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
//        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        $sheet->getColumnDimension('X')->setAutoSize(true);
        $sheet->getColumnDimension('Y')->setAutoSize(true);

        //ESTILOS
        $sheet->getStyle('A1:Y1')->getFont()->applyFromArray(array(
            'bold' => true
        ));
        $sheet->getStyle('A1:Y1')->getFont()->applyFromArray(array(
            'bold' => true
        ));
        $sheet->getStyle('A1:Y1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));
        $sheet->getStyle('A1:Y' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        ## GUARDA Y DESCARGA ##
        $file = 'FormatoActualizacionMasiva';
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($file . '.xls'));

        $this->load->helper('download');
        $data = file_get_contents($this->app->getTempFileDir($file . '.xls')); // Read the file's contents
        force_download($file . '.xls', $data);
    }

    function addDatosDinamicosMasivo() {
        ini_set('memory_limit', '2048M');
        $this->load->library('PHPExcel');
        $documentoExcel = $this->input->file('documentoExcel');
        $documentoExcel = $documentoExcel['tmp_name'];

        $sheetIndex = 0;
        $cont = 0;

        if ($documentoExcel) {

            $objPHPExcel2 = PHPExcel_IOFactory :: load($documentoExcel);
            $rowIterator2 = $objPHPExcel2->getActiveSheet()->getRowIterator();
            $objWorksheet2 = $objPHPExcel2->getActiveSheet();

            $rowsCount2 = $objWorksheet2->getHighestRow(); // cantidad de lineas en el excel 
            ##############################################
            ## AQUI VALIDA LOS DATOS INGRESADOS EN EXCELL
            ##############################################
            foreach ($rowIterator2 as $row2) {

                $rowIndex2 = $row2->getRowIndex();
                if ($rowIndex2 > 1) {

                    $codigo_recinto = $objWorksheet2->getCell('A' . $rowIndex2)->getCalculatedValue();
                    $nombre_recinto = $objWorksheet2->getCell('B' . $rowIndex2)->getCalculatedValue();
                    $codigo_subrecinto = $objWorksheet2->getCell('C' . $rowIndex2)->getCalculatedValue();
                    $nombre_subrecinto = $objWorksheet2->getCell('D' . $rowIndex2)->getCalculatedValue();
                    $organismo = $objWorksheet2->getCell('E' . $rowIndex2)->getCalculatedValue();
                    $departamento = $objWorksheet2->getCell('F' . $rowIndex2)->getCalculatedValue();
                    $unidad = $objWorksheet2->getCell('G' . $rowIndex2)->getCalculatedValue();
                    $actividad = $objWorksheet2->getCell('H' . $rowIndex2)->getCalculatedValue();
                    $uso = $objWorksheet2->getCell('I' . $rowIndex2)->getCalculatedValue();
                    $uso_particular = $objWorksheet2->getCell('J' . $rowIndex2)->getCalculatedValue();
                    $propiedad_recinto = $objWorksheet2->getCell('K' . $rowIndex2)->getCalculatedValue();
                    $estatus_recinto = $objWorksheet2->getCell('L' . $rowIndex2)->getCalculatedValue();
                    $estado_recinto = $objWorksheet2->getCell('M' . $rowIndex2)->getCalculatedValue();
                    $cantidad_usuarios = $objWorksheet2->getCell('N' . $rowIndex2)->getCalculatedValue();
                    $ventanas = $objWorksheet2->getCell('O' . $rowIndex2)->getCalculatedValue();
                    $aire_acondicionado = $objWorksheet2->getCell('P' . $rowIndex2)->getCalculatedValue();
                    $calefaccion = $objWorksheet2->getCell('Q' . $rowIndex2)->getCalculatedValue();
                    $luminarias = $objWorksheet2->getCell('R' . $rowIndex2)->getCalculatedValue();
                    $enchufes = $objWorksheet2->getCell('S' . $rowIndex2)->getCalculatedValue();
                    $puntos_de_red = $objWorksheet2->getCell('T' . $rowIndex2)->getCalculatedValue();
                    $proyector = $objWorksheet2->getCell('U' . $rowIndex2)->getCalculatedValue();
                    $wifi = $objWorksheet2->getCell('V' . $rowIndex2)->getCalculatedValue();
                    $obeservacion_recinto = $objWorksheet2->getCell('W' . $rowIndex2)->getCalculatedValue();

                    $objPHPExcel2->getActiveSheet()->getStyle('X' . $rowIndex2)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                    $ultima_actualizacion = $objPHPExcel2->getActiveSheet()->getCell('X' . $rowIndex2)->getFormattedValue();
//                    $ultima_actualizacion = $objWorksheet2->getCell('X' . $rowIndex2)->getCalculatedValue();
                    $usuario = $objWorksheet2->getCell('Y' . $rowIndex2)->getCalculatedValue();
//                    $superficie = $objWorksheet2->getCell('Z' . $rowIndex2)->getCalculatedValue();


                    if ($codigo_recinto != NULL) {
                        $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($codigo_recinto));

                        if ($nodeOtherData) {
                            $node_id = $nodeOtherData->node_id;
                        } else {
                            $success = 'false';
                            $msg = "El código de recinto no existe " . $codigo_recinto . ' Celda : A' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    } else {
                        $success = 'false';
                        $msg = "Campo Obligatorio " . $codigo_recinto . ' Celda : A' . $rowIndex2;
                        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                        exit;
                    }

                    //VALIDA NOMBRE DE RECINTO
                    if ($nombre_recinto != NULL) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 2);

                        if (!$nodeOtherDataNombreRecinto) {
                            $nodeOtherDataNombreRecinto = new InfraOtherDataValue();
                            $nodeOtherDataNombreRecinto->infra_other_data_attribute_id = 2;
                            $nodeOtherDataNombreRecinto->node_id = $node_id;
                            $nodeOtherDataNombreRecinto->save();
                        }
                    }

                    //VALIDA CODIGO  SUBRECINTO
                    if ($codigo_subrecinto != NULL) {
                        $nodeOtherDataCodigoSubrecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 3);

                        if (!$nodeOtherDataCodigoSubrecinto) {
                            $nodeOtherDataCodigoSubrecinto = new InfraOtherDataValue();
                            $nodeOtherDataCodigoSubrecinto->infra_other_data_attribute_id = 3;
                            $nodeOtherDataCodigoSubrecinto->node_id = $node_id;
                            $nodeOtherDataCodigoSubrecinto->save();
                        }
                    }

                    //VALIDA NOMBRE SUBRECINTO
                    if ($nombre_subrecinto != NULL) {
                        $nodeOtherDataNombreSubrecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 4);

                        if (!$nodeOtherDataNombreSubrecinto) {
                            $nodeOtherDataNombreSubrecinto = new InfraOtherDataValue();
                            $nodeOtherDataNombreSubrecinto->infra_other_data_attribute_id = 4;
                            $nodeOtherDataNombreSubrecinto->node_id = $node_id;
                            $nodeOtherDataNombreSubrecinto->save();
                        }
                    }

                    //VALIDA ORGANISMO
                    if ($organismo != NULL) {
                        $nodeOtherDataOrganismo = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 6);

                        if (!$nodeOtherDataOrganismo) {
                            $nodeOtherDataOrganismo = new InfraOtherDataValue();
                            $nodeOtherDataOrganismo->infra_other_data_attribute_id = 6;
                            $nodeOtherDataOrganismo->node_id = $node_id;
                            $nodeOtherDataOrganismo->save();
                        }

                        $InfraOtherDataOptionOrganismo = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataOrganismo->infra_other_data_attribute_id, trim($organismo));

                        if ($InfraOtherDataOptionOrganismo) {
                            if ($InfraOtherDataOptionOrganismo->infra_other_data_option_id != $nodeOtherDataOrganismo->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionOrganismo->infra_other_data_attribute_id != $nodeOtherDataOrganismo->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $organismo . ' Celda : E' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $organismo . ' Celda : E' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA DEPARTAMENTO
                    if ($departamento != NULL) {
                        $nodeOtherDataDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 8);

                        if (!$nodeOtherDataDepartamento) {
                            $nodeOtherDataDepartamento = new InfraOtherDataValue();
                            $nodeOtherDataDepartamento->infra_other_data_attribute_id = 8;
                            $nodeOtherDataDepartamento->node_id = $node_id;
                            $nodeOtherDataDepartamento->save();
                        }

                        $InfraOtherDataOptionDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataDepartamento->infra_other_data_attribute_id, trim($departamento));

                        if ($InfraOtherDataOptionDepartamento) {
                            if ($InfraOtherDataOptionDepartamento->infra_other_data_option_id != $nodeOtherDataDepartamento->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionDepartamento->infra_other_data_attribute_id != $nodeOtherDataDepartamento->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $departamento . ' Celda : F' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $departamento . ' Celda : F' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA UNIDAD
                    if ($unidad != NULL) {
                        $nodeOtherDataUnidad = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 10);

                        if (!$nodeOtherDataUnidad) {
                            $nodeOtherDataUnidad = new InfraOtherDataValue();
                            $nodeOtherDataUnidad->infra_other_data_attribute_id = 10;
                            $nodeOtherDataUnidad->node_id = $node_id;
                            $nodeOtherDataUnidad->save();
                        }

                        $InfraOtherDataOptionUnidad = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataUnidad->infra_other_data_attribute_id, trim($unidad));

                        if ($InfraOtherDataOptionUnidad) {
                            if ($InfraOtherDataOptionUnidad->infra_other_data_option_id != $nodeOtherDataUnidad->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionUnidad->infra_other_data_attribute_id != $nodeOtherDataUnidad->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $unidad . ' Celda : G' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $unidad . ' Celda : G' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA ACTIVIDAD
                    if ($actividad != NULL) {
                        $nodeOtherDataActividad = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 13);

                        if (!$nodeOtherDataActividad) {
                            $nodeOtherDataActividad = new InfraOtherDataValue();
                            $nodeOtherDataActividad->infra_other_data_attribute_id = 13;
                            $nodeOtherDataActividad->node_id = $node_id;
                            $nodeOtherDataActividad->save();
                        }

                        $InfraOtherDataOptionActividad = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataActividad->infra_other_data_attribute_id, trim($actividad));

                        if ($InfraOtherDataOptionActividad) {
                            if ($InfraOtherDataOptionActividad->infra_other_data_option_id != $nodeOtherDataActividad->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionActividad->infra_other_data_attribute_id != $nodeOtherDataActividad->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $actividad . ' Celda : H' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $actividad . ' Celda : H' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA USO
                    if ($uso != NULL) {
                        $nodeOtherDataUso = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 15);

                        if (!$nodeOtherDataUso) {
                            $nodeOtherDataUso = new InfraOtherDataValue();
                            $nodeOtherDataUso->infra_other_data_attribute_id = 15;
                            $nodeOtherDataUso->node_id = $node_id;
                            $nodeOtherDataUso->save();
                        }

                        $InfraOtherDataOptionUso = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataUso->infra_other_data_attribute_id, trim($uso));

                        if ($InfraOtherDataOptionUso) {
                            if ($InfraOtherDataOptionUso->infra_other_data_option_id != $nodeOtherDataUso->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionUso->infra_other_data_attribute_id != $nodeOtherDataUso->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $uso . ' Celda : I' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $uso . ' Celda : I' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA USO PARTICULAR
                    if ($uso_particular != NULL) {
                        $nodeOtherDataUsoParticular = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 17);

                        if (!$nodeOtherDataUsoParticular) {
                            $nodeOtherDataUsoParticular = new InfraOtherDataValue();
                            $nodeOtherDataUsoParticular->infra_other_data_attribute_id = 17;
                            $nodeOtherDataUsoParticular->node_id = $node_id;
                            $nodeOtherDataUsoParticular->save();
                        }

                        $InfraOtherDataOptionUsoParticular = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataUsoParticular->infra_other_data_attribute_id, trim($uso_particular));

                        if ($InfraOtherDataOptionUsoParticular) {
                            if ($InfraOtherDataOptionUsoParticular->infra_other_data_option_id != $nodeOtherDataUsoParticular->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionUsoParticular->infra_other_data_attribute_id != $nodeOtherDataUsoParticular->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $uso_particular . ' Celda : J' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $uso_particular . ' Celda : J' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA PROPIEDAD RECINTO
                    if ($propiedad_recinto != NULL) {
                        $nodeOtherDataPropiedadRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 19);

                        if (!$nodeOtherDataPropiedadRecinto) {
                            $nodeOtherDataPropiedadRecinto = new InfraOtherDataValue();
                            $nodeOtherDataPropiedadRecinto->infra_other_data_attribute_id = 19;
                            $nodeOtherDataPropiedadRecinto->node_id = $node_id;
                            $nodeOtherDataPropiedadRecinto->save();
                        }

                        $InfraOtherDataOptionPropiedadRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataPropiedadRecinto->infra_other_data_attribute_id, trim($propiedad_recinto));

                        if ($InfraOtherDataOptionPropiedadRecinto) {
                            if ($InfraOtherDataOptionPropiedadRecinto->infra_other_data_option_id != $nodeOtherDataPropiedadRecinto->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionPropiedadRecinto->infra_other_data_attribute_id != $nodeOtherDataPropiedadRecinto->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $propiedad_recinto . ' Celda : K' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $propiedad_recinto . ' Celda : K' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA ESTATUS RECINTO
                    if ($estatus_recinto != NULL) {
                        $nodeOtherDataEstatusRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 21);

                        if (!$nodeOtherDataEstatusRecinto) {
                            $nodeOtherDataEstatusRecinto = new InfraOtherDataValue();
                            $nodeOtherDataEstatusRecinto->infra_other_data_attribute_id = 21;
                            $nodeOtherDataEstatusRecinto->node_id = $node_id;
                            $nodeOtherDataEstatusRecinto->save();
                        }

                        $InfraOtherDataOptionEstatusRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataEstatusRecinto->infra_other_data_attribute_id, trim($estatus_recinto));

                        if ($InfraOtherDataOptionEstatusRecinto) {
                            if ($InfraOtherDataOptionEstatusRecinto->infra_other_data_option_id != $nodeOtherDataEstatusRecinto->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionEstatusRecinto->infra_other_data_attribute_id != $nodeOtherDataEstatusRecinto->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $estatus_recinto . ' Celda : L' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $estatus_recinto . ' Celda : L' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA ESTADO RECINTO
                    if ($estado_recinto != NULL) {
                        $nodeOtherDataEstadoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 23);

                        if (!$nodeOtherDataEstadoRecinto) {
                            $nodeOtherDataEstadoRecinto = new InfraOtherDataValue();
                            $nodeOtherDataEstadoRecinto->infra_other_data_attribute_id = 23;
                            $nodeOtherDataEstadoRecinto->node_id = $node_id;
                            $nodeOtherDataEstadoRecinto->save();
                        }

                        $InfraOtherDataOptionEstadoRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataEstadoRecinto->infra_other_data_attribute_id, trim($estado_recinto));

                        if ($InfraOtherDataOptionEstadoRecinto) {
                            if ($InfraOtherDataOptionEstadoRecinto->infra_other_data_option_id != $nodeOtherDataEstadoRecinto->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionEstadoRecinto->infra_other_data_attribute_id != $nodeOtherDataEstadoRecinto->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $estado_recinto . ' Celda : M' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $estado_recinto . ' Celda : M' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA CANTIDAD DE USUARIOS
                    if ($cantidad_usuarios != NULL) {
                        $nodeOtherDataCantidadUsuarios = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 25);

                        if (!$nodeOtherDataCantidadUsuarios) {
                            $nodeOtherDataCantidadUsuarios = new InfraOtherDataValue();
                            $nodeOtherDataCantidadUsuarios->infra_other_data_attribute_id = 25;
                            $nodeOtherDataCantidadUsuarios->node_id = $node_id;
                            $nodeOtherDataCantidadUsuarios->save();
                        }

                        if (is_numeric($cantidad_usuarios) != true) {
                            $success = 'false';
                            $msg = "El numero " . $cantidad_usuarios . ' Celda : N' . $rowIndex2 . " No es numérico";
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA VENTANAS
                    if ($ventanas != NULL) {
                        $nodeOtherDataVentanas = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 26);

                        if (!$nodeOtherDataVentanas) {
                            $nodeOtherDataVentanas = new InfraOtherDataValue();
                            $nodeOtherDataVentanas->infra_other_data_attribute_id = 26;
                            $nodeOtherDataVentanas->node_id = $node_id;
                            $nodeOtherDataVentanas->save();
                        }

                        $InfraOtherDataOptionVentanas = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataVentanas->infra_other_data_attribute_id, trim($ventanas));

                        if ($InfraOtherDataOptionVentanas) {
                            if ($InfraOtherDataOptionVentanas->infra_other_data_option_id != $nodeOtherDataVentanas->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionVentanas->infra_other_data_attribute_id != $nodeOtherDataVentanas->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $ventanas . ' Celda : O' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $ventanas . ' Celda : O' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA AIRE ACONDICIONADO
                    if ($aire_acondicionado != NULL) {
                        $nodeOtherDataAireAcondicionado = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 27);

                        if (!$nodeOtherDataAireAcondicionado) {
                            $nodeOtherDataAireAcondicionado = new InfraOtherDataValue();
                            $nodeOtherDataAireAcondicionado->infra_other_data_attribute_id = 27;
                            $nodeOtherDataAireAcondicionado->node_id = $node_id;
                            $nodeOtherDataAireAcondicionado->save();
                        }

                        $InfraOtherDataOptionAireAcondicionado = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataAireAcondicionado->infra_other_data_attribute_id, trim($aire_acondicionado));

                        if ($InfraOtherDataOptionAireAcondicionado) {
                            if ($InfraOtherDataOptionAireAcondicionado->infra_other_data_option_id != $nodeOtherDataAireAcondicionado->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionAireAcondicionado->infra_other_data_attribute_id != $nodeOtherDataAireAcondicionado->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $aire_acondicionado . ' Celda : P' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $aire_acondicionado . ' Celda : P' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA CALEFACCION
                    if ($calefaccion != NULL) {
                        $nodeOtherDataCalefaccion = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 28);

                        if (!$nodeOtherDataCalefaccion) {
                            $nodeOtherDataCalefaccion = new InfraOtherDataValue();
                            $nodeOtherDataCalefaccion->infra_other_data_attribute_id = 28;
                            $nodeOtherDataCalefaccion->node_id = $node_id;
                            $nodeOtherDataCalefaccion->save();
                        }

                        $InfraOtherDataOptionCalefaccion = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataCalefaccion->infra_other_data_attribute_id, trim($calefaccion));

                        if ($InfraOtherDataOptionCalefaccion) {
                            if ($InfraOtherDataOptionCalefaccion->infra_other_data_option_id != $nodeOtherDataCalefaccion->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionCalefaccion->infra_other_data_attribute_id != $nodeOtherDataCalefaccion->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $calefaccion . ' Celda : Q' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $calefaccion . ' Celda : Q' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA LUMINARIAS
                    if ($luminarias != NULL) {
                        $nodeOtherDataLuminarias = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 29);

                        if (!$nodeOtherDataLuminarias) {
                            $nodeOtherDataLuminarias = new InfraOtherDataValue();
                            $nodeOtherDataLuminarias->infra_other_data_attribute_id = 29;
                            $nodeOtherDataLuminarias->node_id = $node_id;
                            $nodeOtherDataLuminarias->save();
                        }

                        if (is_numeric($luminarias) != true) {
                            $success = 'false';
                            $msg = "El numero " . $luminarias . ' Celda : R' . $rowIndex2 . " No es numérico";
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA ENCHUFES        
                    if ($enchufes != NULL) {
                        $nodeOtherDataEnchufes = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 30);

                        if (!$nodeOtherDataEnchufes) {
                            $nodeOtherDataEnchufes = new InfraOtherDataValue();
                            $nodeOtherDataEnchufes->infra_other_data_attribute_id = 30;
                            $nodeOtherDataEnchufes->node_id = $node_id;
                            $nodeOtherDataEnchufes->save();
                        }

                        if (is_numeric($enchufes) != true) {
                            $success = 'false';
                            $msg = "El numero " . $enchufes . ' Celda : S' . $rowIndex2 . " No es numérico";
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA PUNTO DE RED
                    if ($puntos_de_red != NULL) {
                        $nodeOtherDataPuntoDeRed = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 31);

                        if (!$nodeOtherDataPuntoDeRed) {
                            $nodeOtherDataPuntoDeRed = new InfraOtherDataValue();
                            $nodeOtherDataPuntoDeRed->infra_other_data_attribute_id = 31;
                            $nodeOtherDataPuntoDeRed->node_id = $node_id;
                            $nodeOtherDataPuntoDeRed->save();
                        }


                        if (is_numeric($puntos_de_red) != true) {
                            $success = 'false';
                            $msg = "El numero " . $puntos_de_red . ' Celda : T' . $rowIndex2 . " No es numérico";
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA PROYECTOR
                    if ($proyector != NULL) {
                        $nodeOtherDataProyector = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 32);

                        if (!$nodeOtherDataProyector) {
                            $nodeOtherDataProyector = new InfraOtherDataValue();
                            $nodeOtherDataProyector->infra_other_data_attribute_id = 32;
                            $nodeOtherDataProyector->node_id = $node_id;
                            $nodeOtherDataProyector->save();
                        }

                        $InfraOtherDataOptionProyector = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataProyector->infra_other_data_attribute_id, trim($proyector));

                        if ($InfraOtherDataOptionProyector) {
                            if ($InfraOtherDataOptionProyector->infra_other_data_option_id != $nodeOtherDataProyector->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionProyector->infra_other_data_attribute_id != $nodeOtherDataProyector->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $proyector . ' Celda : U' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $proyector . ' Celda : U' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

                    //VALIDA WIFI
                    if ($wifi != NULL) {

                        $nodeOtherDataWifi = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 33);

                        if (!$nodeOtherDataWifi) {
                            $nodeOtherDataWifi = new InfraOtherDataValue();
                            $nodeOtherDataWifi->infra_other_data_attribute_id = 33;
                            $nodeOtherDataWifi->node_id = $node_id;
                            $nodeOtherDataWifi->save();
                        }

                        $InfraOtherDataOptionWifi = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataWifi->infra_other_data_attribute_id, trim($wifi));
                        if ($InfraOtherDataOptionWifi) {
                            if ($InfraOtherDataOptionWifi->infra_other_data_option_id != $nodeOtherDataWifi->infra_other_data_option_id) {
                                if ($InfraOtherDataOptionWifi->infra_other_data_attribute_id != $nodeOtherDataWifi->infra_other_data_attribute_id) {
                                    $success = 'false';
                                    $msg = "No corresponde a una opción de este campo " . $wifi . ' Celda : V' . $rowIndex2;
                                    echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                    exit;
                                }
                            }
                        } else {
                            $success = 'false';
                            $msg = "No existe como opción " . $wifi . ' Celda : V' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }
                    }

//                    var_dump($ultima_actualizacion);
//                    exit;
                    //VALIDA OBSERVACION RECINTO
                    if ($obeservacion_recinto != NULL) {
                        $nodeOtherDataObservacionRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 34);

                        if (!$nodeOtherDataObservacionRecinto) {
                            $nodeOtherDataObservacionRecinto = new InfraOtherDataValue();
                            $nodeOtherDataObservacionRecinto->infra_other_data_attribute_id = 34;
                            $nodeOtherDataObservacionRecinto->node_id = $node_id;
                            $nodeOtherDataObservacionRecinto->save();
                        }
                    }

                    //VALIDA ULTIMA ACTUALIZACION
                    //ACTUALIZA ULTIMA ACTIALIZACION
                    if ($ultima_actualizacion != NULL) {
//                            $timestamp = PHPExcel_Shared_Date::ExcelToPHP($ultima_actualizacion);
//                            $timestamp = $timestamp + 3600 * 24;
//                            $ultima_actualizacion = date("d/m/Y", $ultima_actualizacion);
//                            $time = strtotime($ultima_actualizacion);
//                            $newformat = date('Y/m/d',$time);
//
                        $nodeOtherDataUltimaActualizacion = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 38);

//                            if ($this->validateDate($newformat) == true) {
                        if (trim($ultima_actualizacion) != $nodeOtherDataUltimaActualizacion->infra_other_data_value_value) {
                            // Actualizar el nombre
//                                    var_dump($ultima_actualizacion);
//                                    exit;
                            $nodeOtherDataUltimaActualizacion->infra_other_data_value_value = $ultima_actualizacion;
                            $nodeOtherDataUltimaActualizacion->save();
//                                }
                        }
                    }

                    //VALIDA USUARIO
                    if ($usuario != NULL) {
                        $nodeOtherDataUsuario = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 398);

                        if (!$nodeOtherDataUsuario) {
                            $nodeOtherDataUsuario = new InfraOtherDataValue();
                            $nodeOtherDataUsuario->infra_other_data_attribute_id = 398;
                            $nodeOtherDataUsuario->node_id = $node_id;
                            $nodeOtherDataUsuario->save();
                        }
                    }

//                    //VALIDA SUPERFICIE 
//                    if ($superficie != NULL) {
//                        if (is_numeric($superficie) != true) {
//                            $success = 'false';
//                            $msg = "El numero " . $superficie . ' Celda : Z' . $rowIndex2 . " No es numérico";
//                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
//                            exit;
//                        }
//                    }
                }
            }

//            $success = 'true';
//            $msg = "VALIDADO TODO OK";
//            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
//            exit;
            ###########################
            ## AQUI INSERTA LO VALIDADO
            ###########################
//            $nodos = array();
            $cont = 0;
            try {

                foreach ($rowIterator2 as $row2) {

                    $rowIndex2 = $row2->getRowIndex();
                    if ($rowIndex2 > 1) {

                        $codigo_recinto = $objWorksheet2->getCell('A' . $rowIndex2)->getCalculatedValue();
                        $nombre_recinto = $objWorksheet2->getCell('B' . $rowIndex2)->getCalculatedValue();
                        $codigo_subrecinto = $objWorksheet2->getCell('C' . $rowIndex2)->getCalculatedValue();
                        $nombre_subrecinto = $objWorksheet2->getCell('D' . $rowIndex2)->getCalculatedValue();
                        $organismo = $objWorksheet2->getCell('E' . $rowIndex2)->getCalculatedValue();
                        $departamento = $objWorksheet2->getCell('F' . $rowIndex2)->getCalculatedValue();
                        $unidad = $objWorksheet2->getCell('G' . $rowIndex2)->getCalculatedValue();
                        $actividad = $objWorksheet2->getCell('H' . $rowIndex2)->getCalculatedValue();
                        $uso = $objWorksheet2->getCell('I' . $rowIndex2)->getCalculatedValue();
                        $uso_particular = $objWorksheet2->getCell('J' . $rowIndex2)->getCalculatedValue();
                        $propiedad_recinto = $objWorksheet2->getCell('K' . $rowIndex2)->getCalculatedValue();
                        $estatus_recinto = $objWorksheet2->getCell('L' . $rowIndex2)->getCalculatedValue();
                        $estado_recinto = $objWorksheet2->getCell('M' . $rowIndex2)->getCalculatedValue();
                        $cantidad_usuarios = $objWorksheet2->getCell('N' . $rowIndex2)->getCalculatedValue();
                        $ventanas = $objWorksheet2->getCell('O' . $rowIndex2)->getCalculatedValue();
                        $aire_acondicionado = $objWorksheet2->getCell('P' . $rowIndex2)->getCalculatedValue();
                        $calefaccion = $objWorksheet2->getCell('Q' . $rowIndex2)->getCalculatedValue();
                        $luminarias = $objWorksheet2->getCell('R' . $rowIndex2)->getCalculatedValue();
                        $enchufes = $objWorksheet2->getCell('S' . $rowIndex2)->getCalculatedValue();
                        $puntos_red = $objWorksheet2->getCell('T' . $rowIndex2)->getCalculatedValue();
                        $proyector = $objWorksheet2->getCell('U' . $rowIndex2)->getCalculatedValue();
                        $wifi = $objWorksheet2->getCell('V' . $rowIndex2)->getCalculatedValue();
                        $obeservacion_recinto = $objWorksheet2->getCell('W' . $rowIndex2)->getCalculatedValue();

                        $objPHPExcel2->getActiveSheet()->getStyle('X' . $rowIndex2)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                        $ultima_actualizacion = $objPHPExcel2->getActiveSheet()->getCell('X' . $rowIndex2)->getFormattedValue();
//                        $ultima_actualizacion = $objWorksheet2->getCell('X' . $rowIndex2)->getCalculatedValue();
                        $usuario = $objWorksheet2->getCell('Y' . $rowIndex2)->getCalculatedValue();
//                        $superficie = $objWorksheet2->getCell('Z' . $rowIndex2)->getCalculatedValue();

                        if ($codigo_recinto != NULL) {
                            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($codigo_recinto));

                            if ($nodeOtherData) {
                                $node_id = $nodeOtherData->node_id;
//                                $nodos[$cont] = $node_id;
                                $cont++;
                            } else {
                                $success = 'false';
                                $msg = "El codigo de recinto no existe " . $codigo_recinto . ' Celda : A' . $rowIndex2;
                                echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                                exit;
                            }
                        } else {
                            $success = 'false';
                            $msg = "Campo Obligatorio " . $codigo_recinto . ' Celda : A' . $rowIndex2;
                            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                            exit;
                        }


                        //ACTUALIZA NOMBRE DE RECINTO
                        if ($nombre_recinto != NULL) {
                            $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 2);

                            if (trim($nombre_recinto) != $nodeOtherDataNombreRecinto->infra_other_data_value_value) {
                                $nodeOtherDataNombreRecinto->infra_other_data_value_value = $nombre_recinto;
                                $nodeOtherDataNombreRecinto->save();
                            }
                        }

                        //ACTUALIZA CODIGO DE RECINTO
                        if ($codigo_subrecinto != NULL) {
                            $nodeOtherDataCodigoSubrecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 3);

                            if (trim($nombre_recinto) != $nodeOtherDataCodigoSubrecinto->infra_other_data_value_value) {
                                $nodeOtherDataCodigoSubrecinto->infra_other_data_value_value = $codigo_subrecinto;
                                $nodeOtherDataCodigoSubrecinto->save();
                            }
                        }

                        //ACTUALIZA NOMBRE SUBRECINTO
                        if ($nombre_subrecinto != NULL) {
                            $nodeOtherDataNombreSubrecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 4);

                            if (trim($nombre_recinto) != $nodeOtherDataNombreSubrecinto->infra_other_data_value_value) {
                                $nodeOtherDataNombreSubrecinto->infra_other_data_value_value = $nombre_subrecinto;
                                $nodeOtherDataNombreSubrecinto->save();
                            }
                        }

                        //ACTUALIZA ORGANISMO
                        if ($organismo != NULL) {
                            $nodeOtherDataOrganismo = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 6);
                            $InfraOtherDataOptionOrganismo = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataOrganismo->infra_other_data_attribute_id, trim($organismo));

                            if ($InfraOtherDataOptionOrganismo) {
                                if ($InfraOtherDataOptionOrganismo->infra_other_data_option_id != $nodeOtherDataOrganismo->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionOrganismo->infra_other_data_attribute_id == $nodeOtherDataOrganismo->infra_other_data_attribute_id) {
                                        $nodeOtherDataOrganismo->infra_other_data_option_id = $InfraOtherDataOptionOrganismo->infra_other_data_option_id;
                                        $nodeOtherDataOrganismo->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA DEPARTAMENTO
                        if ($departamento != NULL) {
                            $nodeOtherDataDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 8);
                            $InfraOtherDataOptionDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataDepartamento->infra_other_data_attribute_id, trim($departamento));

                            if ($InfraOtherDataOptionDepartamento) {
                                if ($InfraOtherDataOptionDepartamento->infra_other_data_option_id != $nodeOtherDataDepartamento->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionDepartamento->infra_other_data_attribute_id == $nodeOtherDataDepartamento->infra_other_data_attribute_id) {
                                        $nodeOtherDataDepartamento->infra_other_data_option_id = $InfraOtherDataOptionDepartamento->infra_other_data_option_id;
                                        $nodeOtherDataDepartamento->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA UNIDAD
                        if ($unidad != NULL) {
                            $nodeOtherDataUnidad = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 10);
                            $InfraOtherDataOptionUnidad = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataUnidad->infra_other_data_attribute_id, trim($unidad));

                            if ($InfraOtherDataOptionUnidad) {
                                if ($InfraOtherDataOptionUnidad->infra_other_data_option_id != $nodeOtherDataUnidad->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionUnidad->infra_other_data_attribute_id == $nodeOtherDataUnidad->infra_other_data_attribute_id) {
                                        $nodeOtherDataUnidad->infra_other_data_option_id = $InfraOtherDataOptionUnidad->infra_other_data_option_id;
                                        $nodeOtherDataUnidad->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA ACTIVIDAD
                        if ($actividad != NULL) {
                            $nodeOtherDataActividad = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 13);
                            $InfraOtherDataOptionActividad = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataActividad->infra_other_data_attribute_id, trim($actividad));

                            if ($InfraOtherDataOptionActividad) {
                                if ($InfraOtherDataOptionActividad->infra_other_data_option_id != $nodeOtherDataActividad->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionActividad->infra_other_data_attribute_id == $nodeOtherDataActividad->infra_other_data_attribute_id) {
                                        $nodeOtherDataActividad->infra_other_data_option_id = $InfraOtherDataOptionActividad->infra_other_data_option_id;
                                        $nodeOtherDataActividad->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA USO
                        if ($uso != NULL) {
                            $nodeOtherDataUso = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 15);
                            $InfraOtherDataOptionUso = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataUso->infra_other_data_attribute_id, trim($uso));

                            if ($InfraOtherDataOptionUso) {
                                if ($InfraOtherDataOptionUso->infra_other_data_option_id != $nodeOtherDataUso->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionUso->infra_other_data_attribute_id == $nodeOtherDataUso->infra_other_data_attribute_id) {
                                        $nodeOtherDataUso->infra_other_data_option_id = $InfraOtherDataOptionUso->infra_other_data_option_id;
                                        $nodeOtherDataUso->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA USO PARTICULAR
                        if ($uso_particular != NULL) {
                            $nodeOtherDataUsoParticular = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 17);
                            $InfraOtherDataOptionUsoParticular = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataUsoParticular->infra_other_data_attribute_id, trim($uso_particular));

                            if ($InfraOtherDataOptionUsoParticular) {
                                if ($InfraOtherDataOptionUsoParticular->infra_other_data_option_id != $nodeOtherDataUsoParticular->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionUsoParticular->infra_other_data_attribute_id == $nodeOtherDataUsoParticular->infra_other_data_attribute_id) {
                                        $nodeOtherDataUsoParticular->infra_other_data_option_id = $InfraOtherDataOptionUsoParticular->infra_other_data_option_id;
                                        $nodeOtherDataUsoParticular->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA PROPIEDAD RECINTO
                        if ($propiedad_recinto != NULL) {
                            $nodeOtherDataPropiedadRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 19);
                            $InfraOtherDataOptionPropiedadRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataPropiedadRecinto->infra_other_data_attribute_id, trim($propiedad_recinto));

                            if ($InfraOtherDataOptionPropiedadRecinto) {
                                if ($InfraOtherDataOptionPropiedadRecinto->infra_other_data_option_id != $nodeOtherDataPropiedadRecinto->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionPropiedadRecinto->infra_other_data_attribute_id == $nodeOtherDataPropiedadRecinto->infra_other_data_attribute_id) {
                                        $nodeOtherDataPropiedadRecinto->infra_other_data_option_id = $InfraOtherDataOptionPropiedadRecinto->infra_other_data_option_id;
                                        $nodeOtherDataPropiedadRecinto->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA ESTATUS RECINTO
                        if ($estatus_recinto != NULL) {
                            $nodeOtherDataEstatusRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 21);
                            $InfraOtherDataOptionEstatusRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataEstatusRecinto->infra_other_data_attribute_id, trim($estatus_recinto));

                            if ($InfraOtherDataOptionEstatusRecinto) {
                                if ($InfraOtherDataOptionEstatusRecinto->infra_other_data_option_id != $nodeOtherDataEstatusRecinto->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionEstatusRecinto->infra_other_data_attribute_id == $nodeOtherDataEstatusRecinto->infra_other_data_attribute_id) {
                                        $nodeOtherDataEstatusRecinto->infra_other_data_option_id = $InfraOtherDataOptionEstatusRecinto->infra_other_data_option_id;
                                        $nodeOtherDataEstatusRecinto->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA ESTADO RECINTO
                        if ($estado_recinto != NULL) {
                            $nodeOtherDataEstadoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 23);
                            $InfraOtherDataOptionEstadoRecinto = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataEstadoRecinto->infra_other_data_attribute_id, trim($estado_recinto));

                            if ($InfraOtherDataOptionEstadoRecinto) {
                                if ($InfraOtherDataOptionEstadoRecinto->infra_other_data_option_id != $nodeOtherDataEstadoRecinto->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionEstadoRecinto->infra_other_data_attribute_id == $nodeOtherDataEstadoRecinto->infra_other_data_attribute_id) {
                                        $nodeOtherDataEstadoRecinto->infra_other_data_option_id = $InfraOtherDataOptionEstadoRecinto->infra_other_data_option_id;
                                        $nodeOtherDataEstadoRecinto->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA CANTIDAD DE USUARIOS
                        if ($cantidad_usuarios == 0) {
                            $nodeOtherDataCantidadUsuarios = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 25);

                            if (is_numeric($cantidad_usuarios) == true) {
                                if (intval($cantidad_usuarios) != $nodeOtherDataCantidadUsuarios->infra_other_data_value_value) {
                                    $nodeOtherDataCantidadUsuarios->infra_other_data_value_value = $cantidad_usuarios;
                                    $nodeOtherDataCantidadUsuarios->save();
                                }
                            }
                        }

                        if ($cantidad_usuarios != NULL) {
                            $nodeOtherDataCantidadUsuarios = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 25);

                            if (is_numeric($cantidad_usuarios) == true) {
                                if (intval($cantidad_usuarios) != $nodeOtherDataCantidadUsuarios->infra_other_data_value_value) {
                                    $nodeOtherDataCantidadUsuarios->infra_other_data_value_value = $cantidad_usuarios;
                                    $nodeOtherDataCantidadUsuarios->save();
                                }
                            }
                        }

                        //ACTUALIZA VENTANAS
                        if ($ventanas != NULL) {
                            $nodeOtherDataVentanas = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 26);
                            $InfraOtherDataOptionVentanas = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataVentanas->infra_other_data_attribute_id, trim($ventanas));

                            if ($InfraOtherDataOptionVentanas) {
                                if ($InfraOtherDataOptionVentanas->infra_other_data_option_id != $nodeOtherDataVentanas->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionVentanas->infra_other_data_attribute_id == $nodeOtherDataVentanas->infra_other_data_attribute_id) {
                                        $nodeOtherDataVentanas->infra_other_data_option_id = $InfraOtherDataOptionVentanas->infra_other_data_option_id;
                                        $nodeOtherDataVentanas->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA AIRE ACONDICIONADO
                        if ($aire_acondicionado != NULL) {
                            $nodeOtherDataAireAcondicionado = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 27);
                            $InfraOtherDataOptionAireAcondicionado = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataAireAcondicionado->infra_other_data_attribute_id, trim($aire_acondicionado));

                            if ($InfraOtherDataOptionAireAcondicionado) {
                                if ($InfraOtherDataOptionAireAcondicionado->infra_other_data_option_id != $nodeOtherDataAireAcondicionado->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionAireAcondicionado->infra_other_data_attribute_id == $nodeOtherDataAireAcondicionado->infra_other_data_attribute_id) {
                                        $nodeOtherDataAireAcondicionado->infra_other_data_option_id = $InfraOtherDataOptionAireAcondicionado->infra_other_data_option_id;
                                        $nodeOtherDataAireAcondicionado->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA CALEFACCION
                        if ($calefaccion != NULL) {
                            $nodeOtherDataCalefaccion = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 28);
                            $InfraOtherDataOptionCalefaccion = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataCalefaccion->infra_other_data_attribute_id, trim($calefaccion));

                            if ($InfraOtherDataOptionCalefaccion) {
                                if ($InfraOtherDataOptionCalefaccion->infra_other_data_option_id != $nodeOtherDataCalefaccion->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionCalefaccion->infra_other_data_attribute_id == $nodeOtherDataCalefaccion->infra_other_data_attribute_id) {
                                        $nodeOtherDataCalefaccion->infra_other_data_option_id = $InfraOtherDataOptionCalefaccion->infra_other_data_option_id;
                                        $nodeOtherDataCalefaccion->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA LUMINARIAS
                        if ($luminarias == 0) {
                            $nodeOtherDataLuminarias = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 29);

                            if (is_numeric($luminarias) == true) {
                                if (intval($luminarias) != $nodeOtherDataLuminarias->infra_other_data_value_value) {
                                    $nodeOtherDataLuminarias->infra_other_data_value_value = $luminarias;
                                    $nodeOtherDataLuminarias->save();
                                }
                            }
                        }

                        if ($luminarias != NULL) {
                            $nodeOtherDataLuminarias = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 29);

                            if (is_numeric($luminarias) == true) {
                                if (intval($luminarias) != $nodeOtherDataLuminarias->infra_other_data_value_value) {
                                    $nodeOtherDataLuminarias->infra_other_data_value_value = $luminarias;
                                    $nodeOtherDataLuminarias->save();
                                }
                            }
                        }

                        //ACTUALIZA  ENCHUFES  
                        if ($enchufes == 0) {
                            $nodeOtherDataEnchufes = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 30);

                            if (is_numeric($enchufes) == true) {
                                if (intval($enchufes) != $nodeOtherDataEnchufes->infra_other_data_value_value) {
                                    $nodeOtherDataEnchufes->infra_other_data_value_value = $enchufes;
                                    $nodeOtherDataEnchufes->save();
                                }
                            }
                        }

                        if ($enchufes != NULL) {
                            $nodeOtherDataEnchufes = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 30);

                            if (is_numeric($enchufes) == true) {
                                if (intval($enchufes) != $nodeOtherDataEnchufes->infra_other_data_value_value) {
                                    $nodeOtherDataEnchufes->infra_other_data_value_value = $enchufes;
                                    $nodeOtherDataEnchufes->save();
                                }
                            }
                        }

                        //ACTUALIZA PUNTOS DE RED
                        if ($puntos_red == 0) {
                            $nodeOtherDataPuntoDeRed = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 31);

                            if (is_numeric($puntos_red) == true) {
                                if (intval($puntos_red) != $nodeOtherDataPuntoDeRed->infra_other_data_value_value) {

                                    $nodeOtherDataPuntoDeRed->infra_other_data_value_value = $puntos_red;
                                    $nodeOtherDataPuntoDeRed->save();
                                }
                            }
                        }

                        if ($puntos_red != NULL) {
                            $nodeOtherDataPuntoDeRed = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 31);

                            if (is_numeric($puntos_red) == true) {
                                if (intval($puntos_red) != $nodeOtherDataPuntoDeRed->infra_other_data_value_value) {

                                    $nodeOtherDataPuntoDeRed->infra_other_data_value_value = $puntos_red;
                                    $nodeOtherDataPuntoDeRed->save();
                                }
                            }
                        }

                        //ACTUALIZA PROYECTOR
                        if ($proyector != NULL) {
                            $nodeOtherDataProyector = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 32);
                            $InfraOtherDataOptionProyector = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataProyector->infra_other_data_attribute_id, trim($proyector));

                            if ($InfraOtherDataOptionProyector) {
                                if ($InfraOtherDataOptionProyector->infra_other_data_option_id != $nodeOtherDataProyector->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionProyector->infra_other_data_attribute_id == $nodeOtherDataProyector->infra_other_data_attribute_id) {
                                        $nodeOtherDataProyector->infra_other_data_option_id = $InfraOtherDataOptionProyector->infra_other_data_option_id;
                                        $nodeOtherDataProyector->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA WIFI
                        if ($wifi != NULL) {
                            $nodeOtherDataWifi = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 33);
                            $InfraOtherDataOptionWifi = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByAttributeName($nodeOtherDataWifi->infra_other_data_attribute_id, trim($wifi));

                            if ($InfraOtherDataOptionWifi) {
                                if ($InfraOtherDataOptionWifi->infra_other_data_option_id != $nodeOtherDataWifi->infra_other_data_option_id) {
                                    if ($InfraOtherDataOptionWifi->infra_other_data_attribute_id == $nodeOtherDataWifi->infra_other_data_attribute_id) {
                                        $nodeOtherDataWifi->infra_other_data_option_id = $InfraOtherDataOptionWifi->infra_other_data_option_id;
                                        $nodeOtherDataWifi->save();
                                    }
                                }
                            }
                        }

                        //ACTUALIZA OBSERVACION RECINTO
                        if ($obeservacion_recinto != NULL) {
                            $nodeOtherDataObservacionRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 34);

                            if (trim($obeservacion_recinto) != $nodeOtherDataObservacionRecinto->infra_other_data_value_value) {
                                $nodeOtherDataObservacionRecinto->infra_other_data_value_value = $obeservacion_recinto;
                                $nodeOtherDataObservacionRecinto->save();
                            }
                        }

                        //ACTUALIZA ULTIMA ACTIALIZACION

                        if ($ultima_actualizacion != NULL) {
//                            $timestamp = PHPExcel_Shared_Date::ExcelToPHP($ultima_actualizacion);
//                            $timestamp = $timestamp + 3600 * 24;
//                            $ultima_actualizacion = date("Y/m/d", $timestamp);
//                            $nodeOtherDataUltimaActualizacion = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 38);
//
//                            if ($this->validateDate($ultima_actualizacion) == true) {
//                                list ($dia, $mes, $anio) = explode("/", $ultima_actualizacion);
//                                $ultima_actualizacion = $anio . "/" . $mes . "/" . $dia;
//                                
                            if (trim($ultima_actualizacion) != $nodeOtherDataUltimaActualizacion->infra_other_data_value_value) {
//                                    // Actualizar el nombre
                                $nodeOtherDataUltimaActualizacion->infra_other_data_value_value = $ultima_actualizacion;
                                $nodeOtherDataUltimaActualizacion->save();
                            }
//                            }
                        }

                        //ACTUALIZA USUARIO
                        if ($usuario != NULL) {
                            $nodeOtherDataUsuario = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 398);

                            if (trim($usuario) != $nodeOtherDataUsuario->infra_other_data_value_value) {
                                $nodeOtherDataUsuario->infra_other_data_value_value = $usuario;
                                $nodeOtherDataUsuario->save();
                            }
                        }
//
//                        //ACTUALIZA SUPERFICIE
//                        if ($superficie != NULL) {
//                            if (is_numeric($superficie) == true) {
//
//                                $infoAntes = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
//                                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
//
//                                if ($info === false) {
//                                    $info = new InfraInfo();
//                                    $info->node_id = $node_id;
//                                }
//                                $info->allowListener = true;
//                                $info->infra_info_usable_area = $superficie;
//                                $info->save();
//
//                                $node = Doctrine::getTable('Node')->find($node_id);
//                                $log_id = $this->syslog->register('add_info_structural_data', array(
//                                    $node->node_name,
//                                    $node->getPath()
//                                )); // registering log
//                                //
//                                if (@$infoAntes->infra_info_usable_area != $info->infra_info_usable_area) {
//                                    $logDetail = new LogDetail();
//                                    $logDetail->log_id = $log_id;
//                                    $logDetail->log_detail_param = $this->translateTag('Infrastructure', 'infra_info_usable_area');
//                                    $logDetail->log_detail_value_old = @$infoAntes->infra_info_usable_area;
//                                    $logDetail->log_detail_value_new = $info->infra_info_usable_area;
//                                    $logDetail->save();
//                                }
//                            }
//                        }
                    }
                }
//                //ACTUALIZA LA SUPERFICIE TOTAL 
//                foreach ($nodos as $value) {
//                    $fieldMapping = array(
//                        'infra_info_usable_area' => 'getTotalUsableArea'
//                    );
//
//                    $node = Doctrine_Core::getTable('Node')->find($value)->getNode();
//                    if ($node->hasParent()) {
//                        foreach (array_reverse($node->getAncestors()->toArray()) as $ancestor) {
//                            $ancestorInfo = Doctrine_Core::getTable('InfraInfo')->findByNodeId($ancestor['node_id']);
//                            if ($ancestorInfo === false) {
//                                $ancestorInfo = new InfraInfo();
//                                $ancestorInfo->node_id = $ancestor['node_id'];
//                            }
//                            foreach ($fieldMapping as $att => $val) {
//                                $ancestorInfo->{$att . '_total'} = $this->{$fieldMapping[$att]}($ancestor['node_id']);
//                            }
//
//                            $ancestorInfo->save();
//                        }
//                    }
//                }
            } catch (Exception $e) {
                $success = false;
                $msg = $e->getMessage();
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
            }
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
            echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
            exit;
        }
    }

    function getTotalUsableArea($node_id) {

        $sum = Doctrine_Query::create()
                ->select('SUM(infra_info_usable_area +  infra_info_usable_area_total)')
                ->from('Node n')
                ->innerJoin('n.InfraInfo iff')
                ->where('n.node_id != ?', $node_id)
                ->groupBy('n.node_parent_id');

        $treeObject = Doctrine_Core::getTable('Node')->getTree();
        $treeObject->setBaseQuery($sum);
        $tree = $treeObject->fetchBranch($node_id, array('depth' => 1));
        $result = $tree->getFirst();

        return $result->SUM;
    }

    function validateDate($date, $format = 'Y/m/d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}
