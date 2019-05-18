<?php

/**
 * @package Model
 * @subpackage DocExtensionTable
 */
class DocExtensionTable extends Doctrine_Table
{

    /**
     * retrieveByCate
     * 
     * retorna extension por id
     * 
     * @param int $nod_id
     */
    function retrieveByExte ( $doc_extension_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'DocExtension dc' )
                ->orderBy ( 'doc_extension_name' );


        return $q->execute ();
    }

    function retrieveByExtension ( $extension )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'DocExtension dc' )
                ->where ( 'doc_extension_extension LIKE ?' , '%' . $extension . '%' );

        return $q->execute ();
    }

    /**
     * Devuelve la info de una tupla de la tabla doc_extension
     * @param integer $doc_extension_id
     * @return 1 row
     *
     */
    function retrieveById ( $doc_extension_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'DocExtension dc' )
                ->where ( 'dc.doc_extension_id = ?' , $doc_extension_id );

        return $q->fetchOne ();
    }

}
