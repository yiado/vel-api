<?php

/**
 * @package Controller
 * @subpackage DocExtensionController
 */
class DocExtensionController extends APP_Controller
{
    function DocExtensionController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     *
     * Retorna la extension del Nodo
     */
    function get ()
    {
        $doc_extension_id = $this->input->post ( 'doc_extension_id' );
        $docsTable = Doctrine_Core::getTable ( 'DocExtension' );
        $docs = $docsTable->retrieveByExte ( $doc_extension_id );

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
     * Agrega una definición de extensiones permitidas para los documentos
     * @param string $doc_extension_name
     * @param string $doc_extension_extension
     * @method POST
     */
    function add ()
    {
        //Recibimos los parametros
        $doc_extension_name = $this->input->post ( 'doc_extension_name' );
        $doc_extension_extension = $this->input->post ( 'doc_extension_extension' );

        try
        {
            $docExtension = new DocExtension();
            $docExtension->doc_extension_name = $doc_extension_name;
            $docExtension->doc_extension_extension = strtolower ( $doc_extension_extension );
            $docExtension->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Actualiza una definición de extensiones permitidas para los documentos
     * @param integer $doc_extension_id
     * @param string $doc_extension_name
     * @param string $doc_extension_extension
     * @method POST
     */
    function update ()
    {
        //Recibimos los parametros
        $doc_extension_id = $this->input->post ( 'doc_extension_id' );
        $doc_extension_name = $this->input->post ( 'doc_extension_name' );
        $doc_extension_extension = $this->input->post ( 'doc_extension_extension' );

        try
        {
            $docExtension = Doctrine::getTable ( 'DocExtension' )->retrieveById ( $doc_extension_id );
            $docExtension->doc_extension_name = $doc_extension_name;
            $docExtension->doc_extension_extension = strtolower ( $doc_extension_extension );
            $docExtension->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Elimina una definición del tipo de extensión.
     * @param integer $doc_extension_id
     *
     */
    function delete ()
    {
        $doc_extension_id = $this->input->post ( 'doc_extension_id' );
        $docExtension = Doctrine::getTable ( 'DocExtension' )->retrieveById ( $doc_extension_id );

        if ( $docExtension->delete () )
        {
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
            $success = true;
        }
        else
        {
            $msg = $this->translateTag ( 'Documen' , 'failed_delete_extension_document' );
            $success = false;
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

}