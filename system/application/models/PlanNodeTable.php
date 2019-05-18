<?php

/**
 */
class PlanNodeTable extends Doctrine_Table {

    function findByNodeId($node_id, $plan_id = null) {

        $q = Doctrine_Query::create()
                ->from('PlanNode pn')
                ->innerJoin('pn.Plan p')
                ->where('pn.node_id = ?', $node_id)
                ->orderBy('p.plan_datetime DESC')
                ->limit(1);

        if (!is_null($plan_id)) {
            $q->andWhere('pn.plan_id = ?', $plan_id);
        }

        return $q->execute();
    }

    function findPlanNode($node_id) {
        
        $q = Doctrine_Query::create()
                ->select('pn.handler, pn.plan_node_id, pn.plan_section_id, p.node_id as nodeLine')
                ->from('PlanNode pn')
                ->innerJoin('pn.Plan p')
                ->where('pn.node_id = ?', $node_id)
                ->orderBy('p.plan_datetime DESC')
                ->limit(1);

      

        return $q->execute();
    }
    
        function findByHandler($id_handler,$plan_id) {
       
        $q = Doctrine_Query::create()
              
                ->from('PlanNode pn')
                ->where('pn.handler = ?', $id_handler)
                ->andWhere('pn.plan_id = ?', $plan_id)
                ->orderBy('pn.plan_node_id DESC')
                ->limit(1);

       

        return $q->execute();
    }
    
    

    function findById($node_id) {

        $q = Doctrine_Query::create()
                ->from('PlanNode pn')
                ->where('pn.node_id = ?', $node_id);

        return $q->execute();
    }

    function findByNodeAndPlan($plan_id, $plan_section_id) {
        $q = Doctrine_Query::create()
                ->from('PlanNode pn')
                ->where('pn.plan_id = ?', $plan_id)
                ->andWhere('pn.plan_section_id = ?', $plan_section_id);

        return $q->execute();
    }

}
