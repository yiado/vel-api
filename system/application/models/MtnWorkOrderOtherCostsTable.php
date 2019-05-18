<?php

/**
 * 
 * @author manuteko
 *
 */
class MtnWorkOrderOtherCostsTable extends Doctrine_Table
{

    function retrieveAll ( $mtn_work_order_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderOtherCosts wooc' )
                ->innerJoin ( 'wooc.MtnOtherCosts moc' )
                ->where ( 'wooc.mtn_work_order_id = ?' , $mtn_work_order_id )
                ->orderBy ( 'wooc.mtn_work_order_other_costs_costs DESC' );


        return $q->execute ();
    }
    
        function retrieveByWoAndPercentage ( $mtn_work_order_id,$mtn_other_costs_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderOtherCosts wooc' )
               
                ->where ( 'wooc.mtn_work_order_id = ?' , $mtn_work_order_id )
                ->andWhere ( 'wooc.mtn_other_costs_id = ?' , $mtn_other_costs_id )
               ;


        return $q->fetchOne ();
    }
    
    
    
    
    
    
    
    

}
