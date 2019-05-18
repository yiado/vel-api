<?php

/**
 * @package    Controller
 * @subpackage RequestProblemController
 */
class RequestStatusController extends APP_Controller
{
    function RequestStatusController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista todos los tipos de equipos existentes
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $RequestStatus = Doctrine_Core::getTable ( 'RequestStatus' )->retrieveAll ($text_autocomplete);

        if ( $RequestStatus->count () )
        {
            echo '({"total":"' . $RequestStatus->count () . '", "results":' . $this->json->encode ( $RequestStatus->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
}