<?php

/**
 */
class RdiStatusTable extends Doctrine_Table {

    function retrieveAll($rdiStatusName) {
        $q = Doctrine_Query::create()
                ->select('rt.*')
                ->from('RdiStatus rt')
                ->where("rt.rdi_status_name LIKE '%{$rdiStatusName}%'")
                ->orderBy('rdi_status_name');
        return $q->execute();
    }

}
