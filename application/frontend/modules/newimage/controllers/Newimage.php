<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Newimage extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->site->write('title', "Nguyen Tat Huy");
        $this->load->model('newimage/Newimage_model');
        $this->load->language('newimage');
    }

    function _remap($method, $params = array()) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        $this->_view();
    }

    function _view() {
        $data = new stdClass();  
        echo $this->load->view('view', $data, false);
    }

}
