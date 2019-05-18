<?php

/**
 * @package Controller
 * @subpackage LanguageTagController
 */
class LanguageTagController extends APP_Controller
{
    function LanguageTagController ()
    {
        parent::APP_Controller ();
    }

    /**
     * Lista todos los tags del idioma
     */
    function get ()
    {
        $language_id = ( int ) $this->input->post ( 'language_id' );
        $module_id = ( int ) $this->input->post ( 'module_id' );
        $language_tags = Doctrine_Core::getTable ( 'LanguageTag' )->retrieveAll ( $language_id , $module_id );
        $json_data = $this->json->encode ( array ( 'total' => $language_tags->count () , 'results' => $language_tags->toArray () ) );
        echo $json_data;
    }

    /**
     * Actualizar un tag
     */
    function update ()
    {
        $language_tag_id = $this->input->post ( 'language_tag_id' );
        $language_id = $this->input->post ( 'language_id' );
        
        //Convertimos los tildes a entidades html
        //$language_tag_value = htmlentities($this->input->post('language_tag_value'), ENT_NOQUOTES, 'UTF-8');
        $language_tag_value = $this->input->post ( 'language_tag_value' );
        $language_tag = Doctrine_Core::getTable ( 'LanguageTag' )->retrieveById ( $language_id , $language_tag_id );

        try
        {
            $language_tag->language_tag_value = $language_tag_value;
            $language_tag->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'tag_successfully_updated' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $this->translateTag ( 'General' , 'language_problems_updating' );
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

}

