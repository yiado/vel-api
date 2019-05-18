<?php

/** @package    Controller
 *  @subpackage PlanTaskController
 */
class PlanTaskController extends APP_Controller
{
    function PlanTaskController ()
    {
        parent::APP_Controller ();
    }

    /**
     * 
     * Lista el plan con sus tareas
     * @param integer $mtn_plan_id
     * 
     */
    function get ()
    {
        $mtn_plan_id = $this->input->post ( 'mtn_plan_id' );
        $mtnPlanTaskTable = Doctrine_Core::getTable ( 'MtnPlanTask' );
        $mtnPlanTask = $mtnPlanTaskTable->retrieveAll ( $mtn_plan_id );

        if ( $mtnPlanTask->count () )
        {
            echo '({"total":"' . $mtnPlanTask->count () . '", "results":' . $this->json->encode ( $mtnPlanTask->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega una nueva tarea al Plan
     * 
     * @post integer mtn_plan_id
     * @post integer mtn_task_id
     * @post integer mtn_plan_task_interval 
     */
    function add ()
    {
        $mtn_plan_id = $this->input->post ( 'mtn_plan_id' );
        $mtn_task_id = $this->input->post ( 'mtn_task_id' );
        $mtn_plan_task_interval = $this->input->post ( 'mtn_plan_task_interval' );

        try
        {
            $mtnPlanTask = new MtnPlanTask();
            $mtnPlanTask->mtn_plan_id = $mtn_plan_id;
            $mtnPlanTask->mtn_task_id = $mtn_task_id;
            $mtnPlanTask->mtn_plan_task_interval = $mtn_plan_task_interval;
            $mtnPlanTask->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica una tarea al Plan
     * 
     * @post integer mtn_task_id
     * @post integer mtn_plan_task_interval 
     */
    function update ()
    {
        $mtnPlanT = Doctrine_Core::getTable ( 'MtnPlanTask' )->find ( $this->input->post ( 'mtn_plan_task_id' ) );
        $mtnPlanT[ 'mtn_task_id' ] = $this->input->post ( 'mtn_task_id' );
        $mtnPlanT[ 'mtn_plan_task_interval' ] = $this->input->post ( 'mtn_plan_task_interval' );
        $mtnPlanT->save ();
        echo '{"success": true}';
    }

    /**
     * delete
     *
     * Elimina una tarea del plan
     *
     * @param integer $mtn_task_id
     */
    function delete ()
    {
        $MtnPlanTask = Doctrine::getTable ( 'MtnPlanTask' )->find ( $this->input->post ( 'mtn_task_id' ) );

        if ( $MtnPlanTask->delete () )
        {
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        else
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
}
