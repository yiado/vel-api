<?php

/**
 * @package Controller
 * @subpackage PmLevelController
 */
class PmLevelController extends APP_Controller
{
    function PmLevelController ()
    {
        parent::APP_Controller ();
    }

    /** getList
     * 
     * Lista todos los niveles
     */
    function getList ()
    {
        $pmLevelTable = Doctrine_Core::getTable ( 'PmLevel' );
        $pmLevel = $pmLevelTable->retrieveAll ();
        if ( $pmLevel->count () )
        {
            echo '({"total":"' . $pmLevel->count () . '", "results":' . $this->json->encode ( $pmLevel->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * getListByPlan
     * 
     * Lista nivel por plan especifico
     * 
     */
    function getListByPlan ()
    {
        $pmLevelTable = Doctrine_Core::getTable ( 'PmLevel' );
        $pmLevel = $pmLevelTable->retrieveAll ( $this->input->post ( 'pm_plan_id' ) );
        if ( $pmLevel->count () )
        {
            echo '({"total":"' . $pmLevel->count () . '", "results":' . $this->json->encode ( $pmLevel->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega un nivel
     * 
     * @post int pm_plan_id 
     * @post string pm_plan_frequency 
     * @post string pm_level_name 
     * @post string pm_level_description 
     */
    function add ()
    {
        $pmLevel = new PmLevel();
        $pmLevel[ 'pm_plan_id' ] = $this->input->post ( 'pm_plan_id' );
        $pmLevel[ 'pm_level_frequency' ] = $this->input->post ( 'pm_level_frequency' );
        $pmLevel[ 'pm_level_name' ] = $this->input->post ( 'pm_level_name' );
        $pmLevel[ 'pm_level_description' ] = $this->input->post ( 'pm_level_description' );
        $pmLevel->save ();
        echo '{"success": true}';
    }

    /**
     * update
     * 
     * Modifica un nivel
     * 
     * @post int pm_plan_id
     * @post string pm_level_frequency
     * @post string pm_plan_name 
     * @post string pm_plan_descripcion 
     */
    function update ()
    {
        $pmLevel = Doctrine_Core::getTable ( 'PmLevel' )->find ( $this->input->post ( 'pm_level_id' ) );
        $pmLevel[ 'pm_plan_id' ] = $this->input->post ( 'pm_plan_id' );
        $pmLevel[ 'pm_level_frequency' ] = $this->input->post ( 'pm_level_frequency' );
        $pmLevel[ 'pm_level_name' ] = $this->input->post ( 'pm_level_name' );
        $pmLevel[ 'pm_level_description' ] = $this->input->post ( 'pm_level_description' );
        $pmLevel->save ();
        echo '{"success": true}';
    }

    /** delete
     * 
     * Elimina nivel
     * @post int pm_level_id
     */
    function delete ()
    {
        $pmLevel = Doctrine::getTable ( 'PmLevel' )->find ( $this->input->post ( 'pm_level_id' ) );

        if ( $pmLevel->delete () )
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"success": false}';
        }
    }
}
