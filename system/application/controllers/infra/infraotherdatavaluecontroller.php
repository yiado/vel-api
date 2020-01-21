<?php

/** @package    Controller
 *  @subpackage InfraOtherDataValueController
 */
class InfraOtherDataValueController extends APP_Controller {

    function InfraOtherDataValueController() {
        parent::APP_Controller();
    }

    /**
     * get
     * 
     * Lista los valores de info
     * 
     * @post int node_id
     */
    function get() {
        $node_id = $this->input->post('node_id');

        if (is_numeric($node_id)) {
            $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
            $attributes = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveByNodeType($nodeType->node_type_id);
            $result = array();
            $cont = 0;

            foreach ($attributes as $att) {
                $value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, $att->infra_other_data_attribute_id);
                $result[$cont] = array();
                $result[$cont]['infra_other_data_attribute_id'] = $att->infra_other_data_attribute_id;
                $result[$cont]['infra_other_data_attribute_type'] = $att->InfraOtherDataAttribute->infra_other_data_attribute_type;
                $result[$cont]['value'] = ($value) ? ($att->InfraOtherDataAttribute->infra_other_data_attribute_type == 5 ? $value->infra_other_data_option_id : $value->infra_other_data_value_value) : NULL;
                $result[$cont]['label'] = $att->InfraOtherDataAttribute->infra_other_data_attribute_name;
                $cont++;
            }
            $output = array('total' => $attributes->count(), 'results' => $result);
        } else {
            $output = array('total' => 0, 'results' => array());
        }
        echo $this->json->encode($output);
    }

    function getResumen() {

        $node_id = $this->input->post('node_id');

        if (is_numeric($node_id)) {
            $number_format = $this->config->item('number_format');
            $this->load->library('TreeNodes');
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $nodes = $treeObject->fetchRoots();

            if ($nodes[0]->node_id == $node_id) {
                $result = Doctrine_Core::getTable('InfraCoordinate')->nodeChildData($node_id);
                $cont = count($result);
            } else {

                $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);

                $result = array();
                $cont = 0;

                //INFORMACION DE INFRAESTRUCTURA
                $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeIdConfig($nodeType->node_type_id);

                if ($infraConfig->count() >= 1) {

                    foreach ($infraConfig as $config) {
                        $result[$cont] = array();
                        $result[$cont]['value'] = ($info) ? $info->{$config->infra_attribute} : NULL;
                        if (is_numeric($result[$cont]['value'])) {
                            $result[$cont]['value'] = number_format($result[$cont]['value'], $number_format['decimal'], $number_format['decimal_simbol'], $number_format['miles_simbol']);
                        }
                        $result[$cont]['label'] = $this->translateTag('Infrastructure', $config->infra_attribute);
                        $cont++;
                    }
                }

                //INFORMACION DE OTROS DATOS
                $attributes = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveByNodeTypeFichaResumen($nodeType->node_type_id);

                foreach ($attributes as $att) {
                    $value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, $att->infra_other_data_attribute_id);

                    if ($value) {//OJO Q AVECES NO ESTA CREADO EL CAMPO EN LA BASE 
                        if ($att->InfraOtherDataAttribute->infra_other_data_attribute_type == 5) {//SI ES TIPO SELECCION TRAE EL NOMBRE DEL CAMPO                      
                            $valorDelCampo = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByOptionAndAtribute($value->infra_other_data_option_id, $att->InfraOtherDataAttribute->infra_other_data_attribute_id);
                            $result[$cont]['value'] = @$valorDelCampo->infra_other_data_option_name;
                        } else {
                            $result[$cont]['value'] = @$value->infra_other_data_value_value;
                        }
                    } else {
                        //SI NO ESTA CREADO EL CAMPO EN LA BASE DE DATOS LO PONE EN BLANCO
                        $result[$cont]['value'] = '';
                    }

                    if ($att->InfraOtherDataAttribute->infra_other_data_attribute_type === '3') {
                        // tpye float
                        $result[$cont]['value'] = number_format($result[$cont]['value'], $number_format['decimal'], $number_format['decimal_simbol'], $number_format['miles_simbol']);
                    } else if ($att->InfraOtherDataAttribute->infra_other_data_attribute_type === '2') {
                        // type int
                        $result[$cont]['value'] = number_format($result[$cont]['value'], 0, '', $number_format['miles_simbol']);
                    }
                    $result[$cont]['label'] = $att->InfraOtherDataAttribute->infra_other_data_attribute_name;
                    $cont++;
                }
            }
            $output = array('total' => $cont, 'results' => $result);
        } else {
            $output = array('total' => 0, 'results' => array());
        }
        echo $this->json->encode($output);
    }

    /**
     * 
     * Guarda la informacion de un nodo
     * 
     * @post int node_id
     * @post misc
     * 
     */
    function add() {
        $node_id = $this->input->post('node_id');
        $node = Doctrine::getTable('Node')->find($node_id);

        try {
            //Obtenemos la conexiÃ³n actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //Iniciar transacciÃ³n
            $conn->beginTransaction();

            $log_id = $this->syslog->register('add_other_data_value', array(
                $node->node_name,
                $node->getPath()
            )); // registering log

            foreach ($this->input->postall() as $att => $val) {
                if (!is_numeric($att))
                    continue;

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
                                $logDetail->log_detail_value_old = $infra_other_data_attribute_name . " " . $other_antes->infra_other_data_option_name;
                                $logDetail->log_detail_value_new = $infra_other_data_attribute_name . " " . $other_ahora->infra_other_data_option_name;
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

                $value->save();
            }
            //Commit de la transacciÃ³n
            $conn->commit();
            $success = true;



            //Imprime el Tag en pantalla
            $msg = $this->translateTag('Infrastructure', 'node_information_stored_successfully');
        } catch (Exception $e) {
            //Rollback de la transacciÃ³n
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica un valor de info
     * 
     * @post int info_value_id
     * @post int infra_other_data_attribute_id
     * @post int node_id
     * @post int infra_other_data_option_id
     * @post int infra_other_data_value_value
     */
    function update() {
        $infraOtherDataOption = Doctrine_core::getTable('InfraOtherDataValue')->find($this->input->post('info_value_id'));
        $infraOtherDataValue['infra_other_data_attribute_id'] = $this->input->post('infra_other_data_attribute_id');
        $infraOtherDataValue['node_id'] = $this->input->post('node_id');
        $infraOtherDataValue['infra_other_data_option_id'] = $this->input->post('infra_other_data_option_id');
        $infraOtherDataValue['infra_other_data_value_value'] = $this->input->post('infra_other_data_value_value');

        try {
            $infraOtherDataValue->save();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag('General', 'record_updated_successfully');
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
     * Elimina un valor de info
     * 
     * @post int info_value_id
     */
    function delete() {
        $InfraOtherDataValue = Doctrine::getTable('InfraOtherDataValue')->find($this->input->post('info_value_id'));

        if ($InfraOtherDataValue->delete()) {
            echo '{"success": true}';
        } else {
            echo '{"success": false}';
        }
    }

}
