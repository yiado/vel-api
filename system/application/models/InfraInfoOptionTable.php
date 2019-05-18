<?php

/**
 */
class InfraInfoOptionTable extends Doctrine_Table
{

    function findByParent ( $infra_info_option_parent_id = NULL, $query=null )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'InfraInfoOption iff' );

        if ( ! is_null ( $infra_info_option_parent_id ) )
        {

            $q->andWhere ( 'infra_info_option_parent_id = ?' , $infra_info_option_parent_id );
        }
        else
        {
            $q->andWhere ( 'infra_info_option_parent_id IS NULL' );
        }

        if ( ! is_null ( $query ) )
        {

            $q->andWhere ("infra_info_option_name LIKE ?" , '%' . $query . '%' );
        }

        return $q->execute ();
    }

    /**
     * Devuelve la info de un campo de opción
     * @param integer $infra_info_option_id
     */
    function retrieveById ( $infra_info_option_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'InfraInfoOption iio' )
                ->where ( 'iio.infra_info_option_id = ?' , $infra_info_option_id );

        return $q->fetchOne ();
    }

}
