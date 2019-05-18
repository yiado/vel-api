<?php

/**
 * @package Model 
 * @subpackage ProviderTable
 */
class ProviderTable extends Doctrine_Table
{

    /**
     * Retorna todos los proveedores
     * 
     * @param string $text_autocomplete
     */
    function retrieveAll ( $text_autocomplete = NULL ,$maintainer_type)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Provider p' )
                ->innerJoin ( 'p.ProviderType pt' )
                ->orderBy ( 'p.provider_name ASC' );

        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'p.provider_name LIKE ?' , $text_autocomplete . '%' );
        }
        $q->andWhere('p.mtn_maintainer_type_id = ?', $maintainer_type);
        
        return $q->execute ();
    }
    
    function retrieveByProvider ( $provider_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Provider p' )
                ->orderBy ( 'p.provider_name ASC' )
                ->where ( 'p.provider_type_id = ?', $provider_type_id);

        
        return $q->execute ();
    }
    
    function retrieveAllType ( $mtn_maintainer_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Provider p' )
                ->where ( 'p.mtn_maintainer_type_id = ?' , $mtn_maintainer_type_id);
        
        return $q->execute ();
    }
    
    function retrieveAllAsset ( $text_autocomplete = NULL )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Provider p' )
                ->innerJoin ( 'p.ProviderType pt' )
                ->where ( 'p.mtn_maintainer_type_id = ?' , 1)
                ->orderBy ( 'p.provider_name ASC' );

        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'p.provider_name LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }
    
    function retrieveAllNode( $text_autocomplete = NULL )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Provider p' )
                ->innerJoin ( 'p.ProviderType pt' )
                ->where ( 'p.mtn_maintainer_type_id = ?' , 2)
                ->orderBy ( 'p.provider_name ASC' );

        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'p.provider_name LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }

    /**
     * Retorna la info de la tupla del proveedor
     * 
     * @param integer $provider_id
     * @return 1 row
     */
    function retrieveById ( $provider_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Provider p' )
                ->innerJoin ( 'p.ProviderType pt' )
                ->where ( 'p.provider_id = ?' , $provider_id );

        return $q->fetchOne ();
    }

    /**
     *
     * Retorna true en el caso que exista un seguro, lista de precio o una ot asociado al proveedor y false en el caso contrario
     * @param integer $provider_id
     * @return boolean true|false
     */
    function checkProviderIdInOtherData ( $provider_id )
    {

        //Proveedor asociado a un seguro
        $q = Doctrine_Query::create ()
                ->from ( 'AssetInsurance at' )
                ->where ( 'at.provider_id = ?' , $provider_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return $assetInsurance = ($results->count () == 0 ? false : true);

     }

}