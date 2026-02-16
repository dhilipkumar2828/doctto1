<?php

class Vendor_model extends CI_Model {

    public $table = 'admin';
    public $logs = 'logs';
    public $ip_check = 'ip_check';
    public $banners = 'banners';
    public $services = 'services';

    function __construct() {

        //load the parent constructor

        parent::__construct();
    }

    public function ip_checking($ip) {
        $this->db->where('ip', $ip);
        $ipcheck = $this->db->get('ipcheck');
        return $ipcheck->row();
    }

    public function ip_insert($ip, $date) {
        $data['date1'] = date('d-m-Y h:i:s');
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->db->insert('ipcheck', $data);
        // return $query->row();
    }

    public function admin_login($username, $password) 
    {
        $this->db->where('mobile', $username);
        $this->db->where('password', $password);
        $query = $this->db->get('vendor_shop');
        //echo $this->db->last_query(); die;
        if ($query->num_rows() == 1) {
            $data['admin'] = 'vendors';
            $data['date'] = date('d-m-Y h:i:s');
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
            //$this->db->insert('logs', $data);
            return $query->row();
        } else {
            return false;
        }
    }

    public function verify_password_by_user_id($id, $password) {

        return $this->db->get_where('admin', ['id' => $id, 'password' => $password])->num_rows();
    }

    public function set_password_by_user_id($id, $new_password) {

        $this->db->set('password', $new_password);
        $this->db->where('id', $id);
        return $this->db->update('admin');
    }

    // Get all table rows order by
    public function get_table_data($table_name, $order_col = null, $order_val = null) {
        if ($order_col && $order_val) {
            $this->db->order_by($order_col, $order_val);
        }
        return $this->db->get($table_name)->result();
    }

    // Get row
    public function get_table_row($table_name, $col_name, $val) {
        $this->db->where($col_name, $val);
        return $this->db->get($table_name)->row();
    }

    // Get rows of where clause
    public function get_table_data_by_value($table_name, $col_name, $val) {
        $this->db->order_by('id', 'desc');
        $this->db->where($col_name, $val);
        return $this->db->get($table_name)->result();
    }

    public function get_table_rows_count($table_name) {
        return $this->db->get($table_name)->num_rows();
    }

    // Delete Product
    public function delete_product($id) {
        return $this->db->query("delete from products where id='" . $id . "'");
    }

    // Delete Vendor Admin Comission
    public function delete_vendor_admin_comission($id) {
        return $this->db->query("delete from admin_comissions where id='" . $id . "'");
    }

    function subcategories($cid,$shop_id)
    {
      

         $qry = $this->db->query("select * from sub_categories where cat_id='".$cid."'");
          $query=$qry->result();
          $output = '<option value="">Select SubCategories</option>';
          foreach($query as $row)
          {
            $qry = $this->db->query("SELECT * FROM `admin_comissions` where cat_id='".$cid."' and shop_id='".$shop_id."' and find_in_set(subcategory_ids,'".$row->id."')");
            if($qry->num_rows()>0)
            {

            }
            else
            {
              $output .= '<option value="'.$row->id.'">'.$row->sub_category_name.'</option>';
            }
           
          }

          print_r($output); die;
          // /return $output;
    }

    function subcategoriesforproducts($cid,$shop_id)
    {
      

         $qry = $this->db->query("select * from sub_categories where cat_id='".$cid."'");
          $query=$qry->result();
          $output = '<option value="">Select SubCategories</option>';
          foreach($query as $row)
          {
            $qry = $this->db->query("SELECT * FROM `admin_comissions` where cat_id='".$cid."' and shop_id='".$shop_id."' and find_in_set('".$row->id."',subcategory_ids)");
            if($qry->num_rows()>0)
            {
              $output .= '<option value="'.$row->id.'">'.$row->sub_category_name.'</option>';
            }
           
          }

          print_r($output); die;
          // /return $output;
    }

    function getAttributes($category,$subcatid)
    {
         
        $qry = $this->db->query("select * from attributes where category='".$category."' and subcategory='".$subcatid."' group by title");
          $query=$qry->result();
          $output = '<option value="">Select Attribute Title</option>';
          foreach($query as $row)
          {
            $output .= '<option value="'.$row->title.'">'.$row->title.'</option>';
          }

          print_r($output); die;
    }  

    function getAttributesvalues($category,$subcatid,$title)
    {
          $qry = $this->db->query("select * from attributes where category='".$category."' and subcategory='".$subcatid."' and title='".$title."' order by value asc");
          $query=$qry->result();
          $output = '<option value="">Select Attribute Values</option>';
          foreach($query as $row)
          {
            $output .= '<option value="'.$row->value.'">'.$row->value.'</option>';
          }

          print_r($output); die;
    }
    

    function getAttributeValues($type_id)
    {
          $qry = $this->db->query("select * from attributes_values where attribute_titleid='".$type_id."' order by id asc");
          $query=$qry->result();
          $output = '<option value="">Select Attribute Values</option>';
          foreach($query as $row)
          {
            $output .= '<option value="'.$row->id.'">'.$row->value.'</option>';
          }

          print_r($output); die;
    }


    function saverecords($data) {

      print_r($data); die;
        $this->db->set($data);
        $response = $this->db->insert("products");
        if ($response) {
            return true;
        } else {
            return false;
        }
    }


}
?>