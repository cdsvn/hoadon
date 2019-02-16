<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->site->write('title', "Nguyen Tat Huy");
        //$this->load->model('home/Home_model');
        $this->load->language('home');
    }

    function _remap($method, $params = array()) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        $this->_view();
    }

    function _view() {
        $data = new stdClass();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8443",
            CURLOPT_URL => "https://demo-sinvoice.viettel.vn:8443/InvoiceAPI/InvoiceUtilsWS/getInvoices/0100109106-997",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\r\n  \"startDate\" : \"2017-12-12T10:14:32.611+07:00\",\r\n  \"endDate\" : \"2017-12-31T10:14:32.611+07:00\",\r\n  \"invoiceType\" : \"02GTTT\",\r\n  \"rowPerPage\" : 20,\r\n  \"pageNum\" : 1,\r\n  \"templateCode\" : null\r\n}",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic MDEwMDEwOTEwNi05OTc6MTExMTExYUBB",
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 3510cabb-462f-76ae-2e03-9ed76823c3c0"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $data->rs = json_decode($response, true);
        }
        print_r($data);
        die;
        $content = $this->load->view('home', $data, true);
        $this->site->write('content', $content, true);
        $this->site->render();
    }

}
