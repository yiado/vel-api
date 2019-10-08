<?php

/**
 * @package    Controller
 * @subpackage ServiceLogController
 */
class ServiceLogController extends APP_Controller {

    function ServiceLogController() {
        parent::APP_Controller();
    }

    function get() {
        $requestLog = Doctrine_Core::getTable('ServiceLog')->findById($this->input->post('service_id'));
        if ($requestLog->count()) {
            echo '({"total":"' . $requestLog->count() . '", "results":' . $this->json->encode($requestLog->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

}
