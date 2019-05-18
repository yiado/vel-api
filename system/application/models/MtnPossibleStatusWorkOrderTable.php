<?php

/**
 * Modelo para los possibles estados de la OT
 * @author manuteko
 *
 */
class MtnPossibleStatusWorkOrderTable extends Doctrine_Table
{
    /*
     * Retorna todos los estados posibles para las OT
     */

    function retrieveAll ( $text_autocomplete = NULL )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPossibleStatusWorkOrder Pswo' )
                ->innerJoin ( 'Pswo.MtnSystemWorkOrderStatus msos' )
                ->groupBy ( 'msos.mtn_system_work_order_status_name' ) //Fix para evitar los datos duplicados
                ->orderBy ( 'Pswo.mtn_possible_status_work_order_position ASC' );


        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'msos.mtn_system_work_order_status_name LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }

    /*
     * Retorna todos los estados posibles para las OT
     */

    function stateAssignedType ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPossibleStatusWorkOrder Pswo' )
                ->innerJoin ( 'Pswo.MtnSystemWorkOrderStatus msos' )
                ->groupBy ( 'msos.mtn_system_work_order_status_name' )//Fix para evitar los datos duplicados
                ->where ( 'msos.mtn_system_work_order_status_id = ?' , 1 );


        return $q->execute ();
    }

    /**
     * Retorna el flujo de estados configurado para el tipo de OT y el tipo de activo
     * @param integer $asset_type_id
     * @param integer $mtn_work_order_type_id
     */
    function retrieveDefaultFlowStatusWo ( $asset_type_id , $mtn_work_order_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPossibleStatusWorkOrder Pswo' )
                ->where ( 'Pswo.asset_type_id = ?' , $asset_type_id )
                ->andWhere ( 'Pswo.mtn_work_order_type_id = ?' , $mtn_work_order_type_id )
                ->orderBy ( 'Pswo.mtn_possible_status_work_order_position ASC' );

        return $q->execute ();
    }

}
