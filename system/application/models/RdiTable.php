<?php

/**
 */
class RdiTable extends Doctrine_Table {

    function retrieveAll($filters = array(), $start = false, $limit = false, $count = false) {
        $q = Doctrine_Query::create()
                ->from('Rdi r')
                ->innerJoin('r.RdiStatus rs')
                ->innerJoin('r.User u')
                ->innerJoin('r.RequestEvaluation re');
        $this->addFilter($q, $filters);
        if (!is_null($start)) {
            $q->offset($start);
        }
        if (!is_null($limit)) {
            $q->limit($limit);
        }
        return $count ? $q->count() : $q->execute();
    }

    function findById($rdi_id) {
        $q = Doctrine_Query::create()
                ->from('Rdi r')
                ->innerJoin('r.RdiStatus rs')
                ->innerJoin('r.User u')
                ->innerJoin('r.RequestEvaluation re')
                ->where('rdi_id = ?', $rdi_id);
        return $q->execute();
    }
    
    function addFilter($q, $filters) {
        $flag = false;
        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                if ($flag === false) {
                    $q->andWhere($field, $value);
                    $flag = true;
                } else {
                    $q->andWhere($field, $value);
                }
            }
        }
    }

}
