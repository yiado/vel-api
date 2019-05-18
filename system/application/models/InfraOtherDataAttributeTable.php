<?php

/**
 * @package Model
 * @subpackage Infrastructure
 * @author manuteko
 *
 */
class InfraOtherDataAttributeTable extends Doctrine_Table
{

    /**
     * 
     * Retorna todos los atributos disponibles ignorando los que ya estan asociados al tipo de nodo.
     * Si el $node_type_id es NULL, lista todos los atributos disponibles
     * @param integer $node_type_id
     * 
     */
    function retrieveAll ( $node_type_id = NULL , $infra_other_data_attribute_id=null )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'InfraOtherDataAttribute ioda' )
                ->leftJoin ( 'ioda.InfraGrupo g' )
                ->orderBy ( 'ioda.infra_other_data_attribute_name ASC' );

        if ( ! is_null ( $infra_other_data_attribute_id ) )
        {
            $q->whereIn ( 'ioda.infra_other_data_attribute_id' , $infra_other_data_attribute_id );
        }

        if ( ! is_null ( $node_type_id ) )
        {
            $q->where ( 'ioda.infra_other_data_attribute_id NOT IN (SELECT iodant.infra_other_data_attribute_id FROM InfraOtherDataAttributeNodeType iodant WHERE iodant.node_type_id = ?)' , $node_type_id );
        }

        return $q->execute ();
    }

    /**
     *
     * checkDataInAttributeNodeType
     * Retorna true en el caso que exista un Dato asociado a un Tipo de Nodo y False en el caso contrario
     * @param integer $infra_other_data_attribute_id
     */
    function checkDataInAttributeNodeType ( $infra_other_data_attribute_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'InfraOtherDataAttributeNodeType iodant' )
                ->where ( 'iodant.infra_other_data_attribute_id = ?' , $infra_other_data_attribute_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }

    function deleteAttribute ( $infra_other_data_attribute_id )
    {

        //Eliminar las opciones del attributo
        $q = Doctrine_Query::create ()
                ->delete ( 'InfraOtherDataOption iodo' )
                ->where ( 'iodo.infra_other_data_attribute_id = ?' , $infra_other_data_attribute_id );

        $q->execute ();

        //Eliminar el atributo
        $q = Doctrine_Query::create ()
                ->delete ( 'InfraOtherDataAttribute ioda' )
                ->where ( 'ioda.infra_other_data_attribute_id = ?' , $infra_other_data_attribute_id );

        $q->execute ();
    }

}
