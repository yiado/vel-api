<?php

/**
 */
class AssetConditionTable extends Doctrine_Table
{
    /**
     * retrieveAll
     * 
     * Recupera todas las condiciones
     */
    function retrieveAll ( $text_autocomplete = NULL )
    {
        $q = Doctrine_Query :: create ()
                ->from ( 'AssetCondition' )
                ->orderBy ( 'asset_condition_name ASC' );
        
        if (!is_null($text_autocomplete))
        {
            $q->andWhere('asset_condition_name LIKE ?', $text_autocomplete . '%');
        }
        
        return $q->execute ();
    }

    /**
     *
     * assetConditionInAsset
     * Retorna true en el caso que exista una Condicion asociado a un Activo  y False en el caso contrario
     */
    function assetConditionInAsset ( $asset_condition_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetCondition ac' )
                ->innerJoin ( 'ac.Asset a' )
                ->where ( 'a.asset_condition_id = ?' , $asset_condition_id )
                ->limit ( 1 );

        $results = $q->execute ();
        return ($results->count () == 0 ? false : true);
    }
    
    function assetAssetConditionInName( $asset_condition_name )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetCondition ac' )
                ->where('ac.asset_condition_name = ?', $asset_condition_name);

        return $q->fetchOne();
    }
}
