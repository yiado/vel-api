<?php

/**
 * @package Controller
 * @subpackage LanguageController
 */
class LanguageController extends APP_Controller
{
    function LanguageController ()
    {
        parent::APP_Controller ();
    }

    /**
     * Lista todos los idiomas disponibles en el sistema
     */
    function get ()
    {
        $languages = Doctrine_Core::getTable ( 'Language' )->retrieveAll ();
        $json_data = $this->json->encode ( array ( 'total' => $languages->count () , 'results' => $languages->toArray () ) );
        echo $json_data;
    }

    /**
     * Crea un nuevo idioma
     */
    function add ()
    {
        $language = new Language();
        $language_name = htmlentities ( $this->input->post ( 'language_name' ) , ENT_NOQUOTES , 'UTF-8' );
        $language_default = $this->input->post ( 'language_default' );
        $language_ref_copy = ( int ) $this->input->post ( 'language_id' );

        try
        {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance ()->getCurrentConnection ();

            //Iniciamos la transacción
            $conn->beginTransaction ();

            //Setear el idioma por defecto
            if ( $language_default == 'true' )
            {
                Doctrine_Core::getTable ( 'Language' )->unSetDefaultLanguage ();
                $language_default = 1;
            }
            else
            {
                $language_default = 0;
            }
            $language->language_name = $language_name;
            $language->language_default = $language_default;
            $language->save ();
            $language_id = $language->language_id;

            //Crear la copia de los tags
            Doctrine_Core::getTable ( 'Language' )->copyAndPasteLanguage ( $language_id , $language_ref_copy );
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
            
            // Si todo OK, commit a la base de datos
            $conn->commit ();
        }
        catch ( Exception $e )
        {
            //Si hay error, rollback de los cambios en la base de datos
            $conn->rollback ();
            $success = false;
            $msg = $this->translateTag ( 'General' , 'problems_creating_language' );
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Actualizar un idioma
     */
    function update ()
    {
        $language_id = $this->input->post ( 'language_id' );
        $language_name = $this->input->post ( 'language_name' );
        $language_default = $this->input->post ( 'language_default' );
        $language = Doctrine_Core::getTable ( 'Language' )->retrieveById ( $language_id );
        try
        {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance ()->getCurrentConnection ();
            
            //Iniciamos la transacción
            $conn->beginTransaction ();

            //Setear el idioma por defecto
            if ( $language_default == 'true' )
            {
                Doctrine_Core::getTable ( 'Language' )->unSetDefaultLanguage ( $language_id );
                $language_default = 1;
            }
            $language->language_name = $language_name;
            $language->language_default = $language_default;
            $language->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );

            // Si todo OK, commit a la base de datos
            $conn->commit ();
        }
        catch ( Exception $e )
        {
            $success = false;

            $msg = $this->translateTag ( 'General' , 'language_problems_updating' );

            //Si hay error, rollback de los cambios en la base de datos
            $conn->rollback ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Eliminar un nuevo idioma
     */
    function delete ()
    {
        $language_id = $this->input->post ( 'language_id' );
        $language = Doctrine_Core::getTable ( 'Language' )->retrieveById ( $language_id );

        try
        {
            if ( $language->language_default == 1 )
            {
                throw new Exception ( $this->translateTag ( 'General' , 'delete_the_default_language' ) );
            }
            else
            {
                $language->delete ();
                $success = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $this->translateTag ( 'General' , 'problems_eliminating_language' );
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

}
