<?php
/**
 */
class MtnNodePlanTaskTable extends Doctrine_Table {

    
    function findAll ($query )
    {

       $q = Doctrine_Query :: create()
			->select('npt.*, np.*, nt.*')
			->from('MtnNodePlanTask npt')
			->innerJoin('npt.MtnNodeTask nt')
                        ->innerJoin('npt.MtnNodePlan np');
       
       if (!is_null($query)){
           
            $q->where ('npt.mtn_node_plan_id = ?' , $query );
       }
                       

        return $q->execute ();
    }
    
    
    function retrieveAllByNodePlanId ( $mtn_node_plan_id )
    {

       $q = Doctrine_Query :: create()
			->select('npt.*, np.*, nt.*')
			->from('MtnNodePlanTask npt')
			->innerJoin('npt.MtnNodeTask nt')
                        ->innerJoin('npt.MtnNodePlan np')
                        ->where ('npt.mtn_node_plan_id =',  $mtn_node_plan_id )
                        ->orderBy('nt.mtn_node_task_name');

        return $q->execute ();
    }


}
