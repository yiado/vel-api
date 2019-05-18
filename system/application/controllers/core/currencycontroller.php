<?php

/**
 * @package Controller
 * @subpackage CurrencyController
 */
class CurrencyController extends APP_Controller
{
    function CurrencyController ()
    {
        parent::APP_Controller ();
    }

    /**
     * Lista todos los tipos de moneda 
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $currencyTable = Doctrine_Core::getTable ( 'Currency' );
        $currency = $currencyTable->retrieveAll ($text_autocomplete);
        $json_data = $this->json->encode ( array ( 'total' => $currency->count () , 'results' => $currency->toArray () ) );
        echo $json_data;
    }

    /**
     * Crea un tipo de moneda
     * 
     * @param string $currency_name
     * @param string $currency_code
     * @param integer $currency_equivalence
     * @param char $currency_decimal_character
     * @param char $currency_thousands_character
     * @param integer $currency_number_of_decimal
     * @method POST
     */
    function add ()
    {
        $currency = new Currency();
        $currency_name = $this->input->post ( 'currency_name' );
        $currency_code = $this->input->post ( 'currency_code' );
        $currency_equivalence = $this->input->post ( 'currency_equivalence' );
        $currency_decimal_character = $this->input->post ( 'currency_decimal_character' );
        $currency_thousands_character = $this->input->post ( 'currency_thousands_character' );
        $currency_number_of_decimal = ( int ) $this->input->post ( 'currency_number_of_decimal' );
        $currency->currency_name = $currency_name;
        $currency->currency_code = $currency_code;
        $currency->currency_equivalence = $currency_equivalence;
        $currency->currency_decimal_character = $currency_decimal_character;
        $currency->currency_thousands_character = $currency_thousands_character;
        $currency->currency_number_of_decimal = $currency_number_of_decimal;

        try
        {
            $currency->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }

        //Output 
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Update de un tipo de moneda
     * 
     * @param integer $currency_id
     * @param string $currency_name
     * @param string $currency_code
     * @param integer $currency_equivalence
     * @param char $currency_decimal_character
     * @param char $currency_thousands_character
     * @param integer $currency_number_of_decimal
     * @method POST
     */
    function update ()
    {
        $currency_id = $this->input->post ( 'currency_id' );
        $currency_name = $this->input->post ( 'currency_name' );
        $currency_code = $this->input->post ( 'currency_code' );
        $currency_equivalence = $this->input->post ( 'currency_equivalence' );
        $currency_decimal_character = $this->input->post ( 'currency_decimal_character' );
        $currency_thousands_character = $this->input->post ( 'currency_thousands_character' );
        $currency_number_of_decimal = ( int ) $this->input->post ( 'currency_number_of_decimal' );
        $currency = Doctrine_Core::getTable ( 'Currency' )->retrieveById ( $currency_id );
        $currency->currency_name = $currency_name;
        $currency->currency_code = $currency_code;
        $currency->currency_equivalence = $currency_equivalence;
        $currency->currency_decimal_character = $currency_decimal_character;
        $currency->currency_thousands_character = $currency_thousands_character;
        $currency->currency_number_of_decimal = $currency_number_of_decimal;
        
        try
        {
            $currency->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }
        //Output 
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * 
     * Elimina un tipo de moneda
     * 
     * @param integer $currency_id
     */
    function delete ()
    {
        $currency_id = $this->input->post ( 'currency_id' );
        $checkCurrencyInPriceList = Doctrine::getTable ( 'Currency' )->checkCurrencyInPriceList ( $currency_id );

        if ( $checkCurrencyInPriceList === false )
        {
            $currency = Doctrine::getTable ( 'Currency' )->retrieveById ( $currency_id );
            if ( $currency->delete () )
            {
                $success = 'true';
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $success = 'false';
            }
        }
        else
        {
            $success = 'false';
            $msg = $this->translateTag ( 'General' , 'currency_associated_lists' );
        }
        //Output 
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

}