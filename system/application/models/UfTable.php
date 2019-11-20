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
    
    function retrieveByMonthAndYear($month, $year) {
        $q = Doctrine_Query :: create()
                ->from('Uf u')
                ->where('EXTRACT(month from u.uf_date) = ?', $month)
                ->andWhere('EXTRACT(year from u.uf_date) = ?', $year)
                ->limit(1)
                ->orderBy('u.uf_date DESC');
        return $q->fetchOne();
    }
    
    function retrieveToday($today) {
        $q = Doctrine_Query :: create()
                ->from('Uf u')
                ->where('u.uf_date = ?', $today)
                ->limit(1)
                ->orderBy('u.uf_date DESC');
        return $q->fetchOne();
    }

}
