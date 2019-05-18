<?php

/**
 * @package Controller
 * @subpackage PmPlanController
 */
class PmPlanController extends APP_Controller
{
    function PmPlanController ()
    {
        parent::APP_Controller ();
    }

    /** getList
     * Lista los planes de mantencion
     */
    function getList ()
    {
        $pmPlanTable = Doctrine_Core::getTable ( 'PmPlan' );
        $pmPlan = $pmPlanTable->retrieveAll ();

        if ( $pmPlan->count () )
        {
            echo '({"total":"' . $pmPlan->count () . '", "results":' . $this->json->encode ( $pmPlan->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega plan de mantenci�n
     * 
     * @post string pm_plan_name 
     */
    function add ()
    {
        $pmPlan = new PmPlan();
        $pmPlan[ 'pm_plan_name' ] = $this->input->post ( 'pm_plan_name' );
        $pmPlan->save ();
        echo '{"success": true}';
    }

    /**
     * update
     * 
     * Modifica plan de mantencion
     * 
     * @post int pm_plan_id
     * @post string pm_plan_name
     */
    function update ()
    {
        $pmPlan = Doctrine_Core::getTable ( 'PmPlan' )->find ( $this->input->post ( 'pm_plan_id' ) );
        $pmPlan[ 'pm_plan_name' ] = $this->input->post ( 'pm_plan_name' );
        $pmPlan->save ();
        echo '{"success": true}';
    }

    /** delete
     * 
     * Elimina plan de mantenci�n
     * @post int pm_plan_id
     */
    function delete ()
    {
        $pmPlan = Doctrine::getTable ( 'PmPlan' )->find ( $this->input->post ( 'pm_plan_id' ) );
        if ( $pmPlan->delete () )
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"success": false}';
        }
    }
}
