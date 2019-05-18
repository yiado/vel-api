<?php
class InfraInfoListener extends Doctrine_Record_Listener {
    
    public function preDelete ( Doctrine_Event $event ) {
        
        $node = $event->getInvoker();
        
        $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node->node_id);
        
        if ($info) $info->delete();
        
    }
    
}
