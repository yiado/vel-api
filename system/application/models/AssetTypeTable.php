<?php

/**
 * 
 * 
 */
class AssetTypeTable extends Doctrine_Table
{

    /**
     * retrieveAll
     * 
     * Retorna todos los tipo equipo
     */
    function retrieveAll ( $text_autocomplete = NULL )
    {

        $q = Doctrine_Query :: create ()
                ->from ( 'AssetType' )
                ->orderBy ( 'asset_type_name ASC' );

        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->andWhere ( 'asset_type_name LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }

    /**
     *
     * assetTypeInAsset
     * Retorna true en el caso que exista un Tipo de Activo asociado a un Activo  y False en el caso contrario
     */
    function assetTypeInAsset ( $asset_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetType at' )
                ->innerJoin ( 'at.Asset a' )
                ->where ( 'a.asset_type_id = ?' , $asset_type_id )
                ->limit ( 1 );

        $results = $q->execute ();
        return ($results->count () == 0 ? false : true);
    }
    
    /**
     *
     * assetTypeInAsset
     * Retorna true en el caso que exista un Tipo de Activo asociado a un Activo  y False en el caso contrario
     */
    function assetTypeInName( $asset_type_name )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetType at' )
                ->where ( 'at.asset_type_name = ?' , $asset_type_name );

        return $q->fetchOne();
    }

}

