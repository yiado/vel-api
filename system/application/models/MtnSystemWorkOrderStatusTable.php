<?php

/**
 */
class MtnSystemWorkOrderStatusTable extends Doctrine_Table
{
    /*
     * Retorna todos los estados posibles para las OT
     */

    function retrieveAll ( $text_autocomplete = NULL ,$maintainer_type)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnSystemWorkOrderStatus msos' )
                ->orderBy ( 'msos.mtn_system_work_order_status_name ASC' );


        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'msos.mtn_system_work_order_status_name LIKE ?' , $text_autocomplete . '%' );
        }
        $q->andWhere('msos.mtn_maintainer_type_id = ?' , $maintainer_type);
        return $q->execute ();
    }

    function findName ( $mtn_system_work_order_status_name )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnSystemWorkOrderStatus msos' )
                ->where ( 'msos.mtn_system_work_order_status_name LIKE ?' , '%' . $mtn_system_work_order_status_name . '%' );

        return $q->execute ( array ( ) , Doctrine_Core :: HYDRATE_SCALAR );
    }
    
 /**
     *
     * assetTypeInAsset
     * Retorna true en el caso que exista un Tipo de Activo asociado a un Activo  y False en el caso contrario
     */
    function stateInWorkingOrder ( $mtn_system_work_order_status_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnSystemWorkOrderStatus mswos' )
                ->innerJoin ( 'mswos.MtnConfigState mcs' )
                ->where ( 'mcs.mtn_system_work_order_status_id = ?' , $mtn_system_work_order_status_id )
                ->limit ( 1 );

        $results = $q->execute ();
        return ($results->count () == 0 ? false : true);
    }

}
