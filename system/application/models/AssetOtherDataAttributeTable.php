<?php

/**
 * @package Model
 * @subpackage Asset
 * @author manuteko
 *
 */
class AssetOtherDataAttributeTable extends Doctrine_Table
{

    /**
     * 
     * Retorna todos los atributos disponibles ignorando los que ya estan asociados al tipo de activo.
     * Si el $asset_type_id es NULL, lista todos los atributos disponibles
     * @param integer $asset_type_id
     * 
     */
    function retrieveAll ( $asset_type_id = NULL )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetOtherDataAttribute aoda' )
                ->orderBy ( 'aoda.asset_other_data_attribute_name ASC' );

        if ( ! is_null ( $asset_type_id ) )
        {
            $q->where ( 'aoda.asset_other_data_attribute_id NOT IN (SELECT aodaat.asset_other_data_attribute_id FROM AssetOtherDataAttributeAssetType aodaat WHERE aodaat.asset_type_id = ?)' , $asset_type_id );
        }
        return $q->execute ();
    }

    /**
     *
     * checkDataInAttributeAssetType
     * Retorna true en el caso que exista un Dato asociado a un Tipo de Activo y False en el caso contrario
     * @param integer $asset_other_data_attribute_id
     */
    function checkDataInAttributeAssetType ( $asset_other_data_attribute_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetOtherDataAttributeAssetType aodaat' )
                ->where ( 'aodaat.asset_other_data_attribute_id = ?' , $asset_other_data_attribute_id )
                ->limit ( 1 );

        $results = $q->execute ();
        return ($results->count () == 0 ? false : true);
    }

    function deleteAttribute ( $asset_other_data_attribute_id )
    {
        //Eliminar el atributo
        $q = Doctrine_Query::create ()
                ->delete ( 'AssetOtherDataAttribute aoda' )
                ->where ( 'aoda.asset_other_data_attribute_id = ?' , $asset_other_data_attribute_id );
        $q->execute ();
    }
}
