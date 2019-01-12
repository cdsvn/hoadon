<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inc extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('inc/Inc_model');
        $this->load->language('inc');
    }

    function load_menu() {
        $data = new stdClass();
        $data->controller = $this->router->fetch_class();
        $data->furl = $this->uri->segment(1);
        if ($data->furl == '' || $data->furl == 'index') {
            $data->furl = 'home';
        }
        return $this->load->view('menu', $data, true);
    }

    function load_footer_sidebar() {
        $data = new stdClass();
        return $this->load->view('footer_sidebar', $data, true);
    }

    function load_footer_content() {
        $data = new stdClass();
        return $this->load->view('footer_content', $data, true);
    }

}
