<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Visual_merchants extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {

    }

    function login() {
        $mobile = $this->input->get_post('mobile');
        $password = $this->input->get_post('password');
        if (!$mobile) {
            $arr = array('status' => "invalid", 'message' => "Mobile Number Required");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }
        if (!$password) {
            $arr = array('status' => "invalid", 'message' => "Password Required");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }

        $this->db->where('mobile', $mobile);
        $this->db->where('password', md5($password));
        $query_result = $this->db->get('visual_merchant')->row();
        if ($query_result) {
            unset($query_result->password);
            $arr = array('status' => "valid", 'message' => "Records Found", 'data' => $query_result);
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        } else {
            $arr = array('status' => "invalid", 'message' => "Invalid Details");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }
    }

    function dashboardDetails() {
        $vm_id = $this->input->get_post('vm_id');
        $this->db->where('vm_id', $vm_id);
        $query_result = $this->db->get('vendor_shop')->result();

        $data['no_of_shops'] = count($query_result);
        $data['today_orders'] = 0;
        $data['total_orders'] = 0;
        $arr = array('status' => "valid", 'message' => "Records Found", 'data' => $data);
        echo json_encode($arr, JSON_PRETTY_PRINT);
        die;
    }

    function shops() {
        $vm_id = $this->input->get_post('vm_id');
        $this->db->where('vm_id', $vm_id);
        $query_result = $this->db->get('vendor_shop')->result();

        if (count($query_result) > 0) {
            foreach ($query_result as $shop) {
                $shop->shop_logo = SHOP_LOGOS_PATH . $shop->shop_logo;
            }
            $arr = array('status' => "valid", 'message' => "Records Found", 'data' => $query_result);
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        } else {
            $arr = array('status' => "invalid", 'message' => "No Shops Found");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }
    }

    function shop_detail() {
        $shop_id = $this->input->get_post('shop_id');
        $this->db->where('id', $shop_id);
        $query_result = $this->db->get('vendor_shop')->row();
        if (count($query_result) > 0) {
            unset($query_result->password);
            $query_result->shop_logo = SHOP_LOGOS_PATH . $query_result->shop_logo;

            $arr = array('status' => "valid", 'message' => "Records Found", 'data' => $query_result);
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        } else {
            $arr = array('status' => "invalid", 'message' => "No Details Found");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }
    }

}
