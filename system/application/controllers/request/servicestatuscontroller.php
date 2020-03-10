<?php

/**
 * @package    Controller
 * @subpackage ServiceStatusController
 */
class ServiceStatusController extends APP_Controller{

    function ServiceStatusController() {
        parent::APP_Controller();
    }

    function get() {
        $request = Doctrine_Core::getTable('ServiceStatus')->retrieveAll($this->input->post('query'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() {
        try {
            $serviceStatus = new ServiceStatus();
            $serviceStatus->service_status_name = $this->input->post('service_status_name');
            $serviceStatus->service_status_commentary = $this->input->post('service_status_commentary');
            $serviceStatus->save();

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
            $serviceStatus = Doctrine_Core::getTable('ServiceStatus')->find($this->input->post('service_status_id'));
            $serviceStatus->service_status_name = $this->input->post('service_status_name');
            $serviceStatus->service_status_commentary = $this->input->post('service_status_commentary');
            $serviceStatus->save();

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
            $service_status_id = $this->input->post('service_status_id');
            $service = Doctrine::getTable('Service')->findByServiceStatusId($service_status_id);
            if (!count($service)) {
                $serviceStatus = Doctrine::getTable('ServiceStatus')->find($service_status_id);
                if ($serviceStatus->delete()) {
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
