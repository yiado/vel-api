<?php

/**
 */
class MtnPercentagesTable extends Doctrine_Table {

    function retrievePercentages($node_id_region, $valor_en_uf) {
        $q = Doctrine_Query :: create()
                ->from('MtnPercentages mp')
                ->where('mp.node_id = ?', $node_id_region)
                ->andWhere('mp.mtn_percentages_value_lower <= ?', $valor_en_uf)
                ->andWhere('mp.mtn_percentages_value_upper >= ?', $valor_en_uf);

        return $q->fetchOne();
    }

}
