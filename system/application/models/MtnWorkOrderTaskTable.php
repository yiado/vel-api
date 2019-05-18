<?php

/**
 * Modelo para las tareas asociadas a la orden de trabajo
 * @author manuteko
 *
 */
class MtnWorkOrderTaskTable extends Doctrine_Table
{

    /**
     * 
     * Devuelve todas las tareas asociadas a la OT
     * @param intreger $mtn_work_order_id
     * @return object as array
     */
    function retrieveAll ( $mtn_work_order_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderTask woT' )
                ->innerJoin ( 'woT.MtnTask tk' )
                ->leftJoin ( 'woT.Currency c' )
                ->leftJoin ( 'woT.MtnWorkOrderTaskComponent woTkC' )
                ->leftJoin ( 'woTkC.MtnPriceListComponent plC' )
                ->where ( 'woT.mtn_work_order_id = ?' , $mtn_work_order_id )
                ->orderBy ( 'woT.mtn_work_order_task_price DESC' );

        $results = $q->execute ()->toArray ();

        //Calculamos el valor de la tarea, sumando el precio a (insumo_cantidad * insumo_price)
        foreach ( $results as $index => $tupla )
        {

            $results[ $index ][ 'mtn_costos_component_in_task' ] = 0;
            $results[ $index ][ 'mtn_amount_component_in_task' ] = 0;
            $results[ $index ][ 'mtn_work_order_component_price' ] = 0;

            foreach ( $tupla[ 'MtnWorkOrderTaskComponent' ] as $component )
            {
                $precio = ( ! empty ( $component[ 'MtnPriceListComponent' ] ) ? $component[ 'MtnPriceListComponent' ][ 'mtn_price_list_component_price' ] : $component[ 'mtn_work_order_component_price' ]);
                $results[ $index ][ 'mtn_work_order_component_price' ] = $precio;
                $results[ $index ][ 'mtn_costos_component_in_task' ] += ( $component[ 'mtn_work_order_component_amount' ] * $precio);
                $results[ $index ][ 'mtn_amount_component_in_task' ] += $component[ 'mtn_work_order_component_amount' ];
            }
        }

        return $results;
    }

}
