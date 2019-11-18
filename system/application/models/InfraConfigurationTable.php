<?php

/**
 */
class InfraConfigurationTable extends Doctrine_Table {

    /**
     * 
     *
     */
    function findByNodeTypeId($node_type_id) {

        $q = Doctrine_Query::create()
                ->from('InfraConfiguration ic')
                ->where('node_type_id = ?', $node_type_id)
                ->orderBy('ic.infra_configuration_order ASC');

        return $q->execute();
    }

    function findByNodeTypeIdConfig($node_type_id) {
        $q = Doctrine_Query::create()
                ->from('InfraConfiguration ic')
                ->where('ic.node_type_id = ?', $node_type_id)
                ->andWhere('ic.infra_the_sumary = ?', 1)
                ->orderBy('ic.infra_configuration_order ASC');
        return $q->execute();
    }
    

    function retrieveByNodeTypeExiste($node_type_id) {

        $q = Doctrine_Query::create()
                ->from('InfraConfiguration ic')
                ->innerJoin('ic.NodeType nt')
                ->where('ic.node_type_id = ?', $node_type_id);

        return $q->fetchOne();
    }

    function retrieveSumary($node_type_id, $infra_attribute) {

        $q = Doctrine_Query::create()
                ->from('InfraConfiguration ic')
                ->where('ic.node_type_id = ?', $node_type_id)
                ->andWhere('ic.infra_attribute = ?', $infra_attribute);

        return $q->fetchOne();
    }

    /**
     * Elimina la configuraci�n de la informaci�n del tipo de nodo
     * @param integer $node_type_id
     */
    function deleteInfoConfigNodeType($node_type_id) {

        $q = Doctrine_Query::create()
                ->delete('InfraConfiguration ic')
                ->where('node_type_id = ?', $node_type_id);

        return $q->execute();
    }

}
