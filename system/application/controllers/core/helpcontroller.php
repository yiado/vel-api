<?php

/**
 * @package Controller
 * @subpackage userController
 */
class HelpController extends APP_Controller {

    function HelpController() {
        parent :: APP_Controller();
    }

    /**
     * Lista todos los usuarios del sistema
     * @param string $query (opcional)
     */
    function get() {
 
        $Help = Doctrine_Core::getTable('Help')->retrieveBySort();
        echo '({"total":"' . $Help->count() . '", "results":' . $this->json->encode($Help->toArray()) . '})';
    }

}
