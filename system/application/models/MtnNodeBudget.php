<?php

/**
 * MtnNodeBudget
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class MtnNodeBudget extends BaseMtnNodeBudget {

    function preHydrate ( $event ) {
        
        $data = $event->data;
        
        
        $data['mtn_node_budget_folio'] = str_pad($data['mtn_node_budget_id'], 10, "0", STR_PAD_LEFT);
        
        $event->data = $data;
        
    }

}
