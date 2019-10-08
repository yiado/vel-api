<?php
/**
 */
class SolicitudEstadoTable extends Doctrine_Table {

    function retrieveAll($solicitudEstado) {
        $q = Doctrine_Query::create()
                ->select('se.*')
                ->from('SolicitudEstado se')
                ->where("se.solicitud_estado_nombre LIKE '%{$solicitudEstado}%'")
                ->orderBy('solicitud_estado_nombre');
        return $q->execute();
    }
    
}
