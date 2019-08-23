<?php

/**
 * @package    Controller
 * @subpackage ServiceStatusController
 */
class ServiceStatusController extends APP_Controller {

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

}
