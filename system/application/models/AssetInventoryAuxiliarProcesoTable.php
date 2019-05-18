<?php
/**
 */
class AssetInventoryAuxiliarProcesoTable extends Doctrine_Table {
    
    function retrieveAll ($user_id)
    {
        $q = Doctrine_Query :: create ()
                ->from ( 'AssetInventoryAuxiliarProceso aiap' )
                ->orderBy ( 'aiap.situacion ASC' )
                ->where('aiap.user_id = ?', $user_id);
        
        return $q->execute ();
    }
    
    function cargaActivoNoRegistrado ($user_id)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetInventoryAuxiliarProceso aiap' )
                ->where ( 'aiap.situacion = ?' , "ACTIVO NO REGISTRADO EN IGEO" )
                ->andWhere('aiap.user_id = ?', $user_id);

        return $q->execute ();
    }
    
    function cargaNodoNoRegistrado ($user_id)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetInventoryAuxiliarProceso aiap' )
                ->where ( 'aiap.situacion = ?' , "NODO NO REGISTRADO EN IGEO" )
                ->andWhere('aiap.user_id = ?', $user_id);

        return $q->execute ();
    }
    
    function cargaConformidades ($user_id)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetInventoryAuxiliarProceso aiap' )
                ->where ( 'aiap.situacion = ?' , "CONFORMIDADES" )
                ->andWhere('aiap.user_id = ?', $user_id);

        return $q->execute ();
    }
    
    
    function cargaTrasladado ($user_id)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetInventoryAuxiliarProceso aiap' )
                ->where ( 'aiap.situacion = ?' , "TRASLADADO" )
                ->andWhere('aiap.user_id = ?', $user_id);

        return $q->execute ();
    }
    
    function cargaFaltante ($user_id)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetInventoryAuxiliarProceso aiap' )
                ->where ( 'aiap.situacion = ?' , "FALTANTE" )
                ->andWhere('aiap.user_id = ?', $user_id);

        return $q->execute ();
    }
    
    function findAllTrasladados($user_id, $start = null, $limit = null, $count = false) {
        $q = Doctrine_Query::create()
                ->from('AssetInventoryAuxiliarProceso aiap')
                ->where('aiap.situacion = ?', "TRASLADADO");

        if (!is_null($start)) {
            $q->offset($start);
        }
        if (!is_null($limit)) {
            $q->limit($limit);
        }
        
        $q->andWhere('aiap.user_id = ?', $user_id);
        if ($count) {
            $q->select('count(*)');
            return $q->fetchOne()->count;
        } else {
            return $q->execute();
        }
      
    }
    
    function findAllAuxiliarProceso($user_id) {
        $q = Doctrine_Query::create()
                ->from('AssetInventoryAuxiliarProceso aiap')
                ->where('aiap.user_id = ?', $user_id);

        
            return $q->execute();
      
    }
    


}
