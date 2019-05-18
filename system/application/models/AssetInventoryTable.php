<?php

/**
 */
class AssetInventoryTable extends Doctrine_Table {
    
    function findAllTrasladados($node_id, $user_id, $start = null, $limit = null, $count = false) {
        
        $q = Doctrine_Query::create()
                ->from('AssetInventoryAuxiliarProceso aiap')
                ->where('aiap.situacion = ?', "TRASLADADO")
                ->andWhere('aiap.user_id = ?', $user_id);
        
         if ($node_id != 'root') {
            $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );
            $q->andWhere( 'aiap.Node.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'aiap.Node.lft > ?' , $node->lft )
                ->andWhere ( 'aiap.Node.rgt < ?' , $node->rgt );
        }
        
        if (!is_null($start)) {
            $q->offset($start);
        }
        if (!is_null($limit)) {
            $q->limit($limit);
        }
        if ($count) {
            $q->select('count(*)');
            return $q->fetchOne()->count;
        } else {
            return $q->execute();
        }
    }
    
    function findAllTrasladadosTotales($node_id, $user_id) {
        
        $q = Doctrine_Query::create()
                ->from('AssetInventoryAuxiliarProceso aiap')
                ->where('aiap.situacion = ?', "TRASLADADO")
                ->andWhere('aiap.user_id = ?', $user_id);
        
         if ($node_id != 'root') {
            $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );
            $q->andWhere( 'aiap.Node.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'aiap.Node.lft > ?' , $node->lft )
                ->andWhere ( 'aiap.Node.rgt < ?' , $node->rgt );
        }
        
            return $q->execute();
    }
    
    function findAllAssetId($user_id) {
        
        $q = Doctrine_Query::create()
                ->from('AssetInventory ai')
                ->where('ai.user_id = ?', $user_id);
        
            return $q->execute();
    }
    
    function findAllAssetIdProceso($user_id) {
        
        $q = Doctrine_Query::create()
                ->from('AssetInventaryAuxiliarProceso aiap')
                ->where('aiap.user_id = ?', $user_id);
        
            return $q->execute();
    }
    
    
    
  }
