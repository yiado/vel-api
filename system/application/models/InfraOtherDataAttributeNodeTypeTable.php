<?php

/**
 * @package Model
 * @subpackage Infrastructure
 * @author manuteko
 *
 */
class InfraOtherDataAttributeNodeTypeTable extends Doctrine_Table
{

    /**
     * Devuelve los atributos asociados al tipo de nodo
     * @param integer $node_type_id
     */
    function retrieveByNodeType ( $node_type_id = NULL )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'InfraOtherDataAttributeNodeType iant' )
                ->innerJoin ( 'iant.InfraOtherDataAttribute ia' )
                ->where ( 'iant.node_type_id = ?' , $node_type_id )
                ->andWhere ( 'infra_other_data_attribute_node_type_order >= 0' )
                ->orderBy ( 'iant.infra_other_data_attribute_node_type_order ASC' );


        return $q->execute ();
    }
    function retrieveByNodeTypeFichaResumen ( $node_type_id = NULL )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'InfraOtherDataAttributeNodeType iant' )
                ->innerJoin ( 'iant.InfraOtherDataAttribute ia' )
//                ->leftJoin ( 'ia.InfraOtherDataOption iodo' )
                ->where ( 'iant.node_type_id = ?' , $node_type_id )
                ->andWhere ( 'iant.infra_other_data_attribute_node_type_the_sumary = ?', 1)
                ->andWhere ( 'infra_other_data_attribute_node_type_order >= 0' )
                ->orderBy ( 'iant.infra_other_data_attribute_node_type_order ASC' );


        return $q->execute ();
    }
    function retrieveSumary($node_type_id, $infra_other_data_attribute_id) {

        $q = Doctrine_Query::create()
                ->from('InfraOtherDataAttributeNodeType iodant')
                ->where('iodant.node_type_id = ?', $node_type_id)
                ->andWhere('iodant.infra_other_data_attribute_id = ?', $infra_other_data_attribute_id);

        return $q->fetchOne();
    }
    function retrieveByNodeTypeExiste ( $node_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'InfraOtherDataAttributeNodeType iant' )
                ->innerJoin ( 'iant.NodeType nt' )
                ->where ( 'iant.node_type_id = ?' , $node_type_id );


        return $q->fetchOne();
    }

    /**
     * Elimina los atributos asociados al tipo de nodo
     * @param integer $node_type_id
     */
    function deleteInfoAttributeNodeType ( $node_type_id )
    {

        $q = Doctrine_Query::create ()
                ->delete ( 'InfraOtherDataAttributeNodeType iant' )
                ->where ( 'iant.node_type_id = ?' , $node_type_id )
                ->andWhere ( 'infra_other_data_attribute_node_type_order >= 0' );

        return $q->execute ();
    }

}
