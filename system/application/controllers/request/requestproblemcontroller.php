<?php

/**
 * @package    Controller
 * @subpackage RequestProblemController
 */
class RequestProblemController extends APP_Controller
{
    function RequestProblemController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista todos los tipos de equipos existentes
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $requestProblem = Doctrine_Core::getTable ( 'RequestProblem' )->retrieveAll ($text_autocomplete);
        if ( $requestProblem->count () )
        {
            echo '({"total":"' . $requestProblem->count () . '", "results":' . $this->json->encode ( $requestProblem->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add ()
    {
        $request = new RequestProblem();
        $request->fromArray ( $this->input->postall () );
        $request->save ();
        echo '{"success": true}';
    }

    function update ()
    {
        $request = Doctrine_Core::getTable ( 'RequestProblem' )->find ( $this->input->post ( 'request_problem_id' ) );
        $request->fromArray ( $this->input->postall () );
        $request->save ();
        echo '{"success": true}';
    }

    function delete ()
    {
        try
        {
            $requestProblem = Doctrine::getTable ( 'RequestProblem' )->find ( $this->input->post ( 'request_problem_id' ) );
            $requestProblem->delete ();
            $success = true;
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