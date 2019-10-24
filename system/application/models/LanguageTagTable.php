<?php

/**
 */
class LanguageTagTable extends Doctrine_Table
{

    function findByLanguage ( $language_id , $modules=null )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'LanguageTag lt' )
                ->innerJoin ( 'lt.Module m' )
                ->where ( 'language_id = ?' , $language_id );

        if ( ! is_null ( $modules ) )
        {
            $q->andWhereIn ( 'm.module_namespace' , $modules );
        }
        return $q->execute ();
    }

    /**
     * Retorna la info de la tupla del tag
     * 
     * @param integer $language_id
     * @param integer $language_tag_id
     * @return 1 row
     */
    function retrieveById ( $language_id , $language_tag_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'LanguageTag lt' )
                ->where ( 'lt.language_id = ?' , $language_id )
                ->andWhere ( 'lt.language_tag_id = ?' , $language_tag_id );

        return $q->fetchOne ();
    }

    function retrieveByTag ( $language_id , $module_namespace , $language_tag_tag )
    {
        
//        echo "-------";

        $q = Doctrine_Query::create ()
                ->from ( 'LanguageTag lt' )
                ->innerJoin ( 'lt.Module m' )
                ->where ( 'm.module_namespace = ?' , $module_namespace )
                ->andWhere ( 'lt.language_tag_tag = ?' , $language_tag_tag );

        return $q->fetchOne ();
    }

    /**
     * Retorna los tag del modulo e idioma
     * 
     * @param integer $language_id
     * @param integer $module_id
     * @return 1 row
     */
    function retrieveAll ( $language_id , $module_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'LanguageTag lt' )
                ->where ( 'lt.language_id = ?' , $language_id )
                ->andWhere ( 'lt.module_id = ?' , $module_id );

        return $q->execute ();
    }

}
