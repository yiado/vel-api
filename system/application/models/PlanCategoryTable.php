<?php

/**
 */
class PlanCategoryTable extends Doctrine_Table
{

    /**
     * retrieveAll
     *
     * Retorna todos los tipo equipo
     */
    function retrieveAll ( $text_autocomplete = NULL )
    {

        $q = Doctrine_Query :: create ()
                ->from ( 'PlanCategory' )
                ->orderBy ( 'plan_category_name ASC' );
        
        if (!is_null($text_autocomplete))
        {
            $q->andWhere('plan_category_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute ();
    }

    /**
     *
     * checkPlanInCategory
     * Retorna true en el caso que exista un plano asociado a la categoria y False en el caso contrario
     */
    function checkPlanInCategory ( $plan_category_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'PlanCategory pc' )
                ->innerJoin ( 'pc.Plan p' )
                ->where ( 'p.plan_category_id = ?' , $plan_category_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }

    /**
     * Setea a 0 el flag de la categoria del plan marcada por defecto para la vinculación de los nodos, excepto al plan_category_id pasado por parametro.
     * @param integer $exclude_language_id
     *
     */
    function unSetDefaultCategory ( $exclude_plan_category_id = NULL )
    {

        $q = Doctrine_Query::create ()
                ->update ( 'PlanCategory pc' )
                ->set ( 'pc.plan_category_default' , 0 );

        if ( ! is_null ( $exclude_plan_category_id ) )
        {

            $q->where ( 'pc.plan_category_id <> ?' , $exclude_plan_category_id );
        }

        return $q->execute ();
    }

}
