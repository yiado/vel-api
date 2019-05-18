<?php

/**
 * @package Model
 * @subpackage NodeTable
 */
class NodeTable extends Doctrine_Table {

    function findByNodeId($node_id, $node_type_id = null) {

        $q = Doctrine_Query::create()
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->where('n.node_id = ?', $node_id)
                ->orderBy('n.node_name DESC')
                ->limit(1);

        if (!is_null($node_type_id)) {
            $q->andWhere('n.node_type_id = ?', $node_type_id);
        }

        return $q->execute();
    }

    function findAll($query = null) {

        $q = Doctrine_Query :: create()
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->where('n.level = ?', 0)
                ->orderBy('n.node_name DESC');

        if (!is_null($query)) {

            $q->where('n.node_id = ?', $query);
        }

        return $q->execute();
    }

    function retrieveByArea($area_id, $infra_other_data_option_name) {
        $q = Doctrine_Query :: create()
                ->from('InfraOtherDataOption iodo')
                ->where('iodo.infra_other_data_attribute_id = ?', $area_id)
                ->andWhere('iodo.infra_other_data_option_name = ?', $infra_other_data_option_name);
        return $q->fetchOne();
    }

    function nodeNombre($piso) {
        $q = Doctrine_Query :: create()
                ->from('Node n')
                ->where('n.node_name = ?', $piso);
        return $q->fetchOne();
    }

    function findAllContractNode($node_id) {

        $q = Doctrine_Query :: create()
                ->from('ContractNode cn')
                ->where('cn.node_id = ?', $node_id);

        return $q->execute();
    }

    function findById($node_id) {

        $q = Doctrine_Query :: create()
                ->from('Node n')
                ->where('n.node_id = ?', $node_id);

        return $q->fetchOne();
    }

    function nodeByPadre($node_name) {
        $q = Doctrine_Query :: create()
                ->from('Node n')
                ->where('n.node_name = ?', $node_name);
        return $q->fetchOne();
    }

    function nodeExiste($node_id) {
        $q = Doctrine_Query :: create()
                ->from('InfraInfo ii')
                ->where('ii.node_id = ?', $node_id);
        return $q->fetchOne();
    }

    function getChildNodes($node) {
        

        $q = Doctrine_Query::create()
                ->select('n.node_id')
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->where('n.node_parent_id = ?', $node->node_parent_id)
                ->andWhere('n.lft >= ?', $node->lft)
                ->andWhere('n.rgt <= ?', $node->rgt);
        
        return $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }

}
