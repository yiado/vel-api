<?php

/**
 * @package Model
 * @subpackage Infrastructure
 * @author manuteko
 *
 */
class InfraOtherDataOptionTable extends Doctrine_Table
{

    /**
     * retrieveById
     * 
     * Retorna opción de info por id
     * 
     * @param integer $infra_other_data_attribute_id
     */
    function retrieveByAttribute($infra_other_data_attribute_id, $text_autocomplete = NULL)
    {

        $q = Doctrine_Query::create()
                ->from('InfraOtherDataOption')
                ->where('infra_other_data_attribute_id = ?', $infra_other_data_attribute_id)
                ->orderBy('infra_other_data_option_name ASC');

        if (!is_null($text_autocomplete))
        {
            $q->andWhere('infra_other_data_option_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }
    
    function retrieveByAttributeName($infra_other_data_attribute_id, $infra_other_data_option_name)
    {

        $q = Doctrine_Query::create()
                ->from('InfraOtherDataOption')
                ->where('infra_other_data_attribute_id = ?', $infra_other_data_attribute_id)
                ->andWhere('infra_other_data_option_name = ?', $infra_other_data_option_name);

        return $q->fetchOne();
    }
    function retrieveByOptionAndAtribute($infra_other_data_option_id, $infra_other_data_attribute_id)
    {

        $q = Doctrine_Query::create()
                ->from('InfraOtherDataOption')
                ->where('infra_other_data_option_id = ?', $infra_other_data_option_id)
                ->andWhere('infra_other_data_attribute_id = ?', $infra_other_data_attribute_id);

        return $q->fetchOne();
    }

}
