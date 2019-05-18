<?php

/**
 */
class MeasureUnitTable extends Doctrine_Table
{

    /**
     * Retorna la info de la tupla del tipo de unidad de medida
     * 
     * @param integer $measure_unit_id
     * @return 1 row
     */
    function retrieveById ( $measure_unit_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MeasureUnit mu' )
                ->where ( 'mu.measure_unit_id = ?' , $measure_unit_id );

        return $q->fetchOne ();
    }

    /**
     * retrieveAll
     * 
     * Retorna todas los tipos de unidades de medicion
     * 
     */
    function retrieveAll ($text_autocomplete = NULL)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MeasureUnit mu' )
                ->orderBy ( 'mu.measure_unit_name ASC' );
        
        if (!is_null($text_autocomplete))
        {
            $q->andWhere('mu.measure_unit_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute ();
    }

    /**
     *
     * Retorna true en el caso que exista un activo asociado al tipo de unidad de medida y false en el caso contrario
     * @param integer $measure_unit_id
     * @return boolean true|false
     */
    function checkMeasureUnitInAsset ( $measure_unit_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'AssetMeasurement am' )
                ->where ( 'am.measure_unit_id = ?' , $measure_unit_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }

}
