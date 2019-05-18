<?php

/**
 */
class AssetStatusTable extends Doctrine_Table
{

    /**
     * retrieveAll
     * 
     * Recupera los estados de los equipos
     */
    function retrieveAll($text_autocomplete = NULL)
    {
        $q = Doctrine_Query :: create()
                ->from('AssetStatus')
                ->orderBy('asset_status_name ASC');

        if (!is_null($text_autocomplete))
        {
            $q->andWhere('asset_status_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

    /**
     *
     * assetStatusInAsset
     * Retorna true en el caso que exista un Estado asociado a un Activo  y False en el caso contrario
     */
    function assetStatusInAsset($asset_status_id)
    {
        $q = Doctrine_Query::create()
                ->from('AssetStatus as')
                ->innerJoin('as.Asset a')
                ->where('a.asset_status_id = ?', $asset_status_id)
                ->limit(1);
        $results = $q->execute();
        return ($results->count() == 0 ? false : true);
    }
    
    function assetAssetStatusInName( $asset_status_name )
    {

        $q = Doctrine_Query::create ()
                ->from('AssetStatus as')
                ->where('as.asset_status_name = ?', $asset_status_name);

        return $q->fetchOne();
    }

}
