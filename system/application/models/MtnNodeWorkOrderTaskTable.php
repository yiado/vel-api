<?php
/**
 */
class MtnNodeWorkOrderTaskTable extends Doctrine_Table {

    
    function retrieveAllByOrderId ( $node_order_id )
    {

       $q = Doctrine_Query :: create()
			->select('wot.*, nt.*, nwo.*')
			->from('MtnNodeWorkOrderTask wot')
			->innerJoin('wot.MtnNodeTask nt')
                        ->innerJoin('wot.MtnNodeWorkOrder nwo')
                        ->where ( 'wot.mtn_node_work_order_id = ?' , $node_order_id );

        return $q->execute ();
    }
    
    function retrieveAll ( $mtn_work_order_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnNodeWorkOrderTask woT' )
                ->innerJoin ( 'woT.MtnNodeTask tk' )
                ->leftJoin ( 'woT.MtnNodeWorkOrder woTkC' )                
                ->where ( 'woT.mtn_node_work_order_id = ?' , $mtn_work_order_id )
                ->orderBy ( 'woT.mtn_node_work_order_task_price DESC' );

        $results = $q->execute ()->toArray ();

//        //Calculamos el valor de la tarea, sumando el precio a (insumo_cantidad * insumo_price)
//        foreach ( $results as $index => $tupla )
//        {
//
//            $results[ $index ][ 'mtn_costos_component_in_task' ] = 0;
//            $results[ $index ][ 'mtn_amount_component_in_task' ] = 0;
//
//            foreach ( $tupla[ 'MtnWorkOrderTaskComponent' ] as $component )
//            {
//                $precio = ( ! empty ( $component[ 'MtnPriceListComponent' ] ) ? $component[ 'MtnPriceListComponent' ][ 'mtn_price_list_component_price' ] : $component[ 'mtn_node_work_order_task_price' ]);
//                $results[ $index ][ 'mtn_costos_component_in_task' ] += ( $component[ 'mtn_work_order_component_amount' ] * $precio);
//                $results[ $index ][ 'mtn_amount_component_in_task' ] += $component[ 'mtn_work_order_component_amount' ];
//            }
//        }

        return $results;
    }
    
     function findByWoId ( $node_order_id )
    {

       $q = Doctrine_Query :: create()
			->select('wot.*, nt.*, nwo.*')
			->from('MtnNodeWorkOrderTask wot')
			->innerJoin('wot.MtnNodeTask nt')
                        ->innerJoin('wot.MtnNodeWorkOrder nwo')
                        ->where ( 'wot.mtn_node_work_order_id = ?' , $node_order_id );

        return $q->execute ();
    }
    
    

}
