<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Vendors extends MY_Controller {

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
        $query_result = $this->db->get('vendor_shop')->row();
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
        $vm_id = $this->input->get_post('vendor_id');
        $this->db->where('id', $vm_id);
        $query_result = $this->db->get('vendor_shop')->result();

        $data['products'] = 0;
        $data['total_orders'] = 0;
        $data['pending_orders'] = 0;
        $data['completed_orders'] = 0;
        $arr = array('status' => "valid", 'message' => "Records Found", 'data' => $data);
        echo json_encode($arr, JSON_PRETTY_PRINT);
        die;
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

    function updateLocation() {
        $lat = $this->input->get_post('lat');
        $lng = $this->input->get_post('lng');

        $data = array(
            'lat' => $lat,
            'lng' => $lng,
            'created_at' => time()
        );
        if ($this->db->insert('locations', $data)) {
            $arr = array('status' => "valid", 'message' => "Data saved");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        } else {
            $arr = array('status' => "invalid", 'message' => "No Details Found");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }
    }

}
