<?php

/**
 */
class ServiceTable extends Doctrine_Table {

    function retrieveAll($filters = array()) {

        $q = Doctrine_Query::create()
                ->select('s.*,se.*, st.*, u.*')
                ->from('Service s')
                ->innerJoin('s.ServiceStatus se')
                ->innerJoin('s.ServiceType st')
                ->innerJoin('s.User u')
                ->orderBy('service_id');

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


        return $q->execute();
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

}
