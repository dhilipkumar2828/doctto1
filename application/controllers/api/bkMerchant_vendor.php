<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Merchant_vendor extends CI_Controller {

    function __construct() {

        parent::__construct();
        $this->load->model("admin_model");
    }

    function index() {

    }

    function login() {
        $mobile = $this->input->get_post('mobile');
        $password = $this->input->get_post('password');
        $user_type = $this->input->get_post('user_type');
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
        if (!$user_type) {
            $arr = array('status' => "invalid", 'message' => "User Type Required - Merchant or Vendor");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }

        if ($user_type == 'visual_merchant') {
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
        } else if ($user_type == 'vendor') {
            $this->db->where('mobile', $mobile);
            $this->db->where('password', md5($password));
            $ven_query_result = $this->db->get('vendor_shop')->row();
            if ($ven_query_result) {
                unset($ven_query_result->password);
                $arr = array('status' => "valid", 'message' => "Records Found", 'data' => $ven_query_result);
                echo json_encode($arr, JSON_PRETTY_PRINT);
                die;
            } else {
                $arr = array('status' => "invalid", 'message' => "Invalid Details");
                echo json_encode($arr, JSON_PRETTY_PRINT);
                die;
            }
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

    function shop_categories() {
        $shop_id = $this->input->get_post('shop_id');
        $this->db->where('id', $shop_id);
        $query_result = $this->db->get('vendor_shop')->row();
        if (count($query_result) > 0) {
            unset($query_result->password);
            $query_result->shop_logo = SHOP_LOGOS_PATH . $query_result->shop_logo;

            $catIds = explode(",", $query_result->cat_ids);

            foreach ($catIds as $catId) {
                $catRow = $this->db->get_where('categories', ['id' => $catId])->row();
                if ($catRow) {
                    $catRow->app_image = CATEGORIES_PATH . $catRow->app_image;
                    $categories[] = $catRow;
                }
            }
            $query_result->categories = $categories;

            $arr = array('status' => "valid", 'message' => "Records Found", 'data' => $query_result);
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        } else {
            $arr = array('status' => "invalid", 'message' => "No Details Found");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }
    }

    function shop_sub_categories() {
        $cat_id = $this->input->get_post('cat_id');
        $this->db->where('cat_id', $cat_id);
        $query_result = $this->db->get('sub_categories')->result();
        if (count($query_result) > 0) {
            foreach ($query_result as $res) {
                $res->app_image = SUB_CATEGORIES_PATH . $res->app_image;
            }
            $arr = array('status' => "valid", 'message' => "Records Found", 'data' => $query_result);
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        } else {
            $arr = array('status' => "invalid", 'message' => "No Details Found");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }
    }

    function sub_categories() {
        $cat_id = $this->input->get_post('cat_id');
        $result = $this->admin_model->get_table_data_by_value('sub_categories', 'cat_id', $cat_id);
        echo json_encode($result);
    }

    function shop_products() {
        $shop_id = $this->input->get_post('shop_id');
        $cat_id = $this->input->get_post('cat_id');
        $sub_cat_id = $this->input->get_post('sub_cat_id');
    }

}
