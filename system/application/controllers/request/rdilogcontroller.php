<?php

/**
 * @package    Controller
 * @subpackage RdiLogController
 */
class RdiLogController extends APP_Controller {

    function RdiLogController() {
        parent::APP_Controller();
    }

    function get() {
        $requestLog = Doctrine_Core::getTable('RdiLog')->findById($this->input->post('rdi_id'));
        if ($requestLog->count()) {
            echo '({"total":"' . $requestLog->count() . '", "results":' . $this->json->encode($requestLog->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

}
