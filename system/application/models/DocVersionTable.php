<?php

/**
 * @package Model
 * @subpackage DocVersionTable
 */
class DocVersionTable extends Doctrine_Table
{

    /**
     * retrieveByDocument
     * 
     * retorna la version actual por id
     * 
     * @param int doc_document_id
     */
    function retrieveByDocument ( $doc_document_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'DocVersion dc' )
                ->leftJoin ( 'dc.DocDocument dd' )
		->leftJoin ( 'dd.DocCategory ca' )
                ->innerJoin ( 'dc.User us' )
                ->where ( 'doc_document_id = ?' , $doc_document_id )
                ->orderBy ( 'doc_version_creation' );

        return $q->execute ();
    }

    function compFileName ( $file_input )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'DocDocument dd' )
                ->where ( 'dd.doc_document_filename = ?' , $file_input )
                ->limit ( 1 );

        $results = $q->execute ();
        return ($results->count () == 1 ? false : true);
    }

    function lastVersionDocument ( $doc_document_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'DocVersion dc' )
                ->where ( 'doc_document_id = ?' , $doc_document_id )
                ->orderBy ( 'doc_version_code DESC' )
                ->limit ( 1 );

        $results = $q->fetchOne ();

        return ( empty ( $results->doc_version_code ) ? 0 : $results->doc_version_code);
    }
    function lastVersionDocument2 ( $doc_document_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'DocVersion dc' )
                ->where ( 'doc_document_id = ?' , $doc_document_id )
                ->orderBy ( 'doc_version_code DESC' )
                ->limit ( 1 );

        return   $q->fetchOne ();

       // return ( empty ( $results->doc_version_code ) ? 0 : $results->doc_version_code);
    }
    /*
     * 		deleteVesionDocument
     *
     *
     * @param integer $node_id
     * @param integer $doc_version_id
     * @param integer $doc_document_id
     */

    function deleteVesionDocument ( $node_id , $doc_document_id , $doc_version_id )
    {
 $CI = & get_instance();
        // Buscar si el documento tiene mas de una version
        $q = Doctrine_Query::create ()
                ->from ( 'DocVersion dv' )
                ->where ( 'doc_document_id = ?' , $doc_document_id );

        $versiones = $q->execute ();

        //Si la cantidad de versiones es mayor  a 1 entra y borra version seteando version actual, sino entrega mensaje se debe borrar documento
        if ( $versiones->count () > 1 )
        {

            $docVersion = Doctrine::getTable ( 'DocVersion' )->find ( $doc_version_id );

            //Quitar el archivo
            $path = './docs/';
            $file_full_path = $path . $docVersion->doc_version_filename;

            if ( unlink ( $file_full_path ) )
            {
                //Eliminamos la versión:
                $q = Doctrine_Query::create ()
                        ->delete ( 'DocVersion dc' )
                        ->where ( 'dc.doc_version_id = ?' , $doc_version_id );
                $q->execute ();

                //Buscamos la ultima version del documento
                //La mayor de las versiones
                $q = Doctrine_Query::create ()
                        ->from ( 'DocVersion dc' )
                        ->where ( 'doc_document_id = ?' , $doc_document_id )
                        ->orderBy ( 'doc_version_code DESC' )
                        ->limit ( 1 );

                $lastVersion = $q->fetchOne ();

                $docDocument = Doctrine_Core::getTable ( 'DocDocument' )->find ( $doc_document_id );

                //Marcamos como versión actual el último plano obtenido
                $docDocument->doc_current_version_id = $lastVersion->doc_version_id;
                $docDocument->save ();
            }
            else
            {
               
                throw new Exception ( $CI->translateTag('Documen', 'problems_associated_document_version') );
            }
        }
        else
        {

            throw new Exception ( $CI->translateTag('Documen', 'you_can_not_delete_the_latest_version'));
        }
    }

}
