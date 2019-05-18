<?php

/**
 * @package Controller
 * @subpackage CostsController
 */
class CostsMonthController extends APP_Controller {

    function CostsMonthController() {
        parent :: APP_Controller();
    }

    function getCostsMonth() {
        
        $CostsMonth = Doctrine_Core::getTable('CostsMonth')->findAll();

        if ($CostsMonth->count()) {
            echo '({"total":"' . $CostsMonth->count() . '", "results":' . $this->json->encode($CostsMonth->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
        
        
    }


}