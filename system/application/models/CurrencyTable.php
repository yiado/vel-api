<?php

/**
 * 
 * @author manuteko
 *
 */
class CurrencyTable extends Doctrine_Table
{

    /**
     * Devuelve la info de un tipo de moneda
     * @param integer $currency_id
     */
    function retrieveById ( $currency_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Currency cy' )
                ->where ( 'cy.currency_id = ?' , $currency_id );

        return $q->fetchOne ();
    }

    /**
     * 
     * Retorna todos los tipos de moneda en el sistema
     * 
     */
    function retrieveAll ($text_autocomplete = NULL)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Currency cy' )
                ->orderBy ( 'cy.currency_name ASC' );
        
        if (!is_null($text_autocomplete))
        {
            $q->where('cy.currency_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute ();
    }

    /**
     *
     * Retorna true en el caso que exista una lista de precios asociada al tipo de moneda y false en el caso contrario
     * @param integer $currency_id
     * @return boolean true|false
     */
    function checkCurrencyInPriceList ( $currency_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPriceList mpl' )
                ->where ( 'mpl.currency_id = ?' , $currency_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }

}
