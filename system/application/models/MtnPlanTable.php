<?php

/**
 */
class MtnPlanTable extends Doctrine_Table
{

    /**
     * retrieveAll
     * 
     * Recupera los planes de los equipos
     */
    function retrieveAll ($text_autocomplete = NULL,$maintainer_type)
    {

        $q = Doctrine_Query :: create ()
                ->from ( 'MtnPlan p' )
                ->orderBy ( 'p.mtn_plan_name ASC' );
        
        if (!is_null($text_autocomplete))
        {
            $q->where('p.mtn_plan_name LIKE ?', $text_autocomplete . '%');
        }
       $q->andWhere('p.mtn_maintainer_type_id = ?', $maintainer_type);
       
        return $q->execute ();
    }

    /**
     *
     * assetStatusInAsset
     * Retorna true en el caso que exista un plan asociado a un Servicio  y False en el caso contrario
     */
    function planStatusInPlanService ( $mtn_plan_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPlan p' )
                ->innerJoin ( 'p.MtnPlanService ps' )
                ->where ( 'ps.mtn_plan_id = ?' , $mtn_plan_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }

}