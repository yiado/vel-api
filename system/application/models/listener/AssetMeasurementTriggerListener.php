<?php
/**
 * Listener para verificar la existencia de triggers asociados al tipo de activo que corresponde a la lectura ingresada.
 * @author manuteko
 *
 */
class AssetMeasurementTriggerListener extends Doctrine_Record_Listener {
/*
    public function postInsert ( Doctrine_Event $event ) {

    	$assetMeasurement = $event->getInvoker();
        Doctrine_Core::getTable('AssetTriggerMeasurementConfig')->checkTriggerAsset($assetMeasurement->asset_id, $assetMeasurement->measure_unit_id, $assetMeasurement->asset_measurement_cantity, $assetMeasurement->asset_measurement_id);

    }
*/
}
