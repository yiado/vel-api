<?php

/**
 * @package Model
 * @subpackage AssetAttributeTable
 */
class AssetAttributeTable extends Doctrine_Table
{

    /**
     * retrieveById
     * 
     * retorna equipo por id
     * 
     * @param int $asset_id
     */
    function retrieveById ( $asset_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetAttribute at' )
                ->innerJoin ( 'at.AssetTypeAttribute ata' )
                ->where ( 'asset_id = ?' , $asset_id );
        return $q->execute ();
    }
}
