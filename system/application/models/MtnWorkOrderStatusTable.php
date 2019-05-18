<?php

/**
 */
class MtnWorkOrderStatusTable extends Doctrine_Table
{

    /**
     * Retorna la info de un estado de la OT
     * @param integer $mtn_work_order_status_id
     *
     */
    function retrieveById ( $mtn_work_order_status_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderStatus wos' )
                ->where ( 'wos.mtn_work_order_status_id = ?' , $mtn_work_order_status_id );

        return $q->fetchOne ();
    }

    ////////////////// Temporalmente deshabilitada, no borrar. ////////////////////
    /**
     * Genera el flujo de estados de la ot.
     * Realiza una copia de ll flujo por defecto y setea las tiempos de inicio y termino
     * @param integer $mtn_work_order_id
     *
     */
    function generateFlowStatusWo ( $mtn_work_order_id )
    {

        //Buscamos la info de la ot
        $workOrder = Doctrine_Core::getTable ( 'MtnWorkOrder' )->retrieveById ( $mtn_work_order_id );

        $asset_type_id = $workOrder->Asset->asset_type_id;
        $mtn_work_order_type_id = $workOrder->mtn_work_order_type_id;

        //Buscamos los estados posibles dependiendo del tipo de ot y tipo de activo
        $mtnDefaultFlowStatusWo = Doctrine_Core::getTable ( 'MtnPossibleStatusWorkOrder' )->retrieveDefaultFlowStatusWo ( $asset_type_id , $mtn_work_order_type_id );

        //Creamos un array de los estados ordenados por posision
        $status_flow_wo = array ( );
        foreach ( $mtnDefaultFlowStatusWo as $status )
        {

            $position = $status->mtn_possible_status_work_order_position;

            $status_flow_wo[ $position ] = array (
                'start_in_hours' => $status->mtn_possible_status_work_order_start_in_hours ,
                'finish_in_hours' => $status->mtn_possible_status_work_order_finish_in_hours ,
                'name' => $status->MtnSystemWorkOrderStatus->mtn_system_work_order_status_name ,
                'start_date' => NULL ,
                'finish_date' => NULL
            );
        }

        try
        {
            foreach ( $status_flow_wo as $clave => $status )
            {

                //Verificamos si existe un estado anterior
                if ( ! empty ( $status_flow_wo[ $clave - 1 ] ) )
                {

                    $date_start = $status_flow_wo[ $clave - 1 ][ 'finish_date' ];
                }
                else
                {

                    $date_start = $workOrder->mtn_work_order_date;
                }

                $year_month_day = explode ( '-' , $date_start );

                $start_in_hours = ( int ) $status_flow_wo[ $clave ][ 'start_in_hours' ];
                $finish_in_hours = ( int ) $status_flow_wo[ $clave ][ 'finish_in_hours' ];

                $mtnWorkOrderStatusTable = new MtnWorkOrderStatus();

                $mtnWorkOrderStatusTable->mtn_work_order_id = $mtn_work_order_id;
                $mtnWorkOrderStatusTable->mtn_work_order_status_name = $status_flow_wo[ $clave ][ 'name' ];
                $mtnWorkOrderStatusTable->mtn_work_order_status_start_in_hours = $start_in_hours;
                $mtnWorkOrderStatusTable->mtn_work_order_status_finish_in_hours = $finish_in_hours;

                //Calculamos la fecha de inicio del estado (termino estado anterior + start_in_hours)
                $date_start_in_seconds = mktime ( 0 , 0 , 0 , $year_month_day[ 1 ] , $year_month_day[ 2 ] , $year_month_day[ 0 ] ) + (60 * 60 * $start_in_hours);
                $mtnWorkOrderStatusTable->mtn_work_order_status_date_start = date ( 'Y-m-d' , $date_start_in_seconds );

                //Calculamos la fecha de termino del estado
                $date_finish_in_seconds = mktime ( 0 , 0 , 0 , $year_month_day[ 1 ] , $year_month_day[ 2 ] , $year_month_day[ 0 ] ) + (60 * 60 * $finish_in_hours);
                $status_flow_wo[ $clave ][ 'finish_date' ] = date ( 'Y-m-d' , $date_finish_in_seconds );
                $mtnWorkOrderStatusTable->mtn_work_order_status_date_finish = $status_flow_wo[ $clave ][ 'finish_date' ];

                $mtnWorkOrderStatusTable->save ();
            }
        }
        catch ( Exception $e )
        {

            throw new Exception ( $e->getMessage () );
        }
    }

    ////////////////// Temporalmente deshabilitada, no borrar. ////////////////////
    /**
     * Retorna el flujo de estados generado para la OT
     * @param integer $mtn_work_order_id
     */
    function retrieveFlowStatusWo ( $mtn_work_order_id , $onlyOutstanding = NULL )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderStatus wos' )
                ->where ( 'wos.mtn_work_order_id = ?' , $mtn_work_order_id );

        if ( $onlyOutstanding === true )
        {
            $q->andWhere ( 'wos.mtn_work_order_status_status IS NULL' );
        }

        return $q->execute ();
    }

    /**
     * Retorna los estados pendientes de una OT
     * @param integer $mtn_work_order_id
     */
    function outstandingStatus ( $mtn_work_order_id )
    {

        $flowStatusWo = Doctrine_Core::getTable ( 'MtnWorkOrderStatus' )->retrieveFlowStatusWo ( $mtn_work_order_id , true );

        //Creamos un array con los estados pendientes de finalización
        $outstanding_status = array ( );
        foreach ( $flowStatusWo as $status )
        {

            $outstanding_status[ $status->mtn_work_order_status_name ] = $status->mtn_work_order_status_id;
        }

        return $outstanding_status;
    }

}