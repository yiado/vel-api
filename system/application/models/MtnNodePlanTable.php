<?php
/**
 */
class MtnNodePlanTable extends Doctrine_Table {
    
    function findAll ($query )
    {

       $q = Doctrine_Query :: create()
			->select('np.*')
			->from('MtnNodePlan np');
			       
       if (!is_null($query)){
           
            $q->where ('np.mtn_node_plan_id = ?' , $query );
       }
                       

        return $q->execute ();
    }

    function retrieveOne ( $mtn_node_plan_id ) {

		$q = Doctrine_Query::create()
			->select('np.*')
			->from('MtnNodePlan np')			
			->where('np.mtn_node_plan_id = ?', $mtn_node_plan_id);
                
                //->innerJoin('wo.UserCreator as uc')

		return $q->fetchOne();
		
    }
    

}
