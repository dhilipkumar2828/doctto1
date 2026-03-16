<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Web_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        
        //load database library
        $this->load->database();
    }

    function send_message($message = "", $mobile_number,$template_id) {


         $message = urlencode($message);

         $URL = "http://login.smsmoon.com/API/sms.php"; // connecting url 

         $post_fields = ['username' => 'RocketWheel', 'password' => 'vizag@123', 'from' => 'RocketWheel', 'to' => $mobile_number, 'msg' => $message, 'type' => 1, 'dnd_check' => 0,'template_id'=>$template_id];

         //file_get_contents("http://login.smsmoon.com/API/sms.php?username=colourmoonalerts&password=vizag@123&from=WEBSMS&to=$mobile_number&msg=$message&type=1&dnd_check=0");
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $URL);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_exec($ch);
         return true;
      }

      function rand_string( $length ) 
            {
                $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                return substr(str_shuffle($chars),0,$length);
            }

    function doRegister($data)
    {
        $email = $data['email'];
        $phone = $data['phone'];
        //$otp = rand(1000,10000);
        $otp = '1234';
        $otp_message = $otp." is OTP to register with A3 Services. Pls do not share OTP to anyone for security reasons.";
        $template_id = '1407161683180266239';
        $data['otp'] = $otp;
        $email_verify = $this->db->query("select * from users where email='".$email."' and phone!='".$phone."' and otp_status=1");
        $phone_verify = $this->db->query("select * from users where email!='".$email."' and phone='".$phone."' and otp_status=1");
        $both = $this->db->query("select * from users where email='".$email."' and phone='".$phone."' and otp_status=1");
        if($email_verify->num_rows()>0)
        {
            echo '@invalid_email'; exit;
        }
        else if($phone_verify->num_rows()>0)
        {
            echo '@invalid_phone'; exit;
        }
        else if($both->num_rows()>0)
        {
            echo '@both'; exit;
        }
        else
        {
            $chk_both = $this->db->query("select * from users where ( email='".$email."' or phone='".$phone."' ) and otp_status=0");
            if($chk_both->num_rows()>0)
            {
                $get = $chk_both->row();
                $wr = array('phone'=>$phone);

                if($this->send_message($otp_message,$phone,$template_id))
                {

                    $ins = $this->db->update("users",$data,$wr);
                    $last_id = $get->id;
                    if($ins)
                    {
                        echo '@success@'.$last_id; exit;
                    }
                }
            }
            else
            {
                if($this->send_message($otp_message,$phone,$template_id))
                {

                    $ins = $this->db->insert("users",$data);
                    $last_id = $this->db->insert_id($ins);
                    if($ins)
                    { 
                        echo '@success@'.$last_id; exit;
                    }
                }
            }
        }
    }


    function verify_OTP($user_id,$otp)
    {
         $qry = $this->db->query("select * from users where id='".$user_id."' and otp='".$otp."'");
         if($qry->num_rows()>0)
         {
            $ar=array('otp_status'=>1);
            $wr=array('id'=>$user_id);
            $ins = $this->db->update("users",$ar,$wr);
            if($ins)
            {

                    $stu_row = $qry->row();
                    $phone =$stu_row->phone;
                   $otp_message = "Dear ".$stu_row->first_name." your successfully registered with A3 Services. Enjoy your local shopping experience with A3 Services. ";
                   $template_id ="1407161683204290058";
            $this->send_message($otp_message,$phone,$template_id);

              $row = $qry->row();
              $name = $row->first_name." ".$row->last_name;
                $sess_arr = array(
                    'user_id' => $row->id,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'name' => $name,
                    'logged_in' => true
                ); 
                $this->session->set_userdata('userdata', $sess_arr);

              echo '@success'; die;  
            }
         }   
         else
         {
             echo '@invalid'; die;
         }  
    }

     function checkLogin($username,$password,$token)
    {
        $chk = $this->db->query("select * from users where ( email='".$username."' or phone='".$username."' ) and password='".$password."' and otp_status=1");
        if($chk->num_rows()>0)
        {
             $row = $chk->row();
             $name = $row->first_name." ".$row->last_name;
             $this->db->update("users",array('token'=>$token,'login_time'=>time()),array('email'=>$row->email));

            

            $sess_arr = array(
                'user_id' => $row->id,
                'email' => $row->email,
                'phone' => $row->phone,
                'name' => $name,
                'logged_in' => true
            ); 
                $this->session->set_userdata('userdata', $sess_arr);

             if($row->lat=='' || $row->lng=='')
             {
                echo '@location'; die;
             }
             else
             {
                 echo '@success@'.$row->id; die;
             }
                
        }
        else
        {
            echo '@invalid'; die;
        }
    }
    
    function resendOTP($user_id)
    {
        $chk = $this->db->query("select * from users where id='".$user_id."'");
        if($chk->num_rows()>0)
        {
            $row = $chk->row();
            //$otp = rand(1000,10000);
            $otp='1234';
            $phone = $row->phone;

            $template_id = '1407161683180266239';

            $otp_message = $otp." is OTP to register with A3 Services. Pls do not share OTP to anyone for security reasons.";
            if($this->send_message($otp_message,$phone,$template_id))
            {
                $ar = array('otp'=>$otp);
                $wr = array('id'=>$user_id);
                $upd = $this->db->update("users",$ar,$wr);
                if($upd)
                {
                   echo '@success'; die;
                }
            }
        }
        else
        {
            echo '@error'; die;
        }
    }


function checkForgot($phone)
{


        $chk = $this->db->query("select * from users where ( phone='".$phone."' or email='".$phone."' ) and otp_status=1");
        if($chk->num_rows()>0)
        {
            //$otp = rand(1000,10000);
            $otp = '1234';
            $otp_message = $otp." is OTP to reset your password. Pls do not share OTP to anyone for security reasons.";
            $ar=array('otp'=>$otp);
            $wr=array('phone'=>$phone);
            $template_id = "1407161683190033363";
            if($this->send_message($otp_message,$phone,$template_id))
            {
                $upd = $this->db->update('users',$ar,$wr);
                if($upd)
                {

                     $stu_row = $chk->row();
                     $user_id = $stu_row->id;
                    echo '@success@'.$user_id; die;
                }
            }
        }
        else
        {
            //return array('status' =>FALSE, 'message'=>"Invalid Email or Phone Number");
            echo '@error'; die;
        }
    }

 
  function resetPassword($user_id,$otp,$password)
    {
        $qry = $this->db->query("select * from users where id='".$user_id."' and otp='".$otp."'");
        if($qry->num_rows()>0)
        {
              $ar = array('password'=>md5($password));
              $wr = array('id'=>$user_id);
              $upd = $this->db->update("users",$ar,$wr);
             if($upd)
             {
                $row = $qry->row();
                $name = $row->first_name." ".$row->last_name;
              $sess_arr = array(
                    'user_id' => $row->id,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'name' => $name,
                    'logged_in' => true
                ); 
                $this->session->set_userdata('userdata', $sess_arr);

              echo '@success'; die;  

             }
        }
        else
        {
            echo '@invalid'; die; 
        }
    }



function profileDetails($user_id)
{
    $qry = $this->db->query("select * from users where id='".$user_id."'");
    if($qry->num_rows()>0)
    {
        $row = $qry->row();
        if($row->image!='')
        {
             $image = base_url()."uploads/users/".$row->image;
        }
        else
        {
             $image = base_url()."uploads/profile-icon-3.png";
        }
       
        return array('id'=>$row->id,'first_name'=>$row->first_name,'last_name'=>$row->last_name,'email'=>$row->email,'phone'=>$row->phone,'image'=>$image);
         //return array('status' =>TRUE,'profile_details'=>$ar);
    }
}

function updateProfile($user_id,$first_name,$last_name)
{
    $upd = $this->db->update("users",array('first_name'=>$first_name,'last_name'=>$last_name),array('id'=>$user_id));    
    if($upd)
    {
         //return array('status' =>TRUE,'message'=>"Profile Updated successfully");
         echo '@success'; die;
    }
    else
    {
        //return array('status' =>FALSE,'message'=>"Something went Wrong , Please try again");
        echo '@invalid'; die;
    }

}

function myAccount($user_id)
{
  $qry = $this->db->query("select * from users where id='".$user_id."'");
  $row = $qry->row();

  $login_time = date("d-m-Y, h:i A",$row->login_time);

  $ord_qry = $this->db->query("select * from orders where user_id='".$user_id."'");
  $orders_num = $ord_qry->num_rows();

  $onoingord_qry = $this->db->query("select * from orders where user_id='".$user_id."' and order_status in (1,2,3,4)");
  $ongoing = $onoingord_qry->num_rows();

  $completed_qry = $this->db->query("select * from orders where user_id='".$user_id."' and order_status=5");
  $completed = $completed_qry->num_rows();

  $cancelled_qry = $this->db->query("select * from orders where user_id='".$user_id."' and order_status=6");
  $cancelled = $cancelled_qry->num_rows();

  $bidqry = $this->db->query("SELECT * from user_bids WHERE user_id='".$user_id."' and bid_status=0");
  $bids = $bidqry->num_rows();


  return array('login_time'=>$login_time,'order_total'=>$orders_num,'ongoing_orders'=>$ongoing,'completed_orders'=>$completed,'cancelled_orders'=>$cancelled,'bids'=>$bids);
}


function orderList($user_id,$order_status)
{
       if($order_status=='total_orders')
       {
         $qry = $this->db->query("select * from orders where user_id='".$user_id."' order by id desc");
       }
       else if($order_status=='ongoing_orders')
       {
         $qry = $this->db->query("select * from orders where user_id='".$user_id."' and order_status in (1,2,3,4) order by id desc");
       }
       else if($order_status=='completed_orders')
       {
         $qry = $this->db->query("select * from orders where user_id='".$user_id."' and order_status=5 order by id desc");
       }
       else if($order_status=='cancelled_orders')
       {
         $qry = $this->db->query("select * from orders where user_id='".$user_id."' and order_status=6 order by id desc");
       }

       
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


                

                $ven1 = $this->db->query("select * from user_reviews where user_id='".$user_id."' and order_id='".$value->id."'");
               
                if($ven1->num_rows()>0)
                {
                    $review_status='true';
                }
                else
                {
                    $review_status='false';
                }
                

                if($value->coupon_id==0)
                {
                    $coupon_disount="0";
                    $sub_t = $value->sub_total;
                    
                    if($value->bid_id==0)
                    {
                        $amount=$sub_t+$value->gst+$value->deliveryboy_commission;
                    }
                    else
                    {
                        $amount=$value->total_price;
                    }
                }
                else
                {
                    $coupon_disount=$value->coupon_disount;

                    $sub_t = $value->sub_total-$coupon_disount;
                    

                    if($value->bid_id==0)
                    {
                        $amount=$sub_t+$value->gst+$value->deliveryboy_commission;
                    }
                    else
                    {
                        $amount=$value->total_price;
                    }
                }
                
               $ar[]=array('id'=>$value->id,'session_id'=>$value->session_id,'customer_name'=>$name,'vendor_name'=>$vendor->shop_name,'address'=>$full_address,'payment_status_name'=>$payment_status,'payment_type'=>$value->payment_option,'service_status'=>$order_status,'amount'=>$amount,'created_date'=>date('d-m-Y',$value->created_at),'order'=>$value->order_status,'review_status'=>$review_status,'vendor_id'=>$value->vendor_id,'user_id'=>$value->user_id,'payment_status'=>$value->payment_status);
            }
            return array('status' =>TRUE, 'orders'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Orders");
        }
    
    
}


function orderDetails($oid)
{
        $qry = $this->db->query("select * from orders where id='".$oid."'");
        if($qry->num_rows()>0)
        {
                $value = $qry->row();
                            $cart = $this->db->query("select * from cart where session_id='".$value->session_id."'");
                            $cartdetails = $cart->result();
                            $cartdata=[];
                            $unit_price=0;
                            foreach ($cartdetails as $c) 
                            {
                                    $var = $this->db->query("select * from link_variant where id='".$c->variant_id."'");
                                    $variants = $var->row();
                                    if($c->status==1)
                                    {
                                        $refundmsg = "Refund Request sent";
                                    }
                                    else if($c->status==2)
                                    {
                                        $refundmsg = "Refund Completed";
                                    }
                                    else
                                    {
                                        $refundmsg = "";
                                    }

                                    $im = $this->db->query("select * from product_images where product_id='".$variants->product_id."' and variant_id='".$variants->id."'");
                                    $images = $im->row();


                                    if($images->image!='')
                                    {
                                        $img = base_url()."uploads/products/".$images->image;
                                    }
                                    else
                                    {
                                        $img = base_url()."uploads/noproduct.png";
                                    }

                                    
                                    $or = $this->db->query("select * from orders where session_id='".$c->session_id."'");
                                    $ord_s = $or->row();
                                    if($ord_s->order_status==5 || $ord_s->order_status==7)
                                    {
                                        $pro = $this->db->query("select * from products where id='".$variants->product_id."' and delete_status=0");
                                        $products = $pro->row();

                                            $cancel_status = $products->cancel_status;
                                            $return_status = $products->return_status;
                                        if($cancel_status=='yes' || $return_status=='yes')
                                        {
                                            $orderDate = $ord_s->created_date;
                                            $days = $products->return_noof_days;

                                           $ldate = strtotime(date("Y-m-d", strtotime($orderDate)) . " +".$days."days"); 
                                           $odr_start_date = time();
                                           if($ldate<$odr_start_date)
                                           {
                                                 $refund_status=false;
                                           }
                                           else
                                           {
                                                $refund_status=true;
                                           }
                                        }
                                        else
                                        {
                                            $refund_status=false;
                                        }

                                    }
                                    else
                                    {
                                        $refund_status=false;
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
                                    $pro = $this->db->query("select * from products where id='".$variants->product_id."' and delete_status=0");
                                    $products = $pro->row();

                                    $ven_q = $this->db->query("select * from vendor_shop where id='".$c->vendor_id."'");
                                    $ven_row = $ven_q->row();

                                $cartdata[]=array('cartid'=>$c->id,'productname'=>$products->name,'price'=>$c->price,'quantity'=>$c->quantity,'total_price'=>$c->unit_price,'image'=>$img,'attributes'=>$attributes,'refund_status'=>$refund_status,'product_id'=>$variants->product_id,'refundmsg'=>$refundmsg,'status'=>$c->status,'shop_name'=>$ven_row->shop_name);

                                $unit_price = $c->unit_price+$unit_price;
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
            


                $full_address = $address->address.", ".$address->area."".$city_row->city_name.", ".$state_row->state_name.", ".$value->pincode;
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
                
                if($value->coupon_id==0)
                {
                    $coupon_disount="0";
                }
                else
                {
                    $coupon_disount=$value->coupon_disount;
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


                    if($value->bid_id==0)
                    {
                        $bid_status = 'no';
                    }
                    else
                    {   
                        $bid_status = 'yes';
                    }

                    $created_date  = date('d-m-Y,h:i A',$value->created_at);
                
               $ar=array('id'=>$value->id,'bid_status'=>$bid_status,'session_id'=>$value->session_id,'delivery_date'=>$value->delivery_timeslots,'order_status'=>$order_status,'vendor_name'=>$vendor->shop_name,'useraddress'=>$full_address,'payment_status'=>$payment_status,'payment_type'=>$value->payment_option,'amount'=>$value->total_price,'sub_total'=>$unit_price,'placed_on'=>date('d-m-Y',$value->created_at),'cartdetails'=>$cartdata,'customer_name'=>$name,'mobile'=>$address->mobile,'coupon_disount'=>$coupon_disount,'deliveryboy_commission'=>$value->deliveryboy_commission,'gst'=>$value->gst,'delivery_name'=>$dl_name,'delivery_phone'=>$dl_phone,'alternative_mobiles'=>$alternative_mobiles,'order_status1'=>$show,'vendor_id'=>$value->vendor_id,'user_id'=>$value->user_id,'owner_name'=>$vendor->owner_name,'vendor_mobile'=>$vendor->mobile,'address'=>$vendor->address,'city'=>$vendor->city,'order_condition'=>$value->order_status,'user_address'=>$value->user_address,'accept_status'=>$value->accept_status,'created_date'=>$created_date);
            
            return array('status' =>TRUE, 'ordersdetails'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Order ID Wrong");
        }
    
    

}
  




    function whishList($user_id)
{
        $qry = $this->db->query("select * from whish_list where user_id='".$user_id."'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $wl) 
                {
                    
                     $prod = $this->db->query("SELECT vendor_shop.shop_name,vendor_shop.seo_url as shop_seo_url,link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id where link_variant.saleprice!=0 and link_variant.status=1 and  products.id='".$wl->product_id."' and products.status=1 and products.delete_status=0 and vendor_shop.status=1 group by link_variant.product_id order by products.id ASC");
                     $value = $prod->row();

                    /*$qry1 = $this->db->query("SELECT * FROM `link_variant` where saleprice!=0 and status=1 and product_id='".$value->id."'");
                    $value12 = $qry1->row();*/

                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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

                    if($value->saleprice!='')
                    {
                       $slaeprice =  $value->saleprice;
                    }
                    else
                    {
                        $slaeprice=0;
                    }

                    if($value->price!='')
                    {
                       $price = $value->price;
                    }
                    else
                    {
                        $price =0;
                    }
                        if($value->id!=''){
                         $ar[]=array('id'=>$value->id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$value->name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$brand->brand_name,'shop'=>$value->shop_name,'shop_seo_url'=>$value->shop_seo_url,'price'=>$price,'saleprice'=>$slaeprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat,'seo_url'=>$value->seo_url);
                        }
                        
                    

                  
                }
            return array('status' =>TRUE,'product_list'=>$ar);
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"No Whishlist Products");
        }
}



function getUserLocation($user_id,$selectedlocation)
{

    $prepAddr = str_replace(' ','+',$selectedlocation);
    $apiKey = 'AIzaSyDiiWRvGqRwOJOVqYc8GEQhBuWj4mgSSTM'; // Google maps now requires an API key.

    $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($prepAddr).'&sensor=false&key='.$apiKey);

    $output= json_decode($geocode);
    $lat = $output->results[0]->geometry->location->lat;
    $lng = $output->results[0]->geometry->location->lng; 

    $admin = $this->db->query("select * from admin where id=1");
                    $search_distance = $admin->row()->distance;
     $shop_qry = $this->db->query("SELECT id,( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop having distance<'".$search_distance."'");
        if($shop_qry->num_rows()>0)
        {
            if($user_id=='guest')
            {
                        

                        $admin = $this->db->query("select * from admin where id=1");
                        $search_distance = $admin->row()->distance;

                        $shop_qry = $this->db->query("SELECT id,( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop having distance<'".$search_distance."'");
                        if($shop_qry->num_rows()>0)
                        {
                            $ins = $this->db->insert("guest_users",array('lat'=>$lat,'lng'=>$lng,'location'=>$selectedlocation),array('id'=>$user_id));
                            if($ins)
                            {
                                $last_id = $this->db->insert_id();
                                    $g_sess_arr = array(
                                        'guest_user_id' => $last_id,
                                        'guest_logged_in' => true
                                    ); 
                                    $this->session->set_userdata('userdata', $g_sess_arr);
                               echo '@success'; die;  
                            }
                            
                        }
                        else
                        {
                            echo '@error'; die;
                        }
            }
            else
            {

                $upd = $this->db->update("users",array('lat'=>$lat,'lng'=>$lng,'home_location'=>$selectedlocation),array('id'=>$user_id));
                //echo $this->db->last_query(); die;
                if($upd)
                {
                   echo '@success'; die;
                }
                else
                {
                   echo '@error'; die;
                }

            }

        }
        else
        {
            echo '@error'; die;
        }
}


function checkValidLocation($user_id)
{
     $qry = $this->db->query("select * from users where id='".$user_id."'");
     $row = $qry->row();
        
        $lat = $row->lat;
        $lng = $row->lng;
        if($lat=='' && $lng=='')
        {
            return FALSE;
        }
        else
        {
            $admin = $this->db->query("select * from admin where id=1");
            $search_distance = $admin->row()->distance;

            $shop_qry = $this->db->query("SELECT id,( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop having distance<'".$search_distance."'");
            if($shop_qry->num_rows()>0)
            {
                /*$upd = $this->db->update("users",array('lat'=>$lat,'lng'=>$lng,'home_location'=>$address),array('id'=>$user_id));
                if($upd)
                {*/
                    return TRUE;
                //}
            }
            else
            {
                return FALSE;
            }
        }

        

}





function getBanners($user_id)
    {
        if($user_id=='guest')
        {
             $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $users = $this->db->query("select * from users where id='".$user_id."'");
            $users_row = $users->row();
            $address = $users_row->address_id;

            $lat =   $users_row->lat;
            $lng =   $users_row->lng; 
        }
             


            $admin = $this->db->query("select * from admin where id=1");
            $search_distance = $admin->row()->distance; 

            $shop_qry = $this->db->query("SELECT id, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop having distance<'".$search_distance."'");
            $shops=$shop_qry->result();

            $shop_ids = array_column($shops, 'id');
            $imp = implode(",", $shop_ids);
        $qry = $this->db->query("select * from banners WHERE find_in_set(shop_id,'".$imp."') and position=1 and type='shops'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    

                    if($value->web_image!='')
                    {
                        $img = base_url()."uploads/banners/".$value->web_image;
                    }
                    else
                    {
                        $img = "";
                    }

                    if($value->type=='products')
                        {
                            $prod_qry = $this->db->query("select * from products where id='".$value->product_id."' and delete_status=0");
                            $dat1 = $prod_qry->row();
                            $title = $dat1->name;
                            $shop_id = $dat1->shop_id;
                            $product_details = array('product_title'=>$title,'product_id'=>$dat1->id,'seo_url'=>$dat1->seo_url,'shop_id'=>$shop_id);
                        }
                        else
                        {
                            $prod_qry = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                            $dat1 = $prod_qry->row();

                             $adm_qry = $this->db->query("select * from admin_comissions where shop_id='".$value->shop_id."' order by id desc");
                             $adm_row = $adm_qry->row();
                            
                            $product_details = array('shop_name'=>$dat1->shop_name,'cat_id'=>$adm_row->cat_id,'seo_url'=>$dat1->seo_url,'shop_id'=>$value->shop_id);
                        }

                   $ar[]=array('id'=>$value->id,'title'=>$value->title,'image'=>$img,'type'=>$value->type,'product_details'=>$product_details);
                }
               
        }
        else
        {
            $ar= array();
        }


        return $ar;
    }
 

function getHomeLimitCategories()
{  
         $qry = $this->db->query("select * from categories where status=1 order by priority asc");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    if($value->app_image!='')
                    {
                        $img = base_url()."uploads/categories/".$value->app_image;
                    }
                    else
                    {
                        $img = "";
                    }

                      $adm = $this->db->query("select * from admin_comissions where cat_id='".$value->id."' and subcategory_ids!=''");
                      $admin = $adm->num_rows();
                      if($admin>0)
                      {
                        $ar[]=array('id'=>$value->id,'title'=>$value->category_name,'image'=>$img,'products_count'=>"",'seo_url'=>$value->seo_url);
                      }     
                   
                }
                return $ar;
        }
        else
        {
            return array();
        }
    }


function getHomeAllCategories()
{  
         $qry = $this->db->query("select * from categories where status=1 order by priority asc");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    if($value->app_image!='')
                    {
                        $img = base_url()."uploads/categories/".$value->app_image;
                    }
                    else
                    {
                        $img = "";
                    }

                      $adm = $this->db->query("select * from admin_comissions where cat_id='".$value->id."' and subcategory_ids!=''");
                      $admin = $adm->num_rows();
                      if($admin>0)
                      {
                        $ar[]=array('id'=>$value->id,'title'=>$value->category_name,'image'=>$img,'products_count'=>"",'seo_url'=>$value->seo_url);
                      }     
                   
                }
                return $ar;
        }
        else
        {
            return array();
        }
    }




function getAllshopsWithoutcategory($user_id)
{
    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {

        $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
        $row = $qry->row();
        $lat=$row->lat;
        $lng=$row->lng;
    }
        $admin = $this->db->query("select * from admin where id=1");
        $search_distance = $admin->row()->distance;
     
     $qry = $this->db->query("SELECT vendor_shop.*,admin_comissions.shop_id, admin_comissions.cat_id, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop INNER JOIN admin_comissions ON vendor_shop.id=admin_comissions.shop_id INNER JOIN products ON vendor_shop.id=products.shop_id where vendor_shop.status=1 and products.status=1 and products.delete_status=0 group by products.shop_id having distance<".$search_distance." order by distance asc LIMIT 5");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                        if($value->shop_logo!='')
                        {
                            $img = base_url()."uploads/shops/".$value->shop_logo;
                        }
                        else
                        {
                            $img = base_url()."uploads/noproduct.png";;
                        }


                        
                        $shop_qry = $this->db->query("select * from shop_favorites where shop_id='".$value->id."' and user_id='".$user_id."'");
                        if($shop_qry->num_rows()>0)
                        {
                            $shop_not = true;
                        }
                        else
                        {
                            $shop_not = false;
                        }

                        $pro = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.shop_id='".$value->id."' and products.status=1 and products.delete_status=0 group by link_variant.product_id order by products.id ASC");
                     $product_total = $pro->num_rows();


                     $dealpro = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.shop_id='".$value->id."' and products.status=1 and products.delete_status=0 and products.top_deal='yes' group by link_variant.product_id order by products.id ASC");
                     $dealproduct_total = $dealpro->num_rows();
                        if($value->status==1)
                        {
                            $stat = "Open";
                        }
                        else
                        {
                             $stat = "Closed";
                        }

                    $ar[]=array('id'=>$value->id,'cat_id'=>$value->cat_id,'shop_name'=>$value->shop_name,'description'=>$value->description,'image'=>$img,'status'=>$stat,'shop_not'=>$shop_not,'distance'=>round($value->distance),'product_total'=>$product_total,'address'=>$value->city,'deal_products'=>$dealproduct_total,'seo_url'=>$value->seo_url);
                }
            return $ar;
        }
        else{
            return array();
        }
    

    }

    public function get_count($user_id) 
    {
        if($user_id=='guest')
       {
            $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
       }
       else
       {

            $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
            $row = $qry->row();
            $lat=$row->lat;
            $lng=$row->lng;
        }
        $admin = $this->db->query("select * from admin where id=1");
        $search_distance = $admin->row()->distance;
     
        $qry = $this->db->query("SELECT vendor_shop.*,admin_comissions.shop_id, admin_comissions.cat_id, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop INNER JOIN admin_comissions ON vendor_shop.id=admin_comissions.shop_id INNER JOIN products ON vendor_shop.id=products.shop_id where vendor_shop.status=1 and products.status=1 and products.delete_status=0 group by products.shop_id having distance<".$search_distance." order by distance asc");
       return $qry->num_rows();
    
    }

    function getAllshopsWithoutcategoriesList($limit, $start, $user_id)
{
    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {

        $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
        $row = $qry->row();
        $lat=$row->lat;
        $lng=$row->lng;
    }
        $admin = $this->db->query("select * from admin where id=1");
        $search_distance = $admin->row()->distance;
     $qry = $this->db->query("SELECT vendor_shop.*,admin_comissions.shop_id, admin_comissions.cat_id, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop INNER JOIN admin_comissions ON vendor_shop.id=admin_comissions.shop_id INNER JOIN products ON vendor_shop.id=products.shop_id where vendor_shop.status=1 and products.status=1 and products.delete_status=0 group by products.shop_id having distance<".$search_distance." order by distance asc LIMIT ".$start.",".$limit);
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                        if($value->shop_logo!='')
                        {
                            $img = base_url()."uploads/shops/".$value->shop_logo;
                        }
                        else
                        {
                            $img = base_url()."uploads/noproduct.png";;
                        }


                        
                        $shop_qry = $this->db->query("select * from shop_favorites where shop_id='".$value->id."' and user_id='".$user_id."'");
                        if($shop_qry->num_rows()>0)
                        {
                            $shop_not = true;
                        }
                        else
                        {
                            $shop_not = false;
                        }

                        $pro = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.shop_id='".$value->id."' and products.status=1 and products.delete_status=0 group by link_variant.product_id order by products.id ASC");
                     $product_total = $pro->num_rows();


                     $dealpro = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.shop_id='".$value->id."' and products.status=1 and products.top_deal='yes' and products.delete_status=0 group by link_variant.product_id order by products.id ASC");
                     $dealproduct_total = $dealpro->num_rows();
                        if($value->status==1)
                        {
                            $stat = "Open";
                        }
                        else
                        {
                             $stat = "Closed";
                        }

                    $ar[]=array('id'=>$value->id,'cat_id'=>$value->cat_id,'shop_name'=>$value->shop_name,'description'=>$value->description,'image'=>$img,'status'=>$stat,'shop_not'=>$shop_not,'distance'=>round($value->distance),'product_total'=>$product_total,'address'=>$value->city,'deal_products'=>$dealproduct_total,'seo_url'=>$value->seo_url);
                }
            return $ar;
        }
        else{
            return array();
        }
    

    }




    function getVendorBanners($vendor_id,$user_id)
    {
        if($user_id=='guest'){}else{
             $this->db->insert("shop_visit",array('shop_id'=>$vendor_id,'user_id'=>$user_id));
        }
       

            $bannerad = $this->db->query("select * from bannerads where shop_id='".$vendor_id."' and blocks=1");
            $bannerads = $bannerad->row();

                    if($bannerads->app_image!='')
                    {
                        $ban1 = base_url()."uploads/bannerads/".$bannerads->app_image;
                    }
                    else
                    {
                        $ban1 = "";
                    }
        $bannerad1 = $this->db->query("select * from bannerads where shop_id='".$vendor_id."' and blocks=2");
        $bannerads1 = $bannerad1->row();
                    if($bannerads1->app_image!='')
                    {
                        $ban2 = base_url()."uploads/bannerads/".$bannerads1->app_image;
                    }
                    else
                    {
                        $ban2 = "";
                    }



        $qry = $this->db->query("select * from vendor_shop_banners where shop_id='".$vendor_id."'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    if($value->app_banner!='')
                    {
                        $img = base_url()."uploads/banners/".$value->web_banner;
                    }
                    else
                    {
                        $img = "";
                    }
                   $ar[]=array('id'=>$value->id,'title'=>$value->title,'image'=>$img);
                }
                return array('bannerslist'=>$ar,'banneradd1'=>$ban1,'banneradd2'=>$ban2);
        }
        else
        {
            $ar = array();
            return array('bannerslist'=>$ar,'banner_ads'=>$ban);
        }
        
           
    }

  function getcategoryWithshopID($shop_id)
    {
        $admin = $this->db->query("SELECT cat_id,sub_cat_id FROM `products` where shop_id='".$shop_id."' and status=1 and delete_status=0 GROUP by sub_cat_id");
    
        
        $admin_result = $admin->result();
        if($admin->num_rows()>0)
        {
            foreach ($admin_result as $admin_row) 
            {
                        $prod = $this->db->query("SELECT id FROM `products` where shop_id='".$shop_id."' and cat_id='".$admin_row->cat_id."' and sub_cat_id='".$admin_row->sub_cat_id."' and status=1 and delete_status=0");
                        $prod_total_rows = $prod->num_rows();
                        $subcategory_ids =$admin_row->sub_cat_id;
                         $qry = $this->db->query("select * from sub_categories where id='".$subcategory_ids."'");
                         $value = $qry->row();
                          if($value->app_image!='')
                            {
                                $img = base_url()."uploads/sub_categories/".$value->app_image;
                            }
                            else
                            {
                                $img = base_url()."uploads/noproduct.png";
                            }
                            if($qry->num_rows()>0)
                            {
                       /* $ar[]=array('id'=>$value->id,'cat_id'=>$value->cat_id,'sub_cat_id'=>$admin_row->sub_cat_id,'title'=>$value->sub_category_name,'image'=>$img,'products'=>$prod_total_rows);*/

                        $ar[]=array('id'=>$value->id,'category_name'=>$value->sub_category_name,'description'=>$value->description,'image'=>$img,'seo_url'=>$value->seo_url);
                    }
                    

            }

            if(count($ar)==0)
            {
                return array();
            }
            else
            {
                return $ar;
            }

            
         }
         else
        {
            return array('status' =>FALSE,'message'=>"No Sub Categories",'best_products'=>$products);
        }


        

    }

 /*function getcategoryWithshopID($shop_id)
 {
        $qry = $this->db->query("SELECT * FROM `admin_comissions` where shop_id='".$shop_id."'");
        $result = $qry->result();
        if($qry->num_rows()>0)
        {
            $ar=[];
            foreach ($result as $value) 
            {
               $cat = $this->db->query("SELECT * FROM `categories` where id='".$value->cat_id."'");
               $categories = $cat->row();

               if($categories->app_image!='')
               {
                    $img = base_url()."uploads/categories/".$categories->app_image;
               }
               else
               {
                    $img = base_url()."uploads/noproduct.png";
               }

               $ar[]=array('id'=>$categories->id,'category_name'=>$categories->category_name,'description'=>$categories->description,'image'=>$img,'seo_url'=>$categories->seo_url);
            }
            return $ar;
        }
        else
        {
            return array();
        }
 }*/



    function bestSeller($shop_id,$user_id)
    {
          $qry = $this->db->query("SELECT cart.variant_id FROM orders INNER JOIN cart ON orders.session_id=cart.session_id where orders.vendor_id='".$shop_id."' and orders.bid_id=0 group by cart.variant_id");
          $result = $qry->result();
          $prod_ar=[];
          foreach ($result as $value) 
          {
             $link = $this->db->query("select * from link_variant where status=1 and id='".$value->variant_id."'");
             $link_row = $link->row();

             $prod = $this->db->query("select * from products where id='".$link_row->product_id."' and status=1 and delete_status=0");
             $products = $prod->row();
                    $im = $this->db->query("select * from product_images where product_id='".$link_row->product_id."' and variant_id='".$value->variant_id."'");
                     $images = $im->row();
                    if($images->image!='')
                    {
                        $img = base_url()."uploads/products/".$images->image;
                    }
                    else
                    {
                        $img = base_url()."uploads/noproduct.png";
                    }
                $wish = $this->db->query("select * from whish_list where product_id='".$link_row->product_id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                    }
                    else
                    {
                        $stat = false;
                    }

             if($prod->num_rows()>0)
             {

                 $shps = $this->db->query("select * from vendor_shop where id='".$products->shop_id."' and delete_status=0");
                 $shps_row = $shps->row();

                $prod_ar[]=array('id'=>$products->id,'variant_id'=>$value->variant_id,'name'=>$products->name,'shop_id'=>$products->shop_id,'shop'=>$shps_row->shop_name,'cat_id'=>$products->cat_id,'image'=>$img,'saleprice'=>$link_row->saleprice,'whishlist_status'=>$stat,'seo_url'=>$products->seo_url);
             }
          }
          return $prod_ar;



    }



    function getProductDetails($pid,$user_id)
 {
        $this->db->insert("most_viewed_products",array('product_id'=>$pid));
        $qry = $this->db->query("select * from products where id='".$pid."' and delete_status=0");
        $value = $qry->row();
        if($qry->num_rows()>0)
        {
            $link_vari11 = $this->db->query("select * from link_variant where product_id='".$value->id."' and status=1 ");
                       $link_variants111 = $link_vari11->row();

                    $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$link_variants111->id."'");
                        $images1 = $im->row();

                        

                        if($images1->image!='')
                        {
                            $img = base_url()."uploads/products/".$images1->image;
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
                    $brnd = $this->db->query("select * from attr_brands where id='".$value->brand."'");
                    $brand = $brnd->row();

                    $vendo = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                    $vendor = $vendo->row();

                    $link_vari = $this->db->query("select * from link_variant where product_id='".$value->id."' and status=1 ");
                    $link_variants1 = $link_vari->result();
                    $link_variants=[];
                    foreach ($link_variants1 as $link_value) 
                    {
                       $im1 = $this->db->query("select * from product_images where product_id='".$link_value->product_id."' and variant_id='".$link_value->id."'");
                        $img_result1 = $im1->result();
                        
                            $img_ar1=[];
                            if($im1->num_rows()>0)
                            {
                                foreach ($img_result1 as $images1) 
                                {
                                    if($images1->image!='')
                                    {
                                        $img = base_url()."uploads/products/".$images1->image;
                                    }
                                    else
                                    {
                                        $img = base_url()."uploads/noproduct.png";
                                    }

                                    $img_ar1[]=array('image'=>$img);
                                }
                            }
                            else
                            {
                                $img = base_url()."uploads/noproduct.png";
                                $img_ar1[]=array('image'=>$img);
                            }
                            

                                if($link_value->stock>10)
                                {
                                    $stock = "Instock";
                                }
                                else
                                {
                                   $stock = $link_value->stock." Left";
                                }
                             $link_variants[]=array('id'=>$link_value->id,'price'=>$link_value->price,'saleprice'=>$link_value->saleprice,'jsondata'=>json_decode($link_value->jsondata),'imageslist'=>$img_ar1,'stock'=>$stock);
                           
                    }


                     $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                    }
                    else
                    {
                        $stat = false;
                    }


                    
                   $ar=array('id'=>$value->id,'shop_id'=>$value->shop_id,'seo_url'=>$vendor->seo_url,'vendor_description'=>$vendor->description,'location'=>$vendor->city,'name'=>$value->name,'description'=>$value->descp,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$brand->brand_name,'brand_id'=>$value->brand,'shop'=>$vendor->shop_name,'product_tags'=>$value->product_tags,'meta_tag_title'=>$value->meta_tag_title,'meta_tag_description'=>$value->meta_tag_description,'meta_tag_keywords'=>$value->meta_tag_keywords,'key_features'=>$value->key_features,'cancel_status'=>$value->cancel_status,'return_status'=>$value->return_status,'attributes'=>$att1,'link_variants'=>$link_variants,'image'=>$img,'selling_date'=>date('d-m-Y',strtotime($value->selling_date)),'taxname'=>$value->taxname,'manage_stock'=>$value->manage_stock,'variant_product'=>$value->variant_product,'status'=>$value->status,'cat_id'=>$value->cat_id,'sub_cat_id'=>$value->sub_cat_id,'tax_class'=>$value->tax_class,'availabile_stock_status'=>$value->availabile_stock_status,'maximum_quantity'=>$link_value->stock,'whishlist_status'=>$stat);
                
            return $ar;
        }
        else
        {
            return 'false';
        }
 }


 function productDetailsFilter($pid,$json_data)
 {
        $jsons = json_encode($json_data); 

        $qry = $this->db->query("select * from products where id='".$pid."' and delete_status=0");
        $value = $qry->row();
        if($qry->num_rows()>0)
        {
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
                    $link_vari = $this->db->query("select * from link_variant where product_id='".$value->id."' and status=1 and jsondata LIKE ".$jsons);
                    $link_variants1 = $link_vari->result();
                    $link_variants=[];
                    foreach ($link_variants1 as $link_value) 
                    {
                            $im1 = $this->db->query("select * from product_images where product_id='".$link_value->product_id."' and variant_id='".$link_value->id."'");
                            $img_result1 = $im1->result();
                        
                            $img_ar1=[];
                            if($im1->num_rows()==0)
                            {
                                $img = base_url()."uploads/noproduct.png";
                                $img_ar1[]=array('image'=>$img);
                            }
                            else
                            {
                                foreach ($img_result1 as $images1) 
                                {
                                    if($images1->image!='')
                                    {
                                        $img = base_url()."uploads/products/".$images1->image;
                                    }
                                    else
                                    {
                                        $img = base_url()."uploads/noproduct.png";
                                    }

                                    $img_ar1[]=array('image'=>$img);
                                }
                            }

                                if($link_value->stock>10)
                                {
                                    $stock = "Instock";
                                }
                                else
                                {
                                   $stock = $link_value->stock." Left";
                                }

                             $link_variants[]=array('id'=>$link_value->id,'price'=>$link_value->price,'saleprice'=>$link_value->saleprice,'jsondata'=>json_decode($link_value->jsondata),'imageslist'=>$img_ar1,'stock'=>$stock);
                    }

                    if($link_vari->num_rows()>0)
                    {
                            $ar=array('id'=>$value->id,'shop_id'=>$value->shop_id,'name'=>$value->name,'description'=>$value->descp,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$brand->brand_name,'brand_id'=>$value->brand,'shop'=>$vendor->shop_name,'product_tags'=>$value->product_tags,'meta_tag_title'=>$value->meta_tag_title,'meta_tag_description'=>$value->meta_tag_description,'meta_tag_keywords'=>$value->meta_tag_keywords,'key_features'=>$value->key_features,'cancel_status'=>$value->cancel_status,'return_status'=>$value->return_status,'attributes'=>$att1,'link_variants'=>$link_variants,'image'=>$img,'selling_date'=>date('d-m-Y',strtotime($value->selling_date)),'taxname'=>$value->taxname,'manage_stock'=>$value->manage_stock,'variant_product'=>$value->variant_product,'status'=>$value->status,'cat_id'=>$value->cat_id,'sub_cat_id'=>$value->sub_cat_id,'tax_class'=>$value->tax_class,'availabile_stock_status'=>$value->availabile_stock_status);
                
                                return $ar;
                    }
                    else
                    {
                            return 'false';
                    }
        }
        else
        {
            return 'false';
        }
    


 }



function getTopDeals($user_id)
{
    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];
            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {
        $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
        $row = $qry->row();

        $lat = $row->lat;
        $lng = $row->lng;
    }

    $admin = $this->db->query("select * from admin where id=1");
    $search_distance = $admin->row()->distance;
   
     $deal_qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.*, products.*, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance,vendor_shop.shop_name FROM link_variant INNER JOIN products ON link_variant.product_id=products.id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id where vendor_shop.status=1 and link_variant.saleprice!=0 and link_variant.status=1 and products.top_deal='yes' and products.availabile_stock_status='available' and products.delete_status=0 group by link_variant.product_id having distance<".$search_distance." order by products.id DESC LIMIT 6");

        $dat = $deal_qry->result();
        if($deal_qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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
                   


                    

                    $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                    }
                    else
                    {
                        $stat = false;
                    }
                   
                        
                            $ar[]=array('id'=>$value->id,'variant_id'=>$value->variant_id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$value->name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$value->shop_name,'price'=>$value->price,'saleprice'=>$value->saleprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat,'distance'=>$value->distance,'seo_url'=>$value->seo_url);
                        
                }

            return $ar;
        }
        else
        {
           
            return array();
        }
}


function getmostViewedProducts($user_id)
 {
    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];
            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {
      $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
      $row = $qry->row();
      $lat=$row->lat;
      $lng=$row->lng;
    }
    $admin = $this->db->query("select * from admin where id=1");
    $search_distance = $admin->row()->distance;


        $qry = $this->db->query("SELECT link_variant.id as variant_id ,link_variant.saleprice,link_variant.price,link_variant.stock,most_viewed_products.product_id,count(most_viewed_products.id) as cnt,products.*,vendor_shop.id as vendor_id,vendor_shop.shop_name,( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM most_viewed_products INNER JOIN products ON products.id =most_viewed_products.product_id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id INNER JOIN link_variant ON link_variant.product_id=most_viewed_products.product_id WHERE products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' and vendor_shop.status=1 and link_variant.saleprice!='0' and link_variant.status=1 GROUP by most_viewed_products.product_id having distance<'".$search_distance."' order by most_viewed_products.id DESC LIMIT 6");
        $dat = $qry->result();
    
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                    

                        /*$qry1 = $this->db->query("SELECT * FROM `link_variant` where saleprice!='0' and status=1 and product_id='".$value->id."'");
                        $value12 = $qry1->row();*/

                         $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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
                        


                            
                    $state_id = $row->state_id;
                    $city_id = $row->address_id;
                    $pincode_id = $row->pincode_id;

                                if($value->status==1)
                                {
                                    $shopstat = "Open";
                                }
                                else
                                {
                                     $shopstat = "Closed";
                                }


                        $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                        if($wish->num_rows()>0)
                        {
                            $stat = true;
                        }
                        else
                        {
                            $stat = false;
                        }
                        if($value->saleprice!='')
                        {
                           $slaeprice =  $value->saleprice;
                        }
                        else
                        {
                            $slaeprice=0;
                        }

                        if($value->price!='')
                        {
                           $price = $value->price;
                        }
                        else
                        {
                            $price =0;
                        }
                        
                            $name = $value->name;

                             $ar[]=array('id'=>$value->id,'variant_id'=>$value->variant_id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$value->shop_name,'price'=>$price,'saleprice'=>$slaeprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat,'shop_status'=>$shopstat,'distance'=>round($value->distance),'seo_url'=>$value->seo_url);
                             
                    }

            return $ar;
        }
        else
        {
            return array();
        }

    

 
 }





function addToCart($variant_id,$vendor_id,$user_id,$price,$quantity,$session_id)
{
  $shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
  $shop_num = $shop_qry->row();
    if($shop_num->status==0)
    {
        //$msg = "Shop Closed";
        //return array('status' =>FALSE,'message'=>$msg);
        echo '@shopclosed'; die; 
    }
    $session_vendor_id = $_SESSION['session_data']['vendor_id'];

    
    $session_id = $_SESSION['session_data']['session_id'];


    $chk_quant_qry = $this->db->query("select * from link_variant where id='".$variant_id."'");
    $chk_quant_row = $chk_quant_qry->row();
    $stock = $chk_quant_row->stock;

    $chk = $this->db->query("select * from cart where session_id='".$session_id."' and vendor_id='".$vendor_id."'");
    if($chk->num_rows()>0)
    {
        $qry_chk = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."' and vendor_id='".$vendor_id."' and variant_id='".$variant_id."'");
        if($qry_chk->num_rows()>0)
        {
                $row = $qry_chk->row();
                $qty = $row->quantity;
                $quantity_f = $quantity+$qty;
                if($stock<$quantity_f)
                {
                    $msg = "Left only ".$stock." Products";
                    echo '@error'; die; 
                    //return array('status' =>FALSE,'message'=>$msg);
                }


                $un_pric = $price*$quantity_f;
                $ar = array('quantity'=>$quantity_f,'unit_price'=>$un_pric);
                $wr = array('session_id'=>$session_id,'variant_id'=>$variant_id,'vendor_id'=>$vendor_id,'user_id'=>$user_id);
                $ins = $this->db->update("cart",$ar,$wr);
                if($ins)
                {
                     $sess_arr = array(
                    'session_id' => $session_id,
                    'vendor_id' => $vendor_id,
                    'session_status' => true
                ); 
        $this->session->set_userdata('session_data', $sess_arr);

                    $cart_qry = $this->db->query("select * from cart where session_id='".$session_id."'");
                    $cart_count = $cart_qry->num_rows();
                    //return array('status' =>TRUE,'message'=>"Product added to cart");
                    echo '@success@'.$cart_count.'@'.$session_id; die; 
                }
        }
        else
        {

                if($stock<$quantity)
                {
                    $msg = "Left only ".$stock." Products";
                    //return array('status' =>FALSE,'message'=>$msg);
                    echo '@error'; die; 
                }

           $tprice = $price*$quantity;
            $ar = array('session_id'=>$session_id,'variant_id'=>$variant_id,'vendor_id'=>$vendor_id,'user_id'=>$user_id,'price'=>$price,'quantity'=>$quantity,'unit_price'=>$tprice);
            $ins = $this->db->insert("cart",$ar);
            if($ins)
            {
                 $sess_arr = array(
                    'session_id' => $session_id,
                    'vendor_id' => $vendor_id,
                    'session_status' => true
                ); 
        $this->session->set_userdata('session_data', $sess_arr);
                   $cart_qry = $this->db->query("select * from cart where session_id='".$session_id."'");
                    $cart_count = $cart_qry->num_rows();
                    //return array('status' =>TRUE,'message'=>"Product added to cart");
                    echo '@success@'.$cart_count.'@'.$session_id; die; 
            } 
        }
    }
    else
    {

       /* $check_qry = $this->db->query("select * from cart where session_id='".$session_id."' and vendor_id='".$vendor_id."'");
        if($check_qry->num_rows()>0)
        {

        }
        else
        {
*/        



        $session_id1 = rand(10000000000000,100000000000000);
        $sess_arr = array(
                    'session_id' => $session_id1,
                     'vendor_id' => $vendor_id,
                    'session_status' => true
                ); 
        $this->session->set_userdata('session_data', $sess_arr);

         $session_id = $_SESSION['session_data']['session_id']; 
        if($stock<$quantity)
                {
                    $msg = "Left only ".$stock." Products";
                    //return array('status' =>FALSE,'message'=>$msg);
                    echo '@error'; die; 
                }

            $tprice = $price*$quantity;
            $ar = array('session_id'=>$session_id,'variant_id'=>$variant_id,'vendor_id'=>$vendor_id,'user_id'=>$user_id,'price'=>$price,'quantity'=>$quantity,'unit_price'=>$tprice);
            $ins = $this->db->insert("cart",$ar);
            if($ins)
            {
                    $cart_qry = $this->db->query("select * from cart where session_id='".$session_id."'");
                    $cart_count = $cart_qry->num_rows();
                    //return array('status' =>TRUE,'message'=>"Product added to cart");
                    echo '@success@'.$cart_count.'@'.$session_id1; die; 
            }

        //}
                
    }
}


function getshopwiseProducts($cat_id,$shop_id,$user_id,$subcat_id,$searchdata)
    {

         if($user_id=='guest')
        {
             $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $user_qry = $this->db->query("select * from users where id='".$user_id."'");
            $user_row = $user_qry->row();
              $lat = $user_row->lat;
              $lng = $user_row->lng;
        }
          $admin = $this->db->query("select * from admin where id=1");
          $search_distance = $admin->row()->distance;

          if($cat_id=='shop' && $subcat_id=='nosubcat')
          {
                   $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' and products.name LIKE '%".$searchdata."%' group by link_variant.product_id order by products.id ASC");
          }
          else if($cat_id=='shop' && $subcat_id!='nosubcat')
          {
            
               $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.sub_cat_id='".$subcat_id."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' and products.name LIKE '%".$searchdata."%' group by link_variant.product_id order by products.id ASC");
          }
          else if($cat_id!='shop' && $subcat_id=='nosubcat')
          {
              $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.cat_id='".$cat_id."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' and products.name LIKE '%".$searchdata."%' group by link_variant.product_id order by products.id ASC");
          }
          else if($cat_id!='shop' && $subcat_id!='nosubcat')
          {

              $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.delete_status=0 and products.cat_id='".$cat_id."' and products.sub_cat_id='".$subcat_id."' and products.shop_id='".$shop_id."' and products.status=1 and products.availabile_stock_status='available' and products.name LIKE '%".$searchdata."%' group by link_variant.product_id order by products.id ASC");
          }

          
          
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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
                    /*$brnd = $this->db->query("select * from attr_brands where id='".$value->brand."'");
                    $brand = $brnd->row();*/

                    $vendo = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                    $vendor = $vendo->row();


                    // print_r($value); 
                    $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                    }
                    else
                    {
                        $stat = false;
                    }

                        $ar[]=array('id'=>$value->id,'seo_url'=>$value->seo_url,'variant_id'=>$value->variant_id,'shop_id'=>$vendor->id,'variant_product'=>$value->variant_product,'name'=>$value->name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$vendor->shop_name,'price'=>$value->price,'saleprice'=>$value->saleprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat);
                    
                }


            return $ar;
        }
        else
        {
           
            return array();
        }

    }


function getProducts($cat_id,$shop_id,$user_id,$subcat_id)
    {

         if($user_id=='guest')
        {
             $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $user_qry = $this->db->query("select * from users where id='".$user_id."'");
            $user_row = $user_qry->row();
              $lat = $user_row->lat;
              $lng = $user_row->lng;
        }
          $admin = $this->db->query("select * from admin where id=1");
          $search_distance = $admin->row()->distance;

          if($cat_id=='shop')
          {
                $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.sub_cat_id='".$subcat_id."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' group by link_variant.product_id order by products.id ASC");
          }
          else
          {
            
             $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.cat_id='".$cat_id."' and products.sub_cat_id='".$subcat_id."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' group by link_variant.product_id order by products.id ASC");
          }
          
          
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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
                    /*$brnd = $this->db->query("select * from attr_brands where id='".$value->brand."'");
                    $brand = $brnd->row();*/

                    $vendo = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                    $vendor = $vendo->row();


                    // print_r($value); 
                    $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                    }
                    else
                    {
                        $stat = false;
                    }

                        $ar[]=array('id'=>$value->id,'seo_url'=>$value->seo_url,'variant_id'=>$value->variant_id,'shop_id'=>$vendor->id,'variant_product'=>$value->variant_product,'name'=>$value->name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$vendor->shop_name,'price'=>$value->price,'saleprice'=>$value->saleprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat);
                    
                }


            return $ar;
        }
        else
        {
           
            return array();
        }

    }


    

function getsortProducts($cat_id,$shop_id,$user_id,$type,$subcatid)
{

          $admin = $this->db->query("select * from admin where id=1");
          $search_distance = $admin->row()->distance;


          $user_qry = $this->db->query("select * from users where id='".$user_id."'");
          $user_row = $user_qry->row();
          $lat = $user_row->lat;
          $lng = $user_row->lng;
         
        if($type==0)
        {
          if($cat_id=='shop')
          {
               $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.sub_cat_id='".$subcatid."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' group by link_variant.product_id order by link_variant.id desc");
          }
          else
          {
              $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.cat_id='".$cat_id."' and products.sub_cat_id='".$subcatid."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' group by link_variant.product_id order by link_variant.id desc");
          }

          
        }
        else if($type==1)
        {
          if($cat_id=='shop')
          {
             $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.sub_cat_id='".$subcatid."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' group by link_variant.product_id order by link_variant.saleprice desc");
         }
         else
         {
             $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.cat_id='".$cat_id."' and products.sub_cat_id='".$subcatid."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' group by link_variant.product_id order by link_variant.saleprice desc");
         }
        }
        else if($type==2)
        {
            if($cat_id=='shop')
           {
            $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.sub_cat_id='".$subcatid."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' group by link_variant.product_id order by link_variant.saleprice asc");
           }else{

             $qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.cat_id='".$cat_id."' and products.sub_cat_id='".$subcatid."' and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' group by link_variant.product_id order by link_variant.saleprice asc");
           }
        }


        $dat = $qry->result();
         
                $ar=[];
                foreach ($dat as $value) 
                {
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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
                    /*$brnd = $this->db->query("select * from attr_brands where id='".$value->brand."'");
                    $brand = $brnd->row();*/

                    $vendo = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                    $vendor = $vendo->row();


                    // print_r($value); 
                    $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                        $fav = 'fas';
                    }
                    else
                    {
                        $stat = false;
                        $fav = 'fal';
                    }

                        $ar[]=array('id'=>$value->id,'variant_id'=>$value->variant_id,'shop_id'=>$vendor->id,'variant_product'=>$value->variant_product,'name'=>$value->name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$vendor->shop_name,'price'=>$value->price,'saleprice'=>$value->saleprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat);


                        $output .='<div class="col-lg-2 col-md-3 col-sm-4 col-6" >
                        <div class="single_product">
                            <div class="product_thumb">
                                <a class="primary_img" href="'.base_url().'web/product_view/'.$value->seo_url.'"><img src="'.$img.'" alt=""></a>
                                <a class="secondary_img" href="'.base_url().'web/product_view/'.$value->seo_url.'"><img src="'.$img.'" alt=""></a>
                                <div class="label_product">
                                </div>
                                <div class="wishlist">
                                    <a title="Add to Wishlist" onclick="addFavorite('.$value->id.')">
                                      <span id="bestselling_pro_'.$value->id.'" class="'.$fav.' fa-heart"></span>
                                    </a>
                                  </div>

                                
                            </div>
                            <div class="product_content grid_content">
                                <div class="product_content_inner">
                                    <h4 class="product_name"><a href="'.base_url().'web/product_view/'.$value->seo_url.'">'.$value->name.'</a></h4>
                                    <div class="price_box">
                                        <span class="current_price"><i class="fal fa-rupee-sign"></i> '.$value->saleprice.'</span>
                                    </div>
                                </div>
                                <div class="add_to_cart">
                                    <a onclick="addtocart('.$value->variant_id.','.$value->shop_id.','.$value->saleprice.',1)"><i class="fal fa-shopping-cart fa-lg"></i> Add to cart</a>
                                </div>
                            </div>
                            
                        </div>
                    </div>';
                    
                }


            print_r($output); 
                    die;
}



  
  function getAddress($user_id)
    {
        $qry = $this->db->query("select * from user_address where user_id='".$user_id."'");
        $result = $qry->result();
        $ar=[];
        foreach ($result as $value) 
        {

            $city_qry = $this->db->query("select * from cities where id='".$value->city."'");
            $city_row = $city_qry->row();

            $state_qry = $this->db->query("select * from states where id='".$value->state."'");
            $state_row = $state_qry->row();

            $area_qry = $this->db->query("select * from pincodes where id='".$value->pincode."'");
            $area_row = $area_qry->row();

            if($value->address_type==1)
            {
                $addres_type ="Home";
            }
            else if($value->address_type==2)
            {
                $addres_type ="Office/Commercial";
            }

            $ar[]=array('id'=>$value->id,'name'=>$value->name,'address'=>$value->address,'city'=>$city_row->city_name,'state'=>$state_row->state_name,'pincode'=>$area_row->pincode,'mobile'=>$value->mobile,'city_id'=>$value->city,'pincode_id'=>$value->pincode,'landmark'=>$value->landmark,'address_type'=>$addres_type,'address_status'=>$value->address_type,'state_id'=>$value->state);
        }

        return $ar;
    }


 
 function getstates()
{
    $qry =$this->db->query("select * from states");
    if($qry->num_rows()>0)
    {
        $state = $qry->result();
        return $state;
    }
    else
    {
        return array();
    }
}


function fetchCities($state_id)
{
         
        $qry = $this->db->query("select * from cities where state_id='".$state_id."'");
          $query=$qry->result();
          $output = '<option value="">Select City</option>';
          foreach($query as $row)
          {
            $output .= '<option value="'.$row->id.'">'.$row->city_name.'</option>';
          }
          print_r($output); die;
} 

function getPincodes($state_id,$city_id,$vendor_id)
{     
   $vendor_qry = $this->db->query("SELECT * FROM `vendor_shop` WHERE state_id='".$state_id."' and city_id='".$city_id."' and id='".$vendor_id."'");
         $vendor_row = $vendor_qry->row();
         if($vendor_qry->num_rows()>0)
         {
                 $pincode_list = explode(",", $vendor_row->vendor_pincodes);
                 $pincode_ar=[];
                 foreach ($pincode_list as $value) 
                 {
                    $chk_qry = $this->db->query("select * from pincodes where id='".$value."'");
                    $chk_pincode = $chk_qry->row();
                    if($chk_qry->num_rows()>0)
                    {
                        //$pincode_ar[]=$chk_pincode;

                        $output .= '<option value="'.$chk_pincode->id.'">'.$chk_pincode->pincode.'</option>';
                    }
                    
                 }
                 print_r($output); die;
                 /*if(count($pincode_ar)>0)
                 {
                    return array('status' =>TRUE, 'pincodes'=>$pincode_ar);
                 }
                 else
                 {
                    return array('status' =>TRUE, 'message'=>"There is no vendor for this location");
                 }*/
                
         }




         /*$qry = $this->db->query("select * from pincodes where state_id='".$state_id."' and city_id='".$city_id."' order by pincode asc");
          $query=$qry->result();
          $output = '<option value="">Select Pincode</option>';
          foreach($query as $row)
          {
            $output .= '<option value="'.$row->id.'">'.$row->pincode.'</option>';
          }
          print_r($output); die;*/
}

function getaddresspincodes($state_id,$city_id)
{     
   $qry = $this->db->query("select * from pincodes where state_id='".$state_id."' and city_id='".$city_id."' order by pincode asc");
          $query=$qry->result();
          $output = '<option value="">Select Pincode</option>';
          foreach($query as $row)
          {
            $output .= '<option value="'.$row->id.'">'.$row->pincode.'</option>';
          }
          print_r($output); die;
}
function addAddress($user_id,$name,$mobile,$address,$state,$city,$pincode,$landmark,$address_type)
    {
        $chk = $this->db->query("SELECT * FROM `vendor_shop` where state_id='".$state."' and city_id='".$city."' and find_in_set('".$pincode."',vendor_pincodes)");
        if($chk->num_rows()>0)
        {

        $data=array('user_id'=>$user_id,'name'=>$name,'mobile'=>$mobile,'address'=>$address,'city'=>$city,'state'=>$state,'pincode'=>$pincode,'landmark'=>$landmark,'address_type'=>$address_type);
        $ins = $this->db->insert("user_address",$data);
        if($ins)
        {
            echo '@success'; die;
        }
        else
        {
            echo '@error'; die;
        }
    }
    else
    {
        echo '@nolocation'; die;
         //return array('status' =>FALSE, 'message'=>'No shops in this location,Please change your location');
    }

} 


function addBookAddress($user_id,$name,$mobile,$address,$state,$city,$pincode,$landmark,$address_type)
    {
        $data=array('user_id'=>$user_id,'name'=>$name,'mobile'=>$mobile,'address'=>$address,'city'=>$city,'state'=>$state,'pincode'=>$pincode,'landmark'=>$landmark,'address_type'=>$address_type);
        $ins = $this->db->insert("user_address",$data);
        if($ins)
        {
            echo '@success'; die;
        }
        else
        {
            echo '@error'; die;
        }

} 

function deleteAddress($user_id,$aid)
{
    $del = $this->db->delete("user_address",array('id'=>$aid));
    if($del)
     {
       return 'true';
     }
     else
     {
        return FALSE;
     }
}


function updateAddress($address_id,$user_id,$name,$mobile,$address,$state,$city,$pincode,$landmark,$address_type)
    {

    $chk = $this->db->query("SELECT * FROM `vendor_shop` where state_id='".$state."' and city_id='".$city."' and find_in_set('".$pincode."',vendor_pincodes)");
    if($chk->num_rows()>0)
    {

        $data=array('user_id'=>$user_id,'name'=>$name,'mobile'=>$mobile,'address'=>$address,'city'=>$city,'state'=>$state,'pincode'=>$pincode,'landmark'=>$landmark,'address_type'=>$address_type);
        $wr = array('id'=>$address_id);
        $upd = $this->db->update("user_address",$data,$wr);
        //echo $this->db->last_query(); die;
        if($upd)
        {
            echo '@success'; die;
        }
        else
        {
            echo '@error'; die;
        }

    }
    else
    {
        echo '@nolocation'; die;
         //return array('status' =>FALSE, 'message'=>'No shops in this location,Please change your location');
    }
    }


function updateBookAddress($address_id,$user_id,$name,$mobile,$address,$state,$city,$pincode,$landmark,$address_type)
    {

        $data=array('user_id'=>$user_id,'name'=>$name,'mobile'=>$mobile,'address'=>$address,'city'=>$city,'state'=>$state,'pincode'=>$pincode,'landmark'=>$landmark,'address_type'=>$address_type);
        $wr = array('id'=>$address_id);
        $upd = $this->db->update("user_address",$data,$wr);
        //echo $this->db->last_query(); die;
        if($upd)
        {
            echo '@success'; die;
        }
        else
        {
            echo '@error'; die;
        }
    }



function getCartList($session_id,$user_id)
{
    $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
    $del_b = $qry->row();


    
    $shop = $this->db->query("select * from vendor_shop where id='".$del_b->vendor_id."'");
    $shopdat = $shop->row();
    $min_order_amount = $shopdat->min_order_amount;

    $result = $qry->result();
    $ar=[];
    if($qry->num_rows()>0)
    {
        $unitprice=0;
        $gst=0;
        foreach ($result as $value) 
        {
            $pro = $this->db->query("select * from  product_images where variant_id='".$value->variant_id."'");
            $product = $pro->row();

            if($product->image!='')
            {
                $img = base_url()."uploads/products/".$product->image;
            }
            else
            {
                $img = base_url()."uploads/noproduct.png";
            }
            //$value->variant_id
                $var1 = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
                $link = $var1->row();

                 $pro1 = $this->db->query("select * from  products where id='".$link->product_id."' and delete_status=0");
                 $product1 = $pro1->row();

                 $adm_qry = $this->db->query("select * from  admin_comissions where cat_id='".$product1->cat_id."' and shop_id='".$value->vendor_id."'");
                 if( $adm_qry->num_rows()>0)
                 {
                    $adm_comm = $adm_qry->row();
                    $p_gst = $adm_comm->gst;

                 }
                 else
                 {
                    $p_gst = '0';
                 }

                 $class_percentage = ($value->unit_price/100)*$p_gst;

                 

                    $variants1 = $var1->result();
                    $att1=[];
                    foreach ($variants1 as $value1) 
                    {

                        

                        $jsondata = $value1->jsondata;

                        $values_ar=[];

                        $json =json_decode($jsondata);
                        foreach ($json as $value123) 
                        {
                            $type = $this->db->query("select * from attributes_title where id='".$value123->attribute_type."'");
                            $types = $type->row();
                        
                            $val = $this->db->query("select * from attributes_values where id='".$value123->attribute_value."'");
                            $value1 = $val->row();
                            $values_ar[]=array('id'=>$value1->id,'title'=>$types->title,'value'=>$value1->value);
                        }

                        

                    }


           $ar[]=array('id'=>$value->id,'price'=>$value->price,'quantity'=>$value->quantity,'unit_price'=>$value->unit_price,'image'=>$img,'attributes'=>$values_ar,'product_name'=>$product1->name,'shop_name'=>$shopdat->shop_name,'shop_id'=>$del_b->vendor_id,'gst'=>$class_percentage,'maximum_quantity'=>$link->stock);
           $unitprice = $value->unit_price+$unitprice;
            $gst = $class_percentage+$gst;
        }

        

        $grand_t =  $min_order_amount+$unitprice+$gst;
        return array('cartlist'=>$ar,'total_price'=>$unitprice,'delivery_amount'=>$min_order_amount,'grand_total'=>$grand_t,'gst'=>$gst);
    }
    else
    {
        return array();
    }
    
}



function doOrder($session_id,$user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$grand_total,$razorpay_payment_id,$coupon_id,$coupon_code,$coupon_disount)
{
        

        $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
        $result = $qry->result();
        $ar=[];
            $unitprice=0;
            $admin_total=0;
            foreach ($result as $value) 
            {
                

                $link = $this->db->query("select * from  link_variant where id='".$value->variant_id."'");
                $link_variants = $link->row();


                 $stock = $link_variants->stock;
                 $cart_qry = $value->quantity;

                if($stock<$cart_qry)
                {
                    $msg = "OUT OF STOCK";
                    echo '@noprod@'.$msg; die; 


                    //return array('status' =>FALSE,'message'=>$msg);
                }

                $pro = $this->db->query("select * from  products where id='".$link_variants->product_id."' and delete_status=0");
                $product = $pro->row();

                $adm = $this->db->query("select * from  admin_comissions where shop_id='".$value->vendor_id."' and cat_id='".$product->cat_id."' and find_in_set('".$product->sub_cat_id."',subcategory_ids)");
                $admin = $adm->row();
                
                $admin_price = ($admin->admin_comission /100)*$value->price;
                

                

               $ar[]=array('id'=>$value->id,'price'=>$value->price,'quantity'=>$value->quantity,'unit_price'=>$value->unit_price,'image'=>$img);
               $unitprice = $value->unit_price+$unitprice;

               $admin_total = $admin_price+$admin_total;

               $total=$link_variants->stock-$value->quantity;



                $ar = array('varient_id'=>$value->variant_id,'product_id'=>$link_variants->product_id,'quantity'=>$value->quantity,'paid_status'=>'Debit','message'=>'New Order','total_stock'=>$total,'created_at'=>time());
            $ins11 = $this->db->insert("stock_management",$ar);
            if($ins11)
            {
                $qty = $link_variants->stock-$value->quantity;
               $this->db->update("link_variant",array('stock'=>$qty),array('id'=>$value->variant_id));
            }


            }



                

                        
                    $vendor =$unitprice-$admin_total; 

                    $coupon_qry = $this->db->query("select * from coupon_codes where id='".$coupon_id."'");
                    $coupon_row = $coupon_qry->row();
                    if($coupon_qry->num_rows()>0)
                    {
                        if($coupon_row->shop_id==0)
                        {
                            $admin_total1=$admin_total-$coupon_disount;
                            $vendor1=$vendor;
                        }
                        else
                        {
                            $vendor1=$vendor-$coupon_disount;
                            $admin_total1=$admin_total;
                        }

                    }
                    else
                    {
                        $admin_total1=$admin_total;
                        $vendor1=$vendor;
                    }

                    $vendor_qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
                    $vendor_row = $vendor_qry->row();


                    $vendorshop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
                    $vendorshop_row = $vendorshop_qry->row();

                    $delivery_amount = $vendorshop_row->min_order_amount;


                $delivery_ad_qry = $this->db->query("select * from user_address where id='".$deliveryaddress_id."'");
                $delivery_ad_row = $delivery_ad_qry->row();

                $state_qry = $this->db->query("select * from states where id='".$delivery_ad_row->state."'");
                $state_qry_row = $state_qry->row();

                $cities_qry = $this->db->query("select * from cities where id='".$delivery_ad_row->city."'");
                $cities_qry_row = $cities_qry->row();

                $pincode_qry = $this->db->query("select * from pincodes where id='".$delivery_ad_row->pincode."'");
                $pincode_qry_row = $pincode_qry->row();
                

                if($delivery_ad_row->address_type==1)
                {
                    $add_type ="Home";
                }
                else if($delivery_ad_row->address_type==1)
                {
                    $add_type ="Office";
                }
                $user_address = $add_type.": ".$delivery_ad_row->name.", ".$state_qry_row->state_name.", ".$cities_qry_row->city_name.", ".$pincode_qry_row->pincode.", ".$delivery_ad_row->address.", ".$delivery_ad_row->landmark;
        

        $shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
        $shop_num = $shop_qry->row();
        if($shop_num->status==0)
        {
            $msg = "Shop Closed";
            echo '@shopclosed'; exit;
        }

    $ar = array('session_id'=>$session_id,'user_id'=>$user_id,'vendor_id'=>$vendor_row->vendor_id,'deliveryaddress_id'=>$deliveryaddress_id,'user_address'=>$user_address,'payment_option'=>$payment_option,'order_status'=>$order_status,'deliveryboy_commission'=>$delivery_amount,'created_at'=>$created_at,'sub_total'=>$unitprice,'total_price'=>$grand_total,'admin_commission'=>$admin_total1,'vendor_commission'=>$vendor1,'pay_transaction_id'=>$razorpay_payment_id,'coupon_code'=>$coupon_code,'coupon_id'=>$coupon_id,'coupon_disount'=>$coupon_disount,'payment_status'=>1);

    //,'coupon_code'=>$coupon_code,'coupon_id'=>$coupon_id,'coupon_disount'=>$coupon_disount,'gst'=>$gst

    $ins = $this->db->insert("orders",$ar);
    //echo $this->db->last_query(); die;
    if($ins)
    {

            $sess_arr = array(
                    'session_id' => $session_id,
                    'vendor_id' => $vendor_row->vendor_id,
                    'session_status' => false
                ); 
        $this->session->unset_userdata('session_data', $sess_arr);

        $last_id = $this->db->insert_id();
        $title = "New Order From A3 Services";
        $message = "You Have new Order";
        $this->onesignalnotification($vendor_row->vendor_id,$title,$message);
                       
        $msg = "New Order Created ";
        /*$ins = $this->db->insert("wallet_transactions",array('user_id'=>$user_id,'price'=>$wallet_used_amount,'message'=>$msg,'status'=>'minus','created_at'=>time(),'order_id'=>$last_id));*/

        $vendor_shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
        $vendor_shop_row = $vendor_shop_qry->row();
        $vendor_phone =$vendor_shop_row->mobile;


        $order_message = "Dear vendor new order no.".$last_id." is in your dashboard. Please accept it for confirmation.";
        $vendortemplat_id='1407161900279488583';
        if($this->send_message($order_message,$vendor_phone,$vendortemplat_id))
        {
           $this->db->insert("sms_notifications",array('order_id'=>$last_id,'receiver_id'=>$vendor_row->vendor_id,'sender_id'=>$user_id,'created_at'=>time(),'message'=>$order_message)); 
        }

       

        $user_sms_qry = $this->db->query("select * from users where id='".$user_id."'");
        $user_sms_row = $user_sms_qry->row();
        $user_phone = $user_sms_row->phone;
        $user_name = $user_sms_row->first_name." ".$user_sms_row->last_name;

        $templat_id_user='1407161900222278373';
        $user_order_message = "Dear ".$user_name." your order no.".$last_id." is successfully placed, awaiting for vendors confirmation. Thank u for shopping with us.";

        if($this->send_message($user_order_message,$user_phone,$templat_id_user))
        {
           $this->db->insert("sms_notifications",array('order_id'=>$last_id,'receiver_id'=>$user_id,'sender_id'=>$vendor_row->vendor_id,'created_at'=>time(),'message'=>$user_order_message)); 
        }
        //return array('status' =>TRUE,'message'=>"Order Created successfully",'order_id' =>$last_id);
          echo '@success'; exit;
      
    }
    else
    {
         echo '@error'; exit;
    }
}




function biddoOrder($user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$grand_total,$razorpay_payment_id,$coupon_id,$coupon_code,$coupon_disount,$vendor_id,$bid_id)
{
        

        $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
        $result = $qry->result();
        $ar=[];
            $unitprice=0;
            $admin_total=0;
            foreach ($result as $value) 
            {
                
                $link = $this->db->query("select * from  link_variant where id='".$value->variant_id."'");
                $link_variants = $link->row();

                $pro = $this->db->query("select * from  products where id='".$link_variants->product_id."' and delete_status=0");
                $product = $pro->row();

                $adm = $this->db->query("select * from  admin_comissions where shop_id='".$vendor_id."' and cat_id='".$product->cat_id."' and find_in_set('".$product->sub_cat_id."',subcategory_ids)");
                $admin = $adm->row();
                
                $admin_price = ($admin->admin_comission /100)*$value->price;
                

                

               $ar[]=array('id'=>$value->id,'price'=>$value->price,'quantity'=>$value->quantity,'unit_price'=>$value->unit_price,'image'=>$img);
               $unitprice = $value->unit_price+$unitprice;

               $admin_total = $admin_price+$admin_total;

               $total=$link_variants->stock-$value->quantity;



                $ar = array('varient_id'=>$value->variant_id,'product_id'=>$link_variants->product_id,'quantity'=>$value->quantity,'paid_status'=>'Debit','message'=>'New Order','total_stock'=>$total,'created_at'=>time());
            $ins11 = $this->db->insert("stock_management",$ar);
            if($ins11)
            {
                $qty = $link_variants->stock-$value->quantity;
               $this->db->update("link_variant",array('stock'=>$qty),array('id'=>$value->variant_id));
            }


            }



                

                        
                    $vendor =$unitprice-$admin_total; 

                    $coupon_qry = $this->db->query("select * from coupon_codes where id='".$coupon_id."'");
                    $coupon_row = $coupon_qry->row();
                    if($coupon_qry->num_rows()>0)
                    {
                        if($coupon_row->shop_id==0)
                        {
                            $admin_total1=$admin_total-$coupon_disount;
                            $vendor1=$vendor;
                        }
                        else
                        {
                            $vendor1=$vendor-$coupon_disount;
                            $admin_total1=$admin_total;
                        }

                    }
                    else
                    {
                        $admin_total1=$admin_total;
                        $vendor1=$vendor;
                    }

                    $vendor_qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
                    $vendor_row = $vendor_qry->row();


                    $vendorshop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
                    $vendorshop_row = $vendorshop_qry->row();

                    $delivery_amount = $vendorshop_row->min_order_amount;


                $delivery_ad_qry = $this->db->query("select * from user_address where id='".$deliveryaddress_id."'");
                $delivery_ad_row = $delivery_ad_qry->row();

                $state_qry = $this->db->query("select * from states where id='".$delivery_ad_row->state."'");
                $state_qry_row = $state_qry->row();

                $cities_qry = $this->db->query("select * from cities where id='".$delivery_ad_row->city."'");
                $cities_qry_row = $cities_qry->row();

                $pincode_qry = $this->db->query("select * from pincodes where id='".$delivery_ad_row->pincode."'");
                $pincode_qry_row = $pincode_qry->row();
                

                if($delivery_ad_row->address_type==1)
                {
                    $add_type ="Home";
                }
                else if($delivery_ad_row->address_type==1)
                {
                    $add_type ="Office";
                }
                $user_address = $add_type.": ".$delivery_ad_row->name.", ".$state_qry_row->state_name.", ".$cities_qry_row->city_name.", ".$pincode_qry_row->pincode.", ".$delivery_ad_row->address.", ".$delivery_ad_row->landmark;
        

        $shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
        $shop_num = $shop_qry->row();
        if($shop_num->status==0)
        {
            $msg = "Shop Closed";
            echo '@shopclosed'; exit;
        }


         $bid_qry = $this->db->query("select * from user_bids where id='".$bid_id."'");
         $bid_row = $bid_qry->row();

                  $session_id =$bid_row->session_id;

    $ar = array('bid_id'=>$bid_id,'session_id'=>$session_id,'user_id'=>$user_id,'vendor_id'=>$vendor_id,'deliveryaddress_id'=>$deliveryaddress_id,'user_address'=>$user_address,'payment_option'=>$payment_option,'order_status'=>$order_status,'deliveryboy_commission'=>$delivery_amount,'created_at'=>$created_at,'sub_total'=>$unitprice,'total_price'=>$grand_total,'admin_commission'=>$admin_total1,'vendor_commission'=>$vendor1,'pay_transaction_id'=>$razorpay_payment_id,'payment_status'=>1,'delivery_timeslots'=>0,'delivery_boy'=>0,'coupon_id'=>0,'coupon_code'=>0,'coupon_disount'=>0,'pay_orderid'=>0,'pay_razerpay_id'=>0,'bonus_points'=>0,'wallet_amount'=>0,'gst'=>0,'accept_status'=>0);



    //,'coupon_code'=>$coupon_code,'coupon_id'=>$coupon_id,'coupon_disount'=>$coupon_disount,'gst'=>$gst

    $ins = $this->db->insert("orders",$ar);
    if($ins)
    {
$this->db->update("user_bids",array('bid_status'=>2),array('id'=>$bid_id));
            $sess_arr = array(
                    'session_id' => $session_id,
                    'vendor_id' => $vendor_row->vendor_id,
                    'session_status' => false
                ); 
        $this->session->unset_userdata('session_data', $sess_arr);

        $last_id = $this->db->insert_id();
        $title = "New Order From A3 Services";
        $message = "You Have new Order";
        $this->onesignalnotification($vendor_row->vendor_id,$title,$message);
                       
        $msg = "New Order Created ";
        /*$ins = $this->db->insert("wallet_transactions",array('user_id'=>$user_id,'price'=>$wallet_used_amount,'message'=>$msg,'status'=>'minus','created_at'=>time(),'order_id'=>$last_id));*/

        $vendor_shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
        $vendor_shop_row = $vendor_shop_qry->row();
        $vendor_phone =$vendor_shop_row->mobile;


        $order_message = "Dear vendor new order no.".$last_id." is in your dashboard. Please accept it for confirmation.";
        $vendortemplat_id='1407161900279488583';
        if($this->send_message($order_message,$vendor_phone,$vendortemplat_id))
        {
           $this->db->insert("sms_notifications",array('order_id'=>$last_id,'receiver_id'=>$vendor_row->vendor_id,'sender_id'=>$user_id,'created_at'=>time(),'message'=>$order_message)); 
        }

       

        $user_sms_qry = $this->db->query("select * from users where id='".$user_id."'");
        $user_sms_row = $user_sms_qry->row();
        $user_phone = $user_sms_row->phone;
        $user_name = $user_sms_row->first_name." ".$user_sms_row->last_name;

        $templat_id_user='1407161900222278373';
        $user_order_message = "Dear ".$user_name." your order no.".$last_id." is successfully placed, awaiting for vendors confirmation. Thank u for shopping with us.";

        if($this->send_message($user_order_message,$user_phone,$templat_id_user))
        {
           $this->db->insert("sms_notifications",array('order_id'=>$last_id,'receiver_id'=>$user_id,'sender_id'=>$vendor_row->vendor_id,'created_at'=>time(),'message'=>$user_order_message)); 
        }
        //return array('status' =>TRUE,'message'=>"Order Created successfully",'order_id' =>$last_id);
          echo '@success'; exit;
      
    }
    else
    {
         echo '@error'; exit;
    }
}

function doOrderCOD($session_id,$user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$grand_total,$coupon_id,$coupon_code,$coupon_disount)
{


        $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
        $result = $qry->result();
        $ar=[];
            $unitprice=0;
            $admin_total=0;
            foreach ($result as $value) 
            {
                

                $link = $this->db->query("select * from  link_variant where id='".$value->variant_id."'");
                $link_variants = $link->row();

                 $stock = $link_variants->stock;
                 $cart_qry = $value->quantity;

                if($stock<$cart_qry)
                {
                    $msg = "OUT OF STOCK";
                    echo '@noprod@'.$msg; die; 


                    //return array('status' =>FALSE,'message'=>$msg);
                }

                $pro = $this->db->query("select * from  products where id='".$link_variants->product_id."' and delete_status=0");
                $product = $pro->row();

                $adm = $this->db->query("select * from  admin_comissions where shop_id='".$vendor_id."' and cat_id='".$product->cat_id."' and find_in_set('".$product->sub_cat_id."',subcategory_ids)");
                $admin = $adm->row();
                
                $admin_price = ($admin->admin_comission /100)*$value->price;
                

                

               $ar[]=array('id'=>$value->id,'price'=>$value->price,'quantity'=>$value->quantity,'unit_price'=>$value->unit_price,'image'=>$img);
               $unitprice = $value->unit_price+$unitprice;

               $admin_total = $admin_price+$admin_total;

               $total=$link_variants->stock-$value->quantity;



                $ar = array('varient_id'=>$value->variant_id,'product_id'=>$link_variants->product_id,'quantity'=>$value->quantity,'paid_status'=>'Debit','message'=>'New Order','total_stock'=>$total,'created_at'=>time());
            $ins11 = $this->db->insert("stock_management",$ar);
            if($ins11)
            {
                $qty = $link_variants->stock-$value->quantity;
               $this->db->update("link_variant",array('stock'=>$qty),array('id'=>$value->variant_id));
            }


            }



                

                        
                    $vendor =$unitprice-$admin_total; 

                    $coupon_qry = $this->db->query("select * from coupon_codes where id='".$coupon_id."'");
                    $coupon_row = $coupon_qry->row();
                    if($coupon_qry->num_rows()>0)
                    {
                        if($coupon_row->shop_id==0)
                        {
                            $admin_total1=$admin_total-$coupon_disount;
                            $vendor1=$vendor;
                        }
                        else
                        {
                            $vendor1=$vendor-$coupon_disount;
                            $admin_total1=$admin_total;
                        }

                    }
                    else
                    {
                        $admin_total1=$admin_total;
                        $vendor1=$vendor;
                    }

                    $vendor_qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
                    $vendor_row = $vendor_qry->row();


                    $vendorshop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
                    $vendorshop_row = $vendorshop_qry->row();

                    $delivery_amount = $vendorshop_row->min_order_amount;


                $delivery_ad_qry = $this->db->query("select * from user_address where id='".$deliveryaddress_id."'");
                $delivery_ad_row = $delivery_ad_qry->row();

                $state_qry = $this->db->query("select * from states where id='".$delivery_ad_row->state."'");
                $state_qry_row = $state_qry->row();

                $cities_qry = $this->db->query("select * from cities where id='".$delivery_ad_row->city."'");
                $cities_qry_row = $cities_qry->row();

                $pincode_qry = $this->db->query("select * from pincodes where id='".$delivery_ad_row->pincode."'");
                $pincode_qry_row = $pincode_qry->row();
                

                if($delivery_ad_row->address_type==1)
                {
                    $add_type ="Home";
                }
                else if($delivery_ad_row->address_type==1)
                {
                    $add_type ="Office";
                }
                $user_address = $add_type.": ".$delivery_ad_row->name.", ".$state_qry_row->state_name.", ".$cities_qry_row->city_name.", ".$pincode_qry_row->pincode.", ".$delivery_ad_row->address.", ".$delivery_ad_row->landmark;

        $shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
        $shop_num = $shop_qry->row();
        if($shop_num->status==0)
        {
            $msg = "Shop Closed";
            echo '@shopclosed'; exit;
        }

                    
    $ar = array('session_id'=>$session_id,'user_id'=>$user_id,'vendor_id'=>$vendor_row->vendor_id,'deliveryaddress_id'=>$deliveryaddress_id,'user_address'=>$user_address,'payment_option'=>$payment_option,'order_status'=>$order_status,'deliveryboy_commission'=>$delivery_amount,'created_at'=>$created_at,'sub_total'=>$unitprice,'total_price'=>$grand_total,'admin_commission'=>$admin_total1,'vendor_commission'=>$vendor1,'coupon_code'=>$coupon_code,'coupon_id'=>$coupon_id,'coupon_disount'=>$coupon_disount);

    //,'gst'=>$gst

    $ins = $this->db->insert("orders",$ar);
    //echo $this->db->last_query(); die;
    if($ins)
    {

            $sess_arr = array(
                    'session_id' => $session_id,
                    'vendor_id' => $vendor_row->vendor_id,
                    'session_status' => false
                ); 
        $this->session->unset_userdata('session_data', $sess_arr);

        $last_id = $this->db->insert_id();
        $title = "New Order From A3 Services";
        $message = "You Have new Order";
        $this->onesignalnotification($vendor_row->vendor_id,$title,$message);
                       
        $msg = "New Order Created ";
        
        $vendor_shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
        $vendor_shop_row = $vendor_shop_qry->row();
        $vendor_phone =$vendor_shop_row->mobile;


        $order_message = "Dear vendor new order no.".$last_id." is in your dashboard. Please accept it for confirmation.";
        $vendortemplat_id='1407161900279488583';
        if($this->send_message($order_message,$vendor_phone,$vendortemplat_id))
        {
           $this->db->insert("sms_notifications",array('order_id'=>$last_id,'receiver_id'=>$vendor_row->vendor_id,'sender_id'=>$user_id,'created_at'=>time(),'message'=>$order_message)); 
        }

       

        $user_sms_qry = $this->db->query("select * from users where id='".$user_id."'");
        $user_sms_row = $user_sms_qry->row();
        $user_phone = $user_sms_row->phone;
        $user_name = $user_sms_row->first_name." ".$user_sms_row->last_name;

        $templat_id_user='1407161900222278373';
        $user_order_message = "Dear ".$user_name." your order no.".$last_id." is successfully placed, awaiting for vendors confirmation. Thank u for shopping with us.";

        if($this->send_message($user_order_message,$user_phone,$templat_id_user))
        {
           $this->db->insert("sms_notifications",array('order_id'=>$last_id,'receiver_id'=>$user_id,'sender_id'=>$vendor_row->vendor_id,'created_at'=>time(),'message'=>$user_order_message)); 
        }
        //return array('status' =>TRUE,'message'=>"Order Created successfully",'order_id' =>$last_id);
          echo '@success'; exit;
      
    }
    else
    {
         echo '@error'; exit;
    }
}

function doBidOrderCOD($user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$grand_total,$coupon_id,$coupon_code,$coupon_disount,$bid_id,$vendor_id)
{


        // $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
        $qry = $this->mybidDetails($user_id,$bid)['products'];
        $result = $qry;
        $ar=[];
            $unitprice=0;
            $admin_total=0;
            foreach ($result as $value) 
            {
                

                $link = $this->db->query("select * from  link_variant where id='".$value['variant_id']."'");
                $link_variants = $link->row();

                $pro = $this->db->query("select * from  products where id='".$link_variants->product_id."' and delete_status=0");
                $product = $pro->row();

                $adm = $this->db->query("select * from  admin_comissions where shop_id='".$value['vendor_id']."' and cat_id='".$product->cat_id."' and find_in_set('".$product->sub_cat_id."',subcategory_ids)");
                $admin = $adm->row();
                
                $admin_price = ($admin->admin_comission /100)*$value['price'];
                

                

               $ar[]=array('id'=>$value['id'],'price'=>$value['price'],'quantity'=>$value['quantity'],'unit_price'=>$value['unit_price'],'image'=>['img']);
               $unitprice = $value['unit_price']+$unitprice;

               $admin_total = $admin_price+$admin_total;

               $total=$link_variants->stock-$value['quantity'];



                $ar = array('varient_id'=>$value['variant_id'],'product_id'=>$link_variants->product_id,'quantity'=>$value['quantity'],'paid_status'=>'Debit','message'=>'New Order','total_stock'=>$total,'created_at'=>time());
            $ins11 = $this->db->insert("stock_management",$ar);
            /*if($ins11)
            {
                $qty = $link_variants->stock-$value->quantity;
               $this->db->update("link_variant",array('stock'=>$qty),array('id'=>$value['variant_id']));
            }*/


            }



                

                        
                    $vendor =$unitprice-$admin_total; 

                    $coupon_qry = $this->db->query("select * from coupon_codes where id='".$coupon_id."'");
                    $coupon_row = $coupon_qry->row();
                    if($coupon_qry->num_rows()>0)
                    {
                        if($coupon_row->shop_id==0)
                        {
                            $admin_total1=$admin_total-$coupon_disount;
                            $vendor1=$vendor;
                        }
                        else
                        {
                            $vendor1=$vendor-$coupon_disount;
                            $admin_total1=$admin_total;
                        }

                    }
                    else
                    {
                        $admin_total1=$admin_total;
                        $vendor1=$vendor;
                    }

                    $vendor_qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
                    $vendor_row = $vendor_qry->row();


                    $vendorshop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
                    $vendorshop_row = $vendorshop_qry->row();

                    $delivery_amount = $vendorshop_row->min_order_amount;


                $delivery_ad_qry = $this->db->query("select * from user_address where id='".$deliveryaddress_id."'");
                $delivery_ad_row = $delivery_ad_qry->row();

                $state_qry = $this->db->query("select * from states where id='".$delivery_ad_row->state."'");
                $state_qry_row = $state_qry->row();

                $cities_qry = $this->db->query("select * from cities where id='".$delivery_ad_row->city."'");
                $cities_qry_row = $cities_qry->row();

                $pincode_qry = $this->db->query("select * from pincodes where id='".$delivery_ad_row->pincode."'");
                $pincode_qry_row = $pincode_qry->row();
                

                if($delivery_ad_row->address_type==1)
                {
                    $add_type ="Home";
                }
                else if($delivery_ad_row->address_type==1)
                {
                    $add_type ="Office";
                }
                $user_address = $add_type.": ".$delivery_ad_row->name.", ".$state_qry_row->state_name.", ".$cities_qry_row->city_name.", ".$pincode_qry_row->pincode.", ".$delivery_ad_row->address.", ".$delivery_ad_row->landmark;

        $shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
        $shop_num = $shop_qry->row();
        if($shop_num->status==0)
        {
            $msg = "Shop Closed";
            echo '@shopclosed'; exit;
        }

         $bid_qry = $this->db->query("select * from user_bids where id='".$bid_id."'");
         $bid_row = $bid_qry->row();

                  $session_id =$bid_row->session_id; 
    $ar = array('session_id'=>$session_id,'user_id'=>$user_id,'vendor_id'=>$vendor_id,'deliveryaddress_id'=>$deliveryaddress_id,'user_address'=>$user_address,'payment_option'=>$payment_option,'order_status'=>$order_status,'deliveryboy_commission'=>$delivery_amount,'created_at'=>$created_at,'sub_total'=>$unitprice,'total_price'=>$grand_total,'admin_commission'=>$admin_total1,'vendor_commission'=>$vendor1,'coupon_code'=>$coupon_code,'coupon_id'=>$coupon_id,'coupon_disount'=>$coupon_disount,"bid_id"=>$bid_id);

    //,'gst'=>$gst

    $ins = $this->db->insert("orders",$ar);
    //echo $this->db->last_query(); die;
    if($ins)
    {
            
             $this->db->update("user_bids",array('bid_status'=>2),array('id'=>$bid_id));
            // echo $this->db->last_query(); die;
            $sess_arr = array(
                    'session_id' => $session_id,
                    'vendor_id' => $vendor_row->vendor_id,
                    'session_status' => false
                ); 
        $this->session->unset_userdata('session_data', $sess_arr);

        $last_id = $this->db->insert_id();
        $title = "New Order From A3 Services";
        $message = "You Have new Order";
        $this->onesignalnotification($vendor_row->vendor_id,$title,$message);
                       
        $msg = "New Order Created ";
        
        $vendor_shop_qry = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
        $vendor_shop_row = $vendor_shop_qry->row();
        $vendor_phone =$vendor_shop_row->mobile;


        $order_message = "Dear vendor new order no.".$last_id." is in your dashboard. Please accept it for confirmation.";
        $vendortemplat_id='1407161900279488583';
        if($this->send_message($order_message,$vendor_phone,$vendortemplat_id))
        {
           $this->db->insert("sms_notifications",array('order_id'=>$last_id,'receiver_id'=>$vendor_row->vendor_id,'sender_id'=>$user_id,'created_at'=>time(),'message'=>$order_message)); 
        }

       

        $user_sms_qry = $this->db->query("select * from users where id='".$user_id."'");
        $user_sms_row = $user_sms_qry->row();
        $user_phone = $user_sms_row->phone;
        $user_name = $user_sms_row->first_name." ".$user_sms_row->last_name;

        $templat_id_user='1407161900222278373';
        $user_order_message = "Dear ".$user_name." your order no.".$last_id." is successfully placed, awaiting for vendors confirmation. Thank u for shopping with us.";

        if($this->send_message($user_order_message,$user_phone,$templat_id_user))
        {
           $this->db->insert("sms_notifications",array('order_id'=>$last_id,'receiver_id'=>$user_id,'sender_id'=>$vendor_row->vendor_id,'created_at'=>time(),'message'=>$user_order_message)); 
        }
        //return array('status' =>TRUE,'message'=>"Order Created successfully",'order_id' =>$last_id);
          echo '@success'; exit;
      
    }
    else
    {
         echo '@error'; exit;
    }
}


function becomeVendor($data)
    {
        $ins = $this->db->insert("become_a_vendor",$data);
        
        if($ins)
        {
            //$ar = array('status' =>TRUE,'message'=>"Vendor Added Successfully");
            echo '@success'; die;
        }
        else
        {
            //$ar = array('status' =>FALSE,'message'=>"Something went Wrong");
            echo '@error'; die;
        }
    }

function add_most_viewed_removewhishList($product_id,$user_id)
{
    $qry = $this->db->query("select * from whish_list where product_id='".$product_id."' and user_id='".$user_id."'");
    if($qry->num_rows()>0)
    {
        $upd = $this->db->delete("whish_list",array('product_id'=>$product_id,'user_id'=>$user_id));
        if($upd)
        {
            echo '@remove'; die;
        }
    }
    else
    {
        
                          
        $ins = $this->db->insert("whish_list",array('product_id'=>$product_id,'user_id'=>$user_id));
        if($ins)
        {
            echo '@add'; die;
        }
    }
}


function addRemoveTopdealWhishList($product_id,$user_id)
{
    $qry = $this->db->query("select * from whish_list where product_id='".$product_id."' and user_id='".$user_id."'");
    if($qry->num_rows()>0)
    {
        $upd = $this->db->delete("whish_list",array('product_id'=>$product_id,'user_id'=>$user_id));
        if($upd)
        {
             echo '@remove'; die;
        }
    }
    else
    {
        $ins = $this->db->insert("whish_list",array('product_id'=>$product_id,'user_id'=>$user_id,'created_date'=>time()));
        if($ins)
        {
             echo '@add'; die;
        }
    }
}


function addedWhistlisthtmldata()
{ 


    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];
            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {
      $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
      $row = $qry->row();
      $lat=$row->lat;
      $lng=$row->lng;
    }
    $admin = $this->db->query("select * from admin where id=1");
    $search_distance = $admin->row()->distance;

        $qry = $this->db->query("SELECT most_viewed_products.product_id,count(most_viewed_products.id) as cnt,products.*,vendor_shop.id as vendor_id,vendor_shop.shop_name,( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM most_viewed_products INNER JOIN products ON products.id =most_viewed_products.product_id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id WHERE products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' and vendor_shop.status=1 GROUP by most_viewed_products.product_id having distance<'".$search_distance."' order by most_viewed_products.id DESC LIMIT 10");
        $dat = $qry->result();
                $ar=[];
                foreach ($dat as $value) 
                {
                    

                        $qry1 = $this->db->query("SELECT * FROM `link_variant` where saleprice!='0' and status=1 and product_id='".$value->id."'");
                        $value12 = $qry1->row();

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
                        


                            
                    $state_id = $row->state_id;
                    $city_id = $row->address_id;
                    $pincode_id = $row->pincode_id;

                                if($value->status==1)
                                {
                                    $shopstat = "Open";
                                }
                                else
                                {
                                     $shopstat = "Closed";
                                }


                        $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                        if($wish->num_rows()>0)
                        {
                            $stat = true;
                        }
                        else
                        {
                            $stat = false;
                        }
                        if($value12->saleprice!='')
                        {
                           $slaeprice =  $value12->saleprice;
                        }
                        else
                        {
                            $slaeprice=0;
                        }

                        if($value12->price!='')
                        {
                           $price = $value12->price;
                        }
                        else
                        {
                            $price =0;
                        }
                        
                            $name = $value->name;

                            /* $ar[]=array('id'=>$value->id,'variant_id'=>$value12->id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$value->shop_name,'price'=>$price,'saleprice'=>$slaeprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat,'shop_status'=>$shopstat,'distance'=>round($value->distance),'seo_url'=>$value->seo_url);*/
                             
                    



     $output .= '<div class="col-lg-3">
                        <article class="single_product">
                              <span id="favorites<?php echo $value->id; ?>"></span>
                                <figure>
                                    <div class="product_thumb">
                                        <a class="primary_img" href="<?php echo base_url(); ?>web/product_view/<?php echo $value->seo_url; ?>"><img src="<?php echo $value->image; ?>" alt=""></a>
                                        <a class="secondary_img" href="<?php echo base_url(); ?>web/product_view/<?php echo $value->seo_url; ?>"><img src="<?php echo $value->image; ?>" alt=""></a>
                                        <div class="label_product">
                                            <span class="label_sale">Sale</span>
                                        </div>
                                        <div class="action_links">
                                            <ul>
                                                <li class="quick_button"><a href="<?php echo base_url(); ?>web/product_view/<?php echo $value->seo_url; ?>" title="View Details"> <span class="pe-7s-search"></span></a></li>
                                                <li class="wishlist" onclick="addFavorite(<?php echo $value->id; ?>)"><a href="" title="Add to Wishlist" <?php if($value->whishlist_status==true){ ?>class="fav"<?php } ?>><span class="pe-7s-like"></span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <figcaption class="product_content">
                                        <div class="product_content_inner">
                                            <h4 class="product_name"><a href="<?php echo base_url(); ?>web/product_view/<?php echo $value->seo_url; ?>"><?php echo $value->name; ?></a></h4>
                                            <div class="price_box"> 
                                                <span class="current_price"><i class="fal fa-rupee-sign"></i> <?php echo $value->saleprice; ?></span>


                                            </div>
                                        </div>
                                        <div class="add_to_cart">
                                            <a onclick="addtocart(<?php echo $value->variant_id; ?>,<?php echo $value->shop_id; ?>,"<?php echo $value->saleprice; ?>",1)" ><i class="fal fa-shopping-cart fa-lg"></i> Add to cart</a>
                                        </div>
                                    </figcaption>
                                </figure>
                        </article>
                   </div>';

 }

 print_r($output); die;
}

function removewhishList($product_id,$user_id)
{
     $upd = $this->db->delete("whish_list",array('product_id'=>$product_id,'user_id'=>$user_id));
        if($upd)
        {
            return TRUE;
        }
}

function getBannerads($user_id)
{
        if($user_id=='guest')
        {
             $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $users = $this->db->query("select * from users where id='".$user_id."'");
            $users_row = $users->row();
            $address = $users_row->address_id;
            $lat =   $users_row->lat;
            $lng =   $users_row->lng; 
        }

             

            $admin = $this->db->query("select * from admin where id=1");
            $search_distance = $admin->row()->distance;  

            $shop_qry = $this->db->query("SELECT id, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop having distance<'".$search_distance."'");
            $shops=$shop_qry->result();

            $shop_ids = array_column($shops, 'id');
            $imp = implode(",", $shop_ids);
        $qry = $this->db->query("select * from banners WHERE find_in_set(shop_id,'".$imp."') and position=2 and type='shops' order by RAND() desc LIMIT 3");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    

                    if($value->web_image!='')
                    {
                        $img = base_url()."uploads/banners/".$value->web_image;
                    }
                    else
                    {
                        $img = "";
                    }

                    if($value->type=='products')
                        {
                            $prod_qry = $this->db->query("select * from products where id='".$value->product_id."' and delete_status=0");
                            $dat1 = $prod_qry->row();
                            $title = $dat1->name;
                            $shop_id = $dat1->shop_id;
                            $product_details = array('product_title'=>$title,'product_id'=>$dat1->id,'shop_id'=>$shop_id,'seo_url'=>$dat1->seo_url);
                        }
                        else
                        {
                            $prod_qry = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                            $dat1 = $prod_qry->row();

                             $adm_qry = $this->db->query("select * from admin_comissions where shop_id='".$value->shop_id."' order by id desc");
                             $adm_row = $adm_qry->row();
                            
                            $product_details = array('shop_name'=>$dat1->shop_name,'cat_id'=>$adm_row->cat_id,'shop_id'=>$value->shop_id,'seo_url'=>$dat1->seo_url);
                        }

                   $ar[]=array('id'=>$value->id,'title'=>$value->title,'image'=>$img,'type'=>$value->type,'product_details'=>$product_details);
                }
               
        }
        else
        {
            $ar= array();
        }


        return $ar;
    }

function getLastBannerads($user_id)
{
        if($user_id=='guest')
        {
            $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $users = $this->db->query("select * from users where id='".$user_id."'");
            $users_row = $users->row();
            $address = $users_row->address_id;
            $lat =   $users_row->lat;
            $lng =   $users_row->lng; 
        } 

            $admin = $this->db->query("select * from admin where id=1");
            $search_distance = $admin->row()->distance; 

            $shop_qry = $this->db->query("SELECT id, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop having distance<'".$search_distance."'");
            $shops=$shop_qry->result();

            $shop_ids = array_column($shops, 'id');
            $imp = implode(",", $shop_ids);
        $qry = $this->db->query("select * from banners WHERE find_in_set(shop_id,'".$imp."') and position=3 and type='shops' order by id desc LIMIT 1");
        $value = $qry->row();

                    if($value->web_image!='')
                    {
                        $img = base_url()."uploads/banners/".$value->web_image;
                    }
                    else
                    {
                        $img = "";
                    }

                    if($value->type=='products')
                        {
                            $prod_qry = $this->db->query("select * from products where id='".$value->product_id."' and delete_status=0");
                            $dat1 = $prod_qry->row();
                            $title = $dat1->name;
                            $shop_id = $dat1->shop_id;
                            $product_details = array('product_title'=>$title,'product_id'=>$dat1->id,'shop_id'=>$shop_id,'seo_url'=>$dat1->seo_url);
                        }
                        else
                        {
                            $prod_qry = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                            $dat1 = $prod_qry->row();

                             $adm_qry = $this->db->query("select * from admin_comissions where shop_id='".$value->shop_id."' order by id desc");
                             $adm_row = $adm_qry->row();
                            
                            $product_details = array('shop_name'=>$dat1->shop_name,'cat_id'=>$adm_row->cat_id,'shop_id'=>$value->shop_id,'seo_url'=>$dat1->seo_url);
                        }

                   $ar=array('id'=>$value->id,'title'=>$value->title,'image'=>$img,'type'=>$value->type,'product_details'=>$product_details);
                
               
        


        return $ar;
    }



 function fetchProducts($keyword,$user_id)
 {

    if($user_id=='guest')
        {
            $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $users = $this->db->query("select * from users where id='".$user_id."'");
            $users_row = $users->row();
            $address = $users_row->address_id;

            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        } 


   

                            $admin = $this->db->query("select * from admin where id=1");
                          $search_distance = $admin->row()->distance;
                         
        $qry = $this->db->query("SELECT vendor_shop.shop_name,link_variant.id as variant_id,products.*, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM link_variant INNER JOIN products ON link_variant.product_id=products.id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id where link_variant.saleprice!=0 and link_variant.status=1 and  products.name LIKE '%".$keyword."%' and vendor_shop.status=1 and products.status=1 and products.delete_status=0 group by link_variant.product_id having distance<".$search_distance." order by products.id ASC LIMIT 6");
        $dat = $qry->result();
         
                $ar1=[];
                foreach ($dat as $value) 
                {
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
                     $images = $im->row();
                    if($images->image!='')
                    {
                        $img = base_url()."uploads/products/".$images->image;
                    }
                    else
                    {
                        $img = base_url()."uploads/noproduct.png";
                    }

                    
                        /*$ar1[]=array('id'=>$value->id,'shop_id'=>$value->shop_id,'title'=>$value->name,'image'=>$img,'cat_id'=>$value->cat_id);*/ 

                        $output .= '<li><a href="'.base_url().'web/product_view/'.$value->seo_url.'">'.$value->name.' - '.$value->shop_name.'</a></li>';
                    
                }

        

             $row = $this->db->query("select * from vendor_shop where shop_name LIKE '%".$keyword."%' order by id desc LIMIT 6");
             $dat = $row->result();
                $ar2=[];
                foreach ($dat as $value) 
                {
                   
                        if($value->shop_logo!='')
                        {
                            $img = base_url()."uploads/shops/".$value->shop_logo;
                        }
                        else
                        {
                            $img = base_url()."uploads/noproduct.png";
                        }

                       
                          $admin = $this->db->query("select * from admin where id=1");
                          $admin_row = $admin->row();
                          
                             $pro = $this->db->query("select * from products where shop_id='".$value->id."' and delete_status=0");
                             $prod_row = $pro->row();

                             if($pro->num_rows()>0)
                             {
                                if($value->status==1)
                                {
                                    $stat = "Open";
                                }
                                else
                                {
                                     $stat = "Closed";
                                }
                                /*$ar2[]=array('id'=>$value->id,'shop_name'=>$value->shop_name,'description'=>$value->description,'image'=>$img,'status'=>$stat,'cat_id'=>$prod_row->cat_id);*/

                                $output .= '<li><a href="'.base_url().'web/store/'.$value->seo_url.'/shop">'.$value->shop_name.'</a></li>';
                             }

                }

                print_r($output); die;
             


 }



function searchAllProductsandShops($keyword,$user_id)
 {

    if($user_id=='guest')
        {
            $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $users = $this->db->query("select * from users where id='".$user_id."'");
            $users_row = $users->row();
            $address = $users_row->address_id;

            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        } 


   

                            $admin = $this->db->query("select * from admin where id=1");
                          $search_distance = $admin->row()->distance;

        $qry = $this->db->query("SELECT vendor_shop.shop_name,link_variant.id as variant_id,link_variant.saleprice,products.*, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM link_variant INNER JOIN products ON link_variant.product_id=products.id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id where link_variant.saleprice!=0 and link_variant.status=1 and products.delete_status=0 and  products.name LIKE '%".$keyword."%' and vendor_shop.status=1 and products.status=1 group by link_variant.product_id having distance<".$search_distance." order by products.id ASC");
        $dat = $qry->result();
         
                $ar1=[];
                foreach ($dat as $value) 
                {
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
                     $images = $im->row();
                    if($images->image!='')
                    {
                        $img = base_url()."uploads/products/".$images->image;
                    }
                    else
                    {
                        $img = base_url()."uploads/noproduct.png";
                    }

                    $ar1[]=array('id'=>$value->id,'variant_id'=>$value->variant_id,'shop_id'=>$value->shop_id,'name'=>$value->name,'image'=>$img,'cat_id'=>$value->cat_id,'seo_url'=>$value->seo_url,'shop'=>$value->shop_name,'saleprice'=>$value->saleprice,'distance'=>$value->distance);                    
                }

        

             $row = $this->db->query("select *,( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance from vendor_shop where shop_name LIKE '%".$keyword."%' having distance<".$search_distance." order by distance ASC");
             $dat = $row->result();
                $ar2=[];
                foreach ($dat as $value) 
                {
                   
                        if($value->shop_logo!='')
                        {
                            $img = base_url()."uploads/shops/".$value->shop_logo;
                        }
                        else
                        {
                            $img = base_url()."uploads/noproduct.png";
                        }

                       
                          $admin = $this->db->query("select * from admin where id=1");
                          $admin_row = $admin->row();
                          
                             $pro = $this->db->query("select * from products where shop_id='".$value->id."' and delete_status=0");
                             $prod_row = $pro->row();

                             if($pro->num_rows()>0)
                             {
                                if($value->status==1)
                                {
                                    $stat = "Open";
                                }
                                else
                                {
                                     $stat = "Closed";
                                }
                                $ar2[]=array('id'=>$value->id,'shop_name'=>$value->shop_name,'description'=>$value->description,'image'=>$img,'status'=>$stat,'cat_id'=>$prod_row->cat_id,'seo_url'=>$value->seo_url,'shop_name'=>$value->shop_name,'distance'=>round($value->distance),'product_total'=>$pro->num_rows());
                             }

                }
                $shop_total = count($ar1);
                $product_total = count($ar2);
                $search_count = $shop_total+$product_total;
                return array('products'=>$ar1,'shops'=>$ar2,'search_count'=>$search_count);
             


 }



 function fetchstorewisesearchProducts($keyword,$user_id,$shop_id)
 {

    $users = $this->db->query("select * from users where id='".$user_id."'");
            $users_row = $users->row();
            $address = $users_row->address_id;

            $lat =   $users_row->lat;
            $lng =   $users_row->lng;

                          $admin = $this->db->query("select * from admin where id=1");
                          $search_distance = $admin->row()->distance;

        
        
        $qry = $this->db->query("SELECT link_variant.id as variant_id,products.*, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM link_variant INNER JOIN products ON link_variant.product_id=products.id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id where link_variant.saleprice!=0 and link_variant.status=1 and  products.name LIKE '%".$keyword."%' and vendor_shop.status=1 and products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 group by link_variant.product_id having distance<".$search_distance." order by products.id ASC");
        $dat = $qry->result();
         if($qry->num_rows()>0){
                $ar1=[];
                foreach ($dat as $value) 
                {
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
                     $images = $im->row();
                    if($images->image!='')
                    {
                        $img = base_url()."uploads/products/".$images->image;
                    }
                    else
                    {
                        $img = base_url()."uploads/noproduct.png";
                    }
                    $output .= '<li><a href="'.base_url().'web/product_view/'.$value->seo_url.'">'.$value->name.'</a></li>';
                    
                }
                print_r($output); die;

            }
            else
            {
                $output = '<li></li><li style="color:red; text-align:center;"><a>No Products Found </a></li>';
                print_r($output); die;
            }
            
             


 }


 function deleteCartRow($cart_id,$user_id)
{
    $del = $this->db->delete("cart",array('id'=>$cart_id));
    if($del)
    {
            echo '@success'; die;
    }
    else
    {
         echo '@error'; die;
    }
}

function getSMSContent($type)
{
    $qry = $this->db->query("select id,title,description from content where id='".$type."'");
    return $qry->row();
}


function docancelOrder($user_id,$orderid)
{
            $ar = array('order_status'=>'6');
            $wr = array('id'=>$orderid);
            $upd = $this->db->update("orders",$ar,$wr);
            if($upd)
            {
                $msg = "Order Cancelled by ";
                $aar = array('user_id'=>$user_id,'order_id'=>$orderid,'message'=>$msg);
                $this->db->insert("admin_notifications",$aar);

                $qry = $this->db->query("select * from orders where id='".$orderid."'");
                $row = $qry->row();
                $usr_qry = $this->db->query("select * from vendor_shop where id='".$row->vendor_id."'");
                $usr_row = $usr_qry->row();
                            
                    $phone =$usr_row->mobile;
                    
                    $msg="Dear vendor order no.".$orderid." is cancelled by user, kindly collect return goods and confirm items are in good order.";
                    $template_id = "1407161900188019493";
                    $this->send_message($msg,$phone,$template_id);

                    
                    $title = "Order Cancelled";
                    $this->onesignalnotification($row->vendor_id,$title,$msg);



                    if($row->bid_id==0){

                       $usr_qry = $this->db->query("select * from vendor_shop where id='".$row->vendor_id."'");
                       $usr_row = $usr_qry->row();
                            
                            $phone =$usr_row->mobile;

                        $qry = $this->db->query("select * from cart where session_id='".$row->session_id."'");
                        $result = $qry->result();
                        $ar=[];
                            foreach ($result as $value) 
                            {
                                $link = $this->db->query("select * from  link_variant where id='".$value->variant_id."'");
                                $link_variants = $link->row();

                                $pro = $this->db->query("select * from  products where id='".$link_variants->product_id."' and delete_status=0");
                                $product = $pro->row();

                                $adm = $this->db->query("select * from  admin_comissions where shop_id='".$vendor_id."' and cat_id='".$product->cat_id."' and find_in_set('".$product->sub_cat_id."',subcategory_ids)");
                                $admin = $adm->row();
                                
                                $admin_price = ($admin->admin_comission /100)*$value->price;

                                $total=$link_variants->stock+$value->quantity;
                                    $ar = array('varient_id'=>$value->variant_id,'product_id'=>$link_variants->product_id,'quantity'=>$value->quantity,'paid_status'=>'Credit','message'=>'Order cancelled by user','total_stock'=>$total,'created_at'=>time());
                                    $ins11 = $this->db->insert("stock_management",$ar);

                                    if($ins11)
                                    {
                                        $qty = $link_variants->stock+$value->quantity;
                                       $this->db->update("link_variant",array('stock'=>$qty),array('id'=>$value->variant_id));
                                    }
                            }
                }
                    echo '@success';
            }
            else
            {
                echo '@error';
            }
}


function exchangeRefund($session_id,$product_id,$user_id,$vendor_id,$cartid,$delivery_type,$reson)
{
    $chk = $this->db->query("select * from refund_exchange where session_id='".$session_id."' and product_id='".$product_id."' and user_id='".$user_id."' and cartid='".$cartid."'");
    if($chk->num_rows()>0)
    {
          return array('status' =>FALSE,'message'=>"your request already sent Successfully");
    }   
    else
    {
            $user = $this->db->query("select * from users where id='".$user_id."'");
            $users = $user->row();

            $phone = $users->phone;
            $pro = $this->db->query("select * from products where id='".$product_id."' and delete_status=0");
            $product = $pro->row();
             $otp_message = "Dear vendor order no:".$session_id." is requested for return by the customer. please review and and confirm. Contact customer care for more details.";
            
             $template_id="1407161684049275169";
        if($this->send_message($otp_message,$phone,$template_id))
        {

            $ar = array('session_id'=>$session_id,'user_id'=>$user_id,'product_id'=>$product_id,'vendor_id'=>$vendor_id,'cartid'=>$cartid,'delivery_type'=>$delivery_type,'message'=>$reson,'status'=>0);
            $ins = $this->db->insert("refund_exchange",$ar);
            if($ins)
            {
                $cart_ar = array('status'=>$delivery_type);
                $upd_ar = array('id'=>$cartid);
                $this->db->update("cart",$cart_ar,$upd_ar);
                if($delivery_type==1)
                {
                     //return array('status' =>TRUE,'message'=>"Your Exchange Request sent Successfully");
                    echo '@success'; die;
                }
                else if($delivery_type==2)
                {
                   // return array('status' =>TRUE,'message'=>"Your Refund Request sent Successfully");
                    echo '@success'; die;
                }
            }
            else
            {
                //return array('status' =>FALSE,'message'=>"Something went wrong");
                echo '@error'; die;
            }
        }
    }
}


function getHomeCategories($catid)
{
    $category = $this->db->query("select * from categories where id='".$catid."'");
    $category_row = $category->row();

     $subcat = $this->db->query("select * from sub_categories where cat_id='".$catid."'");
            $subcat_row1 = $subcat->result();
            if($subcat->num_rows()>0)
            {
                foreach ($subcat_row1 as $subcat_row) 
                {
                        if($subcat_row->app_image!='')
                        {
                            $img1 = base_url()."uploads/sub_categories/".$subcat_row->app_image;
                        }
                        else
                        {
                            $img1 = "";
                        }

                        $prod1 = $this->db->query("select * from products where cat_id='".$catid."' and sub_cat_id='".$subcat_row->id."' and delete_status=0");
                        $products1 = $prod1->num_rows();
                        if($products1>0)
                        {
                            $ar[]=array('id'=>$subcat_row->id,'title'=>$subcat_row->sub_category_name,'image'=>$img1,'products_count'=>$products1,'seo_url'=>$subcat_row->seo_url);
                        }
                     
                }

                 return array('sub_category_list'=>$ar,'category_name'=>$category_row->category_name);
             }
             else
             {
                return array('status' =>FALSE,'message'=>"No Data found");
             }
}


function getshopsWithcategoryID($cat_id,$subcatid,$user_id)
    {

        if($user_id=='guest')
        {
             $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $users = $this->db->query("select * from users where id='".$user_id."'");
         $users_row = $users->row();

            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }

         

        $qry = $this->db->query("select shop_id from admin_comissions where cat_id='".$cat_id."' and find_in_set('".$subcatid."',subcategory_ids) group by shop_id");
        $dat = $qry->result();
       /* $shop_ids = array_column($dat, 'shop_id');

        $im = implode(",", $shop_ids);
print_r($im); die;*/
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value1) 
                {

                     $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
                    $row = $qry->row();
                    
                    $admin = $this->db->query("select * from admin where id=1");
                          $search_distance = $admin->row()->distance;


                $row=$this->db->query("SELECT vendor_shop.*,products.status, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop INNER JOIN products ON vendor_shop.id=products.shop_id where vendor_shop.id='".$value1->shop_id."' and products.status=1 and products.delete_status=0 and vendor_shop.status=1 group by products.shop_id having distance<".$search_distance." order by distance asc");
                    
                    $value = $row->row();
                        if($value->shop_logo!='')
                        {
                            $img = base_url()."uploads/shops/".$value->shop_logo;
                        }
                        else
                        {
                            $img = base_url()."uploads/noproduct.png";;
                        }


                        
                        $shop_qry = $this->db->query("select * from shop_favorites where shop_id='".$value->id."' and user_id='".$user_id."'");
                        if($shop_qry->num_rows()>0)
                        {
                            $shop_not = true;
                        }
                        else
                        {
                            $shop_not = false;
                        }

                      $pro = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.* FROM link_variant INNER JOIN products ON link_variant.product_id=products.id where link_variant.saleprice!=0 and link_variant.status=1 and products.cat_id='".$cat_id."' and products.shop_id='".$value->id."' and products.status=1 and products.delete_status=0 group by link_variant.product_id order by products.id ASC");


                     $product_total = $pro->num_rows();
                     if($pro->num_rows()>0)
                     {
                        if($value->status==1)
                        {
                            $stat = "Open";
                        }
                        else
                        {
                             $stat = "Closed";
                        }

                        $ar[]=array('id'=>$value->id,'shop_name'=>$value->shop_name,'description'=>$value->description,'image'=>$img,'status'=>$stat,'shop_not'=>$shop_not,'distance'=>round($value->distance),'product_total'=>$product_total,'address'=>$value->city,'seo_url'=>$value->seo_url);
                     }
                }
            return array('shop_list'=>$ar);
        }
        else{
            return array('message'=>"No Shops");
        }
    }

 
 function getbrands()
 {
    $brnd = $this->db->query("select id,brand_name from attr_brands ");
    return $brnd->result();
 }


function getfilterProducts($json_data,$shop_id,$cat_id,$user_id)
{

     if($user_id=='guest')
        {
             $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $user_qry = $this->db->query("select * from users where id='".$user_id."'");
            $user_row = $user_qry->row();
              $lat = $user_row->lat;
              $lng = $user_row->lng;
        }




    $str = json_decode($json_data);
    $sp=[];

    //echo "<pre>"; print_r($json_data); die;
    foreach ($str as $value) 
     {
         $str = json_encode($value); 
         $filt_qry = "and link_variant.jsondata LIKE '%".$str."%'";


         $admin = $this->db->query("select * from admin where id=1");
     $search_distance = $admin->row()->distance;

            $qry = $this->db->query("SELECT vendor_shop.id as shop_id,vendor_shop.shop_name,link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.*, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM link_variant INNER JOIN products ON link_variant.product_id=products.id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id where link_variant.saleprice!=0 and link_variant.status=1 and  products.cat_id='".$cat_id."' and  products.shop_id='".$shop_id."' and products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' and vendor_shop.status=1 ".$filt_qry." group by link_variant.product_id having distance<".$search_distance." order by link_variant.id desc ");



         //$qry = $this->db->query("SELECT * FROM `link_variant` where saleprice!=0 and status=1 ".$filt_qry);
         $dat = $qry->result();
         if($qry->num_rows()>0)
         {
                $ar=[];
                foreach ($dat as $value) 
                { 
                    /*$qry11 = $this->db->query("select * from products where cat_id='".$cat_id."' and sub_cat_id='".$subcat_id."' and shop_id='".$shop_id."' and id='".$value12->product_id."'");
                    $value = $qry11->row();*/

                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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


                    /*$vendo = $this->db->query("select * from vendor_shop where id='".$value->shop_id."'");
                    $vendor = $vendo->row();*/



                    $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                    }
                    else
                    {
                        $stat = false;
                    }


                        $ar[]=array('id'=>$value->id,'variant_id'=>$value->variant_id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$value->name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$value->shop_name,'price'=>$value->price,'saleprice'=>$value->saleprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat,'seo_url'=>$value->seo_url);
                    
                }
             return $ar;
        
         }
         else
         {
            return array();
         }
     }

   
   
}




function attributesWithCategory($cat_id)
{
        $qry = $this->db->query("select * from manage_attributes where categories='".$cat_id."'");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {
                    $att = $this->db->query("select * from attributes_title where id='".$value->attribute_titleid."'");
                    $attribute = $att->row();

                     $typ = $this->db->query("select * from attributes_values where attribute_titleid='".$attribute->id."'");
                     $datails = $typ->result();
                      $ar_val=[];
                        foreach ($datails as $valuesdata) 
                        {                    
                           $ar_val[]=array('id'=>$valuesdata->id,'title'=>$valuesdata->value);
                        }
                   $ar[]=array('id'=>$attribute->id,'title'=>$attribute->title,'attributes_values'=>$ar_val);
                }
                return $ar;
        }
        else
        {
            return array();
        }
}


function getContactDetails()
{
    $qry = $this->db->query("select * from admin where id=1");
    return $qry->row();
}

function saveContact($ar)
{
    $ins = $this->db->insert("contact_us",$ar);
    if($ins)
    {
        echo '@success'; die;
    }
    else
    {
        echo '@error'; die;
    }
}



function updateCart($cartid,$quantity)
{
    


    $cart_qry = $this->db->query("select * from cart where id='".$cartid."'");
    $cart_row = $cart_qry->row();

    $chk_quant_qry = $this->db->query("select * from link_variant where id='".$cart_row->variant_id."'");
    $chk_quant_row = $chk_quant_qry->row();
    $stock=$chk_quant_row->stock;

    $qty = $cart_row->quantity;
    $quantity_f = $quantity;

                if($stock<$quantity_f)
                {
                    $msg = "Left only ".$stock." Products";
                    echo '@error@'.$msg; die; 
                    //return array('status' =>FALSE,'message'=>$msg);
                }
    $unitprice = $quantity*$cart_row->price;
    $upd = $this->db->update("cart",array('quantity'=>$quantity,'unit_price'=>$unitprice),array('id'=>$cartid));
    if($upd)
    { ?>
        <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-12 col-12">
                        <div class="table_desc">
                            <div class="cart_page table-responsive">
                              <table>
                                <thead>
                                    <tr>
                                        <!-- <th class="product_remove">Delete</th> -->
                                        <th class="product_thumb">Image</th>
                                        <th class="product_name">Product</th>
                                        <th class="product-price">Price</th>
                                        <th class="product_quantity">Quantity</th>
                                        <th class="product_total">Total</th>
                                        <th class="product_total">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                <?php 

                   $session_id = $_SESSION['session_data']['session_id'];
                   $user_id= $_SESSION['userdata']['user_id']; 
                    $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
                    $del_b = $qry->row();
                    $shop = $this->db->query("select * from vendor_shop where id='".$del_b->vendor_id."'");
                    $shopdat = $shop->row();
                    $min_order_amount = $shopdat->min_order_amount;

                    $result = $qry->result();
                    
                        $unitprice=0;
                        $gst=0;
                        foreach ($result as $value) 
                        {
                                $pro = $this->db->query("select * from  product_images where variant_id='".$value->variant_id."'");
                                $product = $pro->row();

                                if($product->image!='')
                                {
                                    $img = base_url()."uploads/products/".$product->image;
                                }
                                else
                                {
                                    $img = base_url()."uploads/noproduct.png";
                                }
                                $var1 = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
                                $link = $var1->row();

                                 $pro1 = $this->db->query("select * from  products where id='".$link->product_id."' and delete_status=0");
                                 $product1 = $pro1->row();

                                 $adm_qry = $this->db->query("select * from  admin_comissions where cat_id='".$product1->cat_id."' and shop_id='".$value->vendor_id."'");

                                 if($adm_qry->num_rows()>0)
                                 {
                                    $adm_comm = $adm_qry->row();
                                    $p_gst = $adm_comm->gst;
                                 }
                                 else
                                 {
                                    $p_gst = '0';
                                 }

                                 $class_percentage = ($value->unit_price/100)*$p_gst;

                                    $variants1 = $var1->result();
                                    $att1=[];
                                    foreach ($variants1 as $value1) 
                                    {

                                        $jsondata = $value1->jsondata;
                                        $values_ar=[];
                                        $json =json_decode($jsondata);
                                        foreach ($json as $value123) 
                                        {
                                            $type = $this->db->query("select * from attributes_title where id='".$value123->attribute_type."'");
                                            $types = $type->row();
                                        
                                            $val = $this->db->query("select * from attributes_values where id='".$value123->attribute_value."'");
                                            $value1 = $val->row();
                                            $values_ar[]=array('id'=>$value1->id,'title'=>$types->title,'value'=>$value1->value);
                                        }
                                    }

                                    $unitprice = $value->unit_price+$unitprice;
                                    $gst = $class_percentage+$gst;
                                ?>
                                <tr>
                                    <td class="product_thumb"><a href="<?php echo base_url(); ?>web/product_view/<?php echo $product1->seo_url; ?>" ><img src="<?php echo $img; ?>" alt=""></a></td>
                                    <td class="product_name"><a href="<?php echo base_url(); ?>web/product_view/<?php echo $product1->seo_url; ?>" ><?php echo $product1->name; ?></a><br>
                                      <p class="mb-0"><a href="<?php echo base_url(); ?>web/store/<?php echo $shopdat->seo_url; ?>/shop"><b>Shop:</b> <?php echo $shopdat->shop_name; ?></a></p>
                                      <p><b>Location:</b><?php echo $shopdat->city; ?></p>
                                    </td>
                                    <td class="product-price"><i class="fal fa-rupee-sign"></i> <?php echo $value->price; ?></td>
                                <form class="form-horizontal" enctype="multipart/form-data"  >
                                    <td class="product_quantity"><label></label> 
                                       <a  onclick="decreaseQty(<?php echo $value->id; ?>)">➖</a>
                                      <input min="1" max="100" name="quantity" id="quantity<?php echo $value->id; ?>" value="<?php echo $value->quantity; ?>" type="text" readonly>
                                      <a onclick="increaseQty(<?php echo $value->id; ?>)">➕</a>
                                    </td>
                                    <td class="product_total"><i class="fal fa-rupee-sign"></i> <?php echo $value->unit_price; ?></td>
                                    <td>
                                      <a onclick="deletecartitems(<?php echo $value->id; ?>)" class="remove-item"><i class="fal fa-trash-alt"></i></a>
                                    </td>
                                </form>
                                </tr>                                
                                <?php } 
                                    $grand_t =  $min_order_amount+$unitprice+$gst;
                                ?>
                                <tr>
                                    <td colspan="6">
                                        <a class="btn pink-btn float-right" href="<?php echo base_url(); ?>web/store/<?php echo $seo_url; ?>/shop">Continue Shopping</a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>   
                            </div>  
                                
                        </div>
                     </div>
                 </div>
                <div class="row justify-content-center pb-5">
                        <div class="col-lg-5 col-md-6">
                            <div class="coupon_code left">
                                <h3>Apply Coupon</h3>
                                <div class="coupon_inner">   
                                    <p>Enter your coupon code if you have one.</p>   
                                    <div id="coupon_msg"></div>  

                                    


                                   <form class="form-horizontal" enctype="multipart/form-data"  >                       
                                        <input placeholder="Coupon code" id="couponcode" type="text">
                                        <button type="button" onclick="validatecouponcode()">Apply coupon</button>
                                    </form> 
                                </div>
                                <hr>
                                <?php $coupons = $this->getCouponcodes($user_id,$session_id); 
                                      if(count($coupons)>0){
                                ?>
                                <div class="row justify-content-center" id="show_button">
                                    <div class="col-md-4">
                                        <a  class="btn-viewcoup mb-2" onclick="showhide()">View Coupons</a> 
                                    </div>
                                </div>
                                 <div class="row justify-content-center" id="close_button" style="display: none;">
                                    <div class="col-md-4">
                                        <a  class="btn-viewcoup mb-2" onclick="closecoupon()">Close Coupons</a> 
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                            <div class="couponlist-box collapse multi-collapse" id="viewcoup">
                                <?php

                                 
                                 //print_r($coupons); 
                                 foreach($coupons as $coup){ ?>
                                <div class="row">
                                <div class="col-8">
                                    <h5><?php echo $coup['percentage']; ?>% OFF</h5>
                                    <p><strong><?php echo $coup['description']; ?></strong></p>
                                </div>
                                <div class="col-4 alig-self-center">
                                    <h6><input type="text" style="width: 100px; border: none; background: none;" value="<?php echo $coup['coupon_code']; ?>" id="myInput<?php echo $coup['id']; ?>"></h6>
                                    <p class="text-center"><small><a onclick="applyCouponcode('<?php echo $coup['coupon_code']; ?>')">Apply Coupon Code</a></small></p>
                                </div>
                                </div>
                              <?php } ?>
                            </div>

                        </div>
                        <div class="col-lg-5 col-md-6">
                            <div class="coupon_code right">
                                <h3>Cart Totals</h3>
                                <div class="coupon_inner">
                                   <div class="cart_subtotal">
                                       <p>Subtotal</p>
                                       <p class="cart_amount"><i class="fal fa-rupee-sign"></i> <?php echo $unitprice; ?></p>
                                       <input type="hidden" id="sub_total" value="<?php echo $unitprice; ?>" >
                                   </div>
                                   <div class="cart_subtotal ">
                                       <p>Shipping Charges</p>
                                       <p class="cart_amount"><i class="fal fa-rupee-sign"></i>  <?php echo $min_order_amount; ?></p>
                                       <input type="hidden" id="min_order_amount" value="<?php echo $min_order_amount; ?>" >
                                   </div>
                                   <div class="cart_subtotal">
                                       <p>Discount</p>
                                       <p class="cart_amount" id="discount">0</p>
                                   </div>
                                   <div class="cart_subtotal" id="total_default">
                                       <p>Total</p>
                                       <p class="cart_amount"><i class="fal fa-rupee-sign"></i> <input type="hidden" id="cart_total" value="<?php echo $grand_t; ?>" > <?php echo $grand_t; ?></p>
                                   </div>
                                   <div class="cart_subtotal" id="total_default_show" style="display: none;">
                                       <p>Total</p>
                                       <p class="cart_amount" id="total_p"><?php echo $grand_t; ?></p>
                                   </div>
                                   <form class="form-horizontal" enctype="multipart/form-data" method="post" action="<?php echo base_url(); ?>web/goaddress_page">
                                     <input type="hidden" name="coupon_id" id="coupon_id" value="0">
                                     <input type="hidden" name="applied_coupon_code" id="applied_coupon_code" value="0">
                                     <input type="hidden" name="coupon_discount" id="coupon_discount" value="0">
                                   
                                       <div class="checkout_btn">
                                        <button type="submit">Proceed to Checkout</button>
                                       </div>
                                   </form>
                                   <?php $adm_qry = $this->db->query("select * from admin where bid_show_status=1");
                                         if($adm_qry->num_rows()>0){ ?>
                                   <p class="bid-text row" style="font-weight: 600;color: #cf1673;">
                                    <span class="col-11">
                                        <a data-toggle="modal" onclick="doBidProducts()">BID ABOVE PRODUCTS </a>
                                    </span>
                                    <span class="col" style="padding: 0px; max-width: 21px;">
                                        <a href="#bidModal" data-toggle="modal" class="text-dark"><i class="fal fa-comment-exclamation" style="font-size: 20px; padding-top: 3px;"></i></a>
                                    </span>
                                    </p>
                                  <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
    <?php }
    else
    {
        echo '@error'; die;
    }
}



function descrementCart($cartid,$quantity)
{
    


    $cart_qry = $this->db->query("select * from cart where id='".$cartid."'");
    $cart_row = $cart_qry->row();

    $chk_quant_qry = $this->db->query("select * from link_variant where id='".$cart_row->variant_id."'");
    $chk_quant_row = $chk_quant_qry->row();
    $stock=$chk_quant_row->stock;

    $qty = $cart_row->quantity;
    $quantity_f = $quantity;

                
    $unitprice = $quantity*$cart_row->price;
    $upd = $this->db->update("cart",array('quantity'=>$quantity,'unit_price'=>$unitprice),array('id'=>$cartid));
    if($upd)
    {
       ?>
        <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-12 col-12">
                        <div class="table_desc">
                            <div class="cart_page table-responsive">
                              <table>
                                <thead>
                                    <tr>
                                        <!-- <th class="product_remove">Delete</th> -->
                                        <th class="product_thumb">Image</th>
                                        <th class="product_name">Product</th>
                                        <th class="product-price">Price</th>
                                        <th class="product_quantity">Quantity</th>
                                        <th class="product_total">Total</th>
                                        <th class="product_total">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                <?php 

                   $session_id = $_SESSION['session_data']['session_id'];
                   $user_id= $_SESSION['userdata']['user_id']; 
                    $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
                    $del_b = $qry->row();
                    $shop = $this->db->query("select * from vendor_shop where id='".$del_b->vendor_id."'");
                    $shopdat = $shop->row();
                    $min_order_amount = $shopdat->min_order_amount;

                    $result = $qry->result();
                    
                        $unitprice=0;
                        $gst=0;
                        foreach ($result as $value) 
                        {
                                $pro = $this->db->query("select * from  product_images where variant_id='".$value->variant_id."'");
                                $product = $pro->row();

                                if($product->image!='')
                                {
                                    $img = base_url()."uploads/products/".$product->image;
                                }
                                else
                                {
                                    $img = base_url()."uploads/noproduct.png";
                                }
                                $var1 = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
                                $link = $var1->row();

                                 $pro1 = $this->db->query("select * from  products where id='".$link->product_id."' and delete_status=0");
                                 $product1 = $pro1->row();

                                 $adm_qry = $this->db->query("select * from  admin_comissions where cat_id='".$product1->cat_id."' and shop_id='".$value->vendor_id."'");

                                 if($adm_qry->num_rows()>0)
                                 {
                                    $adm_comm = $adm_qry->row();
                                    $p_gst = $adm_comm->gst;
                                 }
                                 else
                                 {
                                    $p_gst = '0';
                                 }

                                 $class_percentage = ($value->unit_price/100)*$p_gst;

                                    $variants1 = $var1->result();
                                    $att1=[];
                                    foreach ($variants1 as $value1) 
                                    {

                                        $jsondata = $value1->jsondata;
                                        $values_ar=[];
                                        $json =json_decode($jsondata);
                                        foreach ($json as $value123) 
                                        {
                                            $type = $this->db->query("select * from attributes_title where id='".$value123->attribute_type."'");
                                            $types = $type->row();
                                        
                                            $val = $this->db->query("select * from attributes_values where id='".$value123->attribute_value."'");
                                            $value1 = $val->row();
                                            $values_ar[]=array('id'=>$value1->id,'title'=>$types->title,'value'=>$value1->value);
                                        }
                                    }

                                    $unitprice = $value->unit_price+$unitprice;
                                    $gst = $class_percentage+$gst;
                                ?>
                                <tr>
                                    <td class="product_thumb"><a href="<?php echo base_url(); ?>web/product_view/<?php echo $product1->seo_url; ?>" ><img src="<?php echo $img; ?>" alt=""></a></td>
                                    <td class="product_name"><a href="<?php echo base_url(); ?>web/product_view/<?php echo $product1->seo_url; ?>" ><?php echo $product1->name; ?></a><br>
                                      <p class="mb-0"><a href="<?php echo base_url(); ?>web/store/<?php echo $shopdat->seo_url; ?>/shop"><b>Shop:</b> <?php echo $shopdat->shop_name; ?></a></p>
                                      <p><b>Location:</b><?php echo $shopdat->city; ?></p>
                                    </td>
                                    <td class="product-price"><i class="fal fa-rupee-sign"></i> <?php echo $value->price; ?></td>
                                <form class="form-horizontal" enctype="multipart/form-data"  >
                                    <td class="product_quantity"><label></label> 
                                       <a  onclick="decreaseQty(<?php echo $value->id; ?>)">➖</a>
                                      <input min="1" max="100" name="quantity" id="quantity<?php echo $value->id; ?>" value="<?php echo $value->quantity; ?>" type="text" readonly>
                                      <a onclick="increaseQty(<?php echo $value->id; ?>)">➕</a>
                                    </td>
                                    <td class="product_total"><i class="fal fa-rupee-sign"></i> <?php echo $value->unit_price; ?></td>
                                    <td>
                                      <a onclick="deletecartitems(<?php echo $value->id; ?>)" class="remove-item"><i class="fal fa-trash-alt"></i></a>
                                    </td>
                                </form>
                                </tr>                                
                                <?php } 
                                    $grand_t =  $min_order_amount+$unitprice+$gst;
                                ?>
                                <tr>
                                    <td colspan="6">
                                        <a class="btn pink-btn float-right" href="<?php echo base_url(); ?>web/store/<?php echo $seo_url; ?>/shop">Continue Shopping</a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>   
                            </div>  
                                
                        </div>
                     </div>
                 </div>
                <div class="row justify-content-center pb-5">
                        <div class="col-lg-5 col-md-6">
                            <div class="coupon_code left">
                                <h3>Apply Coupon</h3>
                                <div class="coupon_inner">   
                                    <p>Enter your coupon code if you have one.</p>   
                                    <div id="coupon_msg"></div>  

                                    


                                   <form class="form-horizontal" enctype="multipart/form-data"  >                       
                                        <input placeholder="Coupon code" id="couponcode" type="text">
                                        <button type="button" onclick="validatecouponcode()">Apply coupon</button>
                                    </form> 
                                </div>
                                <hr>
                                <?php 
                                $coupons = $this->getCouponcodes($user_id,$session_id);
                                if(count($coupons)>0){?>
                                <div class="row justify-content-center" id="show_button">
                                    <div class="col-md-4">
                                        <a  class="btn-viewcoup mb-2" onclick="showhide()">View Coupons</a> 
                                    </div>
                                </div>
                                 <div class="row justify-content-center" id="close_button" style="display: none;">
                                    <div class="col-md-4">
                                        <a  class="btn-viewcoup mb-2" onclick="closecoupon()">Close Coupons</a> 
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                            <div class="couponlist-box collapse multi-collapse" id="viewcoup">
                                <?php
                                
                                 foreach($coupons as $coup){ ?>
                                <div class="row">
                                <div class="col-8">
                                    <h5><?php echo $coup['percentage']; ?>% OFF</h5>
                                    <p><strong><?php echo $coup['description']; ?></strong></p>
                                </div>
                                <div class="col-4 alig-self-center">
                                    <h6><input type="text" style="width: 100px; border: none; background: none;" value="<?php echo $coup['coupon_code']; ?>" id="myInput<?php echo $coup['id']; ?>"></h6>
                                    <p class="text-center"><small><a onclick="applyCouponcode('<?php echo $coup['coupon_code']; ?>')">Apply Coupon Code</a></small></p>
                                </div>
                                </div>
                              <?php } ?>
                            </div>

                        </div>
                        <div class="col-lg-5 col-md-6">
                            <div class="coupon_code right">
                                <h3>Cart Totals</h3>
                                <div class="coupon_inner">
                                   <div class="cart_subtotal">
                                       <p>Subtotal</p>
                                       <p class="cart_amount"><i class="fal fa-rupee-sign"></i> <?php echo $unitprice; ?></p>
                                       <input type="hidden" id="sub_total" value="<?php echo $unitprice; ?>" >
                                   </div>
                                   <div class="cart_subtotal ">
                                       <p>Shipping Charges</p>
                                       <p class="cart_amount"><i class="fal fa-rupee-sign"></i>  <?php echo $min_order_amount; ?></p>
                                       <input type="hidden" id="min_order_amount" value="<?php echo $min_order_amount; ?>" >
                                   </div>
                                   <div class="cart_subtotal">
                                       <p>Discount</p>
                                       <p class="cart_amount" id="discount">0</p>
                                   </div>
                                   <div class="cart_subtotal" id="total_default">
                                       <p>Total</p>
                                       <p class="cart_amount"><i class="fal fa-rupee-sign"></i> <input type="hidden" id="cart_total" value="<?php echo $grand_t; ?>" > <?php echo $grand_t; ?></p>
                                   </div>
                                   <div class="cart_subtotal" id="total_default_show" style="display: none;">
                                       <p>Total</p>
                                       <p class="cart_amount" id="total_p"><?php echo $grand_t; ?></p>
                                   </div>
                                   <form class="form-horizontal" enctype="multipart/form-data" method="post" action="<?php echo base_url(); ?>web/goaddress_page">
                                     <input type="hidden" name="coupon_id" id="coupon_id" value="0">
                                     <input type="hidden" name="applied_coupon_code" id="applied_coupon_code" value="0">
                                     <input type="hidden" name="coupon_discount" id="coupon_discount" value="0">
                                   
                                       <div class="checkout_btn">
                                        <button type="submit">Proceed to Checkout</button>
                                       </div>
                                   </form>
                                   <?php $adm_qry = $this->db->query("select * from admin where bid_show_status=1");
                                         if($adm_qry->num_rows()>0){ ?>
                                   <p class="bid-text row" style="font-weight: 600;color: #cf1673;">
                                    <span class="col-11">
                                        <a data-toggle="modal" onclick="doBidProducts()">BID ABOVE PRODUCTS </a>
                                    </span>
                                    <span class="col" style="padding: 0px; max-width: 21px;">
                                        <a href="#bidModal" data-toggle="modal" class="text-dark"><i class="fal fa-comment-exclamation" style="font-size: 20px; padding-top: 3px;"></i></a>
                                    </span>
                                    </p>
                                  <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
    <?php
    }
    else
    {
        echo '@error'; die;
    }
}



function applyCoupon($coupon_code,$session_id,$grand_total,$total_amount,$user_id)
{
  $date = date("Y-m-d");
   
        

    $chk_qry = $this->db->query("select * from cash_coupons where user_id='".$user_id."' and coupon_code='".$coupon_code."'  and ( start_date<='".$date."' and expiry_date>='".$date."' )");
    if($chk_qry->num_rows()>0)
    {
        $chk_cash_coupon_row = $chk_qry->row();
        $discount=$chk_cash_coupon_row->amount;
        if($grand_total>$discount)
        {
            $final_amount=$grand_total-$discount;
        }
        else
        {
            $final_amount=0;
        }
        
        echo '@success@'.$final_amount.'@'.$discount.'@'.$chk_cash_coupon_row->id.'@'.$coupon_code; die;

         // return array('status' =>TRUE,'message'=>"Coupon Applied successfully",'grand_total' =>$final_amount,'discount' =>$discount,'coupon_id'=>$chk_cash_coupon_row->id,'coupon_code'=>$coupon_code);
    }
    else
    {
        $qry = $this->db->query("select * from coupon_codes where coupon_code='".$coupon_code."' and ( start_date<='".$date."' and expiry_date>='".$date."' )");
        if($qry->num_rows()>0)
        {
            $row = $qry->row();


            $cprice = $row->maximum_amount;

            $percentage = $row->percentage;

            $dis_percentage = ($grand_total/100)*$percentage; 

            if($cprice<round($dis_percentage))
            {
                $final_amount = $grand_total-$cprice;
                $discount = number_format($cprice,2);
            }
            else
            {
                    if($grand_total<round($dis_percentage))
                    {
                        $final_amount =0;
                        $discount = number_format($cprice,2);
                    }
                    else
                    {
                        $final_amount = $grand_total-$dis_percentage;
                        $discount = number_format($dis_percentage,2);
                    }
                
            }

             echo '@success@'.$final_amount.'@'.$discount.'@'.$row->id.'@'.$coupon_code; die;

            //return array('status' =>TRUE,'message'=>"Coupon Applied successfully",'grand_total' =>$final_amount,'discount' =>$discount,'coupon_id'=>$row->id,'coupon_code'=>$coupon_code);

        }
        else
        {
            //return array('status' =>FALSE, 'message'=>"Invalid Coupon");
            echo '@error';
        }
    }




   /* $date = date("Y-m-d");
        $qry = $this->db->query("select * from coupon_codes where coupon_code='".$coupon_code."' and ( start_date<='".$date."' and expiry_date>='".$date."' )");
        if($qry->num_rows()>0)
        {
            $row = $qry->row();
               $minimum_order_amount = $row->minimum_order_amount;

            if($grand_total>$minimum_order_amount)
            {

            $cprice = $row->maximum_amount;

            $percentage = $row->percentage;

            $dis_percentage = ($grand_total/100)*$percentage; 

            if($cprice<round($dis_percentage))
            {
                $final_amount = $grand_total-$cprice;
                $discount = $cprice;
            }
            else
            {
                    if($grand_total<round($dis_percentage))
                    {
                        $final_amount =0;
                        $discount = number_format($cprice,2);
                    }
                    else
                    {
                        $final_amount = $grand_total-$dis_percentage;
                        $discount = $dis_percentage;
                    }
                
            }



            echo '@success@'.$final_amount.'@'.$discount.'@'.$row->id.'@'.$coupon_code; die;
        }
        else
        {
            echo '@minorder@'.$minimum_order_amount;
        }
        }
        else
        {
             echo '@error';
        }*/
   
    
}




function getCouponcodes($user_id,$session_id)
{
    $cart_qry = $this->db->query("select * from cart where session_id='".$session_id."' group by session_id");
    $cart_row =$cart_qry->row();
    $shop_id = $cart_row->vendor_id;
    $date = date("Y-m-d");

         $cashcoupon_qry = $this->db->query("select * from cash_coupons where user_id='".$user_id."' and ( start_date<='".$date."' and expiry_date>='".$date."' )");
        $cashcoupon_result = $cashcoupon_qry->result();
        $ar1=[];
        foreach ($cashcoupon_result as $value) 
        {
            $order_qry1 = $this->db->query("select * from orders where user_id='".$user_id."' and coupon_id='".$value->id."'");
            $order_num_rows1 = $order_qry1->num_rows();
           if($order_num_rows1>0)
            {
            }
            else{
               $ar1[]=array('id'=>$value->id,'coupon_code'=>$value->coupon_code,'description'=>$value->description,'percentage'=>"",'maximum_amount'=>$value->amount,'minimum_order_amount'=>"");
           }
        }

        $qry = $this->db->query("select * from coupon_codes where ( shop_id='".$shop_id."' or shop_id=0 ) and ( start_date<='".$date."' and expiry_date>='".$date."' )");
        $dat = $qry->result();
        if($qry->num_rows()>0)
        {
                $ar=[];
                foreach ($dat as $value) 
                {

                    $order_qry = $this->db->query("select * from orders where user_id='".$user_id."' and coupon_id='".$value->id."'");
                    $order_row = $order_qry->row();

                    $order_num_rows = $order_qry->num_rows();


                     if($order_num_rows==$value->utilization)
                     {}else{
                       $ar[]=array('id'=>$value->id,'coupon_code'=>$value->coupon_code,'description'=>$value->description,'percentage'=>$value->percentage,'maximum_amount'=>$value->maximum_amount,'minimum_order_amount'=>$value->minimum_order_amount);
                     }
                }

                if(count($ar)>0)
                {
                    $result = array_merge($ar,$ar1);
                    return $result;
                }
                else
                {
                     if(count($ar1)>0)
                     {
                          return $ar1;
                     }
                     else
                     {
                        return array();
                     }

                    
                }
                
        }
        else
        {
                     if(count($ar1)>0)
                     {
                          return $ar1;
                     }
                     else
                     {
                        return array();
                     }
        }
}


function onesignalnotification($vendor_id,$title,$message)
    {
        $qr = $this->db->query("select * from vendor_shop where id='".$vendor_id."'");
        $res = $qr->row();
            if($res->token!='')
            {
                       
                        $user_id = $res->token;
                        
                        $fields = array(
        'app_id' => '060fc2b8-3d44-4c82-b285-1d3f058f3e2b',
        'include_player_ids' => [$user_id],
        'contents' => array("en" =>$message),
        'headings' => array("en"=>$title),
        'android_channel_id' => '0646f8b2-a151-443f-ac3a-547061134d54'
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


function getShopdata($shop_id)
{
    $qry = $this->db->query("select * from vendor_shop where id='".$shop_id."'");
    return $qry->row();
}


function checkProductQTY($product_id,$session_id,$user_id)
{
    if($user_id=='guest')
    {
        return '1';
    }
    else
    {
         $variant = $this->db->query("select * from link_variant where product_id='".$product_id."' and status=1 ");
        $variant_row = $variant->row();

        $qry = $this->db->query("select * from cart where session_id='".$session_id."' and variant_id='".$variant_row->id."' and user_id='".$user_id."'");
        if($qry->num_rows()>0)
        {
             $cart_row = $qry->row();
             return $cart_row->quantity;
        }
        else
        {
            return '1';
        }
    }
   
    
}


function add_removewhishList($product_id,$user_id)
{
    $qry = $this->db->query("select * from whish_list where product_id='".$product_id."' and user_id='".$user_id."'");
    if($qry->num_rows()>0)
    {
        $upd = $this->db->delete("whish_list",array('product_id'=>$product_id,'user_id'=>$user_id));
        if($upd)
        {
            //return array('status' =>TRUE, 'message'=>"Removed from the Favourites");
            echo '@remove'; die;
        }
    }
    else
    {
        $ins = $this->db->insert("whish_list",array('product_id'=>$product_id,'user_id'=>$user_id));
        if($ins)
        {
            //return array('status' =>TRUE, 'message'=>"Added to Favourites");
            echo '@add'; die;
        }
    }
}





function getTopDealscount($user_id)
{
    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];
            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {
        $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
        $row = $qry->row();

        $lat = $row->lat;
        $lng = $row->lng;
    }

    $admin = $this->db->query("select * from admin where id=1");
    $search_distance = $admin->row()->distance;
   
     $deal_qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.*, products.*, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance,vendor_shop.shop_name FROM link_variant INNER JOIN products ON link_variant.product_id=products.id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id where vendor_shop.status=1 and link_variant.saleprice!=0 and link_variant.status=1 and products.top_deal='yes' and products.availabile_stock_status='available' and products.delete_status=0 group by link_variant.product_id having distance<".$search_distance." order by products.id DESC");
        return $deal_qry->num_rows();
        
}



function getAllTopDeals($limit,$start,$user_id)
{

    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];
            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {
        $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
        $row = $qry->row();

        $lat = $row->lat;
        $lng = $row->lng;
    }

    $admin = $this->db->query("select * from admin where id=1");
    $search_distance = $admin->row()->distance;
   
     $deal_qry = $this->db->query("SELECT link_variant.id as variant_id,link_variant.price,link_variant.saleprice,link_variant.stock,link_variant.jsondata, products.*, products.*, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance,vendor_shop.shop_name FROM link_variant INNER JOIN products ON link_variant.product_id=products.id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id where vendor_shop.status=1 and link_variant.saleprice!=0 and link_variant.status=1 and products.top_deal='yes' and products.availabile_stock_status='available' and products.delete_status=0 group by link_variant.product_id having distance<".$search_distance." order by products.id DESC LIMIT ".$start.",".$limit);

        $dat = $deal_qry->result();
        if($deal_qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                     $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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
                   


                    

                    $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                    if($wish->num_rows()>0)
                    {
                        $stat = true;
                    }
                    else
                    {
                        $stat = false;
                    }
                   
                        
                            $ar[]=array('id'=>$value->id,'variant_id'=>$value->variant_id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$value->name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$value->shop_name,'price'=>$value->price,'saleprice'=>$value->saleprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat,'distance'=>$value->distance,'seo_url'=>$value->seo_url);
                        
                }

            return $ar;
        }
        else
        {
           
            return array();
        }
}



function getmostViewedProducts_count($user_id)
 {
    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];
            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {
      $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
      $row = $qry->row();
      $lat=$row->lat;
      $lng=$row->lng;
    }
    $admin = $this->db->query("select * from admin where id=1");
    $search_distance = $admin->row()->distance;


        $qry = $this->db->query("SELECT link_variant.id as variant_id ,link_variant.saleprice,link_variant.price,link_variant.stock,most_viewed_products.product_id,count(most_viewed_products.id) as cnt,products.*,vendor_shop.id as vendor_id,vendor_shop.shop_name,( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM most_viewed_products INNER JOIN products ON products.id =most_viewed_products.product_id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id INNER JOIN link_variant ON link_variant.product_id=most_viewed_products.product_id WHERE products.status=1 and products.availabile_stock_status='available' and vendor_shop.status=1 and link_variant.saleprice!='0' and link_variant.status=1 and products.delete_status=0 GROUP by most_viewed_products.product_id having distance<'".$search_distance."' order by most_viewed_products.id DESC LIMIT 10");
        return $qry->num_rows();
 }


 function getmostViewedProductsAll($limit,$start,$user_id)
 {
    if($user_id=='guest')
    {
            $guest_id = $_SESSION['userdata']['guest_user_id'];
            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
    }
    else
    {
      $qry = $this->db->query("SELECT * FROM `users` where id='".$user_id."'");
      $row = $qry->row();
      $lat=$row->lat;
      $lng=$row->lng;
    }
    $admin = $this->db->query("select * from admin where id=1");
    $search_distance = $admin->row()->distance;


        $qry = $this->db->query("SELECT link_variant.id as variant_id ,link_variant.saleprice,link_variant.price,link_variant.stock,most_viewed_products.product_id,count(most_viewed_products.id) as cnt,products.*,vendor_shop.id as vendor_id,vendor_shop.shop_name,( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM most_viewed_products INNER JOIN products ON products.id =most_viewed_products.product_id INNER JOIN vendor_shop ON vendor_shop.id=products.shop_id INNER JOIN link_variant ON link_variant.product_id=most_viewed_products.product_id WHERE products.status=1 and products.delete_status=0 and products.availabile_stock_status='available' and vendor_shop.status=1 and link_variant.saleprice!='0' and link_variant.status=1 GROUP by most_viewed_products.product_id having distance<'".$search_distance."' order by most_viewed_products.id DESC LIMIT ".$start.",".$limit);
        $dat = $qry->result();
    
        if($qry->num_rows()>0)
        {   
                $ar=[];
                foreach ($dat as $value) 
                {
                    

                        /*$qry1 = $this->db->query("SELECT * FROM `link_variant` where saleprice!='0' and status=1 and product_id='".$value->id."'");
                        $value12 = $qry1->row();*/

                         $im = $this->db->query("select * from product_images where product_id='".$value->id."' and variant_id='".$value->variant_id."'");
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
                        


                            
                    $state_id = $row->state_id;
                    $city_id = $row->address_id;
                    $pincode_id = $row->pincode_id;

                                if($value->status==1)
                                {
                                    $shopstat = "Open";
                                }
                                else
                                {
                                     $shopstat = "Closed";
                                }


                        $wish = $this->db->query("select * from whish_list where product_id='".$value->id."' and user_id='".$user_id."'");
                        if($wish->num_rows()>0)
                        {
                            $stat = true;
                        }
                        else
                        {
                            $stat = false;
                        }
                        if($value->saleprice!='')
                        {
                           $slaeprice =  $value->saleprice;
                        }
                        else
                        {
                            $slaeprice=0;
                        }

                        if($value->price!='')
                        {
                           $price = $value->price;
                        }
                        else
                        {
                            $price =0;
                        }
                        
                            $name = $value->name;

                             $ar[]=array('id'=>$value->id,'variant_id'=>$value->variant_id,'shop_id'=>$value->shop_id,'variant_product'=>$value->variant_product,'name'=>$name,'category_name'=>$category->category_name,'subcategory_name'=>$subcategory->sub_category_name,'brand'=>$value->brand,'shop'=>$value->shop_name,'price'=>$price,'saleprice'=>$slaeprice,'image'=>$img,'availabile_stock_status'=>$value->availabile_stock_status,'whishlist_status'=>$stat,'shop_status'=>$shopstat,'distance'=>round($value->distance),'seo_url'=>$value->seo_url);
                             
                    }

            return $ar;
        }
        else
        {
            return array();
        }

    

 
 }




function getshopsWithcategoryID_count($cat_id,$subcatid,$user_id)
    {

        if($user_id=='guest')
        {
             $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $users = $this->db->query("select * from users where id='".$user_id."'");
         $users_row = $users->row();

            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }

         

        $qry = $this->db->query("select shop_id from admin_comissions where cat_id='".$cat_id."' and find_in_set('".$subcatid."',subcategory_ids) group by shop_id");
        $dat = $qry->result();
        $shop_ids = array_column($dat, 'shop_id');

        $shop_im = implode(",", $shop_ids);

                    
        $admin = $this->db->query("select * from admin where id=1");
        $search_distance = $admin->row()->distance;

        $shop_qry=$this->db->query("SELECT link_variant.id as variant_id,vendor_shop.*,products.status, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop INNER JOIN products ON vendor_shop.id=products.shop_id INNER JOIN link_variant ON link_variant.product_id=products.id where find_in_set(vendor_shop.id,'".$shop_im."') and link_variant.saleprice!=0 and link_variant.status=1 and products.status=1 and vendor_shop.status=1 and products.delete_status=0 group by products.shop_id having distance<".$search_distance." order by distance asc");
        return $shop_qry->num_rows();
    }


function getshopsWithcategoryID_storecat($limit,$start,$cat_id,$subcatid,$user_id)
    {

        if($user_id=='guest')
        {
             $guest_id = $_SESSION['userdata']['guest_user_id'];

            $users = $this->db->query("select * from guest_users where id='".$guest_id."'");
            $users_row = $users->row();
            
            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }
        else
        {
            $users = $this->db->query("select * from users where id='".$user_id."'");
         $users_row = $users->row();

            $lat =   $users_row->lat;
            $lng =   $users_row->lng;
        }

         

        $qry = $this->db->query("select shop_id from admin_comissions where cat_id='".$cat_id."' and find_in_set('".$subcatid."',subcategory_ids) group by shop_id");
        $dat = $qry->result();
        $shop_ids = array_column($dat, 'shop_id');

        $shop_im = implode(",", $shop_ids);

                    
        $admin = $this->db->query("select * from admin where id=1");
        $search_distance = $admin->row()->distance;
        
        //$shop_qry=$this->db->query("SELECT link_variant.id as variant_id,vendor_shop.*,products.status, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop INNER JOIN products ON vendor_shop.id=products.shop_id INNER JOIN link_variant ON link_variant.product_id=products.id where find_in_set(vendor_shop.id,'".$shop_im."') and link_variant.saleprice!=0 and link_variant.status=1 and products.status=1 and vendor_shop.status=1 group by products.shop_id having distance<".$search_distance." order by distance asc");

        $shop_qry=$this->db->query("SELECT link_variant.id as variant_id,vendor_shop.*,products.status, ( 3959 * acos ( cos ( radians('".$lat."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$lng."') ) + sin ( radians('".$lat."') ) * sin( radians( lat ) ) ) ) * 1.60934 AS distance FROM vendor_shop INNER JOIN products ON vendor_shop.id=products.shop_id INNER JOIN link_variant ON link_variant.product_id=products.id where find_in_set(vendor_shop.id,'".$shop_im."') and link_variant.saleprice!=0 and link_variant.status=1 and products.status=1 and vendor_shop.status=1 and products.delete_status=0 group by products.shop_id having distance<".$search_distance." order by distance asc LIMIT ".$start.",".$limit);
        if($shop_qry->num_rows()>0)
        {   
            $shop_result = $shop_qry->result();
                $ar=[];
                foreach ($shop_result as $value) 
                {
                        if($value->shop_logo!='')
                        {
                            $img = base_url()."uploads/shops/".$value->shop_logo;
                        }
                        else
                        {
                            $img = base_url()."uploads/noproduct.png";;
                        }


                        
                        $shop_qry = $this->db->query("select * from shop_favorites where shop_id='".$value->id."' and user_id='".$user_id."'");
                        if($shop_qry->num_rows()>0)
                        {
                            $shop_not = true;
                        }
                        else
                        {
                            $shop_not = false;
                        }

                      $pro = $this->db->query("SELECT * from products where shop_id='".$value->id."' and delete_status=0");


                     $product_total = $pro->num_rows();
                     /*if($pro->num_rows()>0)
                     {*/
                        if($value->status==1)
                        {
                            $stat = "Open";
                        }
                        else
                        {
                             $stat = "Closed";
                        }

                        $ar[]=array('id'=>$value->id,'shop_name'=>$value->shop_name,'description'=>$value->description,'image'=>$img,'status'=>$stat,'shop_not'=>$shop_not,'distance'=>round($value->distance),'product_total'=>$product_total,'address'=>$value->city,'seo_url'=>$value->seo_url);
                     //}
                }
            return $ar;
        }
        else{
            return array();
        }
    }


function createUserBid($ar)
{

  if($ar['grand_total']<10000)
  {
     echo '@minimum'; die;
  }
  else
  {
          if($ar['grand_total']!='')
          {
            $ins = $this->db->insert('user_bids',$ar);
            if($ins)
            {

                $order_message = "Bid Placed : order ID :".$ar['session_id']." amounting to Rs.".$ar['grand_total'].". from A3 Services"; 

                $user_sms_qry = $this->db->query("select * from users where id='".$ar['user_id']."'");
                $user_sms_row = $user_sms_qry->row();
                $user_phone = $user_sms_qry->mobile;

                /*if($this->send_message($order_message,$user_phone))
                {*/
                   $this->db->insert("sms_notifications",array('order_id'=>$ar['session_id'],'receiver_id'=>$ar['vendor_id'],'sender_id'=>$ar['user_id'],'created_at'=>time(),'message'=>$order_message)); 

                   $this->session->unset_userdata('session_data');
                /*}*/
                echo '@success'; die;
               //return array('status' =>TRUE,'message'=>"Bid Created Successfully"); 
            }
            else
            {
               echo '@error'; die;
            }
          }
   }
}




function mybids($user_id)
{
        $qry = $this->db->query("select * from user_bids where user_id='".$user_id."' order by id desc");
        
        if($qry->num_rows()>0)
        {
            $result = $qry->result();
            $ar=[];
            foreach ($result as $bidds) 
            {   
                if($bidds->bid_status==0 || $bidds->bid_status==1)
                {
                    $status = "Ongoing";
                }
                else if($bidds->bid_status==2)
                {
                  $status = "Completed";
                }
                else if($bidds->bid_status==3)
                {
                  $status = "Cancelled";
                }


                $cart = $this->db->query("select * from cart where session_id='".$bidds->session_id."'");
                $total_products = $cart->num_rows();

                if($bidds->bid_status==0)
                {
                    $bid_status = "Waiting for Bid";  
                }
                else if($bidds->bid_status==1)
                {
                    $bid_status = "Bid accepted";
                }
                else if($bidds->bid_status==2)
                {
                    $bid_status = "Bid Completed";
                }
                else if($bidds->bid_status==3)
                {
                    $bid_status = "Bid Cancelled";
                }

                
                $quote = $this->db->query("select * from vendor_bids where bid_id='".$bidds->id."' and accept='yes'");
                $total_quotes = $quote->num_rows();
                $min_quote = $this->db->query("select MIN(total_price) as minbid from vendor_bids where bid_id='".$bidds->id."' and accept='yes'");
                $min_quote_row = $min_quote->row();
                if($min_quote_row->minbid!='')
                {
                    $low_bid = $min_quote_row->minbid;
                }
                else
                {
                    $low_bid = 'N/A';
                }


                $cart_qry = $this->db->query("select * from cart where session_id='".$bidds->session_id."'");
                $cart_result = $cart_qry->result();
                $products_ar=[];
                foreach ($cart_result as $value) 
                {   
                    $pro = $this->db->query("select * from  product_images where variant_id='".$value->variant_id."'");
                    $product = $pro->row();

                    if($product->image!='')
                    {
                        $img = base_url()."uploads/products/".$product->image;
                    }
                    else
                    {
                        $img = base_url()."uploads/noproduct.png";
                    }
                    //$value->variant_id
                        $var1 = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
                        $link = $var1->row();
                         $pro1 = $this->db->query("select * from  products where id='".$link->product_id."' and delete_status=0");
                         $product1 = $pro1->row();

                         $adm_qry = $this->db->query("select * from  admin_comissions where cat_id='".$product1->cat_id."' and shop_id='".$value->vendor_id."'");
                         if( $adm_qry->num_rows()>0)
                         {
                            $adm_comm = $adm_qry->row();
                            $p_gst = $adm_comm->gst;

                         }
                         else
                         {
                            $p_gst = '0';
                         }

                         $class_percentage = ($value->unit_price/100)*$p_gst;

                         

                            $variants1 = $var1->result();
                            $att1=[];
                            foreach ($variants1 as $value1) 
                            {

                                

                                $jsondata = $value1->jsondata;

                                $values_ar=[];

                                $json =json_decode($jsondata);
                                foreach ($json as $value123) 
                                {
                                    $type = $this->db->query("select * from attributes_title where id='".$value123->attribute_type."'");
                                    $types = $type->row();
                                
                                    $val = $this->db->query("select * from attributes_values where id='".$value123->attribute_value."'");
                                    $value1 = $val->row();
                                    $values_ar[]=array('id'=>$value1->id,'title'=>$types->title,'value'=>$value1->value);
                                }

                                

                            }

                            $shop = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                            $shopdat = $shop->row();

                    $products_ar[]=array('id'=>$value->id,'price'=>$value->price,'quantity'=>$value->quantity,'unit_price'=>$value->unit_price,'image'=>$img,'attributes'=>$values_ar,'product_name'=>$product1->name,'shop_name'=>$shopdat->shop_name,'shop_id'=>$value->vendor_id,'gst'=>$class_percentage);
                }


                $quote_qry = $this->db->query("select * from vendor_bids where bid_id='".$bidds->id."' and bid_status=1");
                $quote_result = $quote_qry->result();
                $quote_list=[];
                foreach ($quote_result as $val) 
                {
                    $shop_q = $this->db->query("select * from vendor_shop where id='".$val->vendor_id."'");
                    $shop_row = $shop_q->row();

                    $qry = $this->db->query("select id,city_name from cities where id='".$shop_row->city_id."'");
                    $city = $qry->row();
                    $addrs = $shop_row->address.", ".$city->city_name;

                    $date = date("d-m-Y h:i A",$val->created_at);

                    if($val->accept=='yes')
                    {
                        $accept_status =1;
                    }
                    else
                    {
                         $accept_status =0;
                    }

                    if($val->accept=='yes')
                    {
                        $finalstatus =1;
                    }
                    
                   $quote_list[]=array('id'=>$val->id,'bid_value'=>$val->total_price,'vendor_id'=>$val->vendor_id,'shop_name'=>$shop_row->shop_name,'shop_name'=>$shop_row->shop_name,'address'=>$addrs,'date'=>$date,'accept_status'=>$accept_status);
                }



               $ar[]=array('id'=>$bidds->id,'order_status'=>$status,'session_id'=>$bidds->session_id,'total_products'=>$total_products,'bid_status'=>$bid_status,'recived_quotes'=>$total_quotes,'lowest_bid'=>$low_bid,'total_price'=>$bidds->grand_total,'products'=>$products_ar,'bidders_list'=>$quote_list,'finalstatus'=>$bidds->bid_status);
            }

            return $ar;
        }
        else
        {
            return array();
        }

}



function mybidDetails($user_id,$bid)
{
        $qry = $this->db->query("select * from user_bids where user_id='".$user_id."' and id='".$bid."' order by id desc");
        
        if($qry->num_rows()>0)
        {
            $bidds = $qry->row();
              
                if($bidds->bid_status==0 || $bidds->bid_status==1)
                {
                    $status = "Ongoing";
                }
                else if($bidds->bid_status==2)
                {
                  $status = "Delivered";
                }
                else if($bidds->bid_status==3)
                {
                  $status = "Cancelled";
                }


                $cart = $this->db->query("select * from cart where session_id='".$bidds->session_id."'");
                $total_products = $cart->num_rows();

                if($bidds->bid_status==0)
                {
                    $bid_status = "Waiting for Bid";  
                }
                else if($bidds->bid_status==1)
                {
                    $bid_status = "Bid accepted";
                }
                else if($bidds->bid_status==2)
                {
                    $bid_status = "Bid Completed";
                }
                else if($bidds->bid_status==3)
                {
                    $bid_status = "Bid Cancelled";
                }

                
                $quote = $this->db->query("select * from vendor_bids where bid_id='".$bidds->id."' and bid_status=1");
                $total_quotes = $quote->num_rows();
                $min_quote = $this->db->query("select MIN(total_price) as minbid from vendor_bids where bid_id='".$bidds->id."' and bid_status=1");
                $min_quote_row = $min_quote->row();
                if($min_quote_row->minbid!='')
                {
                    $low_bid = $min_quote_row->minbid;
                }
                else
                {
                    $low_bid = 'N/A';
                }


                $cart_qry = $this->db->query("select * from cart where session_id='".$bidds->session_id."'");
                $cart_result = $cart_qry->result();
                $products_ar=[];
                foreach ($cart_result as $value) 
                {   
                    $pro = $this->db->query("select * from  product_images where variant_id='".$value->variant_id."'");
                    $product = $pro->row();

                    if($product->image!='')
                    {
                        $img = base_url()."uploads/products/".$product->image;
                    }
                    else
                    {
                        $img = base_url()."uploads/noproduct.png";
                    }
                    //$value->variant_id
                        $var1 = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
                        $link = $var1->row();
                         $pro1 = $this->db->query("select * from  products where id='".$link->product_id."' and delete_status=0");
                         $product1 = $pro1->row();

                         $adm_qry = $this->db->query("select * from  admin_comissions where cat_id='".$product1->cat_id."' and shop_id='".$value->vendor_id."'");
                         if( $adm_qry->num_rows()>0)
                         {
                            $adm_comm = $adm_qry->row();
                            $p_gst = $adm_comm->gst;

                         }
                         else
                         {
                            $p_gst = '0';
                         }

                         $class_percentage = ($value->unit_price/100)*$p_gst;

                         

                            $variants1 = $var1->result();
                            $att1=[];
                            foreach ($variants1 as $value1) 
                            {

                                

                                $jsondata = $value1->jsondata;

                                $values_ar=[];

                                $json =json_decode($jsondata);
                                foreach ($json as $value123) 
                                {
                                    $type = $this->db->query("select * from attributes_title where id='".$value123->attribute_type."'");
                                    $types = $type->row();
                                
                                    $val = $this->db->query("select * from attributes_values where id='".$value123->attribute_value."'");
                                    $value1 = $val->row();
                                    $values_ar[]=array('id'=>$value1->id,'title'=>$types->title,'value'=>$value1->value);
                                }

                                

                            }

                            $shop = $this->db->query("select * from vendor_shop where id='".$value->vendor_id."'");
                            $shopdat = $shop->row();

                    $products_ar[]=array('id'=>$value->id,'price'=>$value->price,'quantity'=>$value->quantity,'unit_price'=>$value->unit_price,'image'=>$img,'variant_id'=>$value->variant_id,'attributes'=>$values_ar,'product_name'=>$product1->name,'shop_name'=>$shopdat->shop_name,'shop_id'=>$value->vendor_id,'gst'=>$class_percentage,'vendor_id'=>$value->vendor_id);
                }


                $quote_qry = $this->db->query("select * from vendor_bids where bid_id='".$bidds->id."' and bid_status=1");
                $quote_result = $quote_qry->result();
                $quote_list=[];
                foreach ($quote_result as $val) 
                {
                    $shop_q = $this->db->query("select * from vendor_shop where id='".$val->vendor_id."'");
                    $shop_row = $shop_q->row();

                    $qry = $this->db->query("select id,city_name from cities where id='".$shop_row->city_id."'");
                    $city = $qry->row();
                    $addrs = $shop_row->address.", ".$city->city_name;

                    $date = date("d-m-Y h:i A",$val->accept_at);

                    if($val->accept=='yes')
                    {
                        $accept_status =1;
                    }
                    else
                    {
                         $accept_status =0;
                    }

                    if($val->accept=='yes')
                    {
                        $finalstatus =1;
                    }
                    
                   $quote_list[]=array('id'=>$val->id,'bid_value'=>$val->total_price,'vendor_id'=>$val->vendor_id,'shop_name'=>$shop_row->shop_name,'shop_name'=>$shop_row->shop_name,'address'=>$addrs,'date'=>$date,'accept_status'=>$accept_status);
                }



               $ar=array('id'=>$bidds->id,'order_status'=>$status,'session_id'=>$bidds->session_id,'total_products'=>$total_products,'bid_status'=>$bid_status,'recived_quotes'=>$total_quotes,'lowest_bid'=>$low_bid,'total_price'=>$bidds->grand_total,'products'=>$products_ar,'bidders_list'=>$quote_list,'finalstatus'=>$bidds->bid_status);
            

            return $ar;
        }
        else
        {
            return array();
        }

}


function createBid($bid)
{
    $ar=array('bid_status'=>3);
    $wr =array('id'=>$bid);
    $upd = $this->db->update("user_bids",$ar,$wr);
    if($upd)
    {
      echo '@success'; die;
        //return array('status' =>TRUE, 'message'=>"Bid Cancelled Successfully");
    }
    else
    {
      echo '@error'; die;
        //return array('status' =>FALSE, 'message'=>"Something Went wrong");
    }
}


}