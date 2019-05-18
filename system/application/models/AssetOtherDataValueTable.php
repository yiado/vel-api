<?php

/**
 * @package Model
 * @subpackage Asset
 * @author manuteko
 *
 */
class AssetOtherDataValueTable extends Doctrine_Table
{

    /**
     * retrieveById
     * 
     * Retorna valor de info por id
     * 
     * @param integer $asset_other_data_value_id
     */
    function retrieveById ( $asset_other_data_value_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetOtherDataValue' )
                ->where ( 'asset_other_data_value_id = ?' , $asset_other_data_value_id );
        return $q->execute ();
    }

    function retrieveByAttributeAsset ( $asset_id , $asset_other_data_attribute_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetOtherDataValue' )
                ->where ( 'asset_id = ?' , $asset_id )
                ->andWhere ( 'asset_other_data_attribute_id = ?' , $asset_other_data_attribute_id );

        return $q->fetchOne ();
    }
    
    function retrieveByAsset ( $asset_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetOtherDataValue aodv' )
                ->where ( 'aodv.asset_id = ?' , $asset_id  )
                ->andWhere ( 'aodv.asset_other_data_attribute_id = ?' , 3 );


        return $q->fetchOne ();
    }
    
    function retrieveByAssetModelo ( $asset_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetOtherDataValue aodv' )
                ->where ( 'aodv.asset_id = ?' , $asset_id  )
                ->andWhere ( 'aodv.asset_other_data_attribute_id = ?' , 2 );


        return $q->fetchOne ();
    }
}
