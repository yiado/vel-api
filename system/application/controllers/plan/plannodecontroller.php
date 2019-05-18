<?php

/** @package    Controller
 *  @subpackage PlanNodeController
 */
class PlanNodeController extends APP_Controller
{
    function PlanNodeController ()
    {
        parent::APP_Controller ();
    }

    function get ()
    {
        $node_id = $this->input->post ( 'node_id' );
        $planNode = Doctrine_Core::getTable ( 'PlanNode' )->findByNodeId ( $node_id );

        if ( $planNode->count () )
        {
            echo '({"total": "' . $planNode->count () . '", "results":' . $this->json->encode ( $planNode->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function save ()
    {
         
        $node_id = $this->input->post ( 'node_id' );
        $plan_id = $this->input->post ( 'plan_id' );
        $plan_section_id = $this->input->post ( 'plan_section_id' );
        $planNode = Doctrine_Core::getTable ( 'PlanNode' )->findOneByNodeId ( $node_id );
      
        if ( ! $planNode )
        {
            $planNode = new PlanNode();
        }
        $planNode->fromArray ( $this->input->postall () );
        $planNode->handler = trim ( $this->input->post ( 'handler' ) , ',' );
        $planNode->save();
        echo '{"success": true}';
        
        $plan = Doctrine_Core::getTable ( 'Plan' )->find( $plan_id  );
        $plan_category = Doctrine::getTable('PlanCategory')->find($plan->plan_category_id);
        $node = Doctrine::getTable('Node')->find($node_id);
        
         $this->syslog->register('add_plan_node', array(
                $plan->plan_filename,
                $plan->plan_description,
                $plan_category->plan_category_name,
                $node->getPath()
            )); // registering log
        
    }
    
    function saveForm ()
    {
        
        $node_id = $this->input->post ( 'node_id' );
        $plan_id = $this->input->post ( 'plan_id' );
        $plan_section_id = $this->input->post ( 'plan_section_id' );
        $planNode = Doctrine_Core::getTable ( 'PlanNode' )->findOneByNodeId ( $node_id );

        if ( ! $planNode )
        {
            $planNode = new PlanNode();
        }
        $planNode->fromArray ( $this->input->postall () );
        $planNode->save ();
        echo '{"success": true}';
        
        $plan = Doctrine_Core::getTable ( 'Plan' )->find( $plan_id  );
        $plan_category = Doctrine::getTable('PlanCategory')->find($plan->plan_category_id);
        $node = Doctrine::getTable('Node')->find($node_id);
        
         $this->syslog->register('add_plan_node_form', array(
                $plan->plan_filename,
                $plan->plan_description,
                $plan_category->plan_category_name,
                $node->node_name,
                $node->getPath()
            )); // registering log
        
    }
}