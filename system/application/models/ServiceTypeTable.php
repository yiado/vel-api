<?php

/**
 */
class ServiceTypeTable extends Doctrine_Table {

    function retrieveAll($serviceTypeName) {
        $q = Doctrine_Query::create()
                ->select('st.*')
                ->from('ServiceType st')
                ->where("st.service_type_name LIKE '%{$serviceTypeName}%'")
                ->orderBy('service_type_name');
        return $q->execute();
    }

}
