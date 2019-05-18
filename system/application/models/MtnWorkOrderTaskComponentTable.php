<?php

/**
 * Modelo para la tabla MtnWorkOrderTaskComponent
 * @author manuteko
 *
 */
class MtnWorkOrderTaskComponentTable extends Doctrine_Table
{

    /**
     * Devuelve todos los insumos asociados a la tarea
     * @param integer $mtn_work_order_task_component_id
     *
     */
    function retrieveAll ( $mtn_work_order_task_id )
    {

        $q = Doctrine_Query::create ()
//                ->select ( 'wotc.*, plc.mtn_price_list_component_price, co.mtn_component_name, cowo.mtn_component_name, ctco.mtn_component_type_name, ctcowo.mtn_component_type_name' )
                ->from ( 'MtnWorkOrderTaskComponent wotc' )
                ->leftJoin ( 'wotc.MtnPriceListComponent plc' )
                ->leftJoin ( 'plc.MtnComponent co' )
                ->leftJoin ( 'co.MeasureUnit mu' )
                ->leftJoin ( 'co.MtnComponentType ctco' )
                ->leftJoin ( 'wotc.MtnComponent cowo' )
                ->leftJoin ( 'cowo.MeasureUnit mucowo' )
                ->leftJoin ( 'cowo.MtnComponentType ctcowo' )
                ->where ( 'wotc.mtn_work_order_task_id = ?' , $mtn_work_order_task_id )
                ->orderBy ( 'wotc.mtn_work_order_component_amount DESC' );

        return $q->execute ();
    }

    /**
     * Devuelve el insumo especifico usado en la tarea
     * @param integer $mtn_work_order_task_component_id
     * @return 1 row
     *
     */
    function retrieveById ( $mtn_work_order_task_component_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderTaskComponent wotc' )
                ->where ( 'wotc.mtn_work_order_task_component_id = ?' , $mtn_work_order_task_component_id );

        return $q->fetchOne ();
    }
    
    function retrieveByIdLista ( $mtn_price_list_component_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderTaskComponent wotc' )
                ->where ( 'wotc.mtn_price_list_component_id = ?' , $mtn_price_list_component_id );

        return $q->fetchOne ();
    }

}
