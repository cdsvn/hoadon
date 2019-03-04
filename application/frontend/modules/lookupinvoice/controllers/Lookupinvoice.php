<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lookupinvoice extends MX_Controller {

    private $rowInPage = 5;
    private $supplierTaxCode = '';

    private $purl = "https://api-sinvoice.viettel.vn:443";
    private $pport = "443";

    function __construct() {
        parent::__construct();
        $this->site->write('title', "Nguyen Tat Huy");
        $this->load->model('lookupinvoice/Lookupinvoice_model');
        $this->load->language('lookupinvoice');
        $this->load->library('pagination');
        $this->supplierTaxCode = $this->session->userdata('supplierTaxCode');
        $this->buyerIdNo = $this->session->userdata('buyerIdNo');
        $this->rowInPage = $this->session->userdata('rowInPage');
        $this->config->load('account');
        $this->purl = $this->config->item('purl');
        $this->pport = $this->config->item('pport');
    }

    function _remap($method, $params = array()) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        $this->_view();
    }

    function checkAccount($supplierTaxCode) {
        $arr = $this->config->item('infoByerIdNo');
        $base64 = '';
        foreach ($arr as $item) {
            if ($supplierTaxCode == $item[0]) {
                $base64 = base64_encode($item[1] . ':' . $item[2]);
                break;
            }
        }
        return $base64;
    }

    function _view() {
        $data = new stdClass();
        $supplierTaxCode = $this->uri->segment(2); // ma so thue
        $buyerIdNo = $this->uri->segment(3); // ma so thue
        $secureSupplierTaxCode = $this->uri->segment(4); // ma bi mat
        // Nếu $buyerIdNo không có
        if (empty($secureSupplierTaxCode)) {
            $secureSupplierTaxCode = $buyerIdNo;
            unset($buyerIdNo);
        }

        // Kiểm tra xem đủ điều kiện hay không
        if (empty($supplierTaxCode) || empty($secureSupplierTaxCode)) {
            $this->session->unset_userdata('supplierTaxCode');
            $this->session->unset_userdata('buyerIdNo');
            die("INVALID PARAMS 1");
        } else {
            $this->session->unset_userdata('supplierTaxCode');
            $this->session->unset_userdata('buyerIdNo');
            // Check supplierTaxCode va secureSupplierTaxCode xem hop le khong
            // http://localhost/hoadon/lookupinvoice/0100109106-997/1bf747e840356a789ce270b04bad1d83.html
            if (isset($buyerIdNo)) {
                //$stmp = md5("1234qwer" . $supplierTaxCode . "0987@@@" . $buyerIdNo);
            } else {
                //$stmp = md5("1234qwer" . $supplierTaxCode . "0987@@@");
            }
            $stmp = md5("1234qwer" . $supplierTaxCode . "0987@@@");
            if ($stmp != $secureSupplierTaxCode) {
                die("INVALID PARAMS 2: " . $stmp);
            }
        }
        // Chưa set rowInPage thi set
        if (!$this->session->has_userdata('rowInPage')) {
            $this->session->set_userdata('rowInPage', 5);
        }
        $this->session->set_userdata('supplierTaxCode', $supplierTaxCode);
        // Nếu tồn tại Buyer ID No thì set vào session
        if (isset($buyerIdNo)) {
            $this->session->set_userdata('buyerIdNo', $buyerIdNo);
        }
        $data->rowInPage = $this->rowInPage;
        $data->buyerIdNo = isset($buyerIdNo) ? $buyerIdNo : '';
        $content = $this->load->view('view', $data, true);
        $this->site->write('content', $content, true);
        $this->site->render();
    }

    private function getListInvoice($rowPerPage = 5, $pageNum = '1', $searchs = array()) {
        $arr_post = array(
            'startDate' => '2017-12-12',
            'endDate' => '2017-12-31',
            'rowPerPage' => $rowPerPage,
            'pageNum' => $pageNum
        );

        if (!empty($searchs['invoiceno'])) {
            $arr_post['invoiceNo'] = $searchs['invoiceno'];
        }
        if (!empty($searchs['invoicetype'])) {
            $arr_post['invoiceType'] = $searchs['invoicetype'];
        }
        if (!empty($searchs['startdate'])) {

            $arr_post['startDate'] = date('Y-m-d', strtotime($searchs['startdate']));
        }
        if (!empty($searchs['enddate'])) {
            $arr_post['endDate'] = date('Y-m-d', strtotime($searchs['enddate']));
        }
        if (!empty($searchs['buyertaxcode'])) {
            $arr_post['buyerTaxCode'] = $searchs['buyertaxcode'];
        }
        if (!empty($searchs['templatecode'])) {
            $arr_post['templateCode'] = $searchs['templatecode'];
        }
        if (!empty($searchs['invoiceseri'])) {
            $arr_post['invoiceSeri'] = $searchs['invoiceseri'];
        }
        // Gán từ link
        if (!empty($this->buyerIdNo)) {
            $arr_post['buyerIdNo'] = $this->buyerIdNo;
        } else if (!empty($searchs['buyeridno'])) {
            $arr_post['buyerIdNo'] = $searchs['buyeridno'];
        }
        // Gán từ form search
        // echo '<pre>'; print_r($searchs); print_r($arr_post); die;
        //echo $this->supplierTaxCode; die;
//        if ($this->supplierTaxCode == 't0311114017') {
//            $up = base64_encode('0311114017:Test@123456');
//        } else if ($this->supplierTaxCode == '0100109106-997') {
//            $up = base64_encode('0100109106-997:123456a@A');
//        } else if ($this->supplierTaxCode == '0311114017') {
//            $up = base64_encode('0311114017_portal:111111a@A');
//        }
        $up = $this->checkAccount($this->supplierTaxCode);
        //echo $this->purl . "/InvoiceAPI/InvoiceUtilsWS/getInvoices/" . $this->supplierTaxCode; die;
        //echo json_encode($arr_post); die;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => $this->pport,
            CURLOPT_URL => $this->purl . "/InvoiceAPI/InvoiceUtilsWS/getInvoices/" . $this->supplierTaxCode,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arr_post),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic $up",
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $rs = "cURL Error #:" . $err;
        } else {
            $rs = json_decode($response, true);
        }
        // echo '<pre>';  print_r($rs); die;
        return $rs;
    }

    function grid() {
        $data = new stdClass();
        $page = $this->input->post('page');
        $searchs = json_decode($this->input->post('filter'), true);
        $rsData = $this->getListInvoice($this->rowInPage, $page, $searchs);
        // Có kết quả trả về
        if (is_array($rsData) && empty($rsData['errorCode'])) {
            $limit = $this->rowInPage;
            $config = $this->Admin_model->config_pagination(site_url() . 'lookupinvoice', $rsData['totalRow'], $limit, $page);
            $this->pagination->initialize($config);
            $links = $this->pagination->create_links();
            $data->rows = $rsData['invoices'];
            $data->pagination = $links . $rsData['totalRow'];
            $data->totalrow = $rsData['totalRow'];
            $data->pos = (($page - 1) * $this->rowInPage) + 1;
            $content = $this->load->view('list', $data, true);
            $rs['totalRow'] = $rsData['totalRow'];
            $rs['from'] = $data->pos;
            $rs['to'] = ($page * $this->rowInPage);
            $rs['to'] = ($rs['to'] > $rs['totalRow'] ? $rs['totalRow'] : $rs['to']);
            $rs['page'] = $page;
            $rs['pages'] = ($rs['totalRow'] % $this->rowInPage == 0 ? $rs['totalRow'] / $this->rowInPage : ( (int) ($rs['totalRow'] / $this->rowInPage) + 1));
            $rs['grid'] = $content;
            $rs['pagination'] = $links;
        } else {
            // Lỗi CURL
            $data->description = (is_array($rsData) ? ($rsData['errorCode'] . ": " . $rsData['description']) : 'Service busy, Please try again in a few seconds (' . $rsData . ').');
            $content = $this->load->view('busy', $data, true);
            $rs['grid'] = $content;
        }
        echo json_encode($rs);
    }

    function getinvoice() {
        // get tu session
        $supplierTaxCode = $this->supplierTaxCode;
        // nhan tu UI post
        $iid = $this->input->post('iid');
        $ino = $this->input->post('ino');
        $itc = $this->input->post('itc');
        $itype = $this->input->post('itype');
        if ($itype == "view") {
            $itype = "pdf";
        }
        $rs = $this->getInvoiceCurl($itype, $supplierTaxCode, $ino, $itc);
        $rt = array("status" => "fail", "reason" => "curl fail");
        if (is_array($rs)) {
            $cf = $this->createFile($iid, $ino, $itype, $rs);
            if ($cf == "1") {
                $rt['file'] = $iid;
                $rt['status'] = "success";
                $rt['reason'] = "";
            } else {
                $rt['status'] = "fail";
                $rt['reason'] = "Can not create file";
            }
        } else {
            $rt['status'] = "fail";
            $rt['reason'] = $rs;
        }
        echo json_encode($rt);
    }

    private function getInvoiceCurl($fileType = 'pdf', $supplierTaxCode = '0100109106-997', $invoiceNo = 'AL/18E0000136', $pattern = '01GTKT0/001') {
        // Khởi tạo param truyen đi
        $arr_post = array(
            'supplierTaxCode' => $supplierTaxCode,
            'invoiceNo' => $invoiceNo,
            'pattern' => $pattern,
            'transactionUuid' => "test",
            'fileType' => strtoupper($fileType)
        );

        // Thông tin account
//        if ($this->supplierTaxCode == 't0311114017') {
//            $up = base64_encode('0311114017:Test@123456');
//        } else if ($this->supplierTaxCode == '0100109106-997') {
//            $up = base64_encode('0100109106-997:123456a@A');
//        } else if ($this->supplierTaxCode == '0311114017') {
//            $up = base64_encode('0311114017_portal:111111a@A');
//        }
        $up = $this->checkAccount($this->supplierTaxCode);
        // Curl Post
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => $this->pport,
            CURLOPT_URL => $this->purl . "/InvoiceAPI/InvoiceUtilsWS/getInvoiceRepresentationFile",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arr_post),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic $up",
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
    }

    function createFile($iid, $ino, $itype, $rs) {
        if ($itype == 'pdf' || $itype == 'view') {
            $ifile = FCPATH . "/files/pdf/" . $iid . ".pdf";
        } else if ($itype == 'zip') {
            $ifile = FCPATH . "/files/zip/" . $iid . ".zip";
        }
        file_put_contents($ifile, base64_decode($rs['fileToBytes']));
        return '1';
    }

    function pdf() {
        $name = $this->uri->segment(3);
        $filename = FCPATH . "/files/pdf/" . $name . ".pdf";
        $fileinfo = pathinfo($filename);
        $sendname = $fileinfo['filename'] . '.' . strtolower($fileinfo['extension']);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=\"$sendname\"");
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        exit;
    }

    function zip() {
        $name = $this->uri->segment(3);
        $filename = FCPATH . "/files/zip/" . $name . ".zip";
        $fileinfo = pathinfo($filename);
        $sendname = $fileinfo['filename'] . '.' . strtolower($fileinfo['extension']);
        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename=\"$sendname\"");
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        exit;
    }

    function detail() {
        $name = $this->uri->segment(3);
        $filename = FCPATH . "/files/pdf/" . $name . ".pdf";
        header('Content-Type: application/pdf');
        readfile($filename);
        exit;
    }

    function setrowinpage() {
        $rip = $this->input->post('rip');
        $this->session->set_userdata('rowInPage', $rip);
    }

}
