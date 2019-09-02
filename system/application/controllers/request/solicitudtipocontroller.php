<?php

/**
 * @package    Controller
 * @subpackage SolicitudTipoController
 */
class SolicitudTipoController extends APP_Controller {

    function SolicitudTipoController() {
        parent::APP_Controller();
    }

    function get() {
        $request = Doctrine_Core::getTable('SolicitudType')->retrieveAll($this->input->post('query'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() {
        try {
            $solicitudType = new SolicitudType();
            $solicitudType->solicitud_type_nombre = $this->input->post('solicitud_type_nombre');
            $solicitudType->solicitud_type_comentario = $this->input->post('solicitud_type_comentario');
            $solicitudType->save();

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function update() {
        try {
            $solicitudType = Doctrine_Core::getTable('SolicitudType')->find($this->input->post('solicitud_type_id'));
            $solicitudType->solicitud_type_nombre = $this->input->post('solicitud_type_nombre');
            $solicitudType->solicitud_type_comentario = $this->input->post('solicitud_type_comentario');
            $solicitudType->save();

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function delete() {
        try {
            $solicitud_type_id = $this->input->post('solicitud_type_id');
            $solicitud = Doctrine::getTable('Solicitud')->findBySolicitudTypeId($solicitud_type_id);
            if (!count($solicitud)) {
                $solicitudType = Doctrine::getTable('SolicitudType')->find($solicitud_type_id);
                if ($solicitudType->delete()) {
                    $success = true;
                    $msg = $this->translateTag('General', 'operation_successful');
                } else {
                    $success = false;
                    $msg = $this->translateTag('General', 'error');
                }
            } else {
                $success = false;
                $msg = $this->translateTag('Asset', 'type_assets_not_eliminated_associated_assets');
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

}
