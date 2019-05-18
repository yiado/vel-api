<?php

/**
 * @package Controller
 * @subpackage PlanController
 */
class PlanController extends APP_Controller {

    function PlanController() {
        parent::APP_Controller();
    }

    /**
     * get
     * 
     * Lista todos los planes existentes 
     */
    function get() {
        $text_autocomplete = $this->input->post('query');
        $mtnPlanTable = Doctrine_Core::getTable('MtnPlan');
        $maintainer_type = 1; //asset
        $mtnPlan = $mtnPlanTable->retrieveAll($text_autocomplete, $maintainer_type);

        if ($mtnPlan->count()) {
            echo '({"total":"' . $mtnPlan->count() . '", "results":' . $this->json->encode($mtnPlan->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getByNode() {
        $text_autocomplete = $this->input->post('query');
        $maintainer_type = 2; //node
        $mtnPlanTable = Doctrine_Core::getTable('MtnPlan');
        $mtnPlan = $mtnPlanTable->retrieveAll($text_autocomplete, $maintainer_type);

        if ($mtnPlan->count()) {
            echo '({"total":"' . $mtnPlan->count() . '", "results":' . $this->json->encode($mtnPlan->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega un nuevo plan
     * 
     * @post string mtn_plan_name
     * @post string mtn_plan_description 
     */
    function add() {
        //Recibimos los parametros
        $mtn_plan_name = $this->input->post('mtn_plan_name');
        $mtn_plan_description = $this->input->post('mtn_plan_description');
        $mtnPlan = new MtnPlan();
        $mtnPlan->mtn_plan_name = $mtn_plan_name;
        $mtnPlan->mtn_plan_description = $mtn_plan_description;
        $mtnPlan->mtn_maintainer_type_id = 1;

        try {
            $mtnPlan->save();
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
    function addByNode() {
        //Recibimos los parametros
        $mtn_plan_name = $this->input->post('mtn_plan_name');
        $mtn_plan_description = $this->input->post('mtn_plan_description');
        $mtnPlan = new MtnPlan();
        $mtnPlan->mtn_plan_name = $mtn_plan_name;
        $mtnPlan->mtn_plan_description = $mtn_plan_description;
        $mtnPlan->mtn_maintainer_type_id = 2;

        try {
            $mtnPlan->save();
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica un plan
     * 
     * @post int mtn_plan_id  
     * @post string mtn_plan_name
     * @post string mtn_plan_description 
     */
    function update() {
        $mtnPlan = Doctrine_Core::getTable('MtnPlan')->find($this->input->post('mtn_plan_id'));
        $mtnPlan['mtn_plan_name'] = $this->input->post('mtn_plan_name');
        $mtnPlan['mtn_plan_description'] = $this->input->post('mtn_plan_description');
        $mtnPlan->save();
        echo '{"success": true}';
    }

    /**
     * delete
     * 
     * Elimina un Plan y todas sus Tareas Plan asociadas
     * 
     * @post int mtn_plan_id
     */
    function delete() {
        $MtnPlan = Doctrine::getTable('MtnPlan')->find($this->input->post('mtn_plan_id'));

        if ($MtnPlan->delete()) {
            echo '{"success": true}';
        } else {
            echo '{"success": false}';
        }
    }

}
