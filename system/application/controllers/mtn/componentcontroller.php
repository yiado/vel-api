<?php

/** @package    Controller
 *  @subpackage Typewocontroller (work order)
 */
class Componentcontroller extends APP_Controller
{
    function Componentcontroller ()
    {
        parent::APP_Controller ();
    }

    /**
     * 
     * Lista todos los Componentes qu no esten repetidos en el precio de lista
     * 
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $mtn_price_list_id = $this->input->post ( 'mtn_price_list_id' );
        $componentTable = Doctrine_Core::getTable ( 'MtnComponent' );
        $maintainer_type=1; //asset
        $component = $componentTable->retrieveAll ( $mtn_price_list_id, $text_autocomplete ,$maintainer_type );

        if ( $component->count () )
        {
            echo '({"total":"' . $component->count () . '", "results":' . $this->json->encode ( $component->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    function getByNode ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $mtn_price_list_id = $this->input->post ( 'mtn_price_list_id' );
        $componentTable = Doctrine_Core::getTable ( 'MtnComponent' );
        $maintainer_type=2;//node
        $component = $componentTable->retrieveAll ( $mtn_price_list_id, $text_autocomplete,$maintainer_type );

        if ( $component->count () )
        {
            echo '({"total":"' . $component->count () . '", "results":' . $this->json->encode ( $component->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega un nuevo Componente
     * 
     * @param integer $mtn_component_id
     * @param integer $mtn_component_type_id
     * @param string $mtn_component_name
     * @param string $mtn_component_weight
     * @param string $brand_id
     * @param string $mtn_component_model
     * @param string $mtn_component_manufacturer
     * @param string $mtn_component_comment
     * @method POST
     */
    function add ()
    {
        $mtn_component_type_id = $this->input->post ( 'mtn_component_type_id' );
        $mtn_component_name = $this->input->post ( 'mtn_component_name' );
        $mtn_component_weight = $this->input->post ( 'mtn_component_weight' );
        $brand_id = $this->input->post ( 'brand_id' );
        $mtn_component_model = $this->input->post ( 'mtn_component_model' );
        $mtn_component_manufacturer = $this->input->post ( 'mtn_component_manufacturer' );
        $mtn_component_comment = $this->input->post ( 'mtn_component_comment' );
        $component = new MtnComponent();

        $component->mtn_component_type_id = $mtn_component_type_id;
        $component->mtn_component_name = $mtn_component_name;
        $component->mtn_component_weight = $mtn_component_weight;
        $component->brand_id = (!empty($brand_id) ? $brand_id: NULL );
        $component->mtn_component_model = $mtn_component_model;
        $component->mtn_component_manufacturer = $mtn_component_manufacturer;
        $component->mtn_component_comment = $mtn_component_comment;
        $component->mtn_maintainer_type_id = 1;

        try
        {
            $component->save ();
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
        $mtn_component_type_id = $this->input->post ( 'mtn_component_type_id' );
        $mtn_component_name = $this->input->post ( 'mtn_component_name' );
        $measure_unit_id = $this->input->post ( 'measure_unit_id' );
        $brand_id = $this->input->post ( 'brand_id' );
        $mtn_component_model = $this->input->post ( 'mtn_component_model' );
        $mtn_component_manufacturer = $this->input->post ( 'mtn_component_manufacturer' );
        $mtn_component_comment = $this->input->post ( 'mtn_component_comment' );
        $component = new MtnComponent();

        $component->mtn_component_type_id = $mtn_component_type_id;
        $component->mtn_component_name = $mtn_component_name;
        $component->measure_unit_id = (!empty($measure_unit_id) ? $measure_unit_id: NULL );
        $component->brand_id = (!empty($brand_id) ? $brand_id: NULL );
        $component->mtn_component_model = $mtn_component_model;
        $component->mtn_component_manufacturer = $mtn_component_manufacturer;
        $component->mtn_component_comment = $mtn_component_comment;
        $component->mtn_maintainer_type_id = 2;

        try
        {
            $component->save ();
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
     * Modifica un componente existente
     * 
     * @param integer $mtn_component_id
     * @param integer $mtn_component_type_id
     * @param string $mtn_component_name
     * @param string $mtn_component_weight
     * @param string $mtn_component_brand
     * @param string $mtn_component_model
     * @param string $mtn_component_manufacturer
     * @param string $mtn_component_comment
     * @method POST
     */
    function update ()
    {
        $mtn_component_id = $this->input->post ( 'mtn_component_id' );
        $mtn_component_type_id = $this->input->post ( 'mtn_component_type_id' );
        $mtn_component_name = $this->input->post ( 'mtn_component_name' );
        $mtn_component_weight = $this->input->post ( 'mtn_component_weight' );
        $brand_id = $this->input->post ( 'brand_id' );
        $mtn_component_model = $this->input->post ( 'mtn_component_model' );
        $mtn_component_manufacturer = $this->input->post ( 'mtn_component_manufacturer' );
        $mtn_component_comment = $this->input->post ( 'mtn_component_comment' );
        $component = Doctrine_Core::getTable ( 'MtnComponent' )->retrieveById ( $mtn_component_id );

        $component->mtn_component_type_id = $mtn_component_type_id;
        $component->mtn_component_name = $mtn_component_name;
        $component->mtn_component_weight = $mtn_component_weight;
        $component->brand_id = $brand_id;
        $component->mtn_component_model = $mtn_component_model;
        $component->mtn_component_manufacturer = $mtn_component_manufacturer;
        $component->mtn_component_comment = $mtn_component_comment;

        try
        {

            $component->save ();
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
     * Elimina un componente
     * 
     * @param integer $provider_id
     */
    function delete ()
    {
        $mtn_component_id = $this->input->post ( 'mtn_component_id' );
        $checkComponentIdInPriceList = Doctrine::getTable ( 'MtnComponent' )->checkComponentIdInPriceList ( $mtn_component_id );

        if ( $checkComponentIdInPriceList === false )
        {
            $component = Doctrine::getTable ( 'MtnComponent' )->retrieveById ( $mtn_component_id );

            if ( $component->delete () )
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
            $msg = $this->translateTag ( 'Maintenance' , 'you_can_not_delete_because_it_is_associated' );
        }

        //Output 
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
}