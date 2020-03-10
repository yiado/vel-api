<?php

/**
 * @package    Controller
 * @subpackage RdiStatusController
 */
class RdiStatusController extends APP_Controller {

    function RdiStatusController() {
        parent::APP_Controller();
    }

    function get() {
        $request = Doctrine_Core::getTable('RdiStatus')->retrieveAll($this->input->post('query'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() {
        try {
            $rdiStatus = new RdiStatus();
            $rdiStatus->rdi_status_name = $this->input->post('rdi_status_name');
            $rdiStatus->save();

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
            $rdiStatus = Doctrine_Core::getTable('RdiStatus')->find($this->input->post('rdi_status_id'));
            $rdiStatus->rdi_status_name = $this->input->post('rdi_status_name');
            $rdiStatus->save();

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
            $rdi_status_id = $this->input->post('rdi_status_id');
            $rdi = Doctrine::getTable('Rdi')->findByRdiStatusId($rdi_status_id);
            if (!count($rdi)) {
                $rdiStatus = Doctrine::getTable('RdiStatus')->find($rdi_status_id);
                if ($rdiStatus->delete()) {
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
