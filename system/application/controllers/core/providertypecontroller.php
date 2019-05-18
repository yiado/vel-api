<?php

/**
 * @package Controller
 * @subpackage ProviderTypeController
 */
class ProviderTypeController extends APP_Controller
{

    function ProviderTypeController()
    {

        parent::APP_Controller();
    }

    /**
     * get
     * 
     * Lista todos los tipo proveedor 
     */
    function get()
    {
        $text_autocomplete = $this->input->post('query');
        $providerTypeTable = Doctrine_Core::getTable('ProviderType');
            $maintainer_type=1; //asset
        $providerType = $providerTypeTable->retrieveAll($text_autocomplete,$maintainer_type );

        //Output 
        $json_data = $this->json->encode(array('total' => $providerType->count(), 'results' => $providerType->toArray()));
        echo $json_data;
    }
    function getByNode()
    {
        $text_autocomplete = $this->input->post('query');
        $providerTypeTable = Doctrine_Core::getTable('ProviderType');
            $maintainer_type=2; //Node
        $providerType = $providerTypeTable->retrieveAll($text_autocomplete,$maintainer_type );

        //Output 
        $json_data = $this->json->encode(array('total' => $providerType->count(), 'results' => $providerType->toArray()));
        echo $json_data;
    }

    /**
     * add
     * 
     * Agrega un nuevo tipo proveedor
     * 
     * @post string provider_type_name
     * @post string provider_type_description
     */
    function add()
    {
        $provider_type_name = $this->input->post('provider_type_name');
        $provider_type_description = $this->input->post('provider_type_description');
        $providerType = new ProviderType();
        $providerType->provider_type_name = $provider_type_name;
        $providerType->provider_type_description = $provider_type_description;
        $providerType->mtn_maintainer_type_id = 1;
        try
        {
            $providerType->save();
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e)
        {
            $success = 'false';
            $msg = $e->getMessage();
        }

        //Output 
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
    function addByNode()
    {
        $provider_type_name = $this->input->post('provider_type_name');
        $provider_type_description = $this->input->post('provider_type_description');
        $providerType = new ProviderType();
        $providerType->provider_type_name = $provider_type_name;
        $providerType->provider_type_description = $provider_type_description;
 $providerType->mtn_maintainer_type_id = 2;
        try
        {
            $providerType->save();
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e)
        {
            $success = 'false';
            $msg = $e->getMessage();
        }

        //Output 
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica un tipo proveedor existente
     * 
     * @post int provider_type_id 
     * @post string provider_type_name
     * @post string provider_type_description
     */
    function update()
    {
        $provider_type_id = $this->input->post('provider_type_id');
        $provider_type_name = $this->input->post('provider_type_name');
        $provider_type_description = $this->input->post('provider_type_description');

        try
        {
            $providerType = Doctrine_Core::getTable('ProviderType')->retrieveById($provider_type_id);
            $providerType->provider_type_name = $provider_type_name;
            $providerType->provider_type_description = $provider_type_description;
            $providerType->save();
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e)
        {
            $success = 'false';
            $msg = $e->getMessage();
        }

        //Output 
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     * 
     * Elimina un tipo proveedor
     * 
     * @post int provider_type_id
     */
    function delete()
    {
        $provider_type_id = $this->input->post('provider_type_id');
        $checkProviderTypeInProvider = Doctrine::getTable('ProviderType')->checkProviderTypeInProvider($provider_type_id);

        if ($checkProviderTypeInProvider === false)
        {
            $providerType = Doctrine::getTable('ProviderType')->retrieveById($provider_type_id);

            if ($providerType->delete())
            {
                $success = 'true';
                $msg = $this->translateTag('General', 'operation_successful');
            } else
            {
                $success = 'false';
                $msg = 'Error';
            }
        } else
        {
            $success = 'false';
            $msg = $this->translateTag('General', 'the_provider_type_associated');
        }

        //Output 
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

}
