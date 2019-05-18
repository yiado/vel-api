<?php

/**
 */
class MtnOtherCostsTable extends Doctrine_Table {
    /*
     * Devuelve todos los insumos de la base de datos.
     * @param array $filters
     *  
     */

    function retrieveAll($text_autocomplete = NULL, $filters = array(),$maintainer_type ) {

        $q = Doctrine_Query::create()
                ->from('MtnOtherCosts oc')
                ->orderBy('oc.mtn_other_costs_name ASC');

        foreach ($filters as $field => $value) {
            if (!is_null($value))
                $q->andWhere($field, $value);
        }

        if (!is_null($text_autocomplete)) {
            $q->andWhere('oc.mtn_other_costs_name LIKE ?', $text_autocomplete . '%');
        }

        $q->andWhere('oc.mtn_maintainer_type_id = ?',  $maintainer_type);
        return $q->execute();
    }

    /**
     *
     * checkDataInPlanTask
     * Retorna true en el caso que exista un Dato asociado a una orden de otros costos  y False en el caso contrario
     * @param integer $mtn_other_costs_id
     */
    function checkDataInOtherCosts($mtn_other_costs_id) {

        $q = Doctrine_Query::create()
                ->from('MtnWorkOrderOtherCosts mwooc')
                ->where('mwooc.mtn_other_costs_id = ?', $mtn_other_costs_id)
                ->limit(1);

        $results = $q->execute();

        return ($results->count() == 0 ? false : true);
    }

}
