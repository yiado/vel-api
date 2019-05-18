<?php

/** @package    Controller
 *  @subpackage Pricelistcomponentcontroller
 */
class Pricelistcomponentcontroller extends APP_Controller
{
    function Pricelistcomponentcontroller ()
    {
        parent::APP_Controller ();
    }

    /**
     * Devuelve la lista de precios de los insumos para un provedor especifico
     * @param integer $provider_id
     * @param integer $current_list
     */
    function get ()
    {
        $current_list = $this->input->post ( 'current_price_list' );
        $mtn_work_order_id = $this->input->post ( 'mtn_work_order_id' );
        $text_autocomplete = $this->input->post ( 'query' );
        $MtnWorkOrderTable = Doctrine_Core::getTable ( 'MtnWorkOrder' );
        $provider = $MtnWorkOrderTable->find ( $mtn_work_order_id );

        if ( $current_list == 'true' )
        {
            $mtnPriceListComponentTable = Doctrine_Core::getTable ( 'MtnPriceListComponent' );
            $priceList = $mtnPriceListComponentTable->retrieveCurrentPriceList ( $provider->provider_id , $text_autocomplete );
        }
        else
        {
            $mtnPriceListTable = Doctrine_Core::getTable ( 'MtnPriceList' );
            $priceList = $mtnPriceListTable->retrieveAll ( $provider->provider_id );
        }

        $json_data = $this->json->encode ( array ( 'total' => $priceList->count () , 'results' => $priceList->toArray () ) );
        echo $json_data;
    }
    
    function getByNode ()
    {
        $current_list = $this->input->post ( 'current_price_list' );
        $mtn_work_order_id = $this->input->post ( 'mtn_work_order_id' );
        $mtn_component_type_id = $this->input->post ( 'mtn_component_type_id' );
        $text_autocomplete = $this->input->post ( 'query' );
        $MtnWorkOrderTable = Doctrine_Core::getTable ( 'MtnWorkOrder' );
        $provider = $MtnWorkOrderTable->find ( $mtn_work_order_id );

        if ( $current_list == 'true' )
        {
            $mtnPriceListComponentTable = Doctrine_Core::getTable ( 'MtnPriceListComponent' );
            $priceList = $mtnPriceListComponentTable->retrieveCurrentPriceListByNode ( $mtn_component_type_id, $provider->provider_id , $text_autocomplete );
        }
        else
        {
//            $mtnPriceListTable = Doctrine_Core::getTable ( 'MtnPriceList' );
//            $priceList = $mtnPriceListTable->retrieveAllNode ( $provider->provider_id );
        }

        $json_data = $this->json->encode ( array ( 'total' => $priceList->count () , 'results' => $priceList->toArray () ) );
        echo $json_data;
    }
    

    /**
     * 
     * Lista todos los Precios de los Componentes
     * 
     */
    function getAll ()
    {
        $mtnPriceListComponentTable = Doctrine_Core::getTable ( 'MtnPriceListComponent' );
        $priceList = $mtnPriceListComponentTable->retrieveAll ();

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
     * 
     * Lista todos los Precios de los Componentes
     * 
     */
    function getByIdList ()
    {
        $mtn_price_list_id = $this->input->post ( 'mtn_price_list_id' );
        $mtnPriceListComponentTable = Doctrine_Core::getTable ( 'MtnPriceListComponent' );
        $priceList = $mtnPriceListComponentTable->checkComponentByIdPriceList ( $mtn_price_list_id );

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
     * Agrega un nuevo Precio Al compomponente
     * 
     * @param integer $mtn_price_list_component_id
     * @param integer $mtn_price_list_id
     * @param integer $mtn_component_id
     * @param integer $mtn_price_list_component_price
     * @method POST
     */
    function add ()
    {
        $mtn_price_list_id = $this->input->post ( 'mtn_price_list_id' );
        $mtn_component_id = $this->input->post ( 'mtn_component_id' );
        $mtn_price_list_component_price = $this->input->post ( 'mtn_price_list_component_price' );
        $priceList = new MtnPriceListComponent();
        $priceList->mtn_price_list_id = $mtn_price_list_id;
        $priceList->mtn_component_id = $mtn_component_id;
        $priceList->mtn_price_list_component_price = $mtn_price_list_component_price;

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
     * Modifica un Precio de un componente
     * 
     * @param integer $mtn_price_list_component_id
     * @param integer $mtn_price_list_id
     * @param integer $mtn_component_id
     * @param integer $mtn_price_list_component_price
     * @method POST
     */
    function update ()
    {
        $mtn_price_list_component_id = $this->input->post ( 'mtn_price_list_component_id' );
        $mtn_price_list_id = $this->input->post ( 'mtn_price_list_id' );
        $mtn_component_id = $this->input->post ( 'mtn_component_id' );
        $mtn_price_list_component_price = $this->input->post ( 'mtn_price_list_component_price' );
        $priceList = Doctrine_Core::getTable ( 'MtnPriceListComponent' )->find ( $mtn_price_list_component_id );
        $priceList->mtn_price_list_id = $mtn_price_list_id;
        $priceList->mtn_component_id = $mtn_component_id;
        $priceList->mtn_price_list_component_price = $mtn_price_list_component_price;

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
     * Elimina un componente de la lista
     *
     * @param integer $mtn_task_id
     */
    function delete ()
    {
        $mtn_price_list_id = $this->input->post ( 'mtn_price_list_id' );
        $mtn_price_list_component_id = $this->input->post ( 'mtn_price_list_component_id' );
        
       $checkComponentIdInOT = Doctrine::getTable ( 'MtnWorkOrderTaskComponent' )->retrieveByIdLista ( $mtn_price_list_component_id );
       
       if ($checkComponentIdInOT){
           $success = 'false';
           $msg = $this->translateTag ( 'Maintenance' , 'if_you_want_to_delete_the_component_must_first_delete' );
           
       } else {
           $checkComponentIdInPriceList = Doctrine::getTable ( 'MtnPriceListComponent' )->find ( $mtn_price_list_component_id );

            if ( $checkComponentIdInPriceList->delete () )
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
