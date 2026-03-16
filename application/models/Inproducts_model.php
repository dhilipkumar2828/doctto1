<?php

class Inproducts_model extends CI_Model {

    public $table = 'products';

    public function __construct() {
        parent::__construct();
    }

    public function get_count() 
    {
        $qry = $this->db->query("select * from products where status=0 and delete_status=0 order by id desc");
        return $qry->num_rows();
    }

    public function get_authors($limit, $start) {
        //$this->db->limit($limit, $start);
        $qry = $this->db->query("select * from products where status=0 and delete_status=0  order by id desc LIMIT ".$start.",".$limit);
        return $qry->result();
    }


     public function search_get_count($kayword) 
    {
        $qry = $this->db->query("select * from products where status=0 and delete_status=0 and name LIKE '%".$kayword."%' order by id desc");
        return $qry->num_rows();
    }

    public function search_get_authors($limit, $start,$kayword) {
        //$this->db->limit($limit, $start);
        $qry = $this->db->query("select * from products where status=0 and delete_status=0 and name LIKE '%".$kayword."%' order by id desc LIMIT ".$start.",".$limit);
        return $qry->result();
    }

    function saverecords($data)
    {
        $name = $data[0];
        $descp = $data[1];
        $cat_id = $data[2];
        $sub_cat_id = $data[3];
        $brand = $data[4];

        $manage_stock = $data[5];
        $shop_id = $data[6];
        $meta_tag_title = $data[7];
        $meta_tag_description = $data[8];
        $meta_tag_keywords = $data[9];

        $product_tags = $data[10];
        $key_features = $data[11];
        $return_status = $data[12];
        $return_noof_days = $data[13];
        $variant_product = $data[14];
        
            $price = $data[15];
            $saleprice = $data[16];
            $stock = $data[17];
         
            $array = array('name'=>$name,'descp'=>$descp,'cat_id'=>$cat_id,'sub_cat_id'=>$sub_cat_id,'brand'=>$brand,'manage_stock'=>$manage_stock,'shop_id'=>$shop_id,'meta_tag_title'=>$meta_tag_title,'meta_tag_description'=>$meta_tag_description,'meta_tag_keywords'=>$meta_tag_keywords,'product_tags'=>$product_tags,'key_features'=>$key_features,'return_status'=>$return_status,'return_noof_days'=>$return_noof_days,'availabile_stock_status'=>'available','variant_product'=>$variant_product);
            $ins = $this->db->insert("products",$array);
            //echo $this->db->last_query(); die;
            if($ins)
            {
                $product_id = $this->db->insert_id();
                if($variant_product=='no')
                {
                    $link_array = array('price'=>$price,'saleprice'=>$saleprice,'stock'=>$stock,'product_id'=>$product_id);
                    $this->db->insert("link_variant",$link_array);
                }
                else
                {
                     

                     $attribute_type = $data[16];
                     $attribute_values = $data[17];
                     $this->insert_variant($product_id,$attribute_type,$attribute_values);
                }
                
                
            }
        
        
    }





    function insert_variant($product_id,$attribute_type,$attribute_values)
    {
        $chk = $this->db->query("select * from add_variant where product_id='".$product_id."' and attribute_type='".$attribute_type."'");
        if($chk->num_rows()>0)
        {
            return 'false'; // attributes already exist 
        }
        else
        {
            $ar = array(
                'product_id'=>$product_id,
                'attribute_type'=>$attribute_type,
                'attribute_values'=>$attribute_values,
                'created_at'=>time()
             );
            $ins = $this->db->insert("add_variant",$ar);
            //echo $this->db->last_query(); die;
            if($ins)
            {
                       
                       
                       $check_var = $this->db->query("select * from add_variant where product_id='".$product_id."'");
                       if($check_var->num_rows()>0)
                       {
                                $get_var = $check_var->result();
                                foreach ($get_var as $value) 
                                {
                                    $att_values1[] =$value->attribute_type;
                                    $att_values[] =explode(",",$value->attribute_values);
                                }
                               
                                $result = array();
                                $arrays = array_values($att_values);
                                $sizeIn = sizeof($arrays);
                                $size = $sizeIn > 0 ? 1 : 0;
                                foreach ($arrays as $array)
                                    $size = $size * sizeof($array);
                                for ($i = 0; $i < $size; $i ++)
                                {
                                    $result[$i] = array();
                                    for ($j = 0; $j < $sizeIn; $j ++)
                                        array_push($result[$i], current($arrays[$j]));
                                    for ($j = ($sizeIn -1); $j >= 0; $j --)
                                    {
                                        if (next($arrays[$j]))
                                            break;
                                        elseif (isset ($arrays[$j]))
                                            reset($arrays[$j]);
                                    }
                                }
                                for ($sp=0; $sp<count($result); $sp++) 
                                { 
                                   $values = $result[$sp];
                                   $types = $att_values1;
                                   $value_array=[];
    
                                   for ($p=0; $p < count($values); $p++) 
                                   { 
                                      $value_array[] = array('attribute_type'=>$types[$p],'attribute_value'=>$values[$p]);
                                   }
                                   $jsondata = json_encode($value_array);
                                   $jsondata1 = json_encode($jsondata);  
                                   $ins11 = $this->db->insert("link_variant",array('product_id'=>$product_id,'jsondata'=>$jsondata,'filter_jsondata'=>$jsondata1));
                                }
                       
                }
            }
            //return TRUE;
            //$this->session->set_flashdata('success_message', 'Variant added Successfully');
            //redirect(base_url().'vendors/inactive_products/addvariant/'.$this->input->get_post('product_id'));
        }

    }

}
