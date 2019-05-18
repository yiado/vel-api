<?php

/**
 */
class MtnNodeWorkOrderTable extends Doctrine_Table {

	function retrieveAllId ( $filters = array(), $node_id, $search_branch = false ) {

		$q = Doctrine_Query :: create()
			->select('wo.*, wot.*, p.*, nost.*, a.*,r.*')
			->from('MtnNodeWorkOrder wo')
			->innerJoin('wo.Node n')
			->innerJoin('wo.MtnNodeWorkOrderType wot')
                        ->innerJoin('wo.MtnNodeStatus nost')
                        ->innerJoin('wo.Applicant a')
                        ->innerJoin('wo.Responsible r')
                        ->innerJoin('wo.Provider p');
			
        if ($search_branch) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $q->where('n.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('n.lft >= ?', $node->lft)
                    ->andWhere('n.rgt <= ?', $node->rgt);
        } else {
            $q->andWhere('wo.node_id = ?', $node_id);
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
	$q->orderBy('wo.mtn_node_work_order_folio ASC');
                

		return $q->execute();
		
	}
        
	
    function retrieveOne ( $mtn_node_work_order_id ) {

		$q = Doctrine_Query::create()
			->select('wo.*, p.*, wot.*, nost.*, a.*,r.*')
			->from('MtnNodeWorkOrder wo')
			->innerJoin('wo.Provider p')
			->innerJoin('wo.Node n')
			->leftJoin('wo.Applicant a')
                        ->leftJoin('wo.Responsible r')
            ->innerJoin('wo.MtnNodeWorkOrderType wot')
            ->innerJoin('wo.MtnNodeStatus nost')
			->where('wo.mtn_node_work_order_id = ?', $mtn_node_work_order_id);
                
                //->innerJoin('wo.UserCreator as uc')

		return $q->fetchOne();
		
    }
	
	function getEventos ( $node_id, $search_branch = false, $start=null, $end=null ) {
		
        $q = Doctrine_Query::create()
            ->select('wo.*, wot.*')
            ->from('MtnNodeWorkOrder wo')
            ->innerJoin('wo.Node n')
            ->innerJoin('wo.MtnNodeWorkOrderType wot')
            ->orderBy('mtn_node_work_order_time_begin');
            
        if ($search_branch) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $q->where('n.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('n.lft >= ?', $node->lft)
                    ->andWhere('n.rgt <= ?', $node->rgt);
        } else {
            $q->where('n.node_id = ?', $node_id);
        }
            
		if (!is_null($start)) {
			$q->andWhere('mtn_node_work_order_date_begin >= ?', $start);
		}
		
		if (!is_null($end)) {
			$q->andWhere('mtn_node_work_order_date_finish <= ?', $end);
		}
		
		return $q->execute();
		
	}
        
        
         function retrieveById($mtn_work_order_id)
        {

            $q = Doctrine_Query::create()
                    ->from('MtnNodeWorkOrder wo')
                    ->innerJoin('wo.Node n')
                    ->innerJoin('wo.MtnNodeWorkOrderType wot')
                    ->innerJoin('wo.MtnNodeStatus nost')
                    ->innerJoin('wo.Provider p')                   
                    ->where('wo.mtn_node_work_order_id = ?', $mtn_work_order_id)
            ;


            return $q->fetchOne();
        }

}