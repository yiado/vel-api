<?php

/**
 */
class LanguageTable extends Doctrine_Table
{

    /**
     * Retorna todos los idiomas disponibles en el sistema
     *
     */
    function retrieveAll ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Language la' )
                ->orderBy ( 'la.language_name ASC' );

        return $q->execute ();
    }

    /**
     * Retorna la info de la tupla del idioma
     *
     * @param integer $language_id
     * @return 1 row
     */
    function retrieveById ( $language_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Language la' )
                ->where ( 'la.language_id = ?' , $language_id );

        return $q->fetchOne ();
    }

    /**
     * Retorna el idioma por defecto
     *
     */
    function defaultLanguage ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Language' )
                ->where ( 'language_default = ?' , 1 );

        return $q->fetchOne ();
    }

    /**
     * Setea a 0 el flag de idioma por defecto a todos los idiomas, excepto al pasado por parametro.
     * @param integer $exclude_language_id
     *
     */
    function unSetDefaultLanguage ( $exclude_language_id = NULL )
    {

        $q = Doctrine_Query::create ()
                ->update ( 'Language' )
                ->set ( 'language_default' , 0 );

        if ( ! is_null ( $exclude_language_id ) )
        {
            $q->where ( 'language_id <> ?' , $exclude_language_id );
        }

        return $q->execute ();
    }

    /**
     * Copia los tag del idioma seleccionado y los asigna al nuevo idioma
     * @param integer $language_id
     * @param integer $language_ref_copy
     */
    function copyAndPasteLanguage ( $language_id , $language_ref_copy )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'LanguageTag lt' )
                ->where ( 'lt.language_id = ?' , $language_ref_copy );

        $tags_ref_language = $q->execute ();

        foreach ( $tags_ref_language as $language_tag )
        {

            $LanguageTag = new LanguageTag();
            $LanguageTag->language_tag_id = $language_tag->language_tag_id;
            $LanguageTag->language_id = $language_id;
            $LanguageTag->module_id = $language_tag->module_id;
            $LanguageTag->language_tag_tag = $language_tag->language_tag_tag;
            $LanguageTag->language_tag_value = $language_tag->language_tag_value;

            $LanguageTag->save ();
        }
    }

}
