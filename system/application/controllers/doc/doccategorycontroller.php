<?php

/**
 * @package Controller
 * @subpackage DocCategoryController
 */
class DocCategoryController extends APP_Controller
{
    function DocCategoryController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     *
     * Retorna la categoria del Nodo
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $doc_category_id = $this->input->post ( 'doc_category_id' );
        $docsTable = Doctrine_Core::getTable ( 'DocCategory' );
        $docs = $docsTable->retrieveByCate ( $doc_category_id, $text_autocomplete );

        if ( $docs->count () )
        {
            echo '({"total":"' . $docs->count () . '", "results":' . $this->json->encode ( $docs->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * Agrega una categoria para los documentos.
     * Método usado desde el back-end
     * @param string $doc_category_name
     * @param string $doc_category_description
     *
     */
    function add ()
    {
        //Recibimos los parametros
        $doc_category_name = $this->input->post ( 'doc_category_name' );
        $doc_category_description = $this->input->post ( 'doc_category_description' );

        try
        {
            $docCategory = new DocCategory();
            $docCategory->doc_category_name = $doc_category_name;
            $docCategory->doc_category_description = $doc_category_description;
            $docCategory->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Actualiza una categoria para los documentos.
     * Método usado desde el back-end
     * @param string $doc_category_name
     * @param string $doc_category_description
     *
     */
    function update ()
    {
        //Recibimos los parametros
        $doc_category_id = $this->input->post ( 'doc_category_id' );
        $doc_category_name = $this->input->post ( 'doc_category_name' );
        $doc_category_description = $this->input->post ( 'doc_category_description' );

        try
        {
            $docCategory = Doctrine::getTable ( 'DocCategory' )->retrieveById ( $doc_category_id );
            $docCategory->doc_category_name = $doc_category_name;
            $docCategory->doc_category_description = $doc_category_description;
            $docCategory->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Elimina una categoria de documentos.
     * @param integer $doc_category_id
     *
     */
    function delete ()
    {
        $doc_category_id = $this->input->post ( 'doc_category_id' );
        $checkCategoryInDocument = Doctrine::getTable ( 'DocCategory' )->checkCategoryInDocument ( $doc_category_id );

        if ( $checkCategoryInDocument === false )
        {
            $docCategory = Doctrine::getTable ( 'DocCategory' )->retrieveById ( $doc_category_id );

            if ( $docCategory->delete () )
            {
                $exito = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $exito = false;
                $msg = $this->translateTag ( 'Documen' , 'category_document_type_cant_be_eliminated' );
            }
        }
        else
        {
            $exito = false;
            $msg = $this->translateTag ( 'Documen' , 'category_document_type_cant_be_eliminated' );
        }

        $json_data = $this->json->encode ( array ( 'success' => $exito , 'msg' => $msg ) );
        echo $json_data;
    }

}