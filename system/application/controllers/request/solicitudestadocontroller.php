<?php

/**
 * @package    Controller
 * @subpackage SolicitudEstadoController
 */
class SolicitudEstadoController extends APP_Controller {

    function SolicitudEstadoController() {
        parent::APP_Controller();
    }

    function get() {
        $request = Doctrine_Core::getTable('SolicitudEstado')->retrieveAll($this->input->post('query'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() {
        try {
            $solicitudEstado = new SolicitudEstado();
            $solicitudEstado->solicitud_estado_nombre = $this->input->post('solicitud_estado_nombre');
            $solicitudEstado->solicitud_estado_comentario = $this->input->post('solicitud_estado_comentario');
            $solicitudEstado->save();

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
            $solicitudEstado = Doctrine_Core::getTable('SolicitudEstado')->find($this->input->post('solicitud_estado_id'));
            $solicitudEstado->solicitud_estado_nombre = $this->input->post('solicitud_estado_nombre');
            $solicitudEstado->solicitud_estado_comentario = $this->input->post('solicitud_estado_comentario');
            $solicitudEstado->save();

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
            $solicitud_estado_id = $this->input->post('solicitud_estado_id');
            $solicitud = Doctrine::getTable('Solicitud')->findBySolicitudEstadoId($solicitud_estado_id);
            if (!count($solicitud)) {
                $solicitudEstado = Doctrine::getTable('SolicitudEstado')->find($solicitud_estado_id);
                if ($solicitudEstado->delete()) {
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
