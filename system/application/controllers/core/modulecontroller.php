<?php

/**
 * @package Controller
 * @subpackage Modulecontroller
 */
class ModuleController extends APP_Controller
{
    function ModuleController ()
    {
        parent::APP_Controller ();
    }

    /**
     * 
     * Lista todos los modulos cargados en el sistema
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $front = ($this->input->post ( 'front' ) ? true : false);
        $module = Doctrine_Core::getTable ( 'Module' )->retrieveAll ( $front, $text_autocomplete );
        $json_data = $this->json->encode ( array ( 'total' => $module->count () , 'results' => $module->toArray () ) );
        echo $json_data;
    }

    /**
     * Retorna las acciones del modulo
     * @param integer $module_id
     */
    function getActionModule ()
    {
        $module_id = $this->input->post ( 'module_id' );
        $user_group_id = $this->input->post ( 'user_group_id' );
        $language_id = $this->session->userdata ( 'language_id' );
        $actionsInModule = Doctrine_Core::getTable ( 'ModuleAction' )->retrieveByModule ( $module_id , $user_group_id , $language_id );
        $json_data = $this->json->encode ( array ( 'total' => $actionsInModule->count () , 'results' => $actionsInModule->toArray () ) );
        echo $json_data;
    }
}