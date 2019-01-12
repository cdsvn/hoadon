<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->site->write('title', "Nguyen Tat Huy");
        $this->load->model('contact/contact_model');
        $this->load->language('contact');
    }

    function _remap($method, $params = array()) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        $this->_view();
    }

    function _view() {
        $data = new stdClass();
        $content = $this->load->view('view', $data, true);
        $this->site->write('content', $content, true);
        $this->site->render();
    }

    function saveContact() {
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $content = '
            <div>Name: <strong>' . $name . '</strong></div>
            <div>Email: <strong>' . $email . '</strong></div>
            <div>Subject: <strong>' . $subject . '</strong></div>
            <div>Message:</div>
            <div style="padding-left: 25px;">' . $message . '</div>
            ';
        $this->load->library('email');
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "no-reply@greystonedatatech.com";
        $config['smtp_pass'] = "WebsitesmtpGDT@2018";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        $config['validate'] = TRUE;
        $this->email->initialize($config);
        $this->email->from('support@greystonedatatech.com', 'Whitecloud Foundation Inc.');
        $this->email->to('vytran@greystonevn.com');
        $this->email->cc('nguyentathuy1986@gmail.com');
        $this->email->subject($subject);
        $this->email->message($content);
        if ($this->email->send()) {
            return '1';
        }
        return $this->email->print_debugger();
    }

}
