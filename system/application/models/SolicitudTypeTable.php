<?php
/**
 */
class SolicitudTypeTable extends Doctrine_Table {

    function retrieveAll($solicitudTipo) {
        $q = Doctrine_Query::create()
                ->select('st.*')
                ->from('SolicitudType st')
                ->where("st.solicitud_type_nombre LIKE '%{$solicitudTipo}%'")
                ->orderBy('solicitud_type_nombre');
        return $q->execute();
    }

}
