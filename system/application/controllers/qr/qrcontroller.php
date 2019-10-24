<?php

require 'vendor/autoload.php';

use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
//use Endroid\QrCode\Response\QrCodeResponse;
//use Endroid\QrCode\ErrorCorrectionLevel;

/**
 * @package    Controller
 * @subpackage ServiceController
 */
class QrController extends Controller {

    function QrController() {
        parent::Controller();
    }

    function get() {
        $qrcode_file_name = "qr_node_{$this->input->post('node_id')}.png";

        if (!file_exists($this->config->item('qr_dir') . "{$qrcode_file_name}")) {
            $url = "{$this->config->item('base_url')}{$this->config->item('index_page')}/qr/redirect/{$this->input->post('node_id')}";
            $qrCode = new QrCode($url);
            $qrCode->setSize(500);
            $qrCode->setMargin(20);
            //$qrCode->setLabel($url, 10, null, LabelAlignment::CENTER);
            $qrCode->writeFile($this->config->item('qr_dir') . "{$qrcode_file_name}");
        }

        echo json_encode($this->config->item('qr_dir') . "{$qrcode_file_name}");
    }

    function redirect($nodo_id) {
        echo "Redireccion y nodo {$nodo_id}";
    }

}
