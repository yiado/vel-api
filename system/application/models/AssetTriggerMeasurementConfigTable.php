<?php

/**
 */
class AssetTriggerMeasurementConfigTable extends Doctrine_Table
{

    /**
     * retrieveAll
     *
     * Retorna todos las configuracion de las lecturas
     */
    function retrieveAll ()
    {
        $q = Doctrine_Query :: create ()
                ->from ( 'AssetTriggerMeasurementConfig atmc' )
                ->innerJoin ( 'atmc.AssetType at' )
                ->innerJoin ( 'atmc.MeasureUnit mu' )
                ->orderBy ( 'at.asset_type_name ASC' );
        return $q->execute ();
    }

    /**
     * Obtiene la Informacion Correspondiente al tipo de Activo y su unidad de Medida
     * 
     * @param integer $asset_type_id
     * @param integer $measure_unit_id
     */
    function checkTriggerAssetMeasurence ( $asset_type_id , $measure_unit_id )
    {

        //Buscamos los triggers configurados para el tipo de activo y la unidad de medida
        $q = Doctrine_Query::create ()
                ->from ( 'AssetTriggerMeasurementConfig atmc' )
                ->where ( 'atmc.asset_type_id = ?' , $asset_type_id )
                ->where ( 'atmc.measure_unit_id = ?' , $measure_unit_id );

//        echo  $q->getSqlQuery();

        return $q->execute ();
    }

    function checkAssetId ( $asset_type_id )
    {

        //Buscamos los triggers configurados para el tipo de activo y la unidad de medida
        $q = Doctrine_Query::create ()
                ->from ( 'AssetTriggerMeasurementConfig atmc' )
                ->where ( 'atmc.asset_type_id = ?' , $asset_type_id );

//        echo  $q->getSqlQuery();

        return $q->fetchOne ();
    }
    function retriveTypeMeasurement($asset_id, $measure_unit_id)
    {
	$q = Doctrine_Query::create()
		->from('AssetTriggerMeasurementConfig atmc')
		->innerJoin('atmc.AssetType at')
		->innerJoin('at.Asset a')
		->innerJoin('a.AssetMeasurement am')
		->where('a.asset_id = ?', $asset_id)
		->andWhere('atmc.measure_unit_id = ?', $measure_unit_id);
	//return $q->execute();
	        return $q->execute ( array ( ) , Doctrine_Core :: HYDRATE_ARRAY );
    }
}
