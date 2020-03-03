<?php

/**
 */
class SolicitudLogTable extends Doctrine_Table {

    function findById($solicitud_id) {

        $q = Doctrine_Query::create()
                ->from('SolicitudLog sl')
                ->innerJoin('sl.User u')
                ->innerJoin('sl.Solicitud s')
                ->where('sl.solicitud_id = ?', $solicitud_id);
        return $q->execute();
    }

}
