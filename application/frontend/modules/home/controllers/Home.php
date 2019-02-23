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
        echo $this->load->view('home', NULL, false);
    }

}
