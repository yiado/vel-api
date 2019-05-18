<?php

/** @package    Controller
 *  @subpackage Pricelistcontroller
 */
class Pricelistcontroller extends APP_Controller
{
    function Pricelistcontroller ()
    {
        parent::APP_Controller ();
    }

    /**
     * 
     * Lista todas las listas de Precio
     * 
     */
    function get ()
    {
        $mtnPriceListTable = Doctrine_Core::getTable ( 'MtnPriceList' );
           $maintainer_type=1; //asset
        $priceList = $mtnPriceListTable->retrieveAllPrice ($maintainer_type );

        if ( $priceList->count () )
        {
            echo '({"total":"' . $priceList->count () . '", "results":' . $this->json->encode ( $priceList->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    function getByNode ()
    {
        $mtnPriceListTable = Doctrine_Core::getTable ( 'MtnPriceList' );
             $maintainer_type=2;//node
        $priceList = $mtnPriceListTable->retrieveAllPrice ($maintainer_type );

        if ( $priceList->count () )
        {
            echo '({"total":"' . $priceList->count () . '", "results":' . $this->json->encode ( $priceList->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega un nuevo lista de Precio 
     * 
     * @param integer $provider_id
     * @param integer $currency_id
     * @param integer $mtn_price_list_date_validity_start
     * @param integer $mtn_price_list_date_validity_finish
     * @method POST
     */
    function add ()
    {
        $provider_id = $this->input->post ( 'provider_id' );
        $currency_id = $this->input->post ( 'currency_id' );
        $mtn_price_list_date_validity_start = $this->input->post ( 'mtn_price_list_date_validity_start' );
        $mtn_price_list_date_validity_finish = $this->input->post ( 'mtn_price_list_date_validity_finish' );
        $priceList = new MtnPriceList();
        $priceList->provider_id = $provider_id;
        $priceList->currency_id = $currency_id;
        $priceList->mtn_price_list_date_validity_start = $mtn_price_list_date_validity_start;
        $priceList->mtn_price_list_date_validity_finish = $mtn_price_list_date_validity_finish;
        $priceList->mtn_maintainer_type_id = 1;

        try
        {
            $priceList->save ();
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
    function addByNode ()
    {
        $provider_id = $this->input->post ( 'provider_id' );
        $currency_id = $this->input->post ( 'currency_id' );
        $mtn_price_list_date_validity_start = $this->input->post ( 'mtn_price_list_date_validity_start' );
        $mtn_price_list_date_validity_finish = $this->input->post ( 'mtn_price_list_date_validity_finish' );
        $priceList = new MtnPriceList();
        $priceList->provider_id = $provider_id;
        $priceList->currency_id = $currency_id;
        $priceList->mtn_price_list_date_validity_start = $mtn_price_list_date_validity_start;
        $priceList->mtn_price_list_date_validity_finish = $mtn_price_list_date_validity_finish;
        $priceList->mtn_maintainer_type_id = 2;

        try
        {
            $priceList->save ();
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
     * update
     * 
     * Modifica una lista de precio
     * 
     * @param integer $mtn_price_list_id
     * @param integer $provider_id
     * @param integer $currency_id
     * @param integer $mtn_price_list_date_validity_start
     * @param integer $mtn_price_list_date_validity_finish
     * @method POST
     */
    function update ()
    {
        $mtn_price_list_id = $this->input->post ( 'mtn_price_list_id' );
        $provider_id = $this->input->post ( 'provider_id' );
        $currency_id = $this->input->post ( 'currency_id' );
        $mtn_price_list_date_validity_start = $this->input->post ( 'mtn_price_list_date_validity_start' );
        $mtn_price_list_date_validity_finish = $this->input->post ( 'mtn_price_list_date_validity_finish' );
        $priceList = Doctrine_Core::getTable ( 'MtnPriceList' )->find ( $mtn_price_list_id );
        $priceList->provider_id = $provider_id;
        $priceList->currency_id = $currency_id;
        $priceList->mtn_price_list_date_validity_start = $mtn_price_list_date_validity_start;
        $priceList->mtn_price_list_date_validity_finish = $mtn_price_list_date_validity_finish;

        try
        {
            $priceList->save ();
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
     * delete
     * 
     * Elimina una lista de precio y todas sus componetes asociados a esa lista de precio
     * 
     * @post int mtn_price_list_id
     */
    function delete ()
    {
        $mtn_price_list_id = $this->input->post ( 'mtn_price_list_id' );
        
        $checkComponentIdInPriceList = Doctrine::getTable ( 'MtnPriceListComponent' )->checkComponentByIdPriceList ( $mtn_price_list_id );
        
        $respuesta = 0;
        foreach ($checkComponentIdInPriceList as $checkComponent) {
            $checkComponentIdInOT = Doctrine::getTable ( 'MtnWorkOrderTaskComponent' )->retrieveByIdLista ( $checkComponent->mtn_price_list_component_id );
            if ($checkComponentIdInOT){
                $respuesta = 1;
            }
        }
        if($respuesta == 1) {
             $success = 'false';
             $msg = $this->translateTag ( 'Maintenance' , 'to_remove_the_first_list_must_remove_the_il_o_that_contains_in_its_task' );
            
        } else {
            
            $MtnPriceList = Doctrine::getTable ( 'MtnPriceList' )->find ($mtn_price_list_id);
            if ( $MtnPriceList->delete () )
            {
                $success = 'true';
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $success = 'false';
                $msg = $e->getMessage ();
            }
            
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
}
