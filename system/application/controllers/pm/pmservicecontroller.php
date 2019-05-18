<?php

/**
 * @package Controller
 * @subpackage PmServiceController
 */
class PmServiceController extends APP_Controller
{
    function PmServiceController ()
    {
        parent::APP_Controller ();
    }

    /** getList
     * 
     * Lista servicios de un nivel 
     */
    function getList ()
    {
        $pmServiceTable = Doctrine_Core::getTable ( 'PmService' );
        $pmService = $pmServiceTable->retrieveAll ();
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
     * Agrega un servicio a un nivel
     * 
     * @post string pm_service_descripcion 
     */
    function add ()
    {
        $pmService = new PmService();
        $pmService[ 'pm_service_name' ] = $this->input->post ( 'pm_service_name' );
        $pmService[ 'pm_service_description' ] = $this->input->post ( 'pm_service_description' );
        $pmService->save ();
        echo '{"success": true}';
    }

    /**
     * update
     * 
     * Modifica un servicio
     * 
     * @post int pm_service_id
     * @post string pm_service_descripcion
     */
    function update ()
    {
        $pmService = Doctrine_Core::getTable ( 'PmService' )->find ( $this->input->post ( 'pm_service_id' ) );
        $pmService[ 'pm_service_name' ] = $this->input->post ( 'pm_service_name' );
        $pmService[ 'pm_service_description' ] = $this->input->post ( 'pm_service_description' );
        $pmService->save ();
        echo '{"success": true}';
    }

    /** delete
     * 
     * Elimina servicio
     * @post int pm_service_id
     */
    function delete ()
    {
        $pmService = Doctrine::getTable ( 'PmService' )->find ( $this->input->post ( 'pm_service_id' ) );
        if ( $pmService->delete () )
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"success": false}';
        }
    }
}
