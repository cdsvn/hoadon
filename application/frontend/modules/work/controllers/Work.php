<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Work extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->site->write('title', "Nguyen Tat Huy");
        $this->load->model('work/Work_model');
        $this->load->language('work');
    }

    function _remap($method, $params = array()) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        $this->_view();
    }

    function _view() {
        $data = new stdClass();
        $product = $this->uri->segment(2);
        $content = $this->load->view(empty($product) ? 'view' : $product, $data, true);
        $this->site->write('content', $content, true);
        $this->site->render();
    }

}
