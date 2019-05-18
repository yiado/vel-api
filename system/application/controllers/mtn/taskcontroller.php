<?php

/** @package    Controller
 *  @subpackage TaskController
 */
class TaskController extends APP_Controller
{
    function TaskController ()
    {
        parent::APP_Controller ();
    }

    /**
     * getList
     * 
     * Retorna todas las tareas del sistema
     * 
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $mtn_plan_id = $this->input->post ( 'mtn_plan_id' );
        $taskTable = Doctrine_Core::getTable ( 'MtnTask' );
        $maintainer_type=1; //asset
        $task = $taskTable->retrieveAll ( $mtn_plan_id, $text_autocomplete,$maintainer_type );

        if ( $task->count () )
        {
            echo '({"total":"' . $task->count () . '", "results":' . $this->json->encode ( $task->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    function getByNode ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $mtn_plan_id = $this->input->post ( 'mtn_plan_id' );
        $taskTable = Doctrine_Core::getTable ( 'MtnTask' );
        $maintainer_type=2;//node
        $task = $taskTable->retrieveAll ( $mtn_plan_id, $text_autocomplete,$maintainer_type );

        if ( $task->count () )
        {
            echo '({"total":"' . $task->count () . '", "results":' . $this->json->encode ( $task->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
   

    function add ()
    {
        $task = new MtnTask();
        $task[ 'mtn_task_time' ] = $this->input->post ( 'mtn_task_time' );
        $task[ 'mtn_task_name' ] = $this->input->post ( 'mtn_task_name' );
        $task[ 'mtn_maintainer_type_id' ] = 1;

        try
        {
            $task->save ();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
    function addByNode ()
    {
        $task = new MtnTask();
        $task[ 'mtn_task_time' ] = $this->input->post ( 'mtn_task_time' );
        $task[ 'mtn_task_name' ] = $this->input->post ( 'mtn_task_name' );
        $task[ 'mtn_maintainer_type_id' ] = 2;

        try
        {
            $task->save ();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    function update ()
    {
        $task = Doctrine_Core::getTable ( 'MtnTask' )->find ( $this->input->post ( 'mtn_task_id' ) );
        $task[ 'mtn_task_time' ] = $this->input->post ( 'mtn_task_time' );
        $task[ 'mtn_task_name' ] = $this->input->post ( 'mtn_task_name' );
        $task->save ();
        echo '{"success": true}';
    }

    /**
     * delete
     * 
     * Elimina una tarea si no esta asociada a un plan
     * @param  integer $mtn_task_id
     */
    function delete ()
    {
        $mtn_task_id = $this->input->post ( 'mtn_task_id' );
        $MtnTask = Doctrine::getTable ( 'MtnTask' )->checkDataInPlanTask ( $mtn_task_id );
        if ( $MtnTask === false )
        {
            $MtnTaskId = Doctrine::getTable ( 'MtnTask' )->find ( $mtn_task_id );
            if ( $MtnTaskId->delete () )
            {
                $exito = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $exito = false;
                $msg = "Error";
            }
        }
        else
        {
            $exito = false;
            $msg = $this->translateTag ( 'Asset' , 'dynamic_data_not_eliminated_by_being_associated' );
        }
        $json_data = $this->json->encode ( array ( 'success' => $exito , 'msg' => $msg ) );
        echo $json_data;
    }

}