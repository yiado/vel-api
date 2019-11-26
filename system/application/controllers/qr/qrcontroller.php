<?php

require 'vendor/autoload.php';

use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;

//use Endroid\QrCode\Response\QrCodeResponse;
//use Endroid\QrCode\ErrorCorrectionLevel;

/**
 * @package    Controller
 * @subpackage ServiceController
 */
class QrController extends APP_Controller {

    function QrController() {
        parent::Controller();
        header('Content-Type: application/json');
    }

    function get() {        
        $qrcode_file_name = "qr_node_{$this->input->post('node_id')}.png";

        if (!file_exists($this->config->item('qr_dir') . "{$qrcode_file_name}")) {
            $url = "{$this->config->item('base_url')}{$this->config->item('index_page')}/qr/redirect/{$this->input->post('node_id')}";
            $qrCode = new QrCode($url);
            $qrCode->setSize(500);
            $qrCode->setMargin(20);
            //$qrCode->setLabel($url, 10, null, LabelAlignment::CENTER);
            $qrCode->writeFile($this->config->item('qr_dir') . "{$qrcode_file_name}");
        }

        echo json_encode($this->config->item('qr_dir') . "{$qrcode_file_name}");
    }

    function redirect($node_id) {        
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $auth_engine_config = $this->config->item('auth_engine');
        $this->load->library($auth_engine_config);

        //Trae el lenguaje por defecto
        $dataDefaultLanguage = Doctrine_Core :: getTable('Language')->defaultLanguage();
        $language_id = $dataDefaultLanguage->language_id;

        //Validar Usuario y Password
        $validation = $this->$auth_engine_config->isValid($username, $password);
        if ($validation === TRUE) {
            $success = true;
            $user_data = $this->$auth_engine_config->getUser();
            $user_type = $user_data->user_type;
            $user_expiration = $user_data->user_expiration;
            $user_status = $user_data->user_status;

            //Fecha de expiración del usuario
            $user_expiration = strtotime($user_expiration);
            //Fecha del día de hoy
            $now = strtotime(date('Y-m-d'));

            //Validar si expiro la fecha del usuario
            if ($user_expiration < $now && $user_expiration > 0) {
                $success = false;
                $msg = $this->translateTag('General', 'its_validity_expired_user', $language_id);
            }

            //Validar si es un usuario inhabilitado
            if ($user_status == 1) {
                $success = false;
                $msg = $this->translateTag('General', 'disabled_users', $language_id);
            }
        } else {
            $success = false;
            if ($validation === FALSE) {
                $msg = $this->translateTag('General', 'invalid_authentication_data', $language_id);
            } else {
                $msg = $validation;
            }
        }

        if ($success === true) {
            $this->auth->session_start($this->$auth_engine_config->getUser());
            $data_to_json = array(
                'success' => true,
                'base_url' => base_url(), 'user_type' => $user_type);

            $this->syslog->register('auth', array(
                $this->auth->get_user_data('user_name'
        ))); // registering auth log
        } else {

            $data_to_json = array(
                'success' => false,
                'message' => $msg
            );
        }

        $json_data = $this->json->encode($data_to_json);
        echo $json_data;
    }

    function servicios() {
        if ($this->config->item('auth_require_login') && !$this->auth->is_logged_in()) {
            die(json_encode(array("status" => false, "msg" => "Usuario no logeado")));            
        }
        $servicios = Doctrine_Core::getTable('ServiceType')->findAll()->toArray();
        echo $this->json->encode($servicios);
    }

    function infraestructura($node_id) {
        if ($this->config->item('auth_require_login') && !$this->auth->is_logged_in()) {
            die(json_encode(array("status" => false, "msg" => "Usuario no logeado")));            
        }
        $node_id = (int) $node_id;

        if ($node_id) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $output['nodo'] = $node->node_name;
            $output['imagen'] = $this->getImagenNodo($node->node_id);
            $output['datos'] = $this->getResumen($node_id);
        } else {
            $output = array('total' => 0, 'results' => array());
        }
        echo $this->json->encode($output);
    }

    function getImagenNodo($node_id) {
        $imagen = null;
        $findGrId = Doctrine_Core::getTable('Node')->find($node_id);
        if ($findGrId) {
            $doc_document_id = (int) $findGrId->node_document_id_default;

            $docsTable = Doctrine_Core::getTable('DocDocument')->findByNodeDefault($doc_document_id);
            if ($docsTable) {
                $docs = $docsTable->toArray();
                $imagen = "{$this->config->item('base_url')}{$this->config->item('doc_dir2')}{$docs['DocCurrentVersion']['doc_version_filename']}";
            }
        }
        return $imagen;
        
        
    }

    function getResumen($node_id) {
        $infraestructura = array();
        $camposDinamicos = false;
        
        $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
        $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);

        //INFORMACION DE INFRAESTRUCTURA
        $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeIdConfig($nodeType->node_type_id);
        if ($infraConfig->count() >= 1) {
            foreach ($infraConfig as $config) {
                array_push($infraestructura, array(
                    'value' => ($info) ? $info->{$config->infra_attribute} : NULL,
                    'label' => $this->translateTag('Infrastructure', $config->infra_attribute)
                ));
            }
        }

        //DATOS DINAMICOS
        $attributes = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveByNodeTypeFichaResumen($nodeType->node_type_id);
        foreach ($attributes as $att) {
            $value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, $att->infra_other_data_attribute_id);
            if ($value) {                
                //SI ES TIPO SELECCION TRAE EL NOMBRE DEL CAMPO
                if ($att->InfraOtherDataAttribute->infra_other_data_attribute_type == 5) {
                    $valorDelCampo = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByOptionAndAtribute($value->infra_other_data_option_id, $att->InfraOtherDataAttribute->infra_other_data_attribute_id);
                    $value = @$valorDelCampo->infra_other_data_option_name;
                } else {
                    $value = @$value->infra_other_data_value_value;
                }
            } else {
                //SI NO ESTA CREADO EL CAMPO EN LA BASE DE DATOS LO PONE EN BLANCO
                $value = '';
            }
            $camposDinamicos[$att->InfraOtherDataAttribute->InfraGrupo->infra_grupo_nombre][] = array(
                'label' => $att->InfraOtherDataAttribute->infra_other_data_attribute_name,
                'value' => $value
            );
        }
        
        return array(
            'Infrastructura' => $infraestructura,
            'Dinamicos' => $camposDinamicos
        );
    }

    function servicio(){
        if ($this->config->item('auth_require_login') && !$this->auth->is_logged_in()) {
            die(json_encode(array("status" => false, "msg" => "Usuario no logeado")));            
        }
        $node = Doctrine_Core::getTable('Node')->find((int) $this->input->post('node_id'));

        //Obtenemos la conexión actual
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

        //Iniciamos la transacción
        $conn->beginTransaction();

        try {
            //Insertamos el nuevo documento en la tabla
            $service = new Service();
            $user_id = $this->auth->get_user_data()['user_id'];
            $service->node_id = $this->input->post('node_id');
            $service->user_id = $user_id;
            $service->service_type_id = $this->input->post('service_type_id');
            $service->service_status_id = 1;
            $service->service_organism = 'UChile';
            $service->service_phone = 987654321;
            $service->service_commentary = $this->input->post('service_commentary');
            $service->save();

            $serviceLog = new ServiceLog();
            $serviceLog->user_id = $service->user_id;
            $serviceLog->service_id = $service->service_id;
            $serviceLog->service_log_detail = 'Creación de Solicitud de Servicio';
            $serviceLog->save();

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');

            $conn->commit();

            //Enviar correo de Alerta de creación de Service
            $service->sendNotificationAdministrador($node);
            $service->sendNotificationRecibido();
        } catch (Exception $e) {
            //Si hay error, rollback de los cambios en la base de datos
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
}
