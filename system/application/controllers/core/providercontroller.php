<?php

/**
 * @package Controller
 * @subpackage ProviderController
 */
class ProviderController extends APP_Controller
{
    function ProviderController ()
    {
        parent::APP_Controller ();
    }

    /**
     * 
     * Lista todos los proveedores
     * @param string $query (text para la busqueda con autocompletar)
     * 
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $providerTable = Doctrine_Core::getTable ( 'Provider' );
        $maintainer_type=1; //asset
        $provider = $providerTable->retrieveAll ( $text_autocomplete ,$maintainer_type );

        //Output 
        $json_data = $this->json->encode ( array ( 'total' => $provider->count () , 'results' => $provider->toArray () ) );
        echo $json_data;
    }
    
    function getByType ()
    {
        $provider_type_id = $this->input->post ( 'provider_type_id' );
        $providerTable = Doctrine_Core::getTable ( 'Provider' )->retrieveByProvider( $provider_type_id );

        //Output 
        $json_data = $this->json->encode ( array ( 'total' => $providerTable->count () , 'results' => $providerTable->toArray () ) );
        echo $json_data;
    }
    
    function getAllType ()
    {
        $mtn_maintainer_type_id = $this->input->post ( 'mtn_maintainer_type_id' );
        $providerTypeTable = Doctrine_Core::getTable ( 'Provider' )->retrieveAllType ( $mtn_maintainer_type_id );

        //Output 
        $json_data = $this->json->encode ( array ( 'total' => $providerTypeTable->count () , 'results' => $providerTypeTable->toArray () ) );
        echo $json_data;
    }
    
    function getTypeMaintainer ()
    {
        $MtnMaintainerType = Doctrine_Core::getTable ( 'MtnMaintainerType' )->findAllTotal();
        
        //Output 
        if ( $MtnMaintainerType->count () )
        {
            echo '({"total":"' . $MtnMaintainerType->count () . '", "results":' . $this->json->encode ( $MtnMaintainerType->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    
    
    function getByNode ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $providerTable = Doctrine_Core::getTable ( 'Provider' );
        $maintainer_type=2;//node
        $provider = $providerTable->retrieveAll ( $text_autocomplete ,$maintainer_type );

        //Output 
        $json_data = $this->json->encode ( array ( 'total' => $provider->count () , 'results' => $provider->toArray () ) );
        echo $json_data;
    }
    function getAsset ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $providerTable = Doctrine_Core::getTable ( 'Provider' );
        $provider = $providerTable->retrieveAllAsset ( $text_autocomplete );

        //Output 
        $json_data = $this->json->encode ( array ( 'total' => $provider->count () , 'results' => $provider->toArray () ) );
        echo $json_data;
    }
    
    function getNode ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $providerTable = Doctrine_Core::getTable ( 'Provider' );
        $provider = $providerTable->retrieveAllNode ( $text_autocomplete );

        //Output 
        $json_data = $this->json->encode ( array ( 'total' => $provider->count () , 'results' => $provider->toArray () ) );
        echo $json_data;
    }

    /**
     * add
     * 
     * Agrega un nuevo proveedor
     * 
     * @param integer $provider_type_id
     * @param string $provider_name
     * @param string $provider_contact
     * @param string $provider_phone
     * @param string $provider_fax
     * @param string $provider_email
     * @param string $provider_description
     * @method POST
     */
    function add ()
    {
        $provider_type_id = $this->input->post ( 'provider_type_id' );
        $provider_name = $this->input->post ( 'provider_name' );
        $provider_contact = $this->input->post ( 'provider_contact' );
        $provider_phone = $this->input->post ( 'provider_phone' );
        $provider_fax = $this->input->post ( 'provider_fax' );
        $provider_email = $this->input->post ( 'provider_email' );
        $provider_description = $this->input->post ( 'provider_description' );
        $provider = new Provider();

        $provider->provider_type_id = $provider_type_id;
        $provider->provider_name = $provider_name;
        $provider->provider_contact = $provider_contact;
        $provider->provider_phone = $provider_phone;
        $provider->provider_fax = $provider_fax;
        $provider->provider_email = $provider_email;
        $provider->provider_description = $provider_description;
   $provider->mtn_maintainer_type_id = 1;
        try
        {
            $provider->save ();
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
     * addNode
     * 
     * Agrega un nuevo proveedor
     * 
     * @param integer $provider_type_id
     * @param string $provider_name
     * @param string $provider_contact
     * @param string $provider_phone
     * @param string $provider_fax
     * @param string $provider_email
     * @param string $provider_description
     * @method POST
     */
    function addNode ()
    {
        $provider_type_id = $this->input->post ( 'provider_type_id' );
        $provider_name = $this->input->post ( 'provider_name' );
        $provider_contact = $this->input->post ( 'provider_contact' );
        $provider_phone = $this->input->post ( 'provider_phone' );
        $provider_fax = $this->input->post ( 'provider_fax' );
        $provider_email = $this->input->post ( 'provider_email' );
        $provider_description = $this->input->post ( 'provider_description' );
        $provider = new Provider();

        $provider->provider_type_id = $provider_type_id;
        $provider->provider_name = $provider_name;
        $provider->provider_contact = $provider_contact;
        $provider->provider_phone = $provider_phone;
        $provider->provider_fax = $provider_fax;
        $provider->provider_email = $provider_email;
        $provider->provider_description = $provider_description;
        $provider->mtn_maintainer_type_id = 2;

        try
        {
            $provider->save ();
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
        $provider_type_id = $this->input->post ( 'provider_type_id' );
        $provider_name = $this->input->post ( 'provider_name' );
        $provider_contact = $this->input->post ( 'provider_contact' );
        $provider_phone = $this->input->post ( 'provider_phone' );
        $provider_fax = $this->input->post ( 'provider_fax' );
        $provider_email = $this->input->post ( 'provider_email' );
        $provider_description = $this->input->post ( 'provider_description' );
        $provider = new Provider();

        $provider->provider_type_id = $provider_type_id;
        $provider->provider_name = $provider_name;
        $provider->provider_contact = $provider_contact;
        $provider->provider_phone = $provider_phone;
        $provider->provider_fax = $provider_fax;
        $provider->provider_email = $provider_email;
        $provider->provider_description = $provider_description;
        $provider->mtn_maintainer_type_id = 2;

        try
        {
            $provider->save ();
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
     * Modifica un proveedor existente
     * 
     * @param integer $provider_id 
     * @param integer $provider_type_id
     * @param string $provider_name
     * @param string $provider_contact
     * @param string $provider_phone
     * @param string $provider_fax
     * @param string $provider_email
     * @param string $provider_description
     * @method POST
     */
    function update ()
    {
        $provider_id = $this->input->post ( 'provider_id' );
        $provider_type_id = $this->input->post ( 'provider_type_id' );
        $provider_name = $this->input->post ( 'provider_name' );
        $provider_contact = $this->input->post ( 'provider_contact' );
        $provider_phone = $this->input->post ( 'provider_phone' );
        $provider_fax = $this->input->post ( 'provider_fax' );
        $provider_email = $this->input->post ( 'provider_email' );
        $provider_description = $this->input->post ( 'provider_description' );
        $provider = Doctrine_Core::getTable ( 'Provider' )->retrieveById ( $provider_id );

        $provider->provider_type_id = $provider_type_id;
        $provider->provider_name = $provider_name;
        $provider->provider_contact = $provider_contact;
        $provider->provider_phone = $provider_phone;
        $provider->provider_fax = $provider_fax;
        $provider->provider_email = $provider_email;
        $provider->provider_description = $provider_description;

        try
        {
            $provider->save ();
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
     * Elimina un proveedor
     * 
     * @param integer $provider_id
     */
    function delete ()
    {
        $provider_id = $this->input->post ( 'provider_id' );
        $checkProviderIdInOtherData = Doctrine::getTable ( 'Provider' )->checkProviderIdInOtherData ( $provider_id );

        if ( $checkProviderIdInOtherData === false )
        {
            $provider = Doctrine::getTable ( 'Provider' )->retrieveById ( $provider_id );

            if ( $provider->delete () )
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
            $msg = $this->translateTag ( 'General' , 'the_provider_type_associated' );
        }

        //Output 
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

}

