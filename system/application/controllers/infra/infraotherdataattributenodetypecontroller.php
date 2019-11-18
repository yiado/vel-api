<?php

/** @package    Controller
 *  @subpackage InfraOtherDataAttributeNodeTypeController
 */
class InfraOtherDataAttributeNodeTypeController extends APP_Controller {

    function InfraOtherDataAttributeNodeTypeController() {
        parent::APP_Controller();
    }

    /**
     * getList
     *
     * Lista los atributos de info segun tipo de nodo
     *
     * @post int infra_other_data_attribute_node_type_id
     */
    function get() {
        $infra_other_data_attribute_node_type_id = $this->input->post('infra_other_data_attribute_node_type_id');
        $infoAttNodeTypeTable = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType');
        $infoAttNodeType = $infoAttNodeTypeTable->retrieveByNodeType($infra_other_data_attribute_node_type_id);

        if ($infoAttNodeType->count()) {
            echo '({"total":"' . $infoAttNodeType->count() . '", "results":' . $this->json->encode($infoAttNodeType->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function updateSumary() {


        $node_type_id = $this->input->post('node_type_id');
        $infra_other_data_attribute_id = $this->input->post('infra_other_data_attribute_id');

        $InfraOtherDataAttributeNodeType = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveSumary($node_type_id, $infra_other_data_attribute_id);


        if ($InfraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary == 1) {
            $InfraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary = null;
        } else {
            $InfraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary = 1;
        }

        $InfraOtherDataAttributeNodeType->save();
    }

    function getExiste() {
        $node_type_id = $this->input->post('node_type_id');
        $infoAttNodeTypeTable = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveByNodeTypeExiste($node_type_id);

        if ($infoAttNodeTypeTable) {
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
        } else {
            $success = false;
            $msg = $this->translateTag('Maintenance', 'select_a_type_of_node_or_save_configuration');
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * add
     *
     * Agrega un nuevo atributo para info segun el tipo de nodo
     *
     * @post int infra_other_data_attribute_id
     * @post int node_type_id
     */
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
                foreach ($infoSelectedFields as $infra_other_data_attribute_id) {
                    $InfraOtherDataAttributeNodeType = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveSumary($node_type_id, $infra_other_data_attribute_id);
                    
                    if (isset($InfraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary)){
                        if ($InfraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary == 1) {
                            $auxTieneFichaResumen[] = $InfraOtherDataAttributeNodeType->infra_other_data_attribute_id;
                        }
                        
                    }
                    
                }
            }




            //Eliminamos la config actual
            Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->deleteInfoAttributeNodeType($node_type_id);

            //Insert de los fields en la configuraci�n para el tipo de nodo
            if (!empty($infoSelectedFields[0])) {
                foreach ($infoSelectedFields as $key => $field) {
                    $infraOtherDataAttributeNodeType = new InfraOtherDataAttributeNodeType();
                    $infraOtherDataAttributeNodeType->infra_other_data_attribute_id = $field;
                    $infraOtherDataAttributeNodeType->node_type_id = $node_type_id;
                    $infraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_order = $key;
                    if (isset($auxTieneFichaResumen)) {
                        if (in_array($field, $auxTieneFichaResumen)) {
                            $infraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary = 1;
                        } else {
                            $infraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary = null;
                        }
                    }
                    $infraOtherDataAttributeNodeType->save();
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
            $infoAttNodeTypeTable = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveByNodeTypeExiste($node_typ);
            if ($infoAttNodeTypeTable) {
                $data[] = $node_typ;
                $AuxNode = $infoAttNodeTypeTable->toArray();

                if ($AuxNode['NodeType']['node_type_id'] == $node_type_id) {
                    Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->deleteInfoAttributeNodeType($AuxNode['NodeType']['node_type_id']);
                } else {
                    Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->deleteInfoAttributeNodeType($AuxNode['NodeType']['node_type_id']);
                }
            }
        }
        //BUSCAR SI TIENE FICHA DE RESUMEN Y GUARDAR
        if (!empty($infoSelectedFields[0])) {
            foreach ($infoSelectedFields as $infra_other_data_attribute_id) {
                $InfraOtherDataAttributeNodeType = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveSumary($node_type_id, $infra_other_data_attribute_id);

                if ($InfraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary == 1) {
                    $auxTieneFichaResumen[] = $InfraOtherDataAttributeNodeType->infra_other_data_attribute_id;
                }
            }
        }

        foreach ($node_type_ids as $node_ty) {
            if (!empty($infoSelectedFields[0])) {
                foreach ($infoSelectedFields as $key => $field) {

                    $infraOtherDataAttributeNodeType = new InfraOtherDataAttributeNodeType();
                    $infraOtherDataAttributeNodeType->infra_other_data_attribute_id = $field;
                    $infraOtherDataAttributeNodeType->node_type_id = $node_ty;
                    $infraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_order = $key;
                    if (isset($auxTieneFichaResumen)) {
                        if (in_array($field, $auxTieneFichaResumen)) {
                            $infraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary = 1;
                        } else {
                            $infraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_the_sumary = null;
                        }
                    }
                    $infraOtherDataAttributeNodeType->save();
                }
            }
        }
        $success = true;
        $msg = $this->translateTag('General', 'operation_successful');
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica atributo de info de nodo tipo
     *
     * @post int infra_other_data_attribute_node_type_id
     * @post int infra_other_data_attribute_id
     * @post int node_type_id
     */
    function update() {
        $infoAttNodeType = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->find($this->input->post('infra_other_data_attribute_node_type_id'));
        $infoAttNodeType['infra_other_data_attribute_id'] = $this->input->post('infra_other_data_attribute_id ');
        $infoAttNodeType['node_type_id'] = $this->input->post('node_type_id');

        try {
            $infoAttNodeType->save();
            $success = true;

            //Imprime el Tag en pantalla
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     *
     * Elimina atributo de info segun tipo de nodo
     *
     * @post int infra_other_data_attribute_node_type_id
     */
    function delete() {
        $infoAttNodeType = Doctrine::getTable('InfraOtherDataAttributeNodeType')->find($this->input->post('infra_other_data_attribute_node_type_id'));

        if ($infoAttNodeType->delete()) {
            echo '{"success": true}';
        } else {
            echo '{"success": false}';
        }
    }

}
