<?php
/**
 */
class MtnNodeBudgetTaskTable extends Doctrine_Table {


    function retrieveAllByNodeBudgetId ( $node_budget_id )
    {

       $q = Doctrine_Query :: create()
			->select('nbt.*, nt.*, np.*')
			->from('MtnNodeBudgetTask nbt')
			->innerJoin('nbt.MtnNodeBudget nb')
                        ->innerJoin('nbt.MtnNodeTask nt')                            
                        ->where ( 'nbt.mtn_node_budget_id = ?' , $node_budget_id );

        return $q->execute ();
    }
    
}
