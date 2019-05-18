<?php

/**
 */
class MtnNodePriceListTable extends Doctrine_Table {

    function findAll($node_id, $search_branch = false) {

        $q = Doctrine_Query :: create()
                ->select('np.*')
                ->from('MtnNodePriceList np')
                ->innerJoin('np.Node n');

        if ($search_branch) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $q->where('n.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('n.lft >= ?', $node->lft)
                    ->andWhere('n.rgt <= ?', $node->rgt);
        } elseif ($node_id) {
            $q->Where('np.node_id = ?', $node_id);
        }


        return $q->execute();
    }

    function retrieveOne($mtn_node_price_list_id) {

        $q = Doctrine_Query::create()
                ->select('np.*')
                ->from('MtnNodePriceList np')
                ->innerJoin('np.Node n')
                ->where('np.mtn_node_price_list_id = ?', $mtn_node_price_list_id);

        //->innerJoin('wo.UserCreator as uc')

        return $q->fetchOne();
    }

}
