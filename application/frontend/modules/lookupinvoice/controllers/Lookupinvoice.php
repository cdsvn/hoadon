<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lookupinvoice extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->site->write('title', "Nguyen Tat Huy");
        $this->load->model('lookupinvoice/Lookupinvoice_model');
        $this->load->language('lookupinvoice');
    }

    function _remap($method, $params = array()) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        $this->_view();
    }

    function _view() {
        $data = new stdClass();
        $supplierTaxCode = $this->uri->segment(3); // ma so thue
        $secureSupplierTaxCode = $this->uri->segment(4); // ma bi mat

        if(empty($supplierTaxCode) || empty($secureSupplierTaxCode )) {
            die("INVALID PARAMS 1");
        } else {
            // Check supplierTaxCode va secureSupplierTaxCode xem hop le khong
            $stmp = md5("1234qwer".$supplierTaxCode."0987@@@");
            if($stmp != $secureSupplierTaxCode) {
                die("INVALID PARAMS 2". $stmp);
            }
        }

        $arr_post = array(
            'startDate'=>'2017-12-12T10:14:32.611+07:00',
            'endDate'=>'2017-12-31T10:14:32.611+07:00',
            'invoiceType'=>'02GTTT',
            'rowPerPage'=>20,
            'pageNum'=>'1',
            'templateCode'=> null
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8443",
            CURLOPT_URL => "https://demo-sinvoice.viettel.vn:8443/InvoiceAPI/InvoiceUtilsWS/getInvoices/".$supplierTaxCode,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arr_post),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic MDEwMDEwOTEwNi05OTc6MTExMTExYUBB",
                "cache-control: no-cache",
                "content-type: application/json"
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
        $content = $this->load->view('view', $data, true);
        $this->site->write('content', $content, true);
        $this->site->render();
    }

    function getinvoice() {
        // get tu session
        $supplierTaxCode = '0100109106-997';
        // nhan tu UI post
        $iid = $this->input->post('iid');
        $ino = $this->input->post('ino');
        $itc = $this->input->post('itc');
        $itype = $this->input->post('itype');
        $itype = ($itype=="view"?"pdf":$itype);
        $rs = $this->getInvoiceCurl($itype, $supplierTaxCode, $ino, $itc);
        $rt = array("status"=>"fail", "reason"=>"curl fail");
        if(is_array($rs)) {
            $cf = $this->createFile($iid, $ino, $itype, $rs);
            if($cf == "1") {
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
        $arr_post = array(
            'supplierTaxCode'=>$supplierTaxCode,
            'invoiceNo'=>$invoiceNo,
            'pattern'=>$pattern,
            'transactionUuid'=>"test",
            'fileType'=>strtoupper($fileType)
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8443",
            CURLOPT_URL => "https://demo-sinvoice.viettel.vn:8443/InvoiceAPI/InvoiceUtilsWS/getInvoiceRepresentationFile",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($arr_post),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic MDEwMDEwOTEwNi05OTc6MTExMTExYUBB",
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
        if($itype == 'pdf' || $itype == 'view') {
            $ifile = FCPATH."/files/pdf/".$iid.".pdf";
        } else if($itype == 'zip') {
            $ifile = FCPATH."/files/zip/".$iid.".zip";
        }
        file_put_contents($ifile, base64_decode($rs['fileToBytes']));
        return '1';
    }

    function pdf() {
        $name = $this->uri->segment(3);
        $filename = FCPATH."/files/pdf/".$name.".pdf";
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
        $filename = FCPATH."/files/zip/".$name.".zip";
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
        $filename = FCPATH."/files/pdf/".$name.".pdf";
        header('Content-Type: application/pdf');
        readfile($filename);
        exit;
    }

}
