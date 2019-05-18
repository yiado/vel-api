<?php

/**
 */
class PlanSectionTable extends Doctrine_Table
{

    function finByPlanId ( $plan_id , $plan_section_status=null )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'PlanSection ps' )
                ->where ( 'plan_id = ?' , $plan_id )
                ->orderBy ( 'plan_section_name' );

        if ( ! is_null ( $plan_section_status ) )
        {
            $q->andWhere ( 'plan_section_status = ?' , $plan_section_status );
        }

        return $q->execute ();
    }
    
     function finBySectionId ( $id_section )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'PlanSection ps' )
                ->where ( 'plan_section_id = ?' , $id_section )
                ->orderBy ( 'plan_section_name' );

     

        return $q->execute ();
    }

}
