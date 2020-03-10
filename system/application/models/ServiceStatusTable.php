<?php

/**
 */
class ServiceStatusTable extends Doctrine_Table {
    function retrieveAll($serviceStatusName) {
        $q = Doctrine_Query::create()
                ->select('st.*')
                ->from('ServiceStatus st')
                ->where("st.service_status_name LIKE '%{$serviceStatusName}%'")
                ->orderBy('service_status_name');
        return $q->execute();
    }
}
