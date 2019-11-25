<?php

/**
 */
class UfTable extends Doctrine_Table {

    function retrieveByFecha($mtn_work_order_date) {
        $q = Doctrine_Query :: create()
                ->from('Uf u')
                ->where('u.uf_date = ?', $mtn_work_order_date);

        return $q->execute();
    }
    
    function retrieveByDate($date) {
        $q = Doctrine_Query :: create()
                ->from('Uf u')
                ->where('u.uf_date = ?', $date)
                ->limit(1)
                ->orderBy('u.uf_date DESC');
        return $q->fetchOne();
    }

}
