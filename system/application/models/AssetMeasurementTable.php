<?php

/**
 * @package Model
 * @subpackage AssetMeasurementTable
 */
class AssetMeasurementTable extends Doctrine_Table
{

    /**
     * retrieveById
     * 
     * Recupera todas las medidas de un equipo
     */
    function retrieveById($asset_id)
    {
	$q = Doctrine_Query::create()
		->from('AssetMeasurement am')
		->innerJoin('am.MeasureUnit mu')
		->where('asset_id = ?', $asset_id)
		->orderBy('asset_measurement_id ASC');

	return $q->execute();
    }



    function retriveLastInsert($asset_id, $measure_unit_id)
    {
	$q = Doctrine_Query::create ()
	->from ( 'AssetMeasurement am' )
	->where ( 'am.asset_measurement_last_insert =?', 1 )
	->andWhere ( 'am.asset_id =?', $asset_id )
	->andWhere('am.measure_unit_id =?', $measure_unit_id);
	return $q->fetchOne();
    }

    function validateLastInsert($asset_id, $measure_unit_id)
    {

	$q = Doctrine_Query::create()
		->select('MAX(am.asset_measurement_cantity)')
		->from('AssetMeasurement am')
		->where('am.asset_id =?', $asset_id)
		->andWhere('am.measure_unit_id =?', $measure_unit_id);
	return $q->fetchOne();
    }

}
