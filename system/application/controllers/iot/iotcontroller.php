<?php

class IotController extends APP_Controller {

    function IotController() {
        parent::APP_Controller();
    }

    function getDevice() {

        $curl = curl_init('http://18.213.235.57:3001/api/v2/sensors');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
//            curl_close($curl);
            echo '({"total":"0", "results":[]})';
        }
        curl_close($curl);
        $decoded = json_decode($curl_response);
        if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
            echo '({"total":"0", "results":[]})';
        }
//        print_r($this->json->encode($decoded->data));
//        exit();
        if ($decoded->succes->status == 200) {
            echo '({"total":"' . count($decoded->data) . '", "results":' . $this->json->encode($decoded->data) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
//        var_export($decoded->response);
    }

    function postIot() {

        $multiselect = '1,2';
        $sensores_ = explode(",", $multiselect);

        $sensores = array();

        foreach ($sensores_ as $key => $value) {
            $sensores[]["id"] = intval($value);
        }


//        $sensores = json_encode($sensores);
//        print_r($sensores);
//        exit();

        $name = 'sdds';
        $model = 'sdsd';
        $description = 'sdaaa';
        $node = intval('2334');

        $service_url = 'http://18.213.235.57:3001/api/v2/nodes';
        $curl = curl_init($service_url);
        $curl_post_data = json_encode(array(
            "modelName" => $name,
            "manufacterName" => $model,
            "description" => $description,
            "group_id" => $node,
            "sensors" => $sensores
        ));

//        print_r($curl_post_data); exit();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($curl_post_data))
        );



        $curl_response = curl_exec($curl);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
//                        curl_close($curl);
            die('error occured during curl exec. Additioanl info: ' . var_export($info));
        }
        curl_close($curl);
//        print_r($curl_response);
//        exit();
        $decoded = json_decode($curl_response);
        if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
            die('error occured: ' . $decoded->response->errormessage);
        }

        print_r($decoded);
        exit();
    }

    function getDeviceInfo() {

        $node_id = $this->input->post('node_id');
        $curl = curl_init('http://18.213.235.57:3001/api/v2/nodes/' . $node_id);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);

            echo '({"total":"0", "results":[]})';
            exit();
        }
        curl_close($curl);
        $decoded = json_decode($curl_response);

        if (isset($decoded->succes->status) && ($decoded->succes->status == 200 )) {
            echo '({"total":"' . count($decoded->data) . '", "results":' . $this->json->encode($decoded->data) . '})';
        } else if (isset($decoded->error->status) && $decoded->error->status == 404) {

            echo '({"total":"0", "results":[]})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getSensorsInfo() {

        $sensors_id = $this->input->post('element_id');
        $device_id = $this->input->post('device_id');
        $curl = curl_init('http://18.213.235.57:3001/api/v2/measures?node_id='.$device_id.'&sensor_id='.$sensors_id.'&limit=10');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);

            echo '({"total":"0", "results":[]})';
        }
        curl_close($curl);
        $decoded = json_decode($curl_response);

        if (isset($decoded->succes->status) && ($decoded->succes->status == 200 )) {
            echo '({"total":"' . count($decoded->data) . '", "results":' . $this->json->encode($decoded->data) . '})';
        } else if (isset($decoded->error->status) && $decoded->error->status == 404) {

            echo '({"total":"0", "results":[]})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

}
