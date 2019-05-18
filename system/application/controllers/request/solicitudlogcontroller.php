<?php

/**
 * @package    Controller
 * @subpackage SolicitudLogController
 */
class SolicitudLogController extends APP_Controller
{
    function SolicitudLogController ()
    {
        parent::APP_Controller ();
    }

    function get ()
    {
        $solicitud_id = $this->input->post('solicitud_id');
        
        $requestLog = Doctrine_Core::getTable('SolicitudLog')->findById($solicitud_id);
        if ($requestLog->count()) {
            echo '({"total":"' . $requestLog->count() . '", "results":' . $this->json->encode($requestLog->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
        
    }

    function add ()
    {
        
    }

    function update ()
    {
        
    }

    function delete ()
    {
        
    }
}