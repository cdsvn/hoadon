<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Authorize extends MX_Controller {

    private $_LOGIN_FAIL_SHOW_CAPCHA = 5;
    private $_mode = 0;

    function __construct() {
        parent::__construct();
        $this->load->model('authorize/Authorize_model');
        $this->load->language('authorize');
        $this->load->helper('captcha');
        $this->load->helper('cookie');
    }

    function index() {
        session_destroy();
        if ($this->session->tempdata('login') == NULL) {
            redirect(admin_url($this->admin->lang . "/authorize/ui"));
        }
        redirect(admin_url($this->admin->lang . "/home"));
    }

    /*
     * Chuan bi du lieu can thiet
     * truoc khi vao dashboard
     */

    function prepareData() {
        if ($this->session->has_userdata('groups')) {
            $groups = $this->session->tempdata('groups');
            $permission = $this->Admin_model->get_permission($groups);
            // get tree menu not permission
            $tree = $this->Admin_model->tree_menu_pure();
            // get tree menu have permission and array route
            $parent = array();
            $path = '';
            $route_path = array();
            $this->Admin_model->tree_menu_route_permission($tree, $permission, $parent, $path, $route_path);
            $this->session->set_tempdata('route_menu', $route_path, 86400);
            $this->session->set_tempdata('tree_menu', $tree, 86400);
        }
    }

    function my_create_capcha() {
        $vals = array(
            'word' => '',
            'img_path' => './files/backend/default/capcha/',
            'img_url' => base_url() . '/files/backend/default/capcha/',
            'font_path' => base_url() . '/files/backend/default/fonts/texb.ttf',
            'img_width' => '145',
            'img_height' => 41,
            'expiration' => 7200,
            'word_length' => 4,
            'font_size' => 16,
            'img_id' => 'img-capcha-id',
            'pool' => '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ',
            'colors' => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(255, 40, 40)
            )
        );
        $cap = create_captcha($vals);
        $this->session->set_tempdata('capcha', $cap, 300);
        return $cap['image'];
    }

    function refreshcapcha() {
        echo $this->my_create_capcha();
    }

    function ui() {
        if ($this->session->tempdata('login') != NULL) {
            redirect(admin_url($this->admin->lang . "/home"));
        }
        $count_wrong_login = $this->session->tempdata('count_wrong_login');
        $data = new stdClass();
        $data->show_capcha = (($count_wrong_login != NULL && $count_wrong_login > $this->_LOGIN_FAIL_SHOW_CAPCHA) ? true : false);
        $data->csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $data->capcha = $this->my_create_capcha();
        $data->remember = get_cookie("remember");
        $data->username = get_cookie("username");
        $data->password = get_cookie("password");
        $this->load->view('login', $data);
    }

    function change_pass() {
        $data = new stdClass();
        $this->load->view('changepass', $data);
    }

    function changefirstpass() {
        $newpass = $this->input->post('pass');
        $id = $this->session->tempdata('iduser');
        $this->Authorize_model->changefirstpass($id, $newpass);
        $this->session->set_tempdata('changepass', 0, 86400);
        echo "success";
    }

    function saveContact() {
        $fdata = $this->input->post('fdata');
        $this->load->library('email');
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "greystonedatasystem@gmail.com";
        $config['smtp_pass'] = "tuleqcxipabrjcam";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        $config['validate'] = TRUE;
        $this->email->initialize($config);
        $this->email->from('songbuon_sb@yahoo.com', 'HUY');
        $this->email->to('tim_nguoi_xa_la@yahoo.com');
        $this->email->cc('huyngt@greystonevn.com');
        $this->email->reply_to('songbuon_sb@yahoo.com', 'HUY');
        $this->email->subject('localhost test');
        $this->email->message('test send email from local host');
        //$this->email->send();
        if ($this->email->send()) {
            echo 'Email sent.';
        } else {
            show_error($this->email->print_debugger());
        }
        $result = $this->model->saveContact($fdata);
        echo $result;
    }

    function emailresetpassword() {
        $email = $this->input->post('email');
        $total = $this->Authorize_model->checkemailexist($email);
        if ($total == 0) {
            echo 2;
            return false;
        }
        $newpass = $this->Share_model->generateRandomString(7);
        $this->load->library('email');
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "resetemailiky@gmail.com";
        $config['smtp_pass'] = "gnknljzjobnvyphb";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        $config['validate'] = TRUE;
        date_default_timezone_set('UTC');
        $this->email->initialize($config);
        $this->email->from('resetemailiky@gmail.com', 'Email reset password');
        $this->email->to($email);
        $this->email->subject('Lấy mật khẩu mới');
        $this->email->message('Mật khẩu mới của bạn là : ' . $newpass);
        if ($this->email->send()) {
            $this->Authorize_model->resetpassword($email, MD5("huynt" . $newpass));
            echo '1';
        } else {
            echo '0';
            //show_error($this->email->print_debugger());
        }
    }

    function lock_login() {
        if ($this->session->tempdata('loggedIn') == "true") {
            redirect(admin_url($this->admin->lang . "/home"));
        }
        if ($this->session->tempdata('loginFail') == NULL || $this->session->tempdata('loginFail') < 3) {
            redirect(admin_url($this->admin->lang . "/authorize/login"));
        }
        $this->load->view('lock', NULL);
    }

    function login() {
        /*
         * 0: Login fail (wrong password or account)
         * 1: Login pass
         * 2: Account several similar names
         * 3: Login fail 5 times, lock account
         * 4: Account not active
         * 5: Change password requests (first login)
         * 6: Wrong capcha
         */
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $capcha = $this->input->post('capcha');
        $remember = $this->input->post('remember');
        // Count fail login
        $count_wrong_login = $this->session->tempdata('count_wrong_login');
        // Check capcha
        $obj_capcha = $this->session->tempdata('capcha');
        // After login 3 times, check capcha
        if ($count_wrong_login != NULL && $count_wrong_login > $this->_LOGIN_FAIL_SHOW_CAPCHA) {
            if (strtolower($capcha) != strtolower($obj_capcha['word'])) {
                die("6");
            }
        }
        // Check login
        $rs = $this->Authorize_model->login($username, $password);
        if (count($rs) == 0) {
            if ($count_wrong_login != NULL) {
                $count_wrong_login +=1;
                $this->session->set_tempdata('count_wrong_login', $count_wrong_login, 60);
            } else {
                $this->session->set_tempdata('count_wrong_login', 1, 60);
            }
            if ($count_wrong_login > 105) {
                die("3");
            }
            die("0-" . $count_wrong_login);
        }
        if (count($rs) > 1) {
            die("2");
        }
        $user = $rs[0];
        if ($user->active == 0) {
            die("4");
        }
        if ($user->changepass == 1) {
            die("5");
        }
        // Login success
        $this->session->set_tempdata('login', $user, 86400);
        $this->session->set_tempdata('groups', $user->groups, 86400);
        $this->session->set_tempdata('privilege', $this->Authorize_model->get_privilege($user->groups), 86400);
        $this->session->set_tempdata('menu', $this->Authorize_model->list_menu(), 86400);
        $this->session->set_tempdata('menuactive', json_encode($this->Authorize_model->active_menu()), 86400);
        if ($remember == 1) {
            set_cookie("remember", $remember, 31536000);
            set_cookie("username", $username, 31536000);
            set_cookie("password", $password, 31536000);
        } else {
            delete_cookie("remember");
            delete_cookie("username");
            delete_cookie("password");
        }
        $this->prepareData();
        die("1");
    }

    function logout() {
        unset($_SESSION['loggedIn']);
        $this->session->sess_destroy();
        redirect(admin_url($this->admin->lang . "/authorize"));
    }

}
