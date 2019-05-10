<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Toinvoice extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->site->write('title', "Nguyen Tat Huy");
        $this->load->model('toinvoice/Toinvoice_model');
        $this->load->language('toinvoice');
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

    function _view() {
        $data = new stdClass();
        $content = $this->load->view('view', $data, true);
        $this->site->write('content', $content, true);
        $this->site->render();
    }

    function createParams($data) {
        $path = FCPATH . "files" . DIRECTORY_SEPARATOR . "toinvoice" . DIRECTORY_SEPARATOR . 'template.txt';
        $org = json_decode(file_get_contents($path), true);
        // Reset 1 so thong tin
        $org['itemInfo'] = array();
        foreach ($data['body'] as $item) {
            $tmp = array(
                "lineNumber" => $item['stt'],
                "itemCode" => "",
                "itemName" => $item['ten_hang_hoa'],
                "unitName" => $item['don_vi_tinh'],
                "unitPrice" => $item['don_gia'],
                "quantity"  => $item['so_luong'],
                "itemTotalAmountWithoutTax"  => 0.0,
                "taxPercentage" => "10",
                "taxAmount" => "94455.000000",
                "discount" => 0,
                "itemDiscount" => 0
            );
            array_push($org['itemInfo'], $tmp);
        }
        $org['generalInvoiceInfo']['templateCode'] = '01GTKT0/695';
        $org['generalInvoiceInfo']['invoiceSeries'] = 'PN/19E';
        $org['generalInvoiceInfo']['invoiceIssuedDate'] = "2019-05-02T23:36:00+07:00";
        //echo '<pre>'; print_r($data['top']);die;
        $org['sellerInfo']['sellerLegalName'] = $data['top']['ten_cong_ty'];
        $org['sellerInfo']['sellerTaxCode'] = $data['top']['ma_so_thue'];
        $org['sellerInfo']['sellerAddressLine'] = $data['top']['dia_chi'];
        return $org;
    }

    function parseExcel($file) {
        // Khởi tạo biến
        $data = new stdClass();
        $info_top = array();
        $info_body = array();
        $info_bottom = array();

        // Load thư viện
        $this->load->library('excel');

        // ini_set('precision', '15'); // return: 3.5410705883463E+14 realy value: 357888057644723

        $objTpl = PHPExcel_IOFactory::load($file);
        $sheetObj = $objTpl->getActiveSheet();
        $highestRow = $sheetObj->getHighestRow();
        $sheetArr = $sheetObj->toArray(null, true, true, true);
        if ($highestRow > 100) {
            return 'Số dòng quá nhiều (' . $highestRow . ')';
        }

        // Lấy top thông tin (tên cty, mst...)
        $info_top['ngay_lap'] = preg_replace('/\s+/', '-', $sheetArr[4]['M']); // cell 4:M
        $info_top['ten_cong_ty'] = $sheetArr[6]['L']; // cell 6:L
        $info_top['ma_so_thue'] = $sheetArr[8]['H']; // cell 8:H
        $info_top['dia_chi'] = $sheetArr[10]['G']; // cell 10:G
        $info_top['loai_thanh_toan'] = $sheetArr[12]['N']; // cell 12:N
        // Khi gặp 2 dòng trống liên tục thì hiểu là hết phần thông tin hàng hóa
        $row_empty = 0; // Đếm số lượng dòng trống liên tục
        $flag_row_empty = false; // Cờ bật khi gặp dòng trống liên tục.
        for ($r = 15; $r < $highestRow; $r++) {
            if (empty($sheetArr[$r]['F'])) { // Nếu ô tên hàng hóa là empty
                if ($flag_row_empty) { // Nếu cờ đánh dấu dòng empty bật
                    $row_empty++; // Tăng biến đếm dòng trống liên tục
                }
                $flag_row_empty = true; // Bật cờ báo dòng trống liên tục
            } else {
                $flag_row_empty = false; // Tắt cờ báo dòng trống
                $row_empty = 0; // Reset lại số dòng trống liên tục
            }
            if ($row_empty >= 2) { // Nếu có >= 2 dòng trống liên tục thì đã kết thúc body, chuẩn bị tới phần bottom
                if (!empty($sheetArr[$r]['W'])) { // Cell tổng tiền chưa thuế
                    $info_bottom['tong_tien_truoc_thue'] = $sheetArr[$r]['W'];
                    $info_bottom['phan_tram_thue'] = $sheetArr[($r + 1)]['I'];
                    $info_bottom['tien_thue'] = $sheetArr[($r + 1)]['W'];
                    $info_bottom['tong_tien_sau_thue'] = $sheetArr[($r + 2)]['W'];
                    $info_bottom['tong_tien_sau_thue_bang_chu'] = trim($sheetArr[($r + 3)]['I']);
                    break;
                }
            } else {
                if (!empty($sheetArr[$r]['F'])) { // Nếu ô tên hàng hóa khác empty
                    $tmp = array();
                    $tmp['stt'] = $sheetArr[$r]['E'];
                    $tmp['ten_hang_hoa'] = $sheetArr[$r]['F'];
                    $tmp['don_vi_tinh'] = $sheetArr[$r]['P'];
                    $tmp['so_luong'] = $sheetArr[$r]['S'];
                    $tmp['don_gia'] = $sheetArr[$r]['U'];
                    $tmp['thanh_tien'] = $sheetArr[$r]['Y'];
                    array_push($info_body, $tmp);
                }
            }
        }
        $luu_y = array_pop($info_body)['ten_hang_hoa'];
        $info['top'] = $info_top;
        $info['body'] = $info_body;
        $info['bottom'] = $info_bottom;
        $info['note'] = $luu_y;
        return $info;
    }

    public function save() {
        if ($this->input->post('importfile')) {
            $path = FCPATH . "files" . DIRECTORY_SEPARATOR . "toinvoice" . DIRECTORY_SEPARATOR;

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('userfile')) {
                $error = array('error' => $this->upload->display_errors());
            } else {
                $data = array('upload_data' => $this->upload->data());
            }

            if (!empty($data['upload_data']['file_name'])) {
                $import_xls_file = $data['upload_data']['file_name'];
            } else {
                $import_xls_file = 0;
            }
            $data = $this->parseExcel($path . $import_xls_file);
            $params = $this->createParams($data);
            echo '<pre>'; print_r(json_encode($params)); die;
            $rt = $this->pushInfoCreateInvoice($params);
            echo '<pre>';
            print_r($rt);
            die;
        }
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

    private function pushInfoCreateInvoice($params) {
        echo '<pre>';
        print_r($params);
        die;
        // Get base64 encode
        // $up = $this->checkAccount();
        $up =  base64_encode('0100109106-215:111111a@A');
        // Curl Post
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => $this->pport,
            CURLOPT_URL => "https://demo-sinvoice.viettel.vn:8443/InvoiceAPI/InvoiceWS/createInvoice/0100109106-215",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($params),
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

}
