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
        $request = Doctrine_Core::getTable('ServiceType')->findAll();
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }
}
