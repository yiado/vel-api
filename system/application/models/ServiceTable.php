<?php

/**
 */
class ServiceTable extends Doctrine_Table {

    function retrieveAll($filters = array(), $start = false, $limit = false, $count = false) {
        $q = Doctrine_Query::create()
                ->from('Service s')
                ->innerJoin('s.ServiceStatus se')
                ->innerJoin('s.ServiceType st')
                ->innerJoin('s.User u');
        $this->addFilter($q, $filters);
        if (!is_null($start)) {
            $q->offset($start);
        }
        if (!is_null($limit)) {
            $q->limit($limit);
        }
        return $count?$q->count():$q->execute();
    }

    function findById($service_id) {
        $q = Doctrine_Query::create()
                ->from('Service s')
                ->innerJoin('s.ServiceStatus se')
                ->innerJoin('s.ServiceType st')
                ->innerJoin('s.User u')
                ->where('service_id = ?', $service_id);
        return $q->execute();
    }
    
    function groupAllByStatus($filters = array()) {
        $q = Doctrine_Query::create()
                ->select('s.*, se.*, count(*)')
                ->from('Service s')
                ->innerJoin('s.ServiceStatus se')
                ->innerJoin('s.ServiceType st')
                ->groupBy('se.service_status_name');
        $this->addFilter($q, $filters);
        return $q->execute();
    }
    
    function groupAllByType($filters = array()) {
        $q = Doctrine_Query::create()
                ->select('s.*, st.*, count(*)')
                ->from('Service s')
                ->innerJoin('s.ServiceStatus se')
                ->innerJoin('s.ServiceType st')
                ->groupBy('st.service_type_name');
        $this->addFilter($q, $filters);
        return $q->execute();
    }
    
    function addFilter ($q, $filters) {
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
