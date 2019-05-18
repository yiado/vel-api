<?php

/**
 */
class AssetInventoryAuxiliarTable extends Doctrine_Table {

    function retrieveAll($user_id) {
        $q = Doctrine_Query :: create()
                ->from('AssetInventoryAuxiliar')
                ->where('user_id = ?', $user_id)
                ->orderBy('asset_inventory_auxiliar_id ASC');


        return $q->execute();
    }

    function findNumeroInterno($asset_inventory_interno) {
        $q = Doctrine_Query :: create()
                ->from('AssetInventoryAuxiliar')
                ->where('asset_inventory_interno = ?', $asset_inventory_interno);


        return $q->fetchOne();
    }

    function retrieveDuplicaco($assetCodigoRecinto, $assetCodigoActivo) {
        $q = Doctrine_Query :: create()
                ->from('AssetInventoryAuxiliar')
                ->where('asset_inventory_barra = ?', $assetCodigoRecinto)
                ->andWhere('asset_inventory_interno = ?', $assetCodigoActivo);

        $results = $q->execute();
        return ($results->count() == 0 ? false : true);
    }

}
