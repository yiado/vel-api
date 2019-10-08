<?php

/**
 */
class ServiceStatusTable extends Doctrine_Table {
    function retrieveAll($serviceTypeName) {
        $q = Doctrine_Query::create()
                ->select('st.*')
                ->from('ServiceStatus st')
                ->where("st.service_status_name LIKE '%{$serviceTypeName}%'")
                ->orderBy('service_status_name');
        return $q->execute();
    }
}
