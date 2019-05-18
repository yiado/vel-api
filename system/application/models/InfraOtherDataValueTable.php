<?php

/**
 * @package Model
 * @subpackage Infrastructure
 * @author manuteko
 *
 */
class InfraOtherDataValueTable extends Doctrine_Table {

    /**
     * retrieveById
     * 
     * Retorna valor de info por id
     * 
     * @param integer $infra_other_data_value_id
     */
    function retrieveById($infra_other_data_value_id) {

        $q = Doctrine_Query::create()
                ->from('InfraOtherDataValue')
                ->where('infra_other_data_value_id = ?', $infra_other_data_value_id);

        return $q->execute();
    }

    function retrieveByIdAttribute($infra_other_data_attribute_id, $infra_other_data_value_value) {
   
        $q = Doctrine_Query::create()
                ->from('InfraOtherDataValue')
                ->where(' infra_other_data_attribute_id = ?', $infra_other_data_attribute_id)
                ->andWhere('infra_other_data_value_value = ?', $infra_other_data_value_value);
       
        return $q->fetchOne();
    }

    function retrieveByAttributeNode($node_id, $infra_other_data_attribute_id) {

        $q = Doctrine_Query::create()
                ->from('InfraOtherDataValue')
                ->where('node_id = ?', $node_id)
                ->andWhere('infra_other_data_attribute_id = ?', $infra_other_data_attribute_id)
                ->limit(1)
                ->orderBy('infra_other_data_value_id DESC');

        return $q->fetchOne();
    }

}
