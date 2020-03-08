<?php

/**
 * @package    Controller
 * @subpackage EvaluationController
 */
class EvaluationController extends APP_Controller{

    function EvaluationController() {
        parent::APP_Controller();
    }

    function get() {
        $request = Doctrine_Core::getTable('RequestEvaluation')->retrieveAll($this->input->post('query'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }
    
}
