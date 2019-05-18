<?php

/**
 */
class InfraGrupoTable extends Doctrine_Table {

    function retrieveAll($text_autocomplete = NULL) {

        $q = Doctrine_Query::create()
                ->from('InfraGrupo')
                ->orderBy('infra_grupo_order');

        if (!is_null($text_autocomplete)) {
            $q->andWhere('infra_grupo_nombre LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

    function retrieveAllGrupos($node_id, $node_type_id) {

        $q = Doctrine_Query::create()
                ->from('InfraGrupo ig')
                ->innerJoin('ig.InfraOtherDataAttribute ioda')
                ->innerJoin('ioda.InfraOtherDataAttributeNodeType iodant ON iodant.infra_other_data_attribute_id = ioda.infra_other_data_attribute_id AND iodant.node_type_id = ' . $node_type_id)
                ->leftJoin('ioda.InfraOtherDataValue iodv ON ioda.infra_other_data_attribute_id = iodv.infra_other_data_attribute_id AND iodv.node_id = ' . $node_id)
                ->where('ioda.InfraOtherDataAttributeNodeType.infra_other_data_attribute_node_type_order > ?', -1)
                ->orderBy('infra_grupo_order')
                ->orderBy('iodant.infra_other_data_attribute_node_type_order', 'DSC');

        return $q->execute();
    }

//    function retrieveData($node_id, $node_type_id) {
//        
//        $q = Doctrine_Query::create()
//                ->select('ig.*, ioda.infra_other_data_attribute_name, 
//                        ioda.infra_other_data_attribute_type, 
//                        if(ioda.infra_other_data_attribute_type =5 ,iodo.infra_other_data_option_name,
//                         iodv.infra_other_data_value_value) as attribute_value')
//                ->from('InfraGrupo ig')
//                ->innerJoin('ig.InfraOtherDataAttribute ioda')
//                ->innerJoin('ioda.InfraOtherDataAttributeNodeType iodant ON iodant.infra_other_data_attribute_id = ioda.infra_other_data_attribute_id AND iodant.node_type_id = ' . $node_type_id)
//                ->leftJoin('ioda.InfraOtherDataValue iodv ON ioda.infra_other_data_attribute_id = iodv.infra_other_data_attribute_id AND iodv.node_id = ' . $node_id)
//                ->leftJoin('iodv.InfraOtherDataOption iodo ON iodv.infra_other_data_option_id= iodo.infra_other_data_option_id')
//                ->where('ioda.InfraOtherDataAttributeNodeType.infra_other_data_attribute_node_type_order > ?', -1)
//                ->orderBy('infra_grupo_order')
//                ->orderBy('iodant.infra_other_data_attribute_node_type_order', 'DESC');
//
//        return $q->execute();
//    }

//    function retrieveAllGruposConfig (  $node_type_id )
//    {
//
//        $q = Doctrine_Query::create ( )
//                ->from ( 'InfraGrupo ig' )
//                ->innerJoin ( 'ig.InfraOtherDataAttribute ioda' )
//                ->innerJoin ( 'ioda.InfraOtherDataAttributeNodeType iodant ON iodant.infra_other_data_attribute_id = ioda.infra_other_data_attribute_id AND iodant.node_type_id = ' . $node_type_id )
//                ->leftJoin ( 'ioda.InfraOtherDataValue iodv ON ioda.infra_other_data_attribute_id = iodv.infra_other_data_attribute_id AND iodv.node_id = ' . $node_id )
//                ->where ( 'iodant.node_type_id = '. $node_type_id )
//                ->andWhere ( 'iodant.infra_other_data_attribute_node_type_the_sumary = ?', 1)
//                ->andWhere ( 'iodant.infra_other_data_attribute_node_type_order >= 0' )
//                ->orderBy ( 'infra_grupo_order' )
//                ->orderBy ( 'iodant.infra_other_data_attribute_node_type_order', 'DSC' );
//        
//        return $q->execute ();
//    }

    function retrieveAllGruposExportar($node_id, $node_type_id) {

        $q = Doctrine_Query::create()
                ->from('InfraGrupo ig')
                ->innerJoin('ig.InfraOtherDataAttribute ioda')
                ->innerJoin('ioda.InfraOtherDataAttributeNodeType iodant ON iodant.infra_other_data_attribute_id = ioda.infra_other_data_attribute_id AND iodant.node_type_id = ' . $node_type_id)
                ->leftJoin('ioda.InfraOtherDataValue iodv ON ioda.infra_other_data_attribute_id = iodv.infra_other_data_attribute_id AND iodv.node_id = ' . $node_id)
                ->leftJoin('iodv.InfraOtherDataOption iodo')
                ->where('ioda.InfraOtherDataAttributeNodeType.infra_other_data_attribute_node_type_order > ?', -1)
                ->orderBy('infra_grupo_order')
                ->orderBy('iodant.infra_other_data_attribute_node_type_order', 'DSC');

//        return $q->execute ( array ( ) , Doctrine_Core :: HYDRATE_SCALAR );
        return $q->execute();
    }

    /**
     *
     * deleteInfraGrupo
     * Retorna true en el caso que exista un Atributo asociado a un Grupo  y False en el caso contrario
     */
    function deleteInfraGrupo($infra_grupo_id) {

        $q = Doctrine_Query::create()
                ->from('InfraGrupo ig')
                ->innerJoin('ig.InfraOtherDataAttribute ioda')
                ->where('ioda.infra_grupo_id = ?', $infra_grupo_id)
                ->limit(1);

        $results = $q->execute();
        return ($results->count() == 0 ? false : true);
    }

    function retrieveByMayor() {
        $q = Doctrine_Query::create()
                ->from('InfraGrupo ig')
                ->orderBy('ig.infra_grupo_order DESC');
        return $q->fetchOne();
    }

    function findByBefore($menos1) {
        $q = Doctrine_Query::create()
                ->from('InfraGrupo ig')
                ->where('ig.infra_grupo_order = ?', $menos1);

        return $q->fetchOne();
    }

    function findGrupoAtribute($infra_grupo_id) {
        $q = Doctrine_Query::create()
                ->from('InfraOtherDataAttribute ioda')
                ->where('ioda.infra_grupo_id = ?', $infra_grupo_id);

        return $q->execute();
    }

}
