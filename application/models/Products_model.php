<?php

class Products_model extends CI_Model {

    public $table = 'products';

    public function __construct() {
        parent::__construct();
    }

    public function get_count() 
    {
        $qry = $this->db->query("select * from products where status=1 and delete_status=0 order by id desc");
        return $qry->num_rows();
    }

    public function get_authors($limit, $start) {
        //$this->db->limit($limit, $start);
        $qry = $this->db->query("select * from products where status=1 and delete_status=0 order by id desc LIMIT ".$start.",".$limit);
        return $qry->result();
    }

    
     public function search_get_count($kayword) 
    {
        $qry = $this->db->query("select * from products where status=1 and delete_status=0 and name LIKE '%".$kayword."%' order by id desc");
        return $qry->num_rows();
    }




   public function get_authors_shop($shop_id) {
        //$this->db->limit($limit, $start);
        $qry = $this->db->query("select * from products where shop_id='".$shop_id."' and delete_status=0 order by id desc");
        return $qry->result();
    }

    public function search_get_authors($limit, $start,$kayword) {
        //$this->db->limit($limit, $start);
        $qry = $this->db->query("select * from products where status=1 and delete_status=0 and name LIKE '%".$kayword."%' order by id desc LIMIT ".$start.",".$limit);
        return $qry->result();
    }
}
