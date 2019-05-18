<?php

/**
 * @package    Controller
 * @subpackage SolicitudEstadoController
 */
class SolicitudEstadoController extends APP_Controller
{
    function SolicitudEstadoController ()
    {
        parent::APP_Controller ();
    }

    function get ()
    {
        $request = Doctrine_Core::getTable('SolicitudEstado')->findAll();
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