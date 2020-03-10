<?php

/**
 * @package    Controller
 * @subpackage EvaluationController
 */
class EvaluationController extends APP_Controller{

    function EvaluationController() {
        parent::Controller();
    }

    function get() {
        $request = Doctrine_Core::getTable('RequestEvaluation')->retrieveAll($this->input->post('query'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }
    
    function rdi($token, $request_evalution_id, $rdi_id){
        if( is_numeric($request_evalution_id) && is_numeric($rdi_id) ) {
            $requestEvaluation = Doctrine_Core::getTable('RequestEvaluation')->find($request_evalution_id);
            $rdi = Doctrine_Core::getTable('Rdi')->findOneByRdiIdAndRdiToken($rdi_id, $token);
            if ($rdi && $requestEvaluation) {
                $rdi->rdi_token = null;
                $rdi->request_evaluation_id = $request_evalution_id;
                $rdi->save();
                echo $this->load->view("evaluation/evaluate", array(), true);
                die;
            } else {
                echo $this->load->view("evaluation/no_evaluate", array(), true);
                die;
            }
        }
    }
    
    function service($token, $request_evalution_id, $service_id) {
        if( is_numeric($request_evalution_id) && is_numeric($service_id) ) {
            $requestEvaluation = Doctrine_Core::getTable('RequestEvaluation')->find($request_evalution_id);
            $service = Doctrine_Core::getTable('Service')->findOneByServiceIdAndServiceToken($service_id, $token);
            if ($service && $requestEvaluation) {
                $service->service_token = null;
                $service->request_evaluation_id = $request_evalution_id;
                $service->save();
                echo $this->load->view("evaluation/evaluate", array(), true);
                die;
            } else {
                echo $this->load->view("evaluation/no_evaluate", array(), true);
                die;
            }
        }
    }
    
}
