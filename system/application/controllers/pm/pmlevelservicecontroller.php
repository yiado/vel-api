<?php

/**
 * @package Controller
 * @subpackage PmLevelServiceController
 */
class PmLevelServiceController extends APP_Controller
{
    function PmLevelServiceController ()
    {
        parent::APP_Controller ();
    }

    /** getList
     * 
     * Retorna servicio(s)asociado(s) a un nivel 
     */
    function getList ()
    {
        $pmLevelServiceTable = Doctrine_Core::getTable ( 'PmLevelService' );
        $pmLevelService = $pmLevelServiceTable->retrieveAll ();
        if ( $pmLevelService->count () )
        {
            echo '({"total":"' . $pmLevelService->count () . '", "results":' . $this->json->encode ( $pmLevelService->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * getListByLevel
     * 
     * Lista servicios por nivel determinado
     * 
     * @post int pm_level_id
     */
    function getListByLevel ()
    {
        $pmServiceTable = Doctrine_Core::getTable ( 'PmLevelService' );
        $pmService = $pmServiceTable->retrieveServiceLevel ( $this->input->post ( 'pm_level_id' ) );
        print_r ( $pmService );

        if ( $pmService->count () )
        {
            echo '({"total":"' . $pmService->count () . '", "results":' . $this->json->encode ( $pmService->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega un nivel servicio
     * 
     * @post int pm_service_id 
     * @post int pm_level_id 
     */
    function add ()
    {
        $pmLevelService = new PmLevelService();
        $pmLevelService[ 'pm_service_id' ] = $this->input->post ( 'pm_service_id' );
        $pmLevelService[ 'pm_level_id' ] = $this->input->post ( 'pm_level_id' );
        $pmLevelService->save ();
        echo '{"success": true}';
    }

    /**
     * update
     * 
     * Modifica un nivel servicio
     * 
     * @post int pm_level_services_id
     * @post int pm_service_id
     * @post string pm_level_id
     */
    function update ()
    {
        $pmLevelService = Doctrine_Core::getTable ( 'PmLevelService' )->find ( $this->input->post ( 'pm_level_services_id' ) );
        $pmLevelService[ 'pm_service_id' ] = $this->input->post ( 'pm_service_id' );
        $pmLevelService[ 'pm_level_id' ] = $this->input->post ( 'pm_level_id' );
        $pmLevelService->save ();
        echo '{"success": true}';
    }

    /** delete
     * 
     * Elimina un nivel servicio
     * @post int pm_level_services_id
     */
    function delete ()
    {
        $pmLevelService = Doctrine::getTable ( 'PmLevelService' )->find ( $this->input->post ( 'pm_level_services_id' ) );

        if ( $pmLevelService->delete () )
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"success": false}';
        }
    }
}
