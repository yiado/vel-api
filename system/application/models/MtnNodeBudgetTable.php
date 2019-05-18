<?php
/**
 */
class MtnNodeBudgetTable extends Doctrine_Table {

    
    function findAll( $filters = array(), $node_id, $search_branch = false ) {

		$q = Doctrine_Query :: create()
			->select('nb.*')
			->from('MtnNodeBudget nb')
                        ->innerJoin('nb.Node n');                        
									
        if ($search_branch) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $q->where('n.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('n.lft >= ?', $node->lft)
                    ->andWhere('n.rgt <= ?', $node->rgt);
        } else {
            $q->Where('nb.node_id = ?', $node_id);
        }
	
                
                $flag = false;
	foreach ($filters as $field => $value)
	{
	    if (!is_null($value))
	    {
		if ($flag === false)
		{
		    $q->andWhere($field, $value);
		    $flag = true;
		}
		else
		{
		    $q->andWhere($field, $value);
		}
	    }
	}
	
	//$q->andWhere('wo.mtn_node_work_order_status = ?', 1);
	//$q->orderBy('nb.mtn_node_budget_date_created ASC');
                

	return $q->execute();
		
	}
        
        
         function retrieveOne ( $mtn_node_budget_id ) {

		$q = Doctrine_Query::create()
			->select('nb.*')
			->from('MtnNodeBudget nb')			
			->innerJoin('nb.Node n')			           
			->where('nb.mtn_node_budget_id = ?', $mtn_node_budget_id);
               
               
		return $q->fetchOne();
		
    }
    
	function getTotal ( $mtn_node_budget_id ) {

		$q = Doctrine_Query :: create()
			->select('SUM(mtn_node_budget_task_amount * mtn_node_budget_task_value) as total')
			->from('MtnNodeBudgetTask m')
			->where('mtn_node_budget_id = ?', $mtn_node_budget_id);

		$total = $q->execute();

		if (!$total->count()) {
			return 0;
		}
                
		return $total[0]->total;

	}

}
