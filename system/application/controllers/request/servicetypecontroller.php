<?php

/**
 * @package    Controller
 * @subpackage ServiceTypeController
 */
class ServiceTypeController extends APP_Controller {

    function ServiceTypeController() {
        parent::APP_Controller();
    }

    function get() {
        $request = Doctrine_Core::getTable('ServiceType')->retrieveAll($this->input->post('query'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() {
        try {
            $serviceType = new ServiceType();
            $serviceType->service_type_name = $this->input->post('service_type_name');
            $serviceType->service_type_commentary = $this->input->post('service_type_commentary');
            $serviceType->save();

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
            $serviceType = Doctrine_Core::getTable('ServiceType')->find($this->input->post('service_type_id'));
            $serviceType->service_type_name = $this->input->post('service_type_name');
            $serviceType->service_type_commentary = $this->input->post('service_type_commentary');
            $serviceType->save();

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
            $service_type_id = $this->input->post('service_type_id');
            $service = Doctrine::getTable('Service')->findByServiceTypeId($service_type_id);
            if (!count($service)) {
                $serviceType = Doctrine::getTable('ServiceType')->find($service_type_id);
                if ($serviceType->delete()) {
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
