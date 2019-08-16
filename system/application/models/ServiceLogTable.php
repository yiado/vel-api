<?php

/**
 */
class ServiceLogTable extends Doctrine_Table {

    function findById($service_id) {

        $q = Doctrine_Query::create()
                ->from('ServiceLog sl')
                ->innerJoin('sl.User u')
                ->innerJoin('sl.Service s')
                ->where('sl.service_id = ?', $service_id);

        return $q->execute();
    }

}
