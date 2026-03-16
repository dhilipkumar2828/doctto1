<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Vendor extends CI_Model {

    public function __construct() {
        parent::__construct();
         $this->load->library('image_lib');
        //load database library
        $this->load->database();
    }


    function doRegister($data)
    {
        $email = $data['email'];
        $phone = $data['mobile'];
        $otp = rand(1000,10000);

        $otp_message = $otp." is OTP to register with A3 Services. Pls do not share OTP to anyone for security reasons.";
        $template_id = '1407161683180266239';

        $data['otp'] = $otp;
        $email_verify = $this->db->query("select * from vendor_shop where email='".$email."' and mobile!='".$phone."' and otp_status=1");
        $phone_verify = $this->db->query("select * from vendor_shop where email!='".$email."' and mobile='".$phone."' and otp_status=1");
        $both = $this->db->query("select * from vendor_shop where email='".$email."' and mobile='".$phone."' and otp_status=1");
        if($email_verify->num_rows()>0)
        {
            return array('status' =>FALSE, 'message'=>"Email already Exist ");
        }
        else if($email_verify->num_rows()>0)
        {
            return array('status' =>FALSE, 'message'=>"Phone already Exist ");
        }
        else if($both->num_rows()>0)
        {
            return array('status' =>FALSE, 'message'=>"Phone and Email already Exist ");
        }
        else
        {

            $chk_both = $this->db->query("select * from vendor_shop where ( email='".$email."' or mobile='".$phone."' ) and otp_status=0");
            if($chk_both->num_rows()>0)
            {
                $get = $chk_both->row();
                $wr = array('mobile'=>$phone);

                if($this->send_message($otp_message,$phone,$template_id))
                {

                    $ins = $this->db->update("vendor_shop",$data,$wr);
                    $last_id = $get->id;
                    if($ins)
                    {
                        $ar=array('status' =>TRUE,'user_id'=>$last_id,'otp'=>$otp,'phone'=>$phone,'email'=>$email,'message'=>"Please enter your OTP");
                        return $ar;
                    }
                }
            }
            else
            {
                 if($this->send_message($otp_message,$phone,$template_id))
                {

                    $ins = $this->db->insert("vendor_shop",$data);
                    $last_id = $this->db->insert_id($ins);
                    if($ins)
                    {
                        $ar=array('status' =>TRUE,'user_id'=>$last_id,'otp'=>$otp,'phone'=>$phone,'email'=>$email,'message'=>"Please enter your OTP");
                        return $ar;
                    }
                }
            }
        }
    }

   

    function verify_OTP($user_id,$otp)
    {
         $qry = $this->db->query("select * from vendor_shop where id='".$user_id."' and otp='".$otp."'");
         if($qry->num_rows()>0)
         {
            $ar=array('otp_status'=>1);
            $wr=array('id'=>$user_id);
            $ins = $this->db->update("vendor_shop",$ar,$wr);
            if($ins)
            {
              $row = $qry->row();
              $res = array('status' =>TRUE,'message'=>"Your Registration under approval, once approved you can login",'user_id'=>$row->id,'phone'=>$row->mobile,'email'=>$row->email);
               return $res;  
            }
         }   
         else
         {
             return array('status' =>FALSE, 'message'=>"Invalid OTP");
         }  
    }
    function checkLogin($username,$password,$token)
    {
                $chk = $this->db->query("select * from vendor_shop where ( mobile='".$username."' or email='".$username."' ) and password='".$password."'");
                if($chk->num_rows()>0)
                {
                     $row = $chk->row();
                     if($row->vendor_verification_status==0)
                     {
                        return array('status' =>FALSE, 'message'=>"Approval Pending,Please try again later");
                     }
                     else if($row->vendor_verification_status==1)
                     {
                            if($row->image!='')
                            {
                                $img = base_url()."uploads/users/".$row->image;
                            }
                            else
                            {
                                $img = base_url()."uploads/noproduct.png";
                            }
                            $this->db->update("vendor_shop",array('token'=>$token),array('email'=>$row->email));
                         $res = array('status' =>TRUE,'user_id'=>$row->id,'phone'=>$row->mobile,'email'=>$row->email,'name'=>$row->owner_name,'shop_name'=>$row->shop_name,'address'=>$row->address,'pincode'=>$row->pincode,'city'=>$row->city,'image'=>$img);
                         return $res;
                     }

                    
                }
                else
                {
                    return array('status' =>FALSE, 'message'=>"Invalid Login Details");
                }
        
    }

    function resendOTP($user_id)
    {
        $chk = $this->db->query("select * from vendor_shop where id='".$user_id."'");
        if($chk->num_rows()>0)
        {
            $row = $chk->row();
            $otp = rand(1000,10000);
            
            $phone = $row->mobile;
            $template_id = '1407161683180266239';

            $otp_message = $otp." is OTP to register with A3 Services. Pls do not share OTP to anyone for security reasons.";
            if($this->send_message($otp_message,$phone,$template_id))
            {
                $ar = array('otp'=>$otp);
                $wr = array('id'=>$user_id);
                $upd = $this->db->update("vendor_shop",$ar,$wr);
                if($upd)
                {
                    $ar = array('status' =>TRUE,'message'=>"OTP sent to your Mobile Number",'otp'=>$otp);
                    return $ar;
                }
            }
        }
        else
        {
            $ar = array('status' =>TRUE,'message'=>"Invalid User ID");
            return $ar;
        }
    }
    function send_message($message = "", $mobile_number,$template_id) {

         $message = urlencode($message);

         $URL = "http://login.smsmoon.com/API/sms.php"; // connecting url 

         $post_fields = ['username' => 'RocketWheel', 'password' => 'vizag@123', 'from' => 'RocketWheelS', 'to' => $mobile_number, 'msg' => $message, 'type' => 1, 'dnd_check' => 0,'template_id'=>$template_id];

         //file_get_contents("http://login.smsmoon.com/API/sms.php?username=colourmoonalerts&password=vizag@123&from=WEBSMS&to=$mobile_number&msg=$message&type=1&dnd_check=0");
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $URL);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_exec($ch);
         return true;
      }


    function checkForgot($phone,$user_type)
    {
                $chk = $this->db->query("select * from vendor_shop where ( mobile='".$phone."' or email='".$phone."' ) and vendor_verification_status=1");
                if($chk->num_rows()>0)
                {
                    $otp = rand(1000,10000);
                    //$otp = '1234';
                     $otp_message = $otp." is OTP to reset your password. Pls do not share OTP to anyone for security reasons.";
                    $template_id="1407161683190033363";
                   if($this->send_message($otp_message,$phone,$template_id))
                   {
                    
                   
                        $ar=array('otp'=>$otp);
                        $wr=array('mobile'=>$phone);
                        $upd = $this->db->update('vendor_shop',$ar,$wr);
                        if($upd)
                        {
                            //$row = $chk->row();
                            $stu_row = $chk->row();
                            $st_email = $stu_row->email;
                            $to_mail = $st_email;
                
                            $res = array('status' =>TRUE,'otp'=>$otp,'phone'=>$phone);
                            return $res;  
                        }
                   }
                }
                else
                {
                    return array('status' =>FALSE, 'message'=>"Invalid Details");
                }
    }

    function resetPassword($phone,$otp,$password,$user_type)
    {
            $qry = $this->db->query("select * from vendor_shop where mobile='".$phone."' and otp='".$otp."'");
            if($qry->num_rows()>0)
            {
                  $ar = array('password'=>md5($password));
                  $wr = array('mobile'=>$phone);
                  $upd = $this->db->update("vendor_shop",$ar,$wr);
                 if($upd)
                 {
                    $row = $qry->row();
                    $res = array('status' =>TRUE,'user_id'=>$row->id,'phone'=>$row->mobile,'email'=>$row->email,'shop_name'=>$row->shop_name,'address'=>$row->address,'pincode'=>$row->pincode,'city'=>$row->city);
                    return $res;
                 }
            }
            else
            {
                return array('status' =>FALSE, 'message'=>"Invalid OTP");
            }
    
        
    }

    function getProfile($user_id)
    {
            $qry = $this->db->query("select * from vendor_shop where id='".$user_id."'");
            if($qry->num_rows()>0)
            {
                $row = $qry->row();
                $res = array('status' =>TRUE,'user_id'=>$row->id,'shop_name'=>$row->shop_name,'name'=>$row->owner_name,'email'=>$row->email,'description'=>$row->description,'address'=>$row->address,'city'=>$row->city,'pincode'=>$row->pincode,'phone'=>$row->mobile,'min_order_amount'=>$row->min_order_amount,'delivery_time'=>$row->delivery_time);
                return $res;
            }
            else
            {
                return array('status' =>FALSE, 'message'=>"Invalid UserID");
            }
        

        
    }



    function getBanners()
    {
        $qry = $this->db->query("select * from banners");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    if($value->app_image!='')
                    {
                        $img = base_url()."uploads/banners/".$value->app_image;
                    }
                    else
                    {
                        $img = "";
                    }
                   $ar[]=array('id'=>$value->id,'title'=>$value->title,'image'=>$img);
                }
                return array('status' =>TRUE,'bannerslist'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Banners");
        }
        
    }

    function getCategories($user_id)
    {
        $qry = $this->db->query("select * from admin_comissions where shop_id='".$user_id."'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    $cat = $this->db->query("select * from categories where id='".$value->cat_id."'");
                    $category = $cat->row();
                    
                    if($category->app_image!='')
                    {
                        $img = base_url()."uploads/categories/".$category->app_image;
                    }
                    else
                    {
                        $img = "";
                    }
                    
                   $ar[]=array('id'=>$category->id,'title'=>$category->category_name,'image'=>$img);
                }
                return array('status' =>TRUE,'category_list'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Categories");
        }
    }
    
    function getSubCategories($cat_id)
    {
        $qry = $this->db->query("select id,sub_category_name from sub_categories where cat_id='".$cat_id."'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
            $ar=[];
            foreach ($dat as $category) 
            {
               $ar[]=array('id'=>$category->id,'title'=>$category->sub_category_name,'cat_id'=>$category->cat_id);
            }
            return array('status' =>TRUE,'subcategory_list'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Sub Categories");
        }
    }
    
    function updateOrder($user_id,$min_order_amount)
    {
        $ar = array('min_order_amount'=>$min_order_amount);
        $wr = array('id'=>$user_id);
        $upd = $this->db->update("vendor_shop",$ar,$wr);
        if($upd)
        {
            return array('status' =>TRUE,'min_order_amount'=>$min_order_amount);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Something Went wrong");
        }
    }

function getSingleProduct($pid)
{
        $qry = $this->db->query("select * from products where id='".$pid."'");
        $value = $qry->row();
        if($qry->num_rows()>0)
        {
                    $im = $this->db->query("select * from product_images where product_id='".$value->id."'");
                    $img_result = $im->result();
                        $images1 = $im->row();
                        if($images1->image!='')
                        {
                            $img = base_url()."uploads/products/".$images1->image;
                        }
                        else
                        {
                            $img = base_url()."uploads/noproduct.png";
                        }
                        $img_ar=[];
                    foreach ($img_result as $images) 
                    {
                        if($images->image!='')
                        {
                            $img = base_url()."uploads/products/".$images->image;
                        }
                        else
                        {
                            $img = base_url()."uploads/noproduct.png";
                        }

                        $img_ar[]=array('image'=>$img);
                    }
                    


                    $var1 = $this->db->query("select * from add_variant where product_id='".$value->id."'");
                    $variants1 = $var1->result();
                    $att1=[];
                    foreach ($variants1 as $value1) 
                    {
                        $type = $this->db->query("select * from attributes_title where id='".$value1->attribute_type."'");
                        $types = $type->row();
                        $ex = explode(",", $value1->attribute_values);
                        $values=[];
                        for ($i=0; $i < count($ex); $i++) 
                        { 
                            $val = $this->db->query("select * from attributes_values where id='".$ex[$i]."'");
                            $value1 = $val->row();
                            $values[]=array('id'=>$value1->id,'value'=>$value1->value);
                        }

                        $att1[]=array('id'=>$types->id,'attribute_type'=>$types->title,'attribute_values'=>$values);
                    }


                    
                    $cat = $this->db->query("select * from categories where id='".$value->cat_id."'");
                    $category = $cat->row();
                    $subcat = $this->db->query("select * from sub_categories where id='".$value->sub_cat_id."'");
                    $subcategory = $subcat->row();
                    $brnd = $this->db->query("select * from attr_brands where id='".$value->brand."'");
                    $brand = $brnd->row();

                    $vendo = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                    $vendor = $vendo->row();



                    $link_vari = $this->db->query("select * from link_variant where product_id='".$value->id."'");
                    $link_variants = $link_vari->result();

                    $link_price = $link_vari->row();
                    if($value->variant_product=='yes')
                    {
                        $price = '';
                        $saleprice = '';
                        $stock = '';
                    }
                    else  if($value->variant_product=='no')
                    {
                        $price = $link_price->price;
                        $saleprice = $link_price->saleprice;
                        $stock = $link_price->stock;
                    }

                    if($value->cancel_status=='yes' || $value->return_status=='yes')
                    {
                        $exchangestatus='yes';
                    }
                    else
                    {
                        $exchangestatus='no';
                    }
                    
                    
                   $ar=array('id'=>$value->id,'shop_id'=>$value->shop_id,'name'=>$value->name,'description'=>$value->descp,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'brand_id'=>$value->brand,'shop'=>$vendor->shop_name,'product_tags'=>$value->product_tags,'meta_tag_title'=>$value->meta_tag_title,'meta_tag_description'=>$value->meta_tag_description,'meta_tag_keywords'=>$value->meta_tag_keywords,'key_features'=>$value->key_features,'cancel_status'=>$value->cancel_status,'return_status'=>$value->return_status,'attributes'=>$att1,'link_variants'=>$link_variants,'image'=>$img,'selling_date'=>date('d-m-Y',strtotime($value->selling_date)),'taxname'=>$value->taxname,'manage_stock'=>$value->manage_stock,'variant_product'=>$value->variant_product,'status'=>$value->status,'cat_id'=>$value->cat_id,'sub_cat_id'=>$value->sub_cat_id,'tax_class'=>$value->tax_class,'imageslist'=>$img_ar,'availabile_stock_status'=>$value->availabile_stock_status,'price'=>$price,'saleprice'=>$saleprice,'stock'=>$stock,'no_of_days'=>$value->return_noof_days,'exchangestatus'=>$exchangestatus);
                
            return array('status' =>TRUE,'product_details'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Products");
        }
    

}
    function get_Products($cat_id,$subcat_id,$shop_id)
    {
        $qry = $this->db->query("select * from products where cat_id='".$cat_id."'  and shop_id='".$shop_id."' and sub_cat_id='".$subcat_id."'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                    
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."'");
                     $images = $im->row();

                    if($images->image!='')
                    {
                        $img = base_url()."uploads/products/".$images->image;
                    }
                    else
                    {
                        $img = base_url()."uploads/noproduct.png";
                    }


                    $var1 = $this->db->query("select * from add_variant where product_id='".$value->id."'");
                    $variants1 = $var1->result();
                    $att1=[];
                    foreach ($variants1 as $value1) 
                    {
                        $type = $this->db->query("select * from attributes_title where id='".$value1->attribute_type."'");
                        $types = $type->row();
                        $ex = explode(",", $value1->attribute_values);
                        $values=[];
                        for ($i=0; $i < count($ex); $i++) 
                        { 
                            $val = $this->db->query("select * from attributes_values where id='".$ex[$i]."'");
                            $value1 = $val->row();
                            $values[]=array('id'=>$value1->id,'value'=>$value1->value);
                        }

                        $att1[]=array('id'=>$types->id,'attribute_type'=>$types->title,'attribute_values'=>$values);
                    }


                    
                    $cat = $this->db->query("select * from categories where id='".$value->cat_id."'");
                    $category = $cat->row();
                    $subcat = $this->db->query("select * from sub_categories where id='".$value->sub_cat_id."'");
                    $subcategory = $subcat->row();
                    /*$brnd = $this->db->query("select * from attr_brands where id='".$value->brand."'");
                    $brand = $brnd->row();*/

                    $vendo = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                    $vendor = $vendo->row();



                    $link_vari = $this->db->query("select * from link_variant where product_id='".$value->id."'");
                    $link_variants = $link_vari->result();



                   $ar[]=array('id'=>$value->id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$value->name,'description'=>$value->descp,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$vendor->shop_name,'product_tags'=>$value->product_tags,'meta_tag_title'=>$value->meta_tag_title,'meta_tag_description'=>$value->meta_tag_description,'meta_tag_keywords'=>$value->meta_tag_keywords,'key_features'=>$value->key_features,'cancel_status'=>$value->cancel_status,'return_status'=>$value->return_status,'attributes'=>$att1,'link_variants'=>$link_variants,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status);
                }
            return array('status' =>TRUE,'product_list'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Products");
        }
    }

    function updateProduct($ar,$novariant,$pid)
   {
       $insert_query = $this->db->update('products', $ar,array('id'=>$pid));
       //echo $this->db->last_query(); die;
       if($insert_query)
       {
           $novariant['product_id']=$pid;

           //$this->db->update('link_variant', $novariant,array('product_id'=>$pid));

           return array('status' =>TRUE,'product_id'=>$novariant['product_id'],'message'=>"Product updated successfully");
       }
       else
       {
            return array('status' =>FALSE, 'message'=>"Something went wrong");
       }
   }


   function updatenovariant_insertProduct($ar,$pid)
   {
       $insert_query = $this->db->update('products', $ar,array('id'=>$pid));
       if($insert_query)
       {
           $novariant['product_id']=$pid;
           return array('status' =>TRUE,'product_id'=>$novariant['product_id'],'message'=>"Product updated successfully");
       }
       else
       {
            return array('status' =>FALSE, 'message'=>"Something went wrong");
       }
   }

   
   function insertProduct($ar,$novariant,$imagepath)
   {

        $st =str_replace("[", "", $imagepath);
        $finalimg =str_replace("]", "", $st);
        $insert_query = $this->db->insert('products', $ar);
           if($insert_query)
           {
               $novariant['product_id']=$this->db->insert_id();
               $vari = $this->db->insert('link_variant', $novariant);
               $variant_id = $this->db->insert_id($vari);


               if($imagepath!='')
               { 
                   $ex = explode(",", $finalimg);
                   for ($i=0; $i < count($ex); $i++) { 
                        
                        /*$config['source_image'] = './uploads/products/'.$ex[$i];
                    $config['wm_text'] = 'A3 Services';
                    $config['wm_type'] = 'text';
                    //$config['wm_font_path'] = './fonts/atlassol.ttf';
                    $config['wm_font_path'] = './fonts/Roboto-Regular.ttf';
                    $config['wm_font_size'] = 40;
                    $config['wm_font_color'] = 'FFFFFF';
                    $config['wm_vrt_alignment'] = 'bottom';
                    $config['wm_hor_alignment'] = 'right';
                    $config['wm_padding'] = '-10';
                    $this->image_lib->initialize($config);
                    if (!$this->image_lib->watermark()) {
                    } else {*/
                           $this->db->insert('product_images', array('product_id'=>$novariant['product_id'],'variant_id'=>$variant_id,'image'=>$ex[$i]));
                       //}
                   }
               }



               return array('status' =>TRUE,'product_id'=>$novariant['product_id'],'message'=>"Product added successfully");
           }
           else
           {
                return array('status' =>FALSE, 'message'=>"Something went wrong");
           }
   

    
   }
   
   function novariant_insertProduct($ar)
   {
        $insert_query = $this->db->insert('products', $ar);
       if($insert_query)
       {
           $last_id=$this->db->insert_id();
           return array('status' =>TRUE,'product_id'=>$last_id,'message'=>"Product Uploaded successfully");
       }
       else
       {
            return array('status' =>FALSE, 'message'=>"Something went wrong");
       }
   }
   
   function attributeTypes($product_id)
   {
       $qry = $this->db->query("select * from products where id='".$product_id."'");
       $product = $qry->row();
       
        $category_id=$product->cat_id;
        $mang = $this->db->query("select * from manage_attributes where categories='".$category_id."'");
        $attribute = $mang->result();
        $ar=[];
        if($mang->num_rows()>0)
        {
            foreach ($attribute as $value) 
            {
                $at = $this->db->query("select * from attributes_title where id='".$value->attribute_titleid."'");
                $title = $at->row();
                $ar[]=array('id'=>$title->id,'title'=>$title->title);
            }
            return array('status' =>TRUE,'attribute_types'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Attribute Types");
        }
        
   }
   
   function AttributeValues($product_id,$attribute_type_id)
   { 
        $qry = $this->db->query("select * from attributes_values where attribute_titleid='".$attribute_type_id."' order by id asc");
        if($qry->num_rows()>0)
        {
            $data = $qry->result();
            return array('status' =>TRUE,'attribute_values'=>$data);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Attribute Values");
        }
   }
   
   function addVariant($product_id,$attribute_type_id,$attribute_value_ids)
   {
       
        $chk = $this->db->query("select * from add_variant where product_id='".$product_id."' and attribute_type='".$attribute_type_id."'");
        if($chk->num_rows()>0)
        {
              return array('status' =>FALSE, 'message'=>"Attribute Type already exist");
        }
        else
        {
            $ar = array(
                'product_id'=>$product_id,
                'attribute_type'=>$attribute_type_id,
                'attribute_values'=>$attribute_value_ids,
                'created_at'=>time()
             );
            $ins = $this->db->insert("add_variant",$ar);
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
                                   $ins11 = $this->db->insert("link_variant",array('product_id'=>$product_id,'jsondata'=>$jsondata));
                                }
                       
                }
            }
            return array('status' =>TRUE, 'message'=>"Variant added Successfully");
        }

    
   }
   
   function browse_file($product_id,$variant_id)
    {
        $image = $this->upload_file('image');
        if($image!='false')
        {

            /* $config['source_image'] = './uploads/products/'.$image;
        //The image path,which you would like to watermarking
                    $config['wm_text'] = 'A3 Services';
                    $config['wm_type'] = 'text';
                    //$config['wm_font_path'] = './fonts/atlassol.ttf';
                    $config['wm_font_path'] = './fonts/Roboto-Regular.ttf';
                    $config['wm_font_size'] = 40;
                    $config['wm_font_color'] = 'FFFFFF';
                    $config['wm_vrt_alignment'] = 'bottom';
                    $config['wm_hor_alignment'] = 'right';
                    $config['wm_padding'] = '-10';
                    $this->image_lib->initialize($config);
                    if (!$this->image_lib->watermark()) {
                    } else {*/


                          $ins = $this->db->insert("product_images",array('product_id'=>$product_id,'variant_id'=>$variant_id,'image'=>$image));
                          if($ins)
                          {
                              return $image;
                          }
                          else
                          {
                              return 'false';
                          }

                      //}
        }
    }


    function addBannerImage($vendor_id,$title,$image)
    {
        $img_replace =str_replace('"', '', $image);
          $ins = $this->db->insert("vendor_shop_banners",array('shop_id'=>$vendor_id,'title'=>$title,'app_banner'=>$image,'web_banner'=>$img_replace) );
          if($ins)
          {
              return 'true';
          }
          else
          {
              return 'false';
          }
    }

    function updateBannerImage($vendor_id,$title,$image,$id)
    {

            $ins = $this->db->update("vendor_shop_banners",array('shop_id'=>$vendor_id,'title'=>$title,'app_banner'=>$image,'web_banner'=>$image),array('id'=>$id));
            
              if($ins)
              {
                  return 'true';
              }
              else
              {
                  return 'false';
              }
    }

    function selectBanner()
    {
         echo $this->uploadBanner('image'); exit;
    }


    function selectProductImages()
    {
         echo $this->uploadProducts('image'); 

    }

    private function uploadProducts($file_name) {
        if($_FILES[$file_name]["size"]<'5114374')
        {
            $upload_path1 = "./uploads/products/";
            $config1['upload_path'] = $upload_path1;
            $config1['allowed_types'] = "*";
            // $config1['allowed_types'] = "*";
            $config1['max_size'] = "204800000";
            $img_name1 = strtolower($_FILES[$file_name]['name']);
            $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
            $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);
            $this->upload->do_upload($file_name);
            $fileDetailArray1 = $this->upload->data();
            // echo $this->upload->display_errors();
            // die;
            return $fileDetailArray1['file_name'];
        }
        else
        {
            return 'false';
        }
        
    }


    private function uploadBanner($file_name) {
        if($_FILES[$file_name]["size"]<'5114374')
        {
            $upload_path1 = "./uploads/banners/";
            $config1['upload_path'] = $upload_path1;
            $config1['allowed_types'] = "*";
            // $config1['allowed_types'] = "*";
            $config1['max_size'] = "204800000";
            $img_name1 = strtolower($_FILES[$file_name]['name']);
            $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
            $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);
            $this->upload->do_upload($file_name);
            $fileDetailArray1 = $this->upload->data();
            // echo $this->upload->display_errors();
            // die;
            return $fileDetailArray1['file_name'];
        }
        else
        {
            return 'false';
        }
        
    }
    
    function getVariantsList($product_id)
    {
        $qry = $this->db->query("select * from link_variant where product_id='".$product_id."'");
        $details = $qry->result();
        if($qry->num_rows()>0)
        {
                $var=[];
                    foreach($details as $dat)
                    {
                        $attributes = json_decode($dat->jsondata);
                        $attributelist=[];
                        foreach($attributes as $attr)
                        {
                            $attribute_type=$attr->attribute_type;
                            $attribute_value=$attr->attribute_value;
                                
                                $at_t = $this->db->query("select * from attributes_title where id='".$attr->attribute_type."'");
                                $attribute = $at_t->row();
                                
                                $at_v = $this->db->query("select * from attributes_values where id='".$attr->attribute_value."'");
                                $at_value = $at_v->row();
                            
                            $attributelist[]=array('attribute_type'=>$attribute->title,'attribute_value'=>$at_value->value);
                        }
                        $var[]=array('variant_id'=>$dat->id,'product_id'=>$dat->product_id,'price'=>$dat->price,'saleprice'=>$dat->saleprice,'stock'=>$dat->stock,'attributes'=>$attributelist);
                    }
                    return array('status' =>TRUE, 'variant_list'=>$var);
        }
        else
        {
             return array('status' =>FALSE, 'message'=>"No Variants");
        }
        
        
    }
    
    
     private function upload_file($file_name) {
        if($_FILES[$file_name]["size"]<'5114374')
        {
            $upload_path1 = "./uploads/products/";
            $config1['upload_path'] = $upload_path1;
            $config1['allowed_types'] = "*";
            // $config1['allowed_types'] = "*";
            $config1['max_size'] = "204800000";
            $img_name1 = strtolower($_FILES[$file_name]['name']);
            $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
            $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);
            $this->upload->do_upload($file_name);
            $fileDetailArray1 = $this->upload->data();
            // echo $this->upload->display_errors();
            // die;
            return $fileDetailArray1['file_name'];
        }
        else
        {
            return 'false';
        }
        
    }
    
    
    function getProductImages($product_id,$variant_id)
    {
        $qry = $this->db->query("select * from product_images where product_id='".$product_id."' and variant_id='".$variant_id."'");
        if($qry->num_rows()>0)
        {
            $data = $qry->result();
            foreach($data as $value)
            {
                if($value->image!='')
                {
                    $img = base_url()."uploads/products/".$value->image;
                }
                else
                {
                     $img ="";
                }
                $ar[]=array('id'=>$value->id,'image'=>$img);
            }
            return array('status' =>TRUE, 'images'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Product Images");
        }
    }
    
    function deleteProductImages($variant_id)
    {
        $qry = $this->db->query("select * from product_images where id='".$variant_id."'");
        $row = $qry->row();
        
        $img = "./uploads/products/".$row->image;
        unlink($img);
        $del = $this->db->delete("product_images",array('id'=>$variant_id));
        if($del)
        {
            return array('status' =>TRUE, 'message'=>"Product Image deleted Successfully");
        }
        else
        {
           return array('status' =>FALSE, 'message'=>"Something went wrong");
        }
    }
    
    function updatePrice($ar,$wr)
    {
        $upd = $this->db->update("link_variant",$ar,$wr);
        if($upd)
        {
            $ins = $this->db->insert("stock_management",array('varient_id'=>$wr['id'],'product_id'=>$ar['product_id'],'quantity'=>$ar['stock'],'paid_status'=>'Credit','total_stock'=>$ar['stock'],'message'=>'Stock Added','created_at'=>time() ));
            if($ins)
            {
               return array('status' =>TRUE, 'message'=>"Price Updated successfully");
            }
            
        }
        else
        {
           return array('status' =>FALSE, 'message'=>"Something went wrong");
        }
    }
   
   function addStock($product_id,$variant_id,$quantity,$stockstatus)
   {

       $qry = $this->db->query("select * from link_variant where id='".$variant_id."'");
       $row=$qry->row();
       $total = $row->stock;
       if($stockstatus=='add')
       {
            $ar = array('varient_id'=>$variant_id,'product_id'=>$product_id,'quantity'=>$quantity,'paid_status'=>'Credit','message'=>'Stock Added','total_stock'=>$total,'created_at'=>time());
            $ins = $this->db->insert("stock_management",$ar);
            if($ins)
            {
              $g = $this->db->query("select * from link_variant where id='".$variant_id."'");
                $g_row = $g->row();
                $qty = $g_row->stock+$quantity;
                $upd = $this->db->update("link_variant",array('stock'=>$qty),array('id'=>$variant_id));
                if($upd)
                {
                    return array('status' =>TRUE, 'message'=>"Stock Updated successfully");
                }
                else
                {
                    return array('status' =>FALSE, 'message'=>"Something went wrong,Please try again");
                }  
               
            }
       }
       else if($stockstatus=='remove')
       {
        if($total>=$quantity)
        {
            $ar = array('varient_id'=>$variant_id,'product_id'=>$product_id,'quantity'=>$quantity,'paid_status'=>'Debit','message'=>'Stock Added','total_stock'=>$total,'created_at'=>time());
            $ins = $this->db->insert("stock_management",$ar);

            if($ins)
            {
              $g = $this->db->query("select * from link_variant where id='".$variant_id."'");
                $g_row = $g->row();
                $qty = $g_row->stock-$quantity;
                $upd = $this->db->update("link_variant",array('stock'=>$qty),array('id'=>$variant_id));
                if($upd)
                {
                    return array('status' =>TRUE, 'message'=>"Stock Updated successfully");
                }
                else
                {
                    return array('status' =>FALSE, 'message'=>"Something went wrong,Please try again");
                }  
               
            }
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Please check your quantity");
        }
        
       }
      
       
   }
   
   function getstockManagement($product_id,$variant_id)
   {
       $qry = $this->db->query("select * from stock_management where varient_id='".$variant_id."' and product_id='".$product_id."' order by id desc");
       $row=$qry->result();
       if($qry->num_rows()>0)
       {
           $stock=[];
           foreach($row as $value)
           {
               $prod = $this->db->query("select * from products where id='".$value->product_id."'");
               $prodoct = $prod->row();
       
               $stock[]=array('id'=>$value->id,'product_id'=>$prodoct->name,'paid_status'=>$value->paid_status,'quantity'=>$value->quantity,'total_stock'=>$value->total_stock,'message'=>$value->message,'created_at'=>date('d-m-Y',$value->created_at));
           }
           return array('status' =>TRUE, 'stock'=>$stock);
       }
       else
       {
           return array('status' =>TRUE, 'message'=>"No Stock");
       }
   }

   function shopsList($user_id)
   {
       $qry = $this->db->query("select id,shop_name,shop_logo,owner_name,email,description,mobile,address,city,pincode from vendor_shop where vm_id='".$user_id."'");
       if($qry->num_rows()>0)
       {
            $query_result = $qry->result();
            foreach ($query_result as $shop) {
                    if($shop->shop_logo!='')
                    {
                        $shop->shop_logo = SHOP_LOGOS_PATH . $shop->shop_logo;
                    }
                    else
                    {
                        $shop->shop_logo = '';
                    }
                
            }
            return array('status' =>TRUE, 'shops'=>$query_result,'shops_count'=>$qry->num_rows());
       }
       else
       {
            return array('status' =>FALSE, 'message'=>"No Shops Found",'shops_count'=>$qry->num_rows());
       }

      
   }

   function getProductTags()
   {
       $qry = $this->db->query("select * from tags");
       if($qry->num_rows()>0)
       {
         $tags = $qry->result();
         return array('status' =>TRUE, 'tags'=>$tags);
       }
       else
       {
         return array('status' =>FALSE, 'message'=>"No Product Tags Found");
       }
   }

   function getBrandslist()
   {
       $qry = $this->db->query("select id,brand_name from attr_brands");
       if($qry->num_rows()>0)
       {
         $tags = $qry->result();
         return array('status' =>TRUE, 'brands'=>$tags);
       }
       else
       {
         return array('status' =>FALSE, 'message'=>"No Brands");
       }
   }

   function getTaxList()
   {
         $qry = $this->db->query("select id,title from tax");
       if($qry->num_rows()>0)
       {
         $tags = $qry->result();
         return array('status' =>TRUE, 'tax'=>$tags);
       }
       else
       {
         return array('status' =>FALSE, 'message'=>"No Tax");
       }
   }

   function getAddVariantList($product_id)
   {
      $qry = $this->db->query("select * from add_variant where product_id='".$product_id."'");
      if($qry->num_rows()>0)
      {
        $result = $qry->result();
        $ar=[];
        foreach ($result as $value) 
        {
            $pro = $this->db->query("select * from products where id='".$value->product_id."'");
            $product = $pro->row();

            $attr = $this->db->query("select * from attributes_title where id='".$value->attribute_type."'");
            $attributes = $attr->row();
                $ex = explode(",", $value->attribute_values);
                $at_v=[];
                for ($i=0; $i <count($ex); $i++) 
                { 
                    $attr_va = $this->db->query("select * from attributes_values where id='".$ex[$i]."'");
                    $attr_value = $attr_va->row();
                    $at_v[] = $attr_value->value."";
                }
                $im = implode(",", $at_v);
           $ar[] = array('id'=>$value->id,'product'=>$product->name,'attribute_type'=>$attributes->title,'attribute_values'=>$im,'attribute_values_ids'=>$value->attribute_values,'attribute_type_id'=>$value->attribute_type);
        }
        return array('status' =>TRUE, 'variant_list'=>$ar);
      }
      else
      {
        return array('status' =>FALSE, 'message'=>"No Variants List");
      }
   }



   function updateVariant($pid,$attribute_type_id,$attribute_values,$vid)
    {

        $ar = array(
                'product_id'=>$pid,
                'attribute_type'=>$attribute_type_id,
                'attribute_values'=>$attribute_values,
                'created_at'=>time()
             );
        $wr = array('id'=>$vid);
        $ins = $this->db->update("add_variant",$ar,$wr);
        //echo $this->db->last_query(); die;
        if($ins)
        {
                   /*$where=array('product_id'=>$pid);
                   $del = $this->db->delete("link_variant",$where);
                   if($del)
                   {*/
                       $check_var = $this->db->query("select * from add_variant where product_id='".$pid."'");
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
                                   $ins11 = $this->db->insert("link_variant",array('product_id'=>$pid,'jsondata'=>$jsondata));
                                }
                        }
                    //}

                    $report=array('status'=>TRUE,'message'=>"Variant updated Successfully");
                return $report;
        }
        else
        {
            $report=array('status'=>FALSE,'message'=>"Something went wrong");
                return $report;
        }
       
    }

    function deleteVariant($pid,$vid)
    {
        //echo $vid; echo $pid; die;
        $del = $this->db->delete("add_variant",array('id'=>$vid));
        if($del)
        {
            $this->db->delete("link_variant",array('product_id'=>$pid));
            $this->db->delete("product_images",array('variant_id'=>$vid,'product_id'=>$pid));
            $this->db->delete("stock_management",array('variant_id'=>$vid,'product_id'=>$pid));

            $report=array('status'=>TRUE,'message'=>'Variant Deleted Successfully');
            return $report;
        }
        else
        {
            $report=array('status'=>FALSE,'message'=>'Something went wrong,Please try again');
            return $report;
        }
    }

    function getLinkVariants($pid)
    {
        $mang = $this->db->query("select * from link_variant where product_id='".$pid."'");
        $varint = $mang->result();
        $ar=[];
        if($mang->num_rows()>0)
        {
            foreach ($varint as $value) 
            {
                    $json = json_decode($value->jsondata);
                    $attributes=[];
                    foreach ($json as $value1) 
                    {
                             $att_type_qry = $this->db->query("select * from attributes_title where id='".$value1->attribute_type."'");
                             $types = $att_type_qry->row();
                            $values_qry = $this->db->query("select * from attributes_values where id='".$value1->attribute_value."'");
                            $values = $values_qry->row();
                            $attributes[] = array('type'=>$types->title,'value'=>$values->value);  
                    }

                    $images = $this->db->query("select * from product_images where product_id='".$pid."' and variant_id='".$value->id."'");
                    $images_count = $images->num_rows();
                        if($value->status==0)
                        {
                            $statusname="Inactive";
                        }
                        else
                        {
                            $statusname="Active";
                        }
                    $ar[]=array('id'=>$value->id,'status'=>$value->status,'statusname'=>$statusname,'product_id'=>$value->product_id,'price'=>$value->price,'saleprice'=>$value->saleprice,'stock'=>$value->stock,'images_count'=>$images_count,'attributes'=>$attributes);
            }

            $report=array('status'=>TRUE,'link_variants'=>$ar);
            return $report;
        }
        else
        {
             $report=array('status'=>FALSE,'message'=>"No Link Variants");
            return $report;
        }

        
    }


    function changevariantStatus($vid,$status)
    {
        if($status==0)
        {
            $ar = array('status'=>1);
            $wr = array('id'=>$vid);
            $upd = $this->db->update("link_variant",$ar,$wr);
        }
        else if($status==1)
        {
            $ar = array('status'=>0);
            $wr = array('id'=>$vid);
            $upd = $this->db->update("link_variant",$ar,$wr);
        }
        if($upd)
        {
            $report=array('status'=>TRUE,'message'=>"Status Changed Successfully");
            return $report;
        }
    }

    function updatePassword($login_type,$user_id,$current_password,$new_password)
    {
        if($login_type=='vendor')
        {
            $se =$this->db->query("select * from vendor_shop where id='".$user_id."'");
            if($se->num_rows()>0)
            {
                $vendor = $se->row();
                    $oldpa =$this->db->query("select * from vendor_shop where id='".$user_id."' and password='".md5($current_password)."'");
                    if($oldpa->num_rows()>0)
                    {
                            $ar = array('password'=>md5($new_password));
                            $wr = array('id'=>$user_id);
                            $upd = $this->db->update("vendor_shop",$ar,$wr);
                            if($upd)
                            {
                                 $report=array('status'=>TRUE,'message'=>"Password Changed Successfully");
                                 return $report;
                            }
                    }
                    else
                    {
                           $report=array('status'=>FALSE,'message'=>"Old Password Wrong");
                             return $report;
                    }
                
            }
            else
            {
               $report=array('status'=>FALSE,'message'=>"Invalid User");
                return $report;
            }
            

        }
        else if($login_type=='visual_merchant') 
        {
            $se =$this->db->query("select * from visual_merchant where id='".$user_id."'");
            if($se->num_rows()>0)
            {
                $vendor = $se->row();
                    $oldpa =$this->db->query("select * from visual_merchant where id='".$user_id."' and password='".md5($current_password)."'");
                    if($oldpa->num_rows()>0)
                    {
                            $ar = array('password'=>md5($new_password));
                            $wr = array('id'=>$user_id);
                            $upd = $this->db->update("visual_merchant",$ar,$wr);
                            if($upd)
                            {
                                $report=array('status'=>TRUE,'message'=>"Password Changed Successfully");
                                return $report;
                            }
                    }
                    else
                    {
                            $report=array('status'=>FALSE,'message'=>"Old Password Wrong");
                             return $report;
                    }
                
            }
            else
            {
                 $report=array('status'=>FALSE,'message'=>"Invalid User");
                return $report;
            }
        }

     
    }

    function getTerms($status)
    {
        if($status==1)
        {
            $se =$this->db->query("select id,title,description from content where id='6'");
        }
        else if($status==2)
        {
            $se =$this->db->query("select id,title,description from content where id=2");
        }
        
        return $se->row();
    }


    function fetchOrdersList($vendor_id)
    {

        $qry = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and order_status=1 order by id desc");
        if($qry->num_rows()>0)
        {
            $result = $qry->result();
            $ar=[];
            foreach ($result as $value) 
            {   
                $qry = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $qry->row();
                $name = $users->first_name." ".$users->last_name;

                $ven = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                $vendor = $ven->row();
                
                $adrs = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                $address = $adrs->row();
                $full_address = $address->address.",".$address->locality.",".$address->city.",".$address->state;
                if($value->payment_status==0)
                {
                    $payment_status="UnPaid";
                }
                else
                {
                    $payment_status="Paid";
                }

                if($value->order_status==1)
                {
                    $order_status = "Pending";
                }
                else if($value->order_status==2)
                {
                     $order_status = "Proccessing";
                }
                else if($value->order_status==3)
                {
                     $order_status = "Assigned to delivery to pick up";
                }
                else if($value->order_status==4)
                {
                    $order_status = "Delivery Boy On the way";
                }
                else if($value->order_status==5)
                {
                    $order_status = "Delivered";
                }
                else if($value->order_status==6)
                {
                    $order_status = "Cancelled";
                }
                else if($value->order_status==7)
                {
                    $order_status = "Refund Completed";
                }

                if($value->bid_id==0)
                {
                    $bidstatus=false;
                }
                else
                {
                     $bidstatus=true;
                }
                
                
               $ar[]=array('id'=>$value->id,'session_id'=>$value->session_id,'customer_name'=>$name,'vendor_name'=>$vendor->shop_name,'address'=>$full_address,'payment_status'=>$payment_status,'payment_type'=>$value->payment_option,'service_status'=>$order_status,'amount'=>$value->total_price,'created_date'=>date('d-m-Y',$value->created_at),'bid_status'=>$bidstatus);
            }
            return array('status' =>TRUE, 'orders'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Orders");
        }
    
    }

    function fetchProcessingOrdersList($vendor_id)
    {

        $qry = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and order_status=2 order by id desc");
        if($qry->num_rows()>0)
        {
            $result = $qry->result();
            $ar=[];
            foreach ($result as $value) 
            {   
                $qry = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $qry->row();
                $name = $users->first_name." ".$users->last_name;

                $ven = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                $vendor = $ven->row();
                
                $adrs = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                $address = $adrs->row();
                $full_address = $address->address.",".$address->locality.",".$address->city.",".$address->state;
                if($value->payment_status==0)
                {
                    $payment_status="UnPaid";
                }
                else
                {
                    $payment_status="Paid";
                }

                if($value->order_status==1)
                {
                    $order_status = "Pending";
                }
                else if($value->order_status==2)
                {
                     $order_status = "Proccessing";
                }
                else if($value->order_status==3)
                {
                     $order_status = "Assigned to delivery to pick up";
                }
                else if($value->order_status==4)
                {
                    $order_status = "Delivery Boy On the way";
                }
                else if($value->order_status==5)
                {
                    $order_status = "Delivered";
                }
                else if($value->order_status==6)
                {
                    $order_status = "Cancelled";
                }
                else if($value->order_status==7)
                {
                    $order_status = "Refund Completed";
                }
                
                 if($value->order_status==2)
                {
                    if($value->accept_status==1)
                    {
                        $del_status ="SELF Delivery";
                         $accept_status=1;
                    }
                    else if($value->accept_status==0)
                    {
                        $del_status ="A3 Services Delivery";
                        $accept_status=0;
                    }
                    
                }
                else
                {
                     $accept_status=0;
                }

               $ar[]=array('id'=>$value->id,'session_id'=>$value->session_id,'customer_name'=>$name,'vendor_name'=>$vendor->shop_name,'address'=>$full_address,'payment_status'=>$payment_status,'payment_type'=>$value->payment_option,'service_status'=>$order_status,'amount'=>$value->total_price,'created_date'=>date('d-m-Y',$value->created_at),'del_status'=>$del_status,'accept_status'=>$accept_status);
            }
            return array('status' =>TRUE, 'orders'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Orders");
        }
    
    

    }




    function fetchTransmitOrdersList($vendor_id)
    {

        $qry = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and order_status=3 order by id desc");
        if($qry->num_rows()>0)
        {
            $result = $qry->result();
            $ar=[];
            foreach ($result as $value) 
            {   
                $qry = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $qry->row();
                $name = $users->first_name." ".$users->last_name;

                $ven = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                $vendor = $ven->row();
                
                $adrs = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                $address = $adrs->row();
                $full_address = $address->address.",".$address->locality.",".$address->city.",".$address->state;
                if($value->payment_status==0)
                {
                    $payment_status="UnPaid";
                }
                else
                {
                    $payment_status="Paid";
                }

                if($value->order_status==1)
                {
                    $order_status = "Pending";
                }
                else if($value->order_status==2)
                {
                     $order_status = "Proccessing";
                }
                else if($value->order_status==3)
                {
                     $order_status = "Assigned to delivery to pick up";
                }
                else if($value->order_status==4)
                {
                    $order_status = "Delivery Boy On the way";
                }
                else if($value->order_status==5)
                {
                    $order_status = "Delivered";
                }
                else if($value->order_status==6)
                {
                    $order_status = "Cancelled";
                }
                else if($value->order_status==7)
                {
                    $order_status = "Refund Completed";
                }
                
               $ar[]=array('id'=>$value->id,'session_id'=>$value->session_id,'customer_name'=>$name,'vendor_name'=>$vendor->shop_name,'address'=>$full_address,'payment_status'=>$payment_status,'payment_type'=>$value->payment_option,'service_status'=>$order_status,'amount'=>$value->total_price,'created_date'=>date('d-m-Y',$value->created_at));
            }
            return array('status' =>TRUE, 'orders'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Orders");
        }
    }


    

    function fetchCompletedOrdersList($vendor_id)
    {
        $qry = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and order_status in (5,6) order by id desc");
        if($qry->num_rows()>0)
        {
            $result = $qry->result();
            $ar=[];
            foreach ($result as $value) 
            {   
                $qry = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $qry->row();
                $name = $users->first_name." ".$users->last_name;

                $ven = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                $vendor = $ven->row();
                
                $adrs = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                $address = $adrs->row();
                $full_address = $address->address.",".$address->locality.",".$address->city.",".$address->state;
                if($value->payment_status==0)
                {
                    $payment_status="UnPaid";
                }
                else
                {
                    $payment_status="Paid";
                }

                if($value->order_status==1)
                {
                    $order_status = "Pending";
                }
                else if($value->order_status==2)
                {
                     $order_status = "Proccessing";
                }
                else if($value->order_status==3)
                {
                     $order_status = "Assigned to delivery to pick up";
                }
                else if($value->order_status==4)
                {
                    $order_status = "Delivery Boy On the way";
                }
                else if($value->order_status==5)
                {
                    $order_status = "Delivered";
                }
                else if($value->order_status==6)
                {
                    $order_status = "Cancelled";
                }
                else if($value->order_status==7)
                {
                    $order_status = "Refund Completed";
                }
                
               $ar[]=array('id'=>$value->id,'session_id'=>$value->session_id,'customer_name'=>$name,'vendor_name'=>$vendor->shop_name,'address'=>$full_address,'payment_status'=>$payment_status,'payment_type'=>$value->payment_option,'service_status'=>$order_status,'amount'=>$value->total_price,'created_date'=>date('d-m-Y',$value->created_at));
            }
            return array('status' =>TRUE, 'orders'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Orders");
        }
    }


    function pendingSettlements($vendor_id)
    {
        $req = $this->db->query("select * from request_payment where vendor_id='".$vendor_id."' and status=0");
        if($req->num_rows()>0)
        {
                $variable = $req->result();
                $ar=[];
                foreach ($variable as $value) 
                {
                        if($value->status==0)
                        {
                            $status = "Pending";
                        }
                        else if($value->status==1)
                        {
                            $status = "Payment Completed";
                        }
                    $ar[]=array('id'=>$value->id,'vendor_amount'=>$value->vendor_amount,'request_amount'=>$value->request_amount,'description'=>$value->description,'status'=>$status,'created_date'=>date('d-m-Y',$value->created_at));
                }
                 return array('status' =>TRUE, 'settlements'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Pending Settlements");
        }
    }

     function completedSettlements($vendor_id)
    {
        $req = $this->db->query("select * from request_payment where vendor_id='".$vendor_id."' and status=1");
        if($req->num_rows()>0)
        {
                $variable = $req->result();
                $ar=[];
                foreach ($variable as $value) 
                {
                        if($value->status==0)
                        {
                            $status = "Pending";
                        }
                        else if($value->status==1)
                        {
                            $status = "Payment Completed";
                        }

                        $img =base_url()."uploads/payments/".$value->image;
                    $ar[]=array('id'=>$value->id,'vendor_amount'=>$value->vendor_amount,'request_amount'=>$value->request_amount,'description'=>$value->description,'status'=>$status,'created_date'=>date('d-m-Y',$value->created_at),'payment_date'=>date('d-m-Y',$value->updated_at),'mode_payment'=>$value->mode_payment,'transaction_id'=>$value->transaction_id,'image'=>$img,'admin_description'=>$value->admin_description);
                }
                 return array('status' =>TRUE, 'settlements'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Completed Settlements");
        }
    }

    function getOrdersDetails($oid)
    {
        $qry = $this->db->query("select * from orders where id='".$oid."'");
        if($qry->num_rows()>0)
        {
            $value = $qry->row();
                            $cart = $this->db->query("select * from cart where session_id='".$value->session_id."'");
                            $cartdetails = $cart->result();
                            $cartdata=[];
                            foreach ($cartdetails as $c) 
                            {
                                    $var = $this->db->query("select * from link_variant where id='".$c->variant_id."'");
                                    $variants = $var->row();
                                    $pro = $this->db->query("select * from products where id='".$variants->product_id."'");
                                    $products = $pro->row();

                                    $pro_img = $this->db->query("select * from product_images where variant_id='".$c->variant_id."' and product_id='".$variants->product_id."'");
                                    $pro_imgs = $pro_img->row();

                                    if($pro_imgs->image!='')
                                    {
                                        $image = base_url()."uploads/products/".$pro_imgs->image;
                                    }
                                    else
                                    {
                                        $image = base_url()."uploads/noproduct.png";
                                    }

                                    $jsondata = json_decode($variants->jsondata);
                                    $attributes=[];
                                    foreach ($jsondata as $val) 
                                    {
                                        $attribute_type=$val->attribute_type;
                                        $attribute_value=$val->attribute_value;

                                        $type = $this->db->query("select * from attributes_title where id='".$attribute_type."'");
                                        $types = $type->row();

                                        $val12 = $this->db->query("select * from attributes_values where id='".$attribute_value."'");
                                        $value12 = $val12->row();
                                        $attributes[]=array('attribute_type'=>$types->title,'attribute_values'=>$value12->value);
                                    }

                                $cartdata[]=array('cartid'=>$c->id,'image'=>$image,'productname'=>$products->name,'price'=>$c->price,'quantity'=>$c->quantity,'unit_price'=>$c->unit_price,'attributes'=>$attributes);
                            }

                 $add = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                 $address = $add->row();

                 $user_full_address = $address->address."".$address->locality."".$address->state."".$address->city."".$address->pincode;

                $qry = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $qry->row();
                $name = $users->first_name." ".$users->last_name;

                $ven = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                $vendor = $ven->row();
                
                $adrs = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                $address = $adrs->row();



                $city_qry = $this->db->query("select * from cities where id='".$address->city."'");
                $city_row = $city_qry->row();

                $state_qry = $this->db->query("select * from states where id='".$address->state."'");
                $state_row = $state_qry->row();

                $area_qry = $this->db->query("select * from areas where id='".$address->area."'");
                $area_row = $area_qry->row();

                if($adrs->num_rows()>0)
                {
                    $full_address = $state_row->state_name.", ".$city_row->city_name.", ".$area_row->area.", ".$address->address." -".$address->pincode;
                }
                else
                {
                    $full_address = "";
                }
                
                if($value->payment_status==0)
                {
                    $payment_status="UnPaid";
                }
                else
                {
                    $payment_status="Paid";
                }

                if($value->order_status==1)
                {
                    $order_status = "Pending";
                }
                else if($value->order_status==2)
                {
                     $order_status = "Proccessing";
                }
                else if($value->order_status==3)
                {
                     $order_status = "Assigned to delivery to pick up";
                }
                else if($value->order_status==4)
                {
                    $order_status = "Delivery Boy On the way";
                }
                else if($value->order_status==5)
                {
                    $order_status = "Delivered";
                }
                else if($value->order_status==6)
                {
                    $order_status = "Cancelled";
                }
                else if($value->order_status==7)
                {
                    $order_status = "Refund Completed";
                }
                

                if($value->accept_status==1){
                    $accept_status = "Self";
                }
                else{
                    $accept_status = "Admin";
                }


                /*if($value->coupon_id==0)
                {
                     $coupon_disount="0";   
                }
                else
                {
                    
                    $coupon_disount=$value->coupon_disount;
                }*/

                 if($value->coupon_id==0)
                {
                    $coupon_disount="0";
                    $sub_t = $value->sub_total;
                    $amount=$sub_t+$value->gst+$value->deliveryboy_commission;
                }
                else
                {
                    $coupon_disount=$value->coupon_disount;

                    $sub_t = $value->sub_total-$coupon_disount;
                    $amount=$sub_t+$value->gst+$value->deliveryboy_commission;
                }
                

                $deliv = $this->db->query("select * from deliveryboy where id='".$value->delivery_boy."'");
                if($deliv->num_rows()>0)
                {
                    $delivery_boy = $deliv->row();
                    $dl_name = $delivery_boy->name;
                    $dl_phone = $delivery_boy->phone;
                    $alternative_mobiles = $delivery_boy->alternative_mobiles;
                }
                else
                {
                    $dl_name = "";
                    $dl_phone = "";
                    $alternative_mobiles = "";
                }
                
                if($value->order_status==3 || $value->order_status==4 || $value->order_status==5)
                {
                    $show =  'show';
                }
                else
                {
                    $show =  'hide';
                }

               $ar=array('id'=>$value->id,'session_id'=>$value->session_id,'delivery_date'=>$value->delivery_timeslots,'gst'=>$value->gst,'order_status'=>$order_status,'vendor_name'=>$vendor->shop_name,'address'=>$full_address,'payment_status'=>$payment_status,'payment_type'=>$value->payment_option,'amount'=>$amount,'sub_total'=>$value->sub_total,'placed_on'=>date('d-m-Y',$value->created_at),'cartdetails'=>$cartdata,'customer_name'=>$name,'mobile'=>$address->mobile,'coupon_disount'=>$coupon_disount,'deliveryboy_commission'=>$value->deliveryboy_commission,'delivery_name'=>$dl_name,'delivery_phone'=>$dl_phone,'alternative_mobiles'=>$alternative_mobiles,'order_status'=>$show,'accept_status'=>$accept_status);
            
            return array('status' =>TRUE, 'ordersdetails'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Order ID Wrong");
        }
    
    }

    function fetchdashboardDetails($vendor_id)
    {   
        $qry11 = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and order_status=5");
        $result = $qry11->result();
        $vendor_amount=0;
        foreach ($result as $value) 
        {

                $cart_qry = $this->db->query("select * from cart where session_id='".$value->session_id."'");
                                              $cart_result = $cart_qry->result();
                                              $admin_total=0;
                                              $unit_price=0;
                                              foreach ($cart_result as $cart_value) 
                                              { 
                                                $link_qry = $this->db->query("select * from link_variant where id='".$cart_value->variant_id."'");
                                                $link_row = $link_qry->row();

                                                $prod_qry = $this->db->query("select * from products where id='".$link_row->product_id."'");
                                                $prod_row = $prod_qry->row();

                                                $cat_id = $prod_row->cat_id;
                                                $sub_cat_id = $prod_row->sub_cat_id;
                                                $cart_vendor_id = $cart_value->vendor_id;

                                            $adminc_qry = $this->db->query("select * from admin_comissions where shop_id='".$cart_vendor_id."' and cat_id='".$cat_id."' and find_in_set('".$sub_cat_id."',subcategory_ids)");
                                            $adminc_row = $adminc_qry->row();
                                                    $cat_qry = $this->db->query("select * from categories where id='".$cat_id."'");
                                                    $cat_row = $cat_qry->row();

                                                    $scat_qry = $this->db->query("select * from sub_categories where id='".$sub_cat_id."'");
                                                    $scat_row = $scat_qry->row();


                                                    if($adminc_row->admin_comission!='')
                                                    {
                                                        $admin_comission=$adminc_row->admin_comission;
                                                    }
                                                    else
                                                    {
                                                        $admin_comission=0;
                                                    }


                                                    $percentage = ($cart_value->unit_price/100)*$admin_comission; 
                                                    $admin_total = $percentage+$admin_total;
                                                    $unit_price=$cart_value->unit_price+$unit_price;
                                         }

                                        if($value->coupon_id==0)
                                        {
                                            $coupon_disount="0";
                                            $sub_t = $unit_price;
                                            $totala=$sub_t;
                                        }
                                        else
                                        {
                                            $coponqry = $this->db->query("select * from coupon_codes where id='".$value->coupon_id."'");
                                            $couonrow = $coponqry->row();
                                            if($couonrow->shop_id==0)
                                            {
                                                $coupon_disount=0;
                                                $sub_t = $unit_price-$coupon_disount;
                                                $totala=$sub_t;
                                            }
                                            else
                                            {
                                                $coupon_disount=$value->coupon_disount;
                                                $sub_t = $unit_price-$coupon_disount;
                                                $totala=$sub_t;
                                            }

                                            
                                        }

                                         //$totala = $unit_price+$value->deliveryboy_commission;

                                

                                $vendor_amount1 = $totala-$admin_total;
                                $vendor_amount += $vendor_amount1;
                //$vendor_amount += $value->vendor_commission;
        
        
                //$vendor_amount += $value->vendor_commission;
        }



        $chk_vend = $this->db->query("select * from vendor_payements where vendor_id='".$vendor_id."'");
        if($chk_vend->num_rows()>0)
        {
                $array = array('vendor_id'=>$vendor_id,'total_payment'=>$vendor_amount);
                $where = array('vendor_id'=>$vendor_id);
                $this->db->update("vendor_payements",$array,$where);
        }
        else
        {
                $array = array('vendor_id'=>$vendor_id,'total_payment'=>$vendor_amount);
                $this->db->insert("vendor_payements",$array);
        }


        $ord = $this->db->query("select * from orders where vendor_id='".$vendor_id."'");
        $total_orders = $ord->num_rows(); 

        $date = date('Y-m-d');
        $today = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and created_date LIKE '%".$date."%'");
        $today_orders = $today->num_rows();

        $prod = $this->db->query("select * from products where shop_id='".$vendor_id."'");
        $prod_total = $prod->num_rows();

        $category = $this->db->query("select * from admin_comissions where shop_id='".$vendor_id."'");
        $category_total = $category->num_rows();

        $vendor_payment = $this->db->query("select * from vendor_payements where vendor_id='".$vendor_id."'");
        $vendor_payments = $vendor_payment->row();

        $shop_visit = $this->db->query("select * from shop_visit where shop_id='".$vendor_id."'");
        $shop_visits = $shop_visit->num_rows();
        
         
        $pending = $vendor_payments->total_payment-$vendor_payments->requested_amount;

       // $vnd_cont = $this->getvendorBidCount($vendor_id);
        $bidqry = $this->db->query("SELECT user_bids.id FROM user_bids INNER JOIN vendor_bids ON vendor_bids.bid_id=user_bids.id where vendor_bids.vendor_id='".$vendor_id."' and user_bids.bid_status in (0,1)");   
        $vnd_cont = $bidqry->num_rows();


    return array('status' =>TRUE, 'total_orders'=>$total_orders,'today_orders'=>$today_orders,'bids_count'=>$vnd_cont,'products_total'=>$prod_total,'categories'=>$category_total,'vendor_total'=>$vendor_payments->total_payment,'pending_amount'=>$pending,'shop_visits'=>$shop_visits);
    }


function getvendorBidCount($vendor_id)
{
        $qry = $this->db->query("SELECT * from vendor_bids WHERE vendor_id='".$vendor_id."' and bid_status=0");   
        return $qry->num_rows();

}


    function fetchVendorStatus($vendor_id,$tokenId)
    {
        $this->db->update("vendor_shop",array('token'=>$tokenId),array('id'=>$vendor_id));
        
        $ord = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and order_status=1");
        $num_rows = $ord->num_rows();
        $qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
        if($qry->num_rows()>0)
        {
            $row = $qry->row();
            if($row->status==1)
            {
                $status=true;
            }
            else
            {
                $status=false;
            }
            return array('status' =>TRUE,'status'=>$status,'orders'=>$num_rows);

        }
        else
        {
            return array('status' =>FALSE,'message'=>"Invalid User ID",'orders'=>$num_rows);
        }
    }

    function changeVendorStatus($vendor_id,$status)
    {
        $qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."' and vendor_verification_status=1");
        if($qry->num_rows()>0)
        {
            if($status=="false")
            {
                $stat="1";
                $ar = array("status"=>$stat);
                 $wr = array("id"=>$vendor_id);
                $upd = $this->db->update("vendor_shop",$ar,$wr);
                return array('status' =>TRUE,'message'=>"Your Shop Will Open");
            }
            else if($status=="true")
            { 
                $stat="0";

                $ar = array("status"=>$stat);
                $wr = array("id"=>$vendor_id);
                $upd = $this->db->update("vendor_shop",$ar,$wr);
                return array('status' =>TRUE,'message'=>"Your Shop Will Closed");
            }

            
        }
        else
        {
            return array('status' =>FALSE,'message'=>"Please contact admin");
        }
    }

    function getShopWorkingHours($vendor_id)
    {
           $qry = $this->db->query("select * from working_hours where vendor_id='".$vendor_id."'");
            if($qry->num_rows()>0)
            {
                $result = $qry->result();
                $ar=[];
                foreach ($result as $value) 
                {
                    $ar[]=array('id'=>$value->id,'weekname'=>$value->weekname,'working'=>$value->working,'open_time'=>date('H:i',strtotime($value->open_time)),'closed_time'=>date('H:i',strtotime($value->closed_time)));
                }
                
                return array('status' =>TRUE,'working_hours'=>$ar);
            }
            else
            {
                return array('status' =>FALSE,'message'=>"No Working Hours");
            }
    }

    function createBusinessHours($vendor_id,$open_time,$closed_time,$weekname,$working)
    {
        $chk = $this->db->query("select * from working_hours where vendor_id='".$vendor_id."' and weekname='".$weekname."'");
        if($chk->num_rows()>0)
        {
            return array('status' =>FALSE,'message'=>"Already added weekday");
        }
        else
        {
               $ar = array('vendor_id'=>$vendor_id,'open_time'=>$open_time,'closed_time'=>$closed_time,'weekname'=>$weekname,'working'=>$working);
                $ins = $this->db->insert("working_hours",$ar);
                if($ins)
                {
                    return array('status' =>TRUE,'message'=>"Business Hours added Successfully");
                }
                else
                {
                    return array('status' =>FALSE,'message'=>"Something went wrong, Please try Again");
                }
        }
    }

    function updateBusinessHours($vendor_id,$open_time,$closed_time,$weekname,$working,$wid)
    {
        $chk = $this->db->query("select * from working_hours where id!='".$wid."' and weekname='".$weekname."' and vendor_id='".$vendor_id."'");
        if($chk->num_rows()>0)
        {
            return array('status' =>FALSE,'message'=>"Already added weekday");
        }
        else
        {
               $ar = array('vendor_id'=>$vendor_id,'open_time'=>$open_time,'closed_time'=>$closed_time,'weekname'=>$weekname,'working'=>$working);
               $wr = array('id'=>$wid);
                $ins = $this->db->update("working_hours",$ar,$wr);
                if($ins)
                {
                    return array('status' =>TRUE,'message'=>"Business Hours updated Successfully");
                }
                else
                {
                    return array('status' =>FALSE,'message'=>"Something went wrong, Please try Again");
                }
        }
    }
function doacceptOrder($vendor_id,$orderid,$delivery_type)
    {
        $qry = $this->db->query("select * from orders where id='".$orderid."'");
        $order_row = $qry->row();
        if($order_row->order_status==6)
        {
            return array('status' =>FALSE,'message'=>"Order already cancelled");
        }
        
        if($delivery_type=='self')
        {

                
                        $user_id = $order_row->user_id;
                    $user_qry = $this->db->query("select * from users where id='".$order_row->user_id."'");
                    $user_row = $user_qry->row();

                    $phone = $user_row->phone;
                    $user_name = $user_row->first_name;

                    $vendor_qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
                    $vendor_row = $vendor_qry->row();

                        $ar = array('order_status'=>'2','accept_status'=>'1');

                        $wr = array('id'=>$orderid);

                        

                        $upd = $this->db->update("orders",$ar,$wr);
                        if($upd)
                        {
                             

                        $title = "Order Accepted";
                        $message = "Your Order Accepted";
                            $this->onesignalnotification($user_id,$message,$title);
                                                
                                            $user_phone = $user_row->mobile;
                                            $user_name = $user_row->first_name;
                                            $user_order_message = "Dear ".$user_name." your order is ".$orderid." is accepted by the vendor and it will be delivered with in 2-24hrs of time. Thank u for shopping with A3 Services.";
                                            $template_id = '1407161683232344120';
                                            if($this->send_message($user_order_message,$user_phone,$template_id))
                                            {
                                               $this->db->insert("sms_notifications",array('order_id'=>$orderid,'receiver_id'=>$user_row->id,'sender_id'=>$vendor_id,'created_at'=>time(),'message'=>$user_order_message,'action_person'=>1)); 
                                               $user_order_message1 = "Self Delivery";

                                               $this->db->insert("sms_notifications",array('order_id'=>$orderid,'receiver_id'=>$user_row->id,'sender_id'=>$vendor_id,'created_at'=>time(),'message'=>$user_order_message,'action_person'=>1,'order_status'=>$user_order_message1)); 
                                            }



                            $msg = "Order Accepted by ";
                            $aar = array('vendor_id'=>$vendor_id,'order_id'=>$orderid,'message'=>$msg);
                            $this->db->insert("admin_notifications",$aar);

                             return array('status' =>TRUE,'message'=>"Order Accepted Successfully");
                        }
                        else
                        {
                            return array('status' =>FALSE,'message'=>"Something went wrong, Please try again");
                        }
        
        }
        else if($delivery_type=='retos')
        {
                $qry = $this->db->query("select * from orders where id='".$orderid."'");
                    $order_row = $qry->row();
                        $user_id = $order_row->user_id;
                    $user_qry = $this->db->query("select * from users where id='".$order_row->user_id."'");
                    $user_row = $user_qry->row();

                    $phone = $user_row->phone;
                    $user_name = $user_row->first_name;

                    $vendor_qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
                    $vendor_row = $vendor_qry->row();

                        $ar = array('order_status'=>'2');

                        $wr = array('id'=>$orderid);

                        $upd = $this->db->update("orders",$ar,$wr);
                        if($upd)
                        {
                             
$title = "Order Accepted";
                        $message = "Your Order Accepted";

                            $this->onesignalnotification($user_id,$message,$title);
                                                $to_mail = $user_row->email;

                                            $user_phone = $user_row->mobile;
                                            $user_name = $user_row->first_name;
                                            $user_order_message = "Dear ".$user_name." your order is ".$orderid." is accepted by the vendor and it will be delivered with in 2-24hrs of time. Thank u for shopping with A3 Services.";
                                            $template_id = '1407161683232344120';
                                            if($this->send_message($user_order_message,$user_phone,$template_id))
                                            {
                                               $this->db->insert("sms_notifications",array('order_id'=>$orderid,'receiver_id'=>$user_row->id,'sender_id'=>$vendor_id,'created_at'=>time(),'message'=>$user_order_message,'action_person'=>1));

                                               $user_order_message1 = "Vendor Accepted ( A3 Services Delivery)";
                                               $this->db->insert("sms_notifications",array('order_id'=>$orderid,'receiver_id'=>$user_row->id,'sender_id'=>$vendor_id,'created_at'=>time(),'message'=>$user_order_message1,'action_person'=>1,'order_status'=>$user_order_message1)); 
                                            }



                            $msg = "Order Accepted by ";
                            $aar = array('vendor_id'=>$vendor_id,'order_id'=>$orderid,'message'=>$msg);
                            $this->db->insert("admin_notifications",$aar);

                             return array('status' =>TRUE,'message'=>"Order Accepted Successfully");
                        }
                        else
                        {
                            return array('status' =>FALSE,'message'=>"Something went wrong, Please try again");
                        }
        }

        
    }




     function onesignalnotification($user_id,$message,$title)
    {
        $qr = $this->db->query("select * from users where id='".$user_id."'");
        $res = $qr->row();
                            
                       $platform = $res->platform;

                       if($platform=='android')
                       {
                            if($res->token!='')
                            { 
                                $user_id = $res->token;
                        
                                $fields = array(
                                    'app_id' => 'e072cc7b-595d-4c4c-a451-b07832b073f9',
                                    'include_player_ids' => [$user_id],
                                    'contents' => array("en" =>$message),
                                    'headings' => array("en"=>$title),
                                    'android_channel_id' => 'ea6c19aa-e55f-4243-af28-605a32901234'
                                );
                                    $fields = json_encode($fields);
                                    //print("\nJSON sent:\n");
                                    //print($fields);

                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                        'Content-Type: application/json; charset=utf-8', 
                                        'Authorization: Basic NzhjMmI5YjItZmViMy00YjNlLWFlMDItY2ZiZTI3OTY0YzYz'
                                    ));
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                                    curl_setopt($ch, CURLOPT_POST, TRUE);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                                                                           
                                    $response = curl_exec($ch);
                                    curl_close($ch);
                                    //print_r($response); die;
                                }
                       }
                       else if($platform=='ios')
                       {
                            $user_id = $res->ios_token;
                        
                            $fields = array(
                                'app_id' => 'e072cc7b-595d-4c4c-a451-b07832b073f9',
                                'include_player_ids' => [$user_id],
                                'contents' => array("en" =>$message),
                                'headings' => array("en"=>$title),
                                'android_channel_id' => 'cd5a40e2-504d-44b2-97b8-26ffecf88eed'
                            );
                               $fields = json_encode($fields);
                                //print("\nJSON sent:\n");
                                //print($fields);

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Content-Type: application/json; charset=utf-8', 
                                    'Authorization: Basic NzhjMmI5YjItZmViMy00YjNlLWFlMDItY2ZiZTI3OTY0YzYz'
                                ));
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                                curl_setopt($ch, CURLOPT_POST, TRUE);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                                                                       
                                $response = curl_exec($ch);
                                curl_close($ch);
                                //print_r($response); die;
                       }
                        

                  
             
    }



    function docancelOrder($vendor_id,$orderid)
    {
            $ar = array('order_status'=>'6');
            $wr = array('id'=>$orderid);
            $upd = $this->db->update("orders",$ar,$wr);
            if($upd)
            {
				$qry = $this->db->query("select * from orders where id='".$orderid."'");
                $row = $qry->row();

                $user_id=$row->user_id;

                $user_qry = $this->db->query("select * from users where id='".$user_id."'");
                $user_row = $user_qry->row();
				$title = "Order Cancelled";
                        $message = "Your Order : ".$row->id." Cancelled by Vendor";
                            $this->onesignalnotification($row->user_id,$message,$title);
							
                $msg = "Order Cancelled by Vendor";
                $aar = array('vendor_id'=>$vendor_id,'order_id'=>$orderid,'message'=>$msg);
                $this->db->insert("admin_notifications",$aar);

                $name = $user_row->first_name." ".$user_row->last_name;
                $msg1="Dear".$name." your order no. ".$orderid." is requested for cancellation, pls wait for vendors confirmation.";

                $phone = $user_row->phone;
                $template_id = "1407161900318144516";
                $this->send_message($msg1,$phone,$template_id);

                $order_status = 'Order Cancelled by vendor';
                $this->db->insert("sms_notifications",array('order_id'=>$orderid,'receiver_id'=>$user_id,'sender_id'=>$vendor_id,'created_at'=>time(),'message'=>$msg1,'action_person'=>1,'order_status'=>$order_status)); 


                 return array('status' =>TRUE,'message'=>"Order Cancelled Successfully");
            }
            else
            {
                return array('status' =>FALSE,'message'=>"Something went wrong, Please try again");
            }
    }


     function completeOrder($vendor_id,$orderid)
    {
            $ar = array('order_status'=>'5','payment_status'=>'1');
            $wr = array('id'=>$orderid);
            $upd = $this->db->update("orders",$ar,$wr);
            
            if($upd)
            {
                $msg = "Order Delivered By ";
                $aar = array('vendor_id'=>$vendor_id,'order_id'=>$orderid,'message'=>$msg);
                $this->db->insert("admin_notifications",$aar);


                $qry = $this->db->query("select * from orders where id='".$orderid."'");
                $row = $qry->row();

                $user_id=$row->user_id;
                $order_status = "Order Completed";
                $this->db->insert("sms_notifications",array('order_id'=>$orderid,'receiver_id'=>$user_id,'sender_id'=>$vendor_id,'created_at'=>time(),'message'=>$msg,'action_person'=>1,'order_status'=>$order_status)); 


                 return array('status' =>TRUE,'message'=>"Order Delivered Successfully");
            }
            else
            {
                return array('status' =>FALSE,'message'=>"Something went wrong, Please try again");
            }
    }

    function getVendorProfile($vendor_id)
    {
        $qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
        $row = $qry->row();
        if($row->shop_logo!='')
        {
            $shopimg = base_url()."uploads/shops/".$row->shop_logo;
        }
        else
        {
            $shopimg = base_url()."uploads/noproduct.png";
        }
        
        if($row->logo!='')
        {
            $shoplogo = base_url()."uploads/shops/".$row->logo;
        }
        else
        {
            $shoplogo = base_url()."uploads/noproduct.png";
        }
        
        if($row->update_status!='')
        {
            $update_status=$row->update_status;
        }
        else
        {
            $update_status=0;
        }
        $ar = array('id'=>$row->id,'shop_name'=>$row->shop_name,'delivery_charges'=>$row->min_order_amount,'owner_name'=>$row->owner_name,'email'=>$row->email,'description'=>$row->description,'mobile'=>$row->mobile,'address'=>$row->address,'city'=>$row->city,'pincode'=>$row->pincode,'shop_image'=>$shopimg,'shop_logo'=>$shoplogo,'min_order_amount'=>$row->min_order_amount,'delivery_time'=>$row->delivery_time,'pan'=>$row->pan,'aadhar'=>$row->aadhar,'gst_number'=>$row->gst_number,'bankname'=>$row->bankname,'account_no'=>$row->account_no,'accountholder_name'=>$row->accountholder_name,'bank_ifsccode'=>$row->bank_ifsccode,'alternative_mobile'=>$row->alternative_mobile,'update_status'=>$update_status);
        return $ar;
    }

    function getMarchantProfile($vm_id)
    {

        $qry = $this->db->query("select * from visual_merchant where id='".$vm_id."'");
        $row = $qry->row();
        
        $ar = array('id'=>$row->id,'name'=>$row->name,'email'=>$row->email,'mobile'=>$row->mobile,'address'=>$row->city);
        return $ar;
    }


    function updateShopImage($vendor_id)
    {
        $image = $this->chnageCoverPhoto('image');
        
          $ins = $this->db->update("vendor_shop",array('shop_logo'=>$image),array('id'=>$vendor_id));
          //echo $this->db->last_query(); die;
          if($ins)
          {
              return $image;
          }
          else
          {
              return 'false';
          }
    }

    function updatevmProfile($vm_id,$name,$address)
    {
        $upd = $this->db->update("visual_merchant",array('name'=>$name,'city'=>$address),array('id'=>$vm_id));
        if($upd)
        {
            return array('status' =>TRUE,'message'=>"Profile Updated Successfully");
        }
        else
        {
            return array('status' =>FALSE,'message'=>"Something went wrong, Please try again");
        }
    }


     private function chnageCoverPhoto($file_name) {
        /*if($_FILES[$file_name]["size"]<'5114374')
        {*/
            $upload_path1 = "./uploads/shops/";
            $config1['upload_path'] = $upload_path1;
            $config1['allowed_types'] = "*";
            // $config1['allowed_types'] = "*";
            $config1['max_size'] = "204800000";
            $img_name1 = strtolower($_FILES[$file_name]['name']);
            $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
            $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);
            $this->upload->do_upload($file_name);
            $fileDetailArray1 = $this->upload->data();
            // echo $this->upload->display_errors();
            // die;
            return $fileDetailArray1['file_name'];
        /*}
        else
        {
            return 'false';
        }*/
        
    }

    function updateLogoImage($vendor_id)
    {
          $image = $this->chnageCoverPhoto('image');
          $ins = $this->db->update("vendor_shop",array('logo'=>$image),array('id'=>$vendor_id));
          if($ins)
          {
              return $image;
          }
          else
          {
              return 'false';
          }
    }

    function updateProfileDetails($vendor_id,$shop_name,$owner_name,$description,$address,$alternative_mobile,$pan,$aadhar,$gst_number,$bankname,$accountholder_name,$bank_ifsccode,$account_no,$delivery_charges)
    {
        if($description!='')
        {
           $des = $description;
        }
        else
        {
            $des = "";
        }
        if($address!='')
        {
            $adrs = $address;
        }
        else
        {
            $adrs = '';
        }

        if($alternative_mobile!='')
        {
            $altr_mobile = $alternative_mobile;
        }
        else
        {
            $altr_mobile = "";
        }

        if($pan!='')
        {
            $pan1=$pan;
        }
        else
        {
            $pan1="";
        }

        if($aadhar!='')
        {
            $aadhar1=$aadhar;
        }
        else
        {
            $aadhar1="";
        }

        if($gst_number!='')
        {
            $gst_number1=$gst_number;
        }
        else
        {
            $gst_number1="";
        }

        if($bankname!='')
        {
            $bankname1=$bankname;
        }
        else
        {
            $bankname1="";
        }             
        if($accountholder_name!='')
        {
            $accountholder_name1=$accountholder_name;
        }
        else
        {
            $accountholder_name1="";
        }

        if($bank_ifsccode!='')
        {
            $bank_ifsccode1=$bank_ifsccode;
        }
        else
        {
            $bank_ifsccode1="";
        }

        if($account_no!='')
        {
            $account_no1=$account_no;
        }
        else
        {
            $account_no1="";
        }

        if($delivery_charges!='')
        {
            $deliverycharge = $delivery_charges;
        }
        else
        {
            $deliverycharge = 0;
        }

        $ar = array("shop_name"=>$shop_name,"owner_name"=>$owner_name,"description"=>$des,"address"=>$adrs,"alternative_mobile"=>$altr_mobile,"pan"=>$pan1,"aadhar"=>$aadhar1,"gst_number"=>$gst_number1,"bankname"=>$bankname1,"accountholder_name"=>$accountholder_name1,"bank_ifsccode"=>$bank_ifsccode1,'account_no'=>$account_no1,'min_order_amount'=>$deliverycharge);
        $wr = array("id"=>$vendor_id);
        $upd = $this->db->update("vendor_shop",$ar,$wr);
        if($upd)
        {
            return array('status' =>TRUE,'message'=>"Profile Updated Successfully");
        }
        else
        {
            return array('status' =>FALSE,'message'=>"Something went wrong, Please try again");
        }
    }

    function getvendorReview($vendor_id)
    {
        $qry = $this->db->query("select * from user_reviews where vendor_id='".$vendor_id."'");
        $result = $qry->result();
        $ar=[];
        if($qry->num_rows()>0)
        {
            foreach ($result as $value) 
            {
                $user = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $user->row();
                $name = $users->first_name." ".$users->last_name;

                if($users->image!='')
                {
                    $img = base_url()."uploads/users/".$users->image;
                }
                else
                {
                    $img = base_url()."uploads/noproduct.png";
                }
               $ar[] = array('id'=>$value->id,'review'=>$value->review,'rating'=>$value->rating,'name'=>$name,'user_image'=>$img,'createdat'=>date("d-M,Y",$value->createdat));


            }
            return array('status' =>TRUE,'reviews'=>$ar);
        }
        else
        {
            return array('status' =>FALSE,'message'=>"No Reviews");
        }

        
    }

    function deleteProduct($pid)
    {
        $del = $this->db->delete("products",array('id'=>$pid));
        if($del)
        {
            $this->db->delete("product_images",array('product_id'=>$pid));
            $this->db->delete("add_variant",array('product_id'=>$pid));
            $this->db->delete("link_variant",array('product_id'=>$pid));
            $this->db->delete("stock_management",array('product_id'=>$pid));
            return array('status' =>TRUE,'message'=>"Product deleted successfully");
        }
        else
        {
            return array('status' =>FALSE,'message'=>"Something went wrong, please try again");
        }
    }

    function getSalesReport($vendor_id)
    {


        $qry = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and order_status=5");
        if($qry->num_rows()>0)
        {
            $result = $qry->result();
            $ar=[];
            foreach ($result as $value) 
            {   
                $qry = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $qry->row();
                $name = $users->first_name." ".$users->last_name;

                $ven = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                $vendor = $ven->row();
                
                $adrs = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                $address = $adrs->row();
                $full_address = $address->address.",".$address->locality.",".$address->city.",".$address->state;
                if($value->payment_status==0)
                {
                    $payment_status="UnPaid";
                }
                else
                {
                    $payment_status="Paid";
                }

                if($value->order_status==1)
                {
                    $order_status = "Pending";
                }
                else if($value->order_status==2)
                {
                     $order_status = "Proccessing";
                }
                else if($value->order_status==3)
                {
                     $order_status = "Assigned to delivery to pick up";
                }
                else if($value->order_status==4)
                {
                    $order_status = "Delivery Boy On the way";
                }
                else if($value->order_status==5)
                {
                    $order_status = "Delivered";
                }
                else if($value->order_status==6)
                {
                    $order_status = "Cancelled";
                }
                else if($value->order_status==7)
                {
                    $order_status = "Refund Completed";
                }
                
                if($value->total_price!='')
                {
                    $total_price=$value->total_price;
                }
                else
                {
                    $total_price =0;
                }

                if($value->admin_commission!='')
                {
                    $admin_commission=$value->admin_commission;
                }
                else
                {
                    $admin_commission=0;
                }

                if($value->vendor_commission!='')
                {
                    $vendor_commission=$value->vendor_commission;
                }
                else
                {
                    $vendor_commission=0;
                }

               $ar[]=array('id'=>$value->id,'payment_status'=>$payment_status,'service_status'=>$order_status,'total_price'=>$total_price,'admin_commission'=>$admin_commission,'gst'=>$value->gst,'vendor_commission'=>$vendor_commission,'deliveryboy_commission'=>$value->delivery_boy,'created_date'=>date('m-Y',strtotime($value->created_date)));
            }
            return array('status' =>TRUE, 'orders'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Orders");
        }

    }






    function getDatewisesalesReport($vendor_id,$sdate)
    {
        $ex = explode("T", $sdate);
        $sdate1 = date("Y-m",strtotime($ex[0]));
        $qry = $this->db->query("select * from orders where vendor_id='".$vendor_id."' and order_status=5 and created_date LIKE '%".$sdate1."%'");
        if($qry->num_rows()>0)
        {
            $result = $qry->result();
            $ar=[];
            foreach ($result as $value) 
            {   
                $qry = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $qry->row();
                $name = $users->first_name." ".$users->last_name;

                $ven = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                $vendor = $ven->row();
                
                $adrs = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                $address = $adrs->row();
                $full_address = $address->address.",".$address->locality.",".$address->city.",".$address->state;
                if($value->payment_status==0)
                {
                    $payment_status="UnPaid";
                }
                else
                {
                    $payment_status="Paid";
                }

                if($value->order_status==1)
                {
                    $order_status = "Pending";
                }
                else if($value->order_status==2)
                {
                     $order_status = "Proccessing";
                }
                else if($value->order_status==3)
                {
                     $order_status = "Assigned to delivery to pick up";
                }
                else if($value->order_status==4)
                {
                    $order_status = "Delivery Boy On the way";
                }
                else if($value->order_status==5)
                {
                    $order_status = "Delivered";
                }
                else if($value->order_status==6)
                {
                    $order_status = "Cancelled";
                }
                else if($value->order_status==7)
                {
                    $order_status = "Refund Completed";
                }

               
                $ar[]=array('id'=>$value->id,'gst'=>$value->gst,'payment_status'=>$payment_status,'service_status'=>$order_status,'total_price'=>$value->total_price,'admin_commission'=>$value->admin_commission,'vendor_commission'=>$value->vendor_commission,'deliveryboy_commission'=>$value->delivery_boy,'created_date'=>date('m-Y',strtotime($value->created_date)));
                
            }
            return array('status' =>TRUE, 'orders'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Orders");
        }

    
    }


    function socialShare()
    {
        $qry = $this->db->query("select * from admin where id=1");
        $row = $qry->row();
        return array('status' =>TRUE, 'id'=>$row->id,'share_title'=>$row->share_title,'playstore_vendorlink'=>$row->playstore_vendorlink);
    }

    function deleteBussness($vendor_id,$bid)
    {
        $del = $this->db->delete("working_hours",array('vendor_id'=>$vendor_id,'id'=>$bid));
        if($del)
        {
            return array('status' =>TRUE, 'message'=>"Business Hours Deleted successfully");
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Please try again");
        }
    }


     function getexchangeOrders($vendor_id)
    {

        $qry = $this->db->query("select * from refund_exchange where vendor_id='".$vendor_id."' and status=0");
        if($qry->num_rows()>0)
        {
            $result = $qry->result();
            $ar=[];
            foreach ($result as $rep) 
            {
                        $ord = $this->db->query("select * from orders where session_id='".$rep->session_id."'");
                        $value = $ord->row();

                $qry = $this->db->query("select * from users where id='".$value->user_id."'");
                $users = $qry->row();
                $name = $users->first_name." ".$users->last_name;

                $ven = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                $vendor = $ven->row();
                
                $adrs = $this->db->query("select * from user_address where id='".$value->deliveryaddress_id."'");
                $address = $adrs->row();
                $full_address = $address->address.",".$address->locality.",".$address->city.",".$address->state;

                if($value->payment_status==0)
                {
                    $payment_status="UnPaid";
                }
                else
                {
                    $payment_status="Paid";
                }

                if($value->order_status==1)
                {
                    $order_status = "Pending";
                }
                else if($value->order_status==2)
                {
                     $order_status = "Proccessing";
                }
                else if($value->order_status==3)
                {
                     $order_status = "Assigned to delivery to pick up";
                }
                else if($value->order_status==4)
                {
                    $order_status = "Delivery Boy On the way";
                }
                else if($value->order_status==5)
                {
                    $order_status = "Delivered";
                }
                else if($value->order_status==6)
                {
                    $order_status = "Cancelled";
                }
                else if($value->order_status==7)
                {
                    $order_status = "Refund Completed";
                }

                    $pro = $this->db->query("select * from products where id='".$rep->product_id."'");
                    $product = $pro->row();

                    $cart = $this->db->query("select * from cart where id='".$rep->cartid."'");
                    $cartdetails = $cart->row();

                    if($rep->delivery_type==1)
                    {
                        $button = "Refund";
                    }
                    else if($rep->delivery_type==2)
                    {
                        $button = "Refund";
                    }
                    
            $ar[]=array('id'=>$rep->id,'session_id'=>$value->session_id,'payment_status'=>$payment_status,'payment_type'=>$value->payment_option,'service_status'=>$order_status,'product_id'=>$product->id,'product_name'=>$product->name,'price'=>$cartdetails->price,'quantity'=>$cartdetails->quantity,'unit_price'=>$cartdetails->unit_price,'created_date'=>date("d-m-Y, h:i A",strtotime($rep->created_date)),'cartid'=>$rep->cartid,'button'=>$button,'delivery_type'=>$rep->delivery_type);
            }

            return array('status' =>TRUE, 'orders'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Orders");
        }
    
    }


    function acceptExchangeOrders($oid,$sid)
    {
            
            $msg = "Exchange Completed";
            $ar = array('status'=>1,'message'=>$msg,'exchange_completed_date'=>date('Y-m-d H:i:s'));
            $wr = array('id'=>$oid);
            $ins = $this->db->update("refund_exchange",$ar,$wr);
            if($ins)
            {
               /* $ord = $this->db->query("select * from orders where session_id='".$sid."'");
                $orderdata = $ord->row();
                $session_id = $orderdata->session_id."".$oid;


                $ar11 = array('session_id'=>$session_id,'user_id'=>$orderdata->user_id,'vendor_id'=>$orderdata->vendor_id,'delivery_timeslots'=>$orderdata->delivery_timeslots,'deliveryaddress_id'=>$orderdata->deliveryaddress_id,'payment_option'=>$orderdata->payment_option,'payment_status'=>$orderdata->payment_status,'order_status'=>$orderdata->order_status,'delivery_boy'=>$orderdata->delivery_boy,'created_at'=>time(),'delivery_boy'=>$orderdata->delivery_boy); 
                $this->db->insert("orders",$ar11);*/

                $ord = $this->db->query("select * from orders where session_id='".$sid."'");
                $orderdata = $ord->row();
                $session_id = $orderdata->id;
                
            $ar11 = array('order_status'=>7); 
            $wr = array('session_id'=>$sid);
               $upd = $this->db->update("orders",$ar11,$wr);
               if($upd)
               {
                  $order_message = "Refund Completed: order ID :".$session_id." amounting to Rs.".$orderdata->total_price.". from A3 Services"; 
               }
                 
                if($this->send_message($order_message,$vendor_phone))
                {
                   $this->db->insert("sms_notifications",array('order_id'=>$session_id,'receiver_id'=>$orderdata->user_id,'sender_id'=>$orderdata->vendor_id,'created_at'=>time(),'message'=>$order_message,'action_person'=>1)); 
                }

                return array('status' =>TRUE, 'message'=>"Refund Accepted Successfully");
            }
            else
            {
                return array('status' =>FALSE, 'message'=>"Something Went Wrong");
            }
    }

    function fetchbasicsubcategories()
    {
        $qry = $this->db->query("select * from sub_categories");
        return $qry->result();
    }



 function deliverySlots($shop_id,$date)
 {
    date_default_timezone_set('Asia/Kolkata'); 

    $timezone_object = date_default_timezone_get(); 

    $ss = '13';
     echo $covnver = date('h A',$ss);  die;

   $cdate = date("Y-m-d"); 
   $st1 = strtotime($date); 
   $st2 = strtotime($cdate);
    
    if($st1==$st2)
    {
           $chour = date('H')+2;

            $dayofweek = date('w', strtotime($sdate));  
           if($dayofweek==0)
           {
             $weekday = 'sunday';
           }
           else if($dayofweek==1)
           {
            $weekday = 'monday';
           }
           else if($dayofweek==2)
           {
            $weekday = 'tuesday';
           }
           else if($dayofweek==3)
           {
            $weekday = 'wednesday';
           }
           else if($dayofweek==4)
           {
            $weekday = 'thursday';
           }
           else if($dayofweek==5)
           {
            $weekday = 'friday';
           }
           else if($dayofweek==6)
           {
            $weekday = 'saturday';
           }

           $qry=$this->db->query("select * from working_hours where vendor_id='".$shop_id."' and weekname='".$weekday."' and working='yes'");
            if($qry->num_rows()>0)
            {
                $result = $qry->row();
                 $start_time = date('H', strtotime($result->open_time));
                 $end_time = date('H', strtotime($result->closed_time)); 
                  $ar=[];
                for ($i=$chour; $i <=$end_time; $i++) 
                { 
                    if($i=="1" || $i=="2" || $i=="3" || $i=="4" || $i=="5" || $i=="6" || $i=="7" || $i=="8" || $i=="9" || $i=="10" || $i=="11" || $i=="08" || $i=="09" || $i=="01" || $i=="02" || $i=="03" || $i=="04" || $i=="05" || $i=="06" || $i=="07")
                    {
                        $start_t = $i.":00 AM";
                    }
                    else
                    {
                        

                        $start_t = $i.":00 PM";
                    }


                        $chek = $i+1;
                        if($chek=='1' || $chek=='2' || $chek=='3' || $chek=='4' || $chek=='5' || $chek=='6' || $chek=='7' || $chek=='8' || $chek=='9' || $chek=='10' || $chek=='11' || $chek=='01' || $chek=='02' || $chek=='03' || $chek=='04' || $chek=='05' || $chek=='06' || $chek=='07' || $chek=='08')
                        {
                           $slotent = ($i+1).":00 AM";
                        }
                        else
                        {
                            $slotent = ($i+1).":00 PM";
                        }

                    $ar[]=array('start_time'=>$start_t,'end_time'=>$slotent);
                }
             
                return array('status' =>TRUE,'time_slots'=>$ar);
            }
            else
            {
                return array('status' =>FALSE,'message'=>"No Time Slots");
            }
    
    }
    else
    {
            $dayofweek = date('w', strtotime($sdate));  
           if($dayofweek==0)
           {
             $weekday = 'sunday';
           }
           else if($dayofweek==1)
           {
            $weekday = 'monday';
           }
           else if($dayofweek==2)
           {
            $weekday = 'tuesday';
           }
           else if($dayofweek==3)
           {
            $weekday = 'wednesday';
           }
           else if($dayofweek==4)
           {
            $weekday = 'thursday';
           }
           else if($dayofweek==5)
           {
            $weekday = 'friday';
           }
           else if($dayofweek==6)
           {
            $weekday = 'saturday';
           }

           $qry=$this->db->query("select * from working_hours where vendor_id='".$shop_id."' and weekname='".$weekday."' and working='yes'");
            if($qry->num_rows()>0)
            {
                $result = $qry->row();
                 $start_time = date('H', strtotime($result->open_time));
                 $end_time = date('H', strtotime($result->closed_time)); 
                  $ar=[];
                for ($i=$start_time; $i <=$end_time; $i++) 
                { 
                    if($i=="1" || $i=="2" || $i=="3" || $i=="4" || $i=="5" || $i=="6" || $i=="7" || $i=="8" || $i=="9" || $i=="10" || $i=="11" || $i=="08" || $i=="09" || $i=="01" || $i=="02" || $i=="03" || $i=="04" || $i=="05" || $i=="06" || $i=="07")
                    {
                        $start_t = $i.":00 AM";
                    }
                    else
                    {
                        /*if($i>="13" && $<="23")
                        {
                           $covnver = date('h A',strtotime($i));
                          $start_t = $covnver;
                        }
                        else
                        {*/
                            $start_t = $i.":00 PM";
                        /*}*/

                       
                    }


                        $chek = $i+1;
                        if($chek=='1' || $chek=='2' || $chek=='3' || $chek=='4' || $chek=='5' || $chek=='6' || $chek=='7' || $chek=='8' || $chek=='9' || $chek=='10' || $chek=='11' || $chek=='01' || $chek=='02' || $chek=='03' || $chek=='04' || $chek=='05' || $chek=='06' || $chek=='07' || $chek=='08')
                        {
                           $slotent = ($i+1).":00 AM";
                        }
                        else
                        {
                            $slotent = ($i+1).":00 PM";
                        }

                    $ar[]=array('start_time'=>$start_t,'end_time'=>$slotent);
                }
             
                return array('status' =>TRUE,'time_slots'=>$ar);
            }
            else
            {
                return array('status' =>FALSE,'message'=>"No Time Slots");
            }
    }

   
 }


 function getproductsFilters($json_data,$shop_id,$cat_id)
{

     $str = json_encode($json_data);

        $qry = $this->db->query("SELECT * FROM `link_variant` where saleprice!=0 and status=1 and jsondata LIKE ".$str);
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value12) 
                {
                    $qry11 = $this->db->query("select * from products where cat_id='".$cat_id."'  and shop_id='".$shop_id."' and id='".$value12->product_id."'");
                    $value = $qry11->row();

                     $im = $this->db->query("select * from product_images where product_id='".$value->id."'");
                     $images = $im->row();
                    if($images->image!='')
                    {
                        $img = base_url()."uploads/products/".$images->image;
                    }
                    else
                    {
                        $img = base_url()."uploads/noproduct.png";
                    }

                    
                    $cat = $this->db->query("select * from categories where id='".$value->cat_id."'");
                    $category = $cat->row();
                    $subcat = $this->db->query("select * from sub_categories where id='".$value->sub_cat_id."'");
                    $subcategory = $subcat->row();
                    $brnd = $this->db->query("select * from attr_brands where id='".$value->brand."'");
                    $brand = $brnd->row();

                    $vendo = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                    $vendor = $vendo->row();



                    $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                    }
                    else
                    {
                        $stat = false;
                    }


                    if($qry11->num_rows()>0)
                    {
                        $ar[]=array('id'=>$value->id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$value->name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$brand->brand_name,'shop'=>$vendor->shop_name,'price'=>$value12->price,'saleprice'=>$value12->saleprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat);
                    }
                }
            return array('status' =>TRUE,'product_list'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Products");
        }

    
   
}


function getVendorDiscount($vendor_id)
{
    $qry = $this->db->query("select id,total_payment,requested_amount from vendor_payements where vendor_id='".$vendor_id."'");
    if($qry->num_rows()>0)
    {
            $data = $qry->row();
            if($data->requested_amount!='')
            {
                $final = $data->total_payment-$data->requested_amount;
            }
            else
            {
                $final = $data->total_payment;
            }
            
            return array('status' =>TRUE,'total_payment'=>round($final,2),'requested_amount'=>$data->requested_amount);
    }
    else
    {
         return array('status' =>FALSE,'total_payment'=>'0','requested_amount'=>'0');
    }
    
}


function requestVendorPayments($vendor_id,$vendor_amount,$description,$total_payment)
{

        $qry = $this->db->query("select * from vendor_payements where vendor_id='".$vendor_id."'");
        $row =$qry->row();
        $t_price =  floor($row->total_payment);

            $chek_qry = $this->db->query("SELECT sum(request_amount) as total_requested_amount FROM `request_payment` WHERE vendor_id='".$vendor_id."'");
          $requqested_row=$chek_qry->row();
          $total_requested_amount=$requqested_row->total_requested_amount+$vendor_amount;


        if($t_price>=$total_requested_amount)
        {
                    $requested_amount = $vendor_amount;
                    $description = $description;
                    $data = array(
                        'vendor_id'=>$vendor_id,
                        'request_amount' => $requested_amount,
                        'vendor_amount' => $total_payment,
                        'description'=> $description,
                        'created_at' => time()
                    );

                    $insert_query = $this->db->insert('request_payment', $data);
                    //echo $this->db->last_query(); die;
                    if ($insert_query) {
                            $qrr = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
                            $vend_row = $qrr->row();

                            $msg = $vend_row->shop_name." Requested the amount ".$requested_amount;
                        $trans_ar=array('sender_name'=>$vend_row->shop_name,'receiver_name'=>'Admin','amount'=>$requested_amount,'message'=>$msg,'created_at'=>time());
                        $this->db->insert('transactions', $trans_ar);
                        return array('status' =>TRUE, 'message'=>"Request sent Successfully");
                       
                    } 
                    else 
                    {
                        return array('status' =>FALSE, 'message'=>"Something went wrong,Please try again");
                    }

        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Please check your wallet balance and Requested amount");
        }
    
}

function getVendorRequests($vendor_id)
{
     

    $qry = $this->db->query("select * from request_payment where vendor_id='".$vendor_id."'");
    $result = $qry->result();
    if($qry->num_rows()>0)
    {
		$has_pending_requests=0;
        foreach ($result as $value) 
        {
            if($value->status==0)
            {
                $status = "Pending";
				$has_pending_requests++;
            }
            else
            {
                $status = "Payment Completed";
            }

            $img = base_url()."uploads/payments/".$value->image;
            if($value->updated_at!='')
            {
                $updated_at=date("d-m-Y",$value->updated_at);
            }
            else
            {
                $updated_at="";
            }
           $ar[] = array('id'=>$value->id,'request_amount'=>$value->request_amount,'vendor_amount'=>$value->vendor_amount,'status'=>$status,'description'=>$value->description,'mode_payment'=>$value->mode_payment,'transaction_id'=>$value->transaction_id,'image'=>$img,'sender_name'=>$value->sender_name,'receiver_name'=>$value->receiver_name,'admin_description'=>$value->admin_description,'updated_at'=>$updated_at,'created_at'=>date("d-m-Y",$value->created_at),'del_stats'=>$value->status);
        }
        return array('status' =>TRUE, 'request_list'=>$ar,'has_pending_requests'=>$has_pending_requests);
    }
    else
    {
        return array('status' =>FALSE, 'message'=>"No Requests Found");
    }
    
}

function clrRequest($id)
{
    $del = $this->db->delete("request_payment",array('id'=>$id));
    if($del)
    {
         return array('status' =>TRUE, 'message'=>"Request deleted Successfully");
    }
    else
    {
         return array('status' =>TRUE, 'message'=>"Something went wrong");
    }
}

function getUsersBids($vendor_id,$bid_status)
{
        
    if($bid_status=='openBid')
    {
     $qry = $this->db->query("SELECT user_bids.*,vendor_bids.created_date FROM user_bids INNER JOIN vendor_bids ON vendor_bids.bid_id=user_bids.id where vendor_bids.vendor_id='".$vendor_id."' and user_bids.bid_status=0 order by user_bids.id desc");
        
    }
    else if($bid_status=='accepted')
    {
        $qry = $this->db->query("SELECT user_bids.*,vendor_bids.created_date FROM user_bids INNER JOIN vendor_bids ON vendor_bids.bid_id=user_bids.id where vendor_bids.vendor_id='".$vendor_id."' and user_bids.bid_status=1 order by user_bids.id desc");
        //$qry = $this->db->query("SELECT * from vendor_bids where vendor_id='".$vendor_id."' and bid_status=1 order by id desc");
    }
    else if($bid_status=='closed')
    {
        $qry = $this->db->query("SELECT user_bids.*,vendor_bids.created_date FROM user_bids INNER JOIN vendor_bids ON vendor_bids.bid_id=user_bids.id where vendor_bids.vendor_id='".$vendor_id."' and user_bids.bid_status=2 order by user_bids.id desc");

         //$qry = $this->db->query("SELECT * from vendor_bids where vendor_id='".$vendor_id."' and bid_status=2 order by id desc");
    }
    $result = $qry->result();

    
    $bid_ar=[];
    if($qry->num_rows()>0)
    {
        foreach ($result as $value1) 
        {

        $cart = $this->db->query("select * from cart where session_id='".$value1->session_id."'");
        $cart_row = $cart->result();
        $cart_ar=[];
        foreach ($cart_row as $value) 
        {
            $varint = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
            $variant_row = $varint->row();

            $prod = $this->db->query("select * from products where id='".$variant_row->product_id."'");
            $prod_row = $prod->row();


            $pro_img = $this->db->query("select * from product_images where variant_id='".$value->variant_id."' and product_id='".$variant_row->product_id."'");
            $pro_imgs = $pro_img->row();

            if($pro_imgs->image!='')
            {
                $image = base_url()."uploads/products/".$pro_imgs->image;
            }
            else
            {
                $image = base_url()."uploads/noproduct.png";
            }

            $jsondata = json_decode($variant_row->jsondata);
            $attributes=[];
            foreach ($jsondata as $val) 
            {
                $attribute_type=$val->attribute_type;
                $attribute_value=$val->attribute_value;

                $type = $this->db->query("select * from attributes_title where id='".$attribute_type."'");
                $types = $type->row();

                $val12 = $this->db->query("select * from attributes_values where id='".$attribute_value."'");
                $value12 = $val12->row();
                $attributes[]=array('attribute_type'=>$types->title,'attribute_values'=>$value12->value);
            }





            $vendor_cat = $this->db->query("select * from admin_comissions where cat_id='".$prod_row->cat_id."' and shop_id='".$vendor_id."'");
            $vendor_cat_row = $vendor_cat->row();

            if($vendor_cat->num_rows()>0)
            {
                $cart_ar[]=array('id'=>$value->id,'session_id'=>$value->session_id,'product_name'=>$prod_row->name,'price'=>$value->price,'quantity'=>$value->quantity,'total'=>$value->unit_price,'image'=>$image,'attributes'=>$attributes);
            }
            

        }

        /*if(count($cart_ar)>0)
        {*/
            $cart_count = count($cart_ar);

            $date = date('d-m-Y,h:i A',strtotime($value1->created_date));

            if($value1->bid_status==0)
            {
                $status = 'open';
            }
            else if($value1->bid_status==1)
            {
                $status = 'Accepted';
            }
            else if($value1->bid_status==2)
            {
                $status = 'Closed';
            }

            $bid_ar[] = array('id'=>$value1->id,'session_id'=>$value1->session_id,'created_at'=>$date,'total_products'=>$cart_count,'cart_products'=>$cart_ar,'cart_count'=>$cart_count,'bidstatus'=>$status);
        }

         return array('status' =>TRUE, 'bidslist'=>$bid_ar);
    }
    else
    {
         return array('status' =>FALSE, 'data'=>"No Bids");
    }



}
function getUsersBids123($vendor_id,$bid_status)
{
    if($bid_status=='openBid')
    {

     $qry = $this->db->query("SELECT * from vendor_bids where vendor_id='".$vendor_id."' and bid_status=0 order by id desc");
        
    }
    else if($bid_status=='accepted')
    {
        $qry = $this->db->query("SELECT * from vendor_bids where vendor_id='".$vendor_id."' and bid_status=1 order by id desc");
    }
    else if($bid_status=='closed')
    {
         $qry = $this->db->query("SELECT * from vendor_bids where vendor_id='".$vendor_id."' and bid_status=2 order by id desc");
    }
    $result = $qry->result();

    
    $bid_ar=[];
    if($qry->num_rows()>0)
    {

    foreach ($result as $value) 
    {

        $qry = $this->db->query("SELECT * from user_bids where id='".$value->bid_id."' order by id desc");
        $value1 = $qry->row();


        $cart = $this->db->query("select * from cart where session_id='".$value1->session_id."'");
        $cart_row = $cart->result();
        $cart_ar=[];
        foreach ($cart_row as $value) 
        {
            $varint = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
            $variant_row = $varint->row();

            $prod = $this->db->query("select * from products where id='".$variant_row->product_id."'");
            $prod_row = $prod->row();


            $pro_img = $this->db->query("select * from product_images where variant_id='".$value->variant_id."' and product_id='".$variant_row->product_id."'");
            $pro_imgs = $pro_img->row();

            if($pro_imgs->image!='')
            {
                $image = base_url()."uploads/products/".$pro_imgs->image;
            }
            else
            {
                $image = base_url()."uploads/noproduct.png";
            }

            $jsondata = json_decode($variant_row->jsondata);
            $attributes=[];
            foreach ($jsondata as $val) 
            {
                $attribute_type=$val->attribute_type;
                $attribute_value=$val->attribute_value;

                $type = $this->db->query("select * from attributes_title where id='".$attribute_type."'");
                $types = $type->row();

                $val12 = $this->db->query("select * from attributes_values where id='".$attribute_value."'");
                $value12 = $val12->row();
                $attributes[]=array('attribute_type'=>$types->title,'attribute_values'=>$value12->value);
            }





            $vendor_cat = $this->db->query("select * from admin_comissions where cat_id='".$prod_row->cat_id."' and shop_id='".$vendor_id."'");
            $vendor_cat_row = $vendor_cat->row();

            if($vendor_cat->num_rows()>0)
            {
                $cart_ar[]=array('id'=>$value->id,'session_id'=>$value->session_id,'product_name'=>$prod_row->name,'price'=>$value->price,'quantity'=>$value->quantity,'total'=>$value->unit_price,'image'=>$image,'attributes'=>$attributes);
            }
            

        }

        /*if(count($cart_ar)>0)
        {*/
            $cart_count = count($cart_ar);

            $date = date('d-m-Y,h:i A',$value1->created_at);

            if($value1->bid_status==0)
            {
                $status = 'open';
            }
            else if($value1->bid_status==1)
            {
                $status = 'Accepted';
            }
            else if($value1->bid_status==2)
            {
                $status = 'open';
            }

            $bid_ar[] = array('id'=>$value1->id,'session_id'=>$value1->session_id,'created_at'=>$date,'total_products'=>$cart_count,'cart_products'=>$cart_ar,'cart_count'=>$cart_count,'bidstatus'=>$status);
        //}        
    }
     return array('status' =>TRUE, 'bidslist'=>$bid_ar);
    }
    else{
        return array('status' =>FALSE, 'data'=>"No Bids");
    }    

}


function getBidDetails($bid,$vendor_id)
{
    $qry = $this->db->query("SELECT * from user_bids where id='".$bid."'");
    
    $value1 = $qry->row();

        $cart = $this->db->query("select * from cart where session_id='".$value1->session_id."'");
        $cart_row = $cart->result();
        $cart_ar=[];
        foreach ($cart_row as $value) 
        {

           
            $varint = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
            $variant_row = $varint->row();

            $prod = $this->db->query("select * from products where id='".$variant_row->product_id."'");
            $prod_row = $prod->row();


            $pro_img = $this->db->query("select * from product_images where variant_id='".$value->variant_id."' and product_id='".$variant_row->product_id."'");
            $pro_imgs = $pro_img->row();

            if($pro_imgs->image!='')
            {
                $image = base_url()."uploads/products/".$pro_imgs->image;
            }
            else
            {
                $image = base_url()."uploads/noproduct.png";
            }

            $jsondata = json_decode($variant_row->jsondata);
            $attributes=[];
            foreach ($jsondata as $val) 
            {
                $attribute_type=$val->attribute_type;
                $attribute_value=$val->attribute_value;

                $type = $this->db->query("select * from attributes_title where id='".$attribute_type."'");
                $types = $type->row();

                $val12 = $this->db->query("select * from attributes_values where id='".$attribute_value."'");
                $value12 = $val12->row();
                $attributes[]=array('attribute_type'=>$types->title,'attribute_values'=>$value12->value);
            }





            $vendor_cat = $this->db->query("select * from admin_comissions where cat_id='".$prod_row->cat_id."' and shop_id='".$vendor_id."'");
            $vendor_cat_row = $vendor_cat->row();

            if($vendor_cat->num_rows()>0)
            {
                $cart_ar[]=array('id'=>$value->id,'session_id'=>$value->session_id,'product_name'=>$prod_row->name,'price'=>$value->price,'quantity'=>$value->quantity,'total'=>$value->unit_price,'image'=>$image,'attributes'=>$attributes);
            }
        }
        if(count($cart_ar)>0)
        {
            $cart_count = count($cart_ar);

            $date = date('d-m-Y,h:i A',$value1->created_at);

            if($value1->bid_status==0)
            {
                $status = 'open';
            }
            else if($value1->bid_status==1)
            {
                $status = 'Accepted';
            }
            else if($value1->bid_status==2)
            {
                $status = 'open';
            }
            $bid_quote = $this->db->query("select * from vendor_bids where bid_id='".$value1->id."' and vendor_id='".$vendor_id."' and bid_status!=0");
            $bid_quote_row = $bid_quote->row();
            if($bid_quote->num_rows()>0)
            {
                $quote_stat = true;
            }
            else
            {
                $quote_stat = false;
            }

            $bid_ar = array('id'=>$value1->id,'user_id'=>$value1->user_id,'session_id'=>$value1->session_id,'created_at'=>$date,'delivery_amount'=>$value1->delivery_amount,'gst'=>$value1->gst,'sub_total'=>$value1->sub_total,'grand_total'=>$value1->grand_total,'total_products'=>$cart_count,'cart_products'=>$cart_ar,'cart_count'=>$cart_count,'bidstatus'=>$status,'quote_status'=>$quote_stat,'quote_amount'=>$bid_quote_row->total_price);
        }
        return array('status' =>TRUE, 'bidslist'=>$bid_ar);
        



} 

function getShopBanners($vendor_id)
{
    $qry = $this->db->query("select * from vendor_shop_banners where shop_id='".$vendor_id."'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    if($value->app_banner!='')
                    {
                        $img = base_url()."uploads/banners/".$value->app_banner;
                    }
                    else
                    {
                        $img = "";
                    }
                   $ar[]=array('id'=>$value->id,'title'=>$value->title,'image'=>$img,'image_file'=>$value->app_banner);
                }
                return array('status' =>TRUE,'bannerslist'=>$ar);
        }
        else
        {
            $img = base_url()."uploads/nobanner.png";
            return array('status' =>FALSE, 'message'=>"No Banners",'image'=>$img);
        }
}

function getCouponcodes($shop_id)
{
    $date = date("Y-m-d");
    $qry = $this->db->query("select * from coupon_codes where shop_id='".$shop_id."'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                   $ar[]=array('id'=>$value->id,'coupon_code'=>$value->coupon_code,'description'=>$value->description,'percentage'=>$value->percentage,'maximum_amount'=>$value->maximum_amount,'start_date'=>$value->start_date,'expiry_date'=>$value->expiry_date);
                }
                return array('status' =>TRUE,'coupons'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Coupons");
        }
}


function submitBidQuote($user_id,$vendor_id,$bid_id,$total_price)
{
    $chk = $this->db->query("select * from vendor_bids where bid_id='".$bid_id."' and vendor_id='".$vendor_id."' and bid_status=1");
    if($chk->num_rows()>0)
    {
            return array('status' =>FALSE, 'message'=>"already added the quote, Please try again");
    }
    else
    {
        $ar = array('bid_status'=>1,'bid_id'=>$bid_id,'vendor_id'=>$vendor_id,'user_id'=>$user_id,'total_price'=>$total_price,'accept_at'=>time(),'accept'=>'yes');
        $wr = array('vendor_id'=>$vendor_id,'bid_id'=>$bid_id);
        $ins = $this->db->update("vendor_bids",$ar,$wr);
        if($ins)
        {
                            $title = "Bid Quotation";
                            $message = "Your Order Accepted";
                            $this->onesignalnotification($user_id,$message,$title);
             return array('status' =>TRUE,'message'=>"Your Quotation sent Successfully");
        }
    }
}


function addCoupon($vendor_id,$coupon_code,$percentage,$maximum_amount,$start_date,$end_date,$description)
{
    $ar = array('shop_id'=>$vendor_id,'coupon_code'=>$coupon_code,'percentage'=>$percentage,'start_date'=>$start_date,'expiry_date'=>$end_date,'maximum_amount'=>$maximum_amount,'description'=>$description);
    $ins = $this->db->insert("coupon_codes",$ar);
    if($ins)
    {
        return array('status' =>TRUE,'message'=>"Coupon added Successfully");
    }
}


function updateCoupon($vendor_id,$coupon_code,$percentage,$maximum_amount,$start_date,$end_date,$description,$id)
{
    $ar = array('shop_id'=>$vendor_id,'coupon_code'=>$coupon_code,'percentage'=>$percentage,'start_date'=>$start_date,'expiry_date'=>$end_date,'maximum_amount'=>$maximum_amount,'description'=>$description);
    $wr = array('id'=>$id);
    $ins = $this->db->update("coupon_codes",$ar,$wr);
    if($ins)
    {
        return array('status' =>TRUE,'message'=>"Coupon updated Successfully");
    }
}


function deleteCoupon($cid)
{
    $del = $this->db->delete("coupon_codes",array('id'=>$cid));
    if($del)
    {
        return array('status' =>TRUE,'message'=>"Coupon deleted Successfully");
    }
}

function deleteBanner($cid)
{
    $del = $this->db->delete("vendor_shop_banners",array('id'=>$cid));
    if($del)
    {
        return array('status' =>TRUE,'message'=>"Coupon deleted Successfully");
    }
}


function searchPreLoadedProducts($keyword,$shopId)
{
    $qry = $this->db->query("select * from admin_comissions where shop_id='".$shopId."'");
    $row = $qry->result();
    $catis=[];
    foreach ($row as $value) 
    {
        $catis[]=$value->cat_id;
    }

    $im = implode(",", $catis);
    $prod = $this->db->query("select id,name from products where find_in_set(cat_id,'".$im."') and name LIKE '%".$keyword."%'");
    return $prod->result();
}



function push_notification_android($device_id,$message,$title){

    //API URL of FCM
    $url = 'https://fcm.googleapis.com/fcm/send';

    /*api_key available in:
    Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/    
    $api_key = 'AAAAM3TVgFs:APA91bHYfyNsbOp2p3oywDFxSy_AaEHtukArA8jCjktiOz_OL7QPWmM-HJ2aT8XpBRRBb0Let3ycY6riMpbK8Bwg3rzcF5Gob-SYWghMvIUoO1Je4lTcGICKUy8Fqf049g_88rBUKpLJ';
                
    $fields = array (
        'registration_ids' => array (
                $device_id
        ),
        'data' => array (
                "title" => $title,
                "body" => $message
        )
    );

    //header includes Content type and api key
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$api_key
    );
                
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);

    return $result;
} 




function versionControl()
    {
        $qry = $this->db->query("select * from version_control where id=1");
        if($qry->num_rows()>0)
        {
            $verion = $qry->row();
            return array('status'=>TRUE,'veersion_no'=>$verion->vendor_version);
        }
        else
        {
             return array('status'=>FALSE);
        }
    }




}
?>