<?php

class InfraInfoTable extends Doctrine_Table {

    function findByNodeId($node_id) {
        $q = Doctrine_Query::create()
            ->from('InfraInfo ff')
            ->where('ff.node_id = ?', $node_id);
        return $q->fetchOne();
    }

    function findByNodeIdAndColumn($node_id, $column) {
        $q = Doctrine_Query::create()
                ->from('InfraInfo ff')
                ->where('ff.node_id = ?', $node_id)
                ->andWhere('ff.infra_info_m_terreno_cad != ?', 0)
                ->andWhere("ff.{$column} != ?", 0);
        return $q->fetchOne();
    }
    
    function findNodosValorUTFSM(){
        $query = Doctrine_Query::create()
            ->from('InfraInfo iff')
            ->where('iff.infra_info_uf != ?', 0);
        return $query->execute();
    }
    
    function getSumatoria($node_id, $formula) {
        $sum = Doctrine_Query::create()
            ->select($formula)
            ->from('Node n')
            ->innerJoin('n.InfraInfo iff')
            ->where('n.node_id != ?', $node_id)
            ->groupBy('n.node_parent_id');
        $treeObject = Doctrine_Core::getTable('Node')->getTree();
        $treeObject->setBaseQuery($sum);
        $tree = $treeObject->fetchBranch($node_id, array('depth' => 1));
        $result = $tree->getFirst();
        return $result->SUM;
    }

    function getPorcentaje($node_id, $formula) {
        $sum = Doctrine_Query::create()
            ->select($formula)
            ->from('Node n')
            ->innerJoin('n.InfraInfo iff')
            ->where('n.node_id = ?', $node_id)
            ->groupBy('n.node_parent_id');
        $treeObject = Doctrine_Core::getTable('Node')->getTree();
        $treeObject->setBaseQuery($sum);
        $tree = $treeObject->fetchBranch($node_id, array('depth' => 1));
        $result = $tree->getFirst();
        return $result->SUM;
    }
    
    function setUfValueDay($uf) {
        $query = Doctrine_Query::create()
            ->update("InfraInfo iff")
            ->set("iff.infra_info_uf_day_value","?", floatval($uf));
        $query->execute();
    }
    
}