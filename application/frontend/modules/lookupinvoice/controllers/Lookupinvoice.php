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

    function checkAccount($supplierTaxCode, $supplierTaxCodeChild = '') {
        $arr = $this->config->item('infoByerIdNo');
        $item = $arr[$supplierTaxCode];
        if (empty($supplierTaxCodeChild) || $supplierTaxCode == $supplierTaxCodeChild) {
            $base64 = base64_encode($item[1] . ':' . $item[2]);
        } else {
            $child = $item[3][$supplierTaxCodeChild];
            $base64 = base64_encode($child[0] . ':' . $child[1]);
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

    private function getListInvoice($rowPerPage = 5, $pageNum = '1', $searchs = array(), $subBuyerIdNo = '') {
        $arr_post = array(
            'startDate' => '2017-12-12',
            'endDate' => '2017-12-31',
            'rowPerPage' => $rowPerPage,
            'pageNum' => $pageNum,
            'getAll' => true
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
        // Kiểm tra tài khoản để tạo ra chuỗi phù hợp.
        $up = $this->checkAccount($this->supplierTaxCode);

        if (!empty($subBuyerIdNo)) {
            $supplierTaxCode = $subBuyerIdNo;
        } else {
            $supplierTaxCode = $this->supplierTaxCode;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => $this->pport,
            CURLOPT_URL => $this->purl . "/InvoiceAPI/InvoiceUtilsWS/getInvoices/" . $supplierTaxCode,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_SSL_VERIFYPEER => false,
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
//        echo '<pre>';
//        print_r($rs);
//        die;
        $this->_writeLogToFile($this->purl . "/InvoiceAPI/InvoiceUtilsWS/getInvoices/" . $supplierTaxCode, $rs, $arr_post);
        return $rs;
    }

    /*
     * Write log access
     * datetime mnpp total err
     */

    function _writeLogToFile($url, $data, $searchs) {
        $str = date("Y-m-d H:i:s") . ' ' . $url;
        if (!empty($searchs['buyerIdNo'])) {
            $str .= " " . $searchs['buyerIdNo'];
        } else {
            $str .= " - empty ";
        }
        if (is_string($data)) {
            $str .= " - " . $data;
        } else {
            if (!empty($data['totalRow'])) {
                $str .= " - " . $data['totalRow'];
            } else {
                $str .= " - null";
            }
            if (!empty($data['errorCode'])) {
                $str .= " - " . $data['errorCode'];
            } else {
                $str .= " - null";
            }
        }
        $logPath = FCPATH . "/files/logs.txt";
        $mode = (!file_exists($logPath)) ? 'w' : 'a';
        $logfile = fopen($logPath, $mode);
        fwrite($logfile, "\r\n" . $str);
        fclose($logfile);
    }

    function processHasData($subBIN, $buyeridno) {
        // Nếu đã có bảng map Mã NPP với link trong session thì lấy ra, chưa thì khởi tạo
        if ($this->session->has_userdata('mapBIN')) {
            $arr = $this->session->userdata('mapBIN');
        } else {
            $arr = array();
        }
        $arr[$buyeridno] = $subBIN;
        $this->session->set_userdata('mapBIN', $arr);
    }

    function checkLinkExistData($buyeridno) {
        $subBIN = '';
        if ($this->session->has_userdata('mapBIN')) {
            $arr = $this->session->userdata('mapBIN');
            if (!empty($arr[$buyeridno])) {
                $subBIN = $arr[$buyeridno];
            }
        }
        return $subBIN;
    }

    function grid() {
        $data = new stdClass();
        $page = $this->input->post('page');
        $searchs = json_decode($this->input->post('filter'), true);
        // Kiểm tra trong session xem mã NPP này đã search có data trong link nào
        $supplierTaxCode = $this->checkLinkExistData($searchs['buyeridno']);
        // Gọi hàm lấy data với link supplierTaxCode bên trên
        $rsData = $this->getListInvoice($this->rowInPage, $page, $searchs, $supplierTaxCode);
        if (isset($rsData['totalRow']) && $rsData['totalRow'] == 0) {
            // Lấy danh sách link con là 3 index trong config
            $arrBIN = $this->config->item('infoByerIdNo');
            $cur = $arrBIN[$this->supplierTaxCode];
            // Nếu danh sách link con có tồn tại
            if (!empty($cur[3]) && is_array($cur[3])) {
                // Duyệt qua từng link
                foreach ($cur[3] as $sub => $val) {
                    // Gọi hàm lấy data theo link con
                    $rsData = $this->getListInvoice($this->rowInPage, $page, $searchs, $sub);
                    // Nếu có data thì lưu link và Ma NPP vào seesion
                    if (isset($rsData['totalRow']) && $rsData['totalRow'] != 0) {
                        $supplierTaxCode = $sub;
                        $this->processHasData($sub, $searchs['buyeridno']);
                        break;
                    }
                }
            }
        } else {
            if (!empty($rsData['totalRow'])) {
                $this->processHasData($supplierTaxCode, $searchs['buyeridno']);
            }
        }
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
            $data->l = $supplierTaxCode;
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
        $itl = $this->input->post('itl');
        if ($itype == "view") {
            $itype = "pdf";
        }
        if (!empty($itl)) {
            $supplierTaxCode = $itl;
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
        //print_r($arr_post); die;
        $up = $this->checkAccount($this->supplierTaxCode, $supplierTaxCode);
        // Curl Post
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => $this->pport,
            CURLOPT_URL => $this->purl . "/InvoiceAPI/InvoiceUtilsWS/getInvoiceRepresentationFile",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_SSL_VERIFYPEER => false,
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
