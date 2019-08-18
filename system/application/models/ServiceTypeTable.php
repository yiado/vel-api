<?php

/**
 */
class ServiceTypeTable extends Doctrine_Table {

    function retrieveAll() {
        $q = Doctrine_Query::create()
                ->select('st.*')
                ->from('ServiceType st')
                ->orderBy('service_type_name');
        return $q->execute();
    }

}
