<?php

/**
 * MtnNodeWorkOrder
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class MtnNodeWorkOrder extends BaseMtnNodeWorkOrder {

    public function preHydrate ( Doctrine_Event $event )
    {
        $data = $event->data;
        $data['mtn_node_work_order_begin'] = null;
        $data['mtn_node_work_order_finish'] = null;
        $data[ 'Node' ] = Doctrine_Core::getTable ( 'Node' )->find ( $data[ 'node_id' ] );
        $event->data = $data;
    }
    
    public function postHydrate ( Doctrine_Event $event ) {

        $data = $event->data;
        $data['mtn_node_work_order_begin']  = $data['mtn_node_work_order_date_begin'] . ' ' . $data['mtn_node_work_order_time_begin'];
        $data['mtn_node_work_order_finish'] = $data['mtn_node_work_order_date_finish'] . ' ' . $data['mtn_node_work_order_time_finish'];
        $event->data = $data;
        
    }

}