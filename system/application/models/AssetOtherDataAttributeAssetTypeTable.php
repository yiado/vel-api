<?php

/**
 * @package Model
 * @subpackage Asset
 * @author manuteko
 *
 */
class AssetOtherDataAttributeAssetTypeTable extends Doctrine_Table
{

    /**
     * Devuelve los atributos asociados al tipo de activo
     * @param integer $asset_type_id
     */
    function retrieveByAssetType ( $asset_type_id = NULL )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetOtherDataAttributeAssetType aodaat' )
                ->innerJoin ( 'aodaat.AssetOtherDataAttribute aoda' )
                ->where ( 'aodaat.asset_type_id = ?' , $asset_type_id )
                ->andWhere ( 'asset_other_data_attribute_asset_type_order >= 0' )
                ->orderBy ( 'aodaat.asset_other_data_attribute_asset_type_order ASC' );
        return $q->execute ();
    }

    /**
     * Elimina los atributos asociados al tipo de Activo
     * @param integer $asset_type_id
     */
    function deleteInfoAttributeAssetType ( $asset_type_id )
    {
        $q = Doctrine_Query::create ()
                ->delete ( 'AssetOtherDataAttributeAssetType aodaat' )
                ->where ( 'aodaat.asset_type_id = ?' , $asset_type_id )
                ->andWhere ( 'asset_other_data_attribute_asset_type_order >= 0' );
        return $q->execute ();
    }
}
