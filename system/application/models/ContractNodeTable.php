<?php

/**
 */
class ContractNodeTable extends Doctrine_Table {

    function getById($node_id) {

        $q = Doctrine_Query::create()
                ->from('ContractNode cn')
                ->where('cn.node_id = ?', $node_id);
        
        return $q->execute();
    }
    
    function retrieveAll($contract_id) {

        $q = Doctrine_Query::create()
                ->from('ContractNode cn')
//                ->innerJoin('cn.Node n')
//                ->innerJoin('n.ContractNode cn')
                ->where('cn.contract_id = ?', $contract_id);
        
//        return $q->execute ( array ( ) , Doctrine_Core :: HYDRATE_SCALAR );
        
        return $q->execute();
    }
    
      function retrieveByTypeProvider ($provider_type_id) {

        $q = Doctrine_Query::create()
                ->from('ContractNode cn')
                ->leftJoin('cn.Contract c')
                ->leftJoin('c.Provider p')
                ->where('p.provider_type_id = ?', $provider_type_id);
        
        return $q->execute();
    }

    function retrieveByFilter($node_id) {

        $q = Doctrine_Query::create()
                ->select('a.*, at.*, ba.*, ac.*, as.*, ca.*')
                ->from('Node n')
                ->leftJoin('n.ContractNode cn')
                ->innerJoin('a.Node n')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->where('a.node_id = ?', $node_id)
                ->leftJoin('a.AssetCondition ac');

        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $q->where('n.node_parent_id = ?', $node->node_parent_id)
                ->andWhere('n.lft >= ?', $node->lft)
                ->andWhere('n.rgt <= ?', $node->rgt);

        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                $q->andWhere($field, $value);
            }
        }
        $q->andWhere('a.asset_estate = ?', 0);
        $q->andWhere('a.asset_id NOT IN ( SELECT caa.asset_id FROM ContractAsset caa)');

        $q->orderBy('asset_name');

        return $q->execute();
    }
    
    function retrieveByContractProvider($node_id) {

        $q = Doctrine_Query::create()
                ->from('ContractNode cn')
                ->orderBy('contract_node_id')
                ->where('cn.node_id = ?', $node_id);
        
        if ($node_id != 'root') {
            $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );
            $q->andWhere( 'cn.Node.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'cn.Node.lft > ?' , $node->lft )
                ->andWhere ( 'cn.Node.rgt < ?' , $node->rgt );
        }

        return $q->execute();
    }

}
