<?php

/**
 */
class AssetLoadTable extends Doctrine_Table {

    function retrieveLastFolio() {
        $q = Doctrine_Query::create()
                ->from('AssetLoad al')
                ->orderBy('asset_load_folio DESC')
                ->limit(1);
 
        return $q->fetchOne();
    }
    function findByFilters($filters = array()) {
        $q = Doctrine_Query::create()
                ->from('AssetLoad al')
                ->innerJoin('al.User u')
                ->orderBy('asset_load_id DESC');

        foreach ($filters as $field => $value) {

            if (!is_null($value)) {

                $q->andWhere($field, $value);
            }
        }
        return $q->execute();
    }

    function findByAssetLoad($asset_load_id) {
        $q = Doctrine_Query::create()
                ->from('AssetLoad al')
                ->innerJoin('al.User u')
           ->where('al.asset_load_id = ?', $asset_load_id); 
        return $q->fetchOne();
    }

    function findAllId($asset_load_id, $start = null, $limit = null, $count = false) {

        $q = Doctrine_Query::create()
                ->from('Asset a')
                ->where('a.asset_load_id = ?', $asset_load_id);


        if (!is_null($start)) {
            $q->offset($start);
        }
        if (!is_null($limit)) {
            $q->limit($limit);
        }

        if ($count) {
            $q->select('count(*)');
            return $q->fetchOne()->count;
        } else {
            return $q->execute();
        }
    }

    /**
     * Devuelve el último folio de la Carga Masiva
     * @return integer
     *
     */
    function lastFolioWo() {

        $q = Doctrine_Query::create()
                ->from('AssetLoad al')
                ->orderBy('asset_load_folio DESC')
                ->limit(1);

        $results = $q->fetchOne();

        $last_folio = (int) ( empty($results->asset_load_folio) ? 0 : $results->asset_load_folio );

        return $last_folio;
    }

}
