<?php

/**
 * @package    Controller
 * @subpackage infrainfoConfigController
 */
class infraInfoConfigController extends APP_Controller {

    function infrainfoconfigController() {
        parent::APP_Controller();
    }

    /**
     * Obtiene la informaci�n de los "infos" posibles de asociar que no est�n asociados al tipo de nodo.
     *
     * @param integer $node_type_id
     * @method POST
     */
    function get() {
        $node_type_id = $this->input->post('node_type_id');

        //Query para obtener las etiquetas asociados al node_type_id
        $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeId($node_type_id);

        //Lees el array de etiquetas
        $fields = $this->config->item('fields_infra_info');
        $fields_free = array();

        //Filtrar; solo dejar las que no estan asociadas al node_type_id
        foreach ($infraConfig->toArray() as $value) {
            if (!empty($fields[$value['infra_attribute']])) {
                unset($fields[$value['infra_attribute']]);
            }
        }

        foreach ($fields as $key => $value) {
            array_push($fields_free, array(
                'field' => $key,
                'label' => $this->translateTag('Infrastructure', $key)
                    )
            );
        }

        $json_data = $this->json->encode(array('total' => count($fields_free), 'results' => $fields_free));
        echo $json_data;
    }

    function getExiste() {
        $node_type_id = $this->input->post('node_type_id');
        $infraConfigurationTable = Doctrine_Core::getTable('InfraConfiguration')->retrieveByNodeTypeExiste($node_type_id);

        if ($infraConfigurationTable) {
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
        } else {
            $success = false;
            $msg = $this->translateTag('Maintenance', 'select_a_type_of_node_or_save_configuration');
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function add() {
        $node_type_id = $this->input->post('node_type_id');
        $infoSelectedFields = explode(',', $this->input->post('itemselector'));

        try {
            //Obtenemos la conexi�n actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //	Iniciar transacci�n
            $conn->beginTransaction();

            //BUSCAR SI TIENE FICHA DE RESUMEN Y GUARDAR
            if (!empty($infoSelectedFields[0])) {
                foreach ($infoSelectedFields as $infra_attribute) {
                    $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->retrieveSumary($node_type_id, $infra_attribute);
                    
                    if (isset($infraConfig->infra_the_sumary)) {
                        if ($infraConfig->infra_the_sumary == 1) {
                            $auxTieneFichaResumen[] = $infraConfig->infra_attribute;
                        }
                    }
                }
            }


            //Eliminamos la config actual
            Doctrine_Core::getTable('InfraConfiguration')->deleteInfoConfigNodeType($node_type_id);

            //Insert de los fields en la configuraci�n para el tipo de nodo
            if (!empty($infoSelectedFields[0])) {
                foreach ($infoSelectedFields as $key => $field) {
                    $infraConfiguration = new InfraConfiguration();
                    $infraConfiguration->node_type_id = $node_type_id;
                    $infraConfiguration->infra_attribute = $field;
                    $infraConfiguration->infra_configuration_order = $key;

                   if (isset($auxTieneFichaResumen)) {
                        if (in_array($field, $auxTieneFichaResumen)) {
                            $infraConfiguration->infra_the_sumary = 1;
                        } else {
                            $infraConfiguration->infra_the_sumary = null;
                        }
                    }


                    $infraConfiguration->save();
                }
            }

            //Commit de la transacci�n
            $conn->commit();
            $success = true;

            //Imprime el Tag en pantalla
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            //Rollback de la transacci�n
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function addConfiguration() {
        $node_type_id = $this->input->post('node_type_id');
        $node_type_ids = explode(',', $this->input->post('node_type_ids'));
        $infoSelectedFields = explode(',', $this->input->post('itemselector'));

        $msg = "";

        foreach ($node_type_ids as $key => $node_typ) {
            $infraConfigurationTable = Doctrine_Core::getTable('InfraConfiguration')->retrieveByNodeTypeExiste($node_typ);
            if ($infraConfigurationTable) {
                $data[] = $node_typ;
                $AuxNode = $infraConfigurationTable->toArray();

                if ($AuxNode['NodeType']['node_type_id'] == $node_type_id) {
                    Doctrine_Core::getTable('InfraConfiguration')->deleteInfoConfigNodeType($AuxNode['NodeType']['node_type_id']);
                } else {
                    Doctrine_Core::getTable('InfraConfiguration')->deleteInfoConfigNodeType($AuxNode['NodeType']['node_type_id']);
                }
            }
        }

        //BUSCAR SI TIENE FICHA DE RESUMEN Y GUARDAR
        if (!empty($infoSelectedFields[0])) {
            foreach ($infoSelectedFields as $infra_attribute) {
                $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->retrieveSumary($node_type_id, $infra_attribute);

                if ($infraConfig->infra_the_sumary == 1) {
                    $auxTieneFichaResumen[] = $infraConfig->infra_attribute;
                }
            }
        }

        foreach ($node_type_ids as $node_ty) {
            if (!empty($infoSelectedFields[0])) {
                foreach ($infoSelectedFields as $key => $field) {
                    $infraConfiguration = new InfraConfiguration();
                    $infraConfiguration->node_type_id = $node_ty;
                    $infraConfiguration->infra_attribute = $field;
                    $infraConfiguration->infra_configuration_order = $key;
                   if (isset($auxTieneFichaResumen)) {
                        if (in_array($field, $auxTieneFichaResumen)) {
                            $infraConfiguration->infra_the_sumary = 1;
                        } else {
                            $infraConfiguration->infra_the_sumary = null;
                        }
                    }
                    $infraConfiguration->save();
                }
            }
        }
        $success = true;
        $msg = $this->translateTag('General', 'operation_successful');

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

}
