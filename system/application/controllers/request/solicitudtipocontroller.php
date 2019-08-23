<?php

/**
 * @package    Controller
 * @subpackage SolicitudTipoController
 */
class SolicitudTipoController extends APP_Controller
{
    function SolicitudTipoController ()
    {
        parent::APP_Controller ();
    }

    function get ()
    {
        $request = Doctrine_Core::getTable('SolicitudType')->retrieveAll($this->input->post('query'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
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