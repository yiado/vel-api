<?php

/**
 */
class ContractAssetTable extends Doctrine_Table {

    function retrieveAll($contract_id) {

        $q = Doctrine_Query::create()
                ->from('ContractAsset ca')
                ->innerJoin('ca.Asset a')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->where('ca.contract_id=?', $contract_id);

        return $q->execute();
    }

    function retrieveByFilter($filters=array(), $node_id) {

        $q = Doctrine_Query::create()
                ->select('a.*, at.*, ba.*, ac.*, as.*, ca.*')
                ->from('Asset a')
                ->leftJoin('a.ContractAsset ca')
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

}
