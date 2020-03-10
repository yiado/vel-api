<?php

/**
 */
class RdiLogTable extends Doctrine_Table {

    function findById($rdi_id) {

        $q = Doctrine_Query::create()
                ->from('RdiLog rl')
                ->innerJoin('rl.User u')
                ->innerJoin('rl.Rdi r')
                ->where('rl.rdi_id = ?', $rdi_id);

        return $q->execute();
    }

}
