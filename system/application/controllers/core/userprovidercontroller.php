<?php

class UserProvidercontroller extends APP_Controller
{
    function UserProvidercontroller ()
    {
        parent::APP_Controller ();
    }

    function get ()
    {
        $provider_id = $this->input->post ( 'provider_id' );
        $user_id = $this->input->post ( 'user_id' );
        $mtn_maintainer_type_id = $this->input->post ( 'mtn_maintainer_type_id' );
        $userProviderTable = Doctrine_Core::getTable ( 'UserProvider' );
        $userProvider = $userProviderTable->retrieveAll ( $provider_id , $user_id, $mtn_maintainer_type_id );

        if ( $userProvider->count () )
        {
            echo '({"total":"' . $userProvider->count () . '", "results":' . $this->json->encode ( $userProvider->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getProvider ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $userproviderTable = Doctrine_Core::getTable ( 'UserProvider' );
        $userprovider = $userproviderTable->retrieveByFilter ($text_autocomplete);

        if ( $userprovider->count () )
        {
            echo '({"total":"' . $userprovider->count () . '", "results":' . $this->json->encode ( $userprovider->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add ()
    {
        $user_id = $this->input->post ( 'user_id' );
        $provider_id = $this->input->post ( 'provider_id' );
        
        try
        {
            $UserProvider = new UserProvider();
            $UserProvider->user_id = $user_id;
            $UserProvider->provider_id = $provider_id;
            $UserProvider->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    function delete ()
    {
        $UserProvider = Doctrine::getTable ( 'UserProvider' )->find ( $this->input->post ( 'user_provider_id' ) );

        try
        {
            $UserProvider->delete ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
}

?>
