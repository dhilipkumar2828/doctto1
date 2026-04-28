<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
/**
 * @property User $user
 * @property Common_model $common_model
 */
class User_api extends REST_Controller {

    public function __construct() 
    { 
     /* header('Access-Control-Allow-Origin: *');
      header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();*/
        //load user model
        
        //$this->load->library('email'); 


         header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
        header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers,Authorization,Access-Control-Allow-Origin,Access-Control-Allow-Methods");
        parent::__construct();
        $this->load->model('user');
        $this->load->model('common_model');
        
        // Exclude public methods from mandatory authentication
        $CI =& get_instance();
        $method = $CI->router->fetch_method();
        
        $public_methods = array(
            'login', 'login_post', 
            'user_registration', 'user_registration_post', 
            'user', 'user_post', 
            'otp_verification', 'otp_verification_post', 
            'resend_otp', 'resend_otp_post',
            'forgotpassword', 'forgotpassword_post', 
            'forgot_password_otp_verification', 'forgot_password_otp_verification_post', 
            'resetPassword', 'resetPassword_post', 
            'getwelcomescreens', 'getwelcomescreens_post', 
            'getpincodeslist', 'getpincodeslist_post', 
            'getAllCategories', 'getAllCategories_post', 
            'getSubCategories', 'getSubCategories_post', 
            'version_control', 'version_control_post', 
            'homepagedata', 'homepagedata_post',
            'getProducts', 'getProducts_post', 
            'productDetails', 'productDetails_post', 
            'getcontent', 'getcontent_post', 
            'contact_details', 'contact_details_post'
        );
        
        if (!in_array($method, $public_methods)) {
            $this->common_model->auth();
        }
       
    }
    
              public function update_token_post() 
    {
              $user_id= $this->post('user_id');
              $token = $this->post('token');
              $platform = $this->post('platform');

              $chk = $this->user->update_token($user_id,$token,$platform);
              if($chk=='error')
              {
                  $this->response($chk, REST_Controller::HTTP_OK);  
              }
              else
              {
                  $this->response($chk, REST_Controller::HTTP_OK);
              }
    }
    
      function auto_logout_post()     
      {
          $user_id= $this->post('user_id');  
       
          $chk = $this->user->autoLogout($user_id);
          
          $this->response($chk, REST_Controller::HTTP_OK); 
                 
          
      }
      
      
     function update_location_post() {
         
        $user_id = $this->post('user_id');          
        $lat = $this->post('latitude');
        $lng = $this->post('longitude');
        $home_location = $this->post('location');

        $chk = $this->user->update_Loc($user_id,$lat, $lng, $home_location);
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        } else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
    }
      
     public function checkingsms_post() 
    {
               $message = $this->post('message');
               $mobile_number = $this->post('mobile_number');
               $chk = $this->user->send_message($message,$mobile_number);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }

    public function validate_pincodes_post() 
    {
               $pincode = $this->post('pincode');
               $chk = $this->user->validatePincodes($pincode);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }

    public function user_registration_post() 
    {
              $doctor_name = $this->post('doctor_name');
              $phone = $this->post('phone');
              $password = md5($this->post('password'));
            //   $token = $this->post('token');
            //   $platform = $this->post('platform');
              $email = $this->post('email');
            //   $device_name = $this->post('device_name');
              //$location = $this->post('location');,'home_location'=>$location,'lat'=>$latitude,'lng'=>$longitude
             // $latitude = $this->post('latitude');
              //$longitude = $this->post('longitude');
            //   if($token=='' || $token==null)
            //   {
            //     $token1="";
            //   }
            //   else
            //   {
            //     $token1=$token;
            //   }


                   $data = array('first_name' =>$doctor_name,'password' =>$password,'phone'=>$phone,'email'=>$email);

               $chk = $this->user->doRegister($data);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }

    public function otp_verification_post() 
    {
              $user_id = $this->post('user_id');
              $otp = $this->post('otp');
               $chk = $this->user->verify_OTP($user_id,$otp);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }

    public function resend_otp_post() 
    {
               $user_id = $this->post('user_id');
               $chk = $this->user->resendOTP($user_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }

    public function getpincode_post() 
    {
               $user_id = $this->post('user_id');
               $chk = $this->user->getpincode($user_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }

    public function getwelcomescreens_post() 
    {
               $chk = $this->user->getWelcomeScreens();
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }


    

    public function updatepincode_post() 
    {
              $user_id = $this->post('user_id'); 
              $pincode = $this->post('pincode');
              $chk = $this->user->updatePincode($user_id,$pincode);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }
    public function homepagedata_post() 
    {
              $user_id = $this->post('user_id'); 
              $pincode = $this->post('pincode');
              $chk = $this->user->getBanners($user_id,$pincode);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }

     public function getpincodeslist_post() 
    {
              $chk = $this->user->getPincodesList();
              $this->response($chk, REST_Controller::HTTP_OK);  
    }

    public function getAllCategories_post()
    {
              $chk = $this->user->getAllCategories();
              $this->response($chk, REST_Controller::HTTP_OK); 
    }

    public function getSubCategories_post()
    {
              $category_id = $this->post('category_id'); 
              $chk = $this->user->getSubCategories($category_id);
              $this->response($chk, REST_Controller::HTTP_OK); 
    }

     public function forgotpassword_post()
    {
               $username = $this->post('username');
               $chk = $this->user->checkForgot($username);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }
    
    
      
         public function forgot_password_otp_verification_post() 
    {
              $user_id = $this->post('user_id');
              $otp = $this->post('otp');
               $chk = $this->user->forgot_password_verify_OTP($user_id,$otp);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }
    

    function deleteCartDetails_post()
    {
          $session_id =  $this->post('sid');
          $user_id =  $this->post('user_id');
          $chk = $this->user->deleteCartData($session_id,$user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function getProducts_post()
    {
              $cat_id = $this->post('cat_id');
              $user_id = $this->post('user_id');
              $subcat_id = $this->post('subcat_id');
              $start_from = $this->post('start_from');
              $perpage = $this->post('perpage');
              $pincode = $this->post('pincode');
              $sid = $this->post('sid');
              $chk = $this->user->getProducts($cat_id,$user_id,$subcat_id,$start_from,$perpage,$pincode,$sid);
             
              $this->response($chk, REST_Controller::HTTP_OK); 
    }


    function productDetails_post()
    {
             $product_id = $this->post('product_id');
              $user_id = $this->post('user_id');
              $sid = $this->post('sid');
              $chk = $this->user->getProductDetails($product_id,$user_id,$sid);
             
              $this->response($chk, REST_Controller::HTTP_OK); 
    }



    function addToCart_post()
    {
          $session_id = $this->post('sid');
          $variant_id = $this->post('variant_id');
          $vendor_id = $this->post('vendor_id');
          $user_id = $this->post('user_id');
          $price = $this->post('price');
          $quantity = $this->post('quantity');
          $chk = $this->user->addToCart($session_id,$variant_id,$vendor_id,$user_id,$price,$quantity);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }


     function cartList_post()
    {
          $session_id = $this->post('sid');
          $chk = $this->user->getCartList($session_id);
          $this->response($chk, REST_Controller::HTTP_OK); 
    }

    function increment_quantity_post()
    {
          $cart_id = $this->post('cart_id');
          $sid = $this->post('sid');
          $chk = $this->user->incrementQuantity($cart_id,$sid);
          $this->response($chk, REST_Controller::HTTP_OK); 
    }

    function decrement_quantity_post()
    {
           $cart_id = $this->post('cart_id');
          $sid = $this->post('sid');
              $chk = $this->user->decrementQuantity($cart_id,$sid);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function removeCart_post()
    {
           $cart_id = $this->post('cart_id');
              $chk = $this->user->removeCart($cart_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function coupon_codes_post()
    {
            $shop_id = $this->post('shop_id');
            $user_id = $this->post('user_id');
              $chk = $this->user->getCouponcodes($shop_id,$user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function apply_user_coupon_post()
    {
            $coupon_code = $this->post('coupon_code');
            $session_id = $this->post('sid');
            $grand_total= $this->post('grand_total');
            $user_id= $this->post('user_id');
            $chk = $this->user->applyUserCoupon($coupon_code,$session_id,$grand_total,$user_id);
            $this->response($chk, REST_Controller::HTTP_OK); 
    }

    function apply_manualcoupon_post()
    {
            $coupon_code = $this->post('coupon_code');
            $session_id = $this->post('sid');
            $grand_total= $this->post('grand_total');
              $chk = $this->user->applyManualCoupon($coupon_code,$session_id,$grand_total);
              $this->response($chk, REST_Controller::HTTP_OK);
    }


    function razerpay_orderId_post()
    {
      
              /* $razorpay_keyid = 'rzp_live_yUSgYWYRFXcTeI';
               $razorpay_secret = 't1r8cnK1pXGzcL3nmGrUeoum';*/
            $razorpay_keyid = 'rzp_test_DFLA3IuxBE958i';
            $razorpay_secret = 'jLjGiWZbt6WZ6XAS7kxff6eq';
            
            $total_amount = $this->post('grand_total');

              $final = (int)round($total_amount * 100);
              
            $data = array(
                'amount' => $final, 
                'currency' => 'INR'
            );
            $payload = json_encode($data);
            $ch = curl_init('https://api.razorpay.com/v1/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$razorpay_keyid:$razorpay_secret");  
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
            ); 
            $result = curl_exec($ch);
             $order_id = json_decode($result)->id; 

            $session_id = $this->post('sid');
          $user_id = $this->post('user_id');
          //$vendor_id = $this->post('vendor_id');
          $deliveryaddress_id = $this->post('deliveryaddress_id');

          $sub_total = $this->post('sub_total');
          $delivery_amount = $this->post('delivery_amount');
          $grand_total = $this->post('grand_total');
          $coupon_id= $this->post('coupon_id');
          $coupon_code= $this->post('coupon_code');
          $coupon_disount= $this->post('coupon_disount');
          $gst= $this->post('gst');
          $created_at = time();
          $order_status = 1;

         $chk = $this->user->dorazerpayOrder($session_id,$user_id,$deliveryaddress_id,$created_at,$order_status,$sub_total,$delivery_amount,$grand_total,$coupon_id,$coupon_code,$coupon_disount,$order_id,$gst,$razorpay_keyid);
          $this->response($chk, REST_Controller::HTTP_OK);  
       
    }


    function razerpay_doOrder_post()
    {
          $orderid = $this->post('order_id');
          $razerpay_orderid = $this->post('razerpay_orderId');
          $razerpay_txnid = $this->post('razerpay_txnid');
          $payment_option = 'ONLINE';
           $chk = $this->user->dorazerpaysuccessOrder($orderid,$razerpay_orderid,$razerpay_txnid,$payment_option);
         $this->response($chk, REST_Controller::HTTP_OK); 
    }



    function doOrder_post()
    {
          $session_id = $this->post('sid');
          $user_id = $this->post('user_id');
          //$vendor_id = $this->post('vendor_id');
          $deliveryaddress_id = $this->post('deliveryaddress_id');
          $payment_option = $this->post('payment_option');

          $sub_total = $this->post('sub_total');
          $delivery_amount = $this->post('delivery_amount');
          $grand_total = $this->post('grand_total');
          $coupon_id= $this->post('coupon_id');
          $coupon_code= $this->post('coupon_code');
          $coupon_disount= $this->post('coupon_disount');
          $gst= $this->post('gst');
          $created_at = time();
          $order_status = 1;

            $chk = $this->user->userCODOrder($session_id,$user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$sub_total,$delivery_amount,$grand_total,$coupon_id,$coupon_code,$coupon_disount,$gst);
            $this->response($chk, REST_Controller::HTTP_OK);
    }




      
    
    function update_profile_post()
    {
             $user_id =  $this->post('user_id');
              $fullname =  $this->post('name');
              $email =  $this->post('email');
              $gender =  $this->post('gender');
              $dob =    $this->post('dob');
            //   $img =  $this->post('image');
            //   print_r($dob);die;
              //$image =  $this->post('image');
              $chk = $this->user->updateProfile($user_id,$fullname,$email,$gender,$dob);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function user_addresslist_post()
    {
              $user_id = $this->post('user_id');
              $pincode = $this->post('pincode');
              $chk = $this->user->getAddress($user_id,$pincode);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function add_cartaddress_post()
    {
             $user_id = $this->post('user_id');
              $name = $this->post('name');
              $mobile = $this->post('mobile');
              $address = $this->post('address');
              $city = $this->post('city');
              $state = $this->post('state');
              $pincode = $this->post('pincode');
              $address_type  = $this->post('address_type');
              $landmark  = $this->post('landmark');
              $vendor_id  = $this->post('vendor_id');
               $chk = $this->user->addCartAddress($user_id,$name,$mobile,$address,$city,$state,$pincode,$address_type,$landmark,$vendor_id);
             
                  $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function update_cartaddress_post()
    {
             $address_id = $this->post('address_id');
              $user_id = $this->post('user_id');
              $name = $this->post('name');
              $mobile = $this->post('mobile');
              $address = $this->post('address');
              $locality = $this->post('locality');
              $city = $this->post('city');
              $state = $this->post('state');
              $pincode = $this->post('pincode');
              $address_type  = $this->post('address_type');
              $landmark= $this->post('landmark');
              $chk = $this->user->updateCartAddress($address_id,$user_id,$name,$mobile,$address,$locality,$city,$state,$pincode,$address_type,$landmark);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }


    function myorders_post()
    {
        $user_id =  $this->post('user_id');
            $order_status =  $this->post('order_status');
              $chk = $this->user->orderList($user_id,$order_status);
              $this->response($chk, REST_Controller::HTTP_OK);  
    }

     function completed_orders_post()
    {
       $user_id =  $this->post('user_id');
              $chk = $this->user->completedOrders($user_id);
              $this->response($chk, REST_Controller::HTTP_OK); 
    }

    function orderDetails_post()
    {
              $order_id =  $this->post('order_id');
              $chk = $this->user->orderDetails($order_id);
              $this->response($chk, REST_Controller::HTTP_OK);
    }


    function getcontent_post()
    {
            $cid  =  $this->post('cid');
          $chk = $this->user->getcontent($cid);
          $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function contact_details_post()
    {
            $chk = $this->user->contactDetails();
          $this->response($chk, REST_Controller::HTTP_OK); 
    }

    function login_post()
    {
              $username = $this->post('username');
              $password = md5($this->post('password'));
              
              // Extract token from Header (Bearer Token)
              $headers = $this->input->get_request_header('Authorization');
              if (empty($headers) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
                  $headers = $_SERVER['HTTP_AUTHORIZATION'];
              } elseif (empty($headers) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                  $headers = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
              }
              
              $token = "";
              if (!empty($headers)) {
                  if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                      $token = $matches[1];
                  } else {
                      $token = $headers;
                  }
              }
              
              // Fallback to body token if header is empty
              if (empty($token)) {
                  $token = $this->post('token');
              }

               $chk = $this->user->checkLogin($username,$password,$token);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }

     function changePassword_post()
    {
           $user_id =  $this->post('user_id');
           $current_password =  $this->post('current_password');
           $new_password =  $this->post('new_password');

           $chk = $this->user->updatePassword($user_id,$current_password,$new_password);
           $this->response($chk, REST_Controller::HTTP_OK); 
    }

     function resetPassword_post()
    {
              //$otp = $this->post('otp');
              $password = $this->post('password');
              $phone = $this->post('phone');
               $chk = $this->user->resetPassword($phone,$password);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }


    function book_ambulance_post()
    {
              $pincode = $this->post('pincode');
              $user_id = $this->post('user_id');
              $from_location = $this->post('from_location');
              $to_location = $this->post('to_location');
              $date = $this->post('date');
              $time = $this->post('time');
              $mobile = $this->post('mobile');
              $create_at = time();
               $chk = $this->user->bookAmbulance($pincode,$user_id,$from_location,$to_location,$date,$time,$mobile,$create_at);
          $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function ambulanceNotifications_post()
    {
              $user_id = $this->post('user_id');
               $chk = $this->user->ambulanceNotifications($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
    }
    

    function emergency_number_post()
    {
          $chk = $this->user->emergencyNumber();
          $this->response($chk, REST_Controller::HTTP_OK);  
    }


    function my_bookings_post()
    {
               $status = $this->post('status');
               $user_id = $this->post('user_id');
               $chk = $this->user->myBookings($status,$user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function cancel_booking_post()
    {
               $user_id = $this->post('user_id');
               $booking_id = $this->post('booking_id');
               $chk = $this->user->cancelBooking($user_id,$booking_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function logout_post()
    {
               $user_id = $this->post('user_id');
               $chk = $this->user->logOut($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function productDetails_filter_post()
    {
              $product_id = $this->post('product_id');
              $json_data = $this->post('json_data');
              $sid = $this->post('sid');
              $chk = $this->user->productDetailsFilter($product_id,$json_data,$sid);
             
              $this->response($chk, REST_Controller::HTTP_OK);   
    }

    function getpincode_wise_address_post()
    {
              $pincode = $this->post('pincode');
              $chk = $this->user->getPincodeWiseAddress($pincode);
             
              $this->response($chk, REST_Controller::HTTP_OK); 
    }


    function profile_details_post($user_id = NULL)
    {
              if (empty($user_id)) {
                  $user_id =  $this->post('user_id');
              }
              $dob =  $this->post('dob');
              $chk = $this->user->profileDetails($user_id,$dob);
              $this->response($chk, REST_Controller::HTTP_OK); 
    }

    function delete_address_post()
    {
          $user_id  =  $this->post('user_id');
          $aid  =  $this->post('aid');
          $chk = $this->user->deleteAddress($user_id,$aid);
          $this->response($chk, REST_Controller::HTTP_OK);  
    }

     function cancelOrder_post()
    {
             $user_id =  $this->post('user_id'); 
             $orderid =  $this->post('order_id'); 
             $cancellation_reason =  $this->post('cancellation_reason'); 
             $chk = $this->user->docancelOrder($user_id,$orderid,$cancellation_reason);
             $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function do_user_reports_post()
    {
             $user_id =  $this->post('user_id'); 
             $fullname =  $this->post('fullname'); 
             $mobile =  $this->post('mobile'); 
             $subject =  $this->post('subject'); 
             $message =  $this->post('message'); 
             $images =  $this->post('images'); 
             $chk = $this->user->doUserReports($user_id,$fullname,$mobile,$subject,$message,$images);
             $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function cancel_reasonlist_post()
    {
             $chk = $this->user->cancelReasonList();
             $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function upload_file_post()
    {
            $user_id =  $this->post('user_id');
            $chk = $this->user->browse_file($user_id);
            $this->response($chk, REST_Controller::HTTP_OK); 
    }


    function search_products_post()
    {
              $user_id = $this->post('user_id');
              $keyword = $this->post('keyword');

              $pincode = $this->post('pincode');
              $sid = $this->post('sid');
              $chk = $this->user->searchProducts($user_id,$keyword,$pincode,$sid);

              $this->response($chk, REST_Controller::HTTP_OK);  
    }

    function filterProducts_post()
    {
              $type = $this->post('type');
              $catId = $this->post('cat_id');
              $user_id = $this->post('user_id');
              $subcat_id = $this->post('subcat_id');
              $pincode = $this->post('pincode');
              $sid = $this->post('sid');
              $chk = $this->user->filterProductslist($type,$catId,$user_id,$subcat_id,$pincode,$sid);
             
              $this->response($chk, REST_Controller::HTTP_OK);    
    }


     function userNotificationsCount_post()
    {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->userNotificationsCount($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);     
    }


     function userNotifications_post()
    {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->userNotifications($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);     
    }

    function userOverallNotifications_post()
    {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->userOverallNotifications($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);     
    }

    function updateUserToken_post()
    {
          $user_id  =  $this->post('user_id');
          $tokenId  =  $this->post('tokenId');
          $chk = $this->user->updateUserToken1($user_id,$tokenId);
          $this->response($chk, REST_Controller::HTTP_OK);
    }

    function getUserReport_post()
    {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->getUserReport($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);
    }

    function generate_session_post()
    {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->generateSession($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);     
    }

    function version_control_post()
    {
         $chk = $this->user->versionControl();
         $this->response($chk, REST_Controller::HTTP_OK);     
    }



    public function user_post() {
       $userData = array();

       if($this->post('action')=='social_login')
       {
              $username = $this->post('username');
              $email = $this->post('email');
              $loginstatus = $this->post('loginstatus');

              $data = array('first_name' =>$username,'email' =>$email,'loginstatus'=>$loginstatus);
               $chk = $this->user->doFacebookRegister($data);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       
       else if($this->post('action')=='login')
       {
              $username = $this->post('username');
              $password = md5($this->post('password'));
              $token = $this->post('token');
              $platform = $this->post('platform');
               $chk = $this->user->checkLogin($username,$password,$token,$platform);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       
        else if($this->post('action')=='resetPassword')
       {
              $otp = $this->post('otp');
              $password = $this->post('password');
              $phone = $this->post('phone');
               $chk = $this->user->resetPassword($phone,$otp,$password);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='add_useraddress')
       {
              $user_id = $this->post('user_id');
              $name = $this->post('name');
              $mobile = $this->post('mobile');
              $address = $this->post('address');
              $city = $this->post('city');
              $state = $this->post('state');
              $pincode = $this->post('pincode');
              $address_type  = $this->post('address_type');
              $landmark  = $this->post('landmark');
               $chk = $this->user->addAddress($user_id,$name,$mobile,$address,$city,$state,$pincode,$address_type,$landmark);
             
                  $this->response($chk, REST_Controller::HTTP_OK);  
       }

       else if($this->post('action')=='add_cartaddress')
       {
              $user_id = $this->post('user_id');
              $name = $this->post('name');
              $mobile = $this->post('mobile');
              $address = $this->post('address');
              $city = $this->post('city');
              $state = $this->post('state');
              $pincode = $this->post('pincode');
              $address_type  = $this->post('address_type');
              $landmark  = $this->post('landmark');
              $vendor_id  = $this->post('vendor_id');
               $chk = $this->user->addCartAddress($user_id,$name,$mobile,$address,$city,$state,$pincode,$address_type,$landmark,$vendor_id);
             
                  $this->response($chk, REST_Controller::HTTP_OK);  
       }


       else if($this->post('action')=='edit_useraddress')
       {
            $address_id = $this->post('aid');
              $user_id = $this->post('user_id');
              $name = $this->post('name');
              $mobile = $this->post('mobile');
              $address = $this->post('address');
              $city = $this->post('city');
              $state = $this->post('state');
              $pincode = $this->post('pincode');
              $address_type  = $this->post('address_type');

               $chk = $this->user->editUseraddress($address_id,$user_id,$name,$mobile,$address,$city,$state,$pincode,$address_type);
             
                  $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='update_useraddress')
       {
            $address_id = $this->post('address_id');
              $user_id = $this->post('user_id');
              $name = $this->post('name');
              $mobile = $this->post('mobile');
              $address = $this->post('address');
              $locality = $this->post('locality');
              $city = $this->post('city');
              $state = $this->post('state');
              $pincode = $this->post('pincode');
              $address_type  = $this->post('address_type');

               $landmark =  $this->post('landmark');
               $chk = $this->user->updateAddress($address_id,$user_id,$name,$mobile,$address,$locality,$city,$state,$pincode,$address_type,$landmark);
             
                  $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='update_cartaddress')
       {
            $address_id = $this->post('address_id');
              $user_id = $this->post('user_id');
              $name = $this->post('name');
              $mobile = $this->post('mobile');
              $address = $this->post('address');
              $locality = $this->post('locality');
              $city = $this->post('city');
              $state = $this->post('state');
              $pincode = $this->post('pincode');
              $address_type  = $this->post('address_type');
              $vendor_id  = $this->post('vendor_id');

               $chk = $this->user->updateCartAddress($address_id,$user_id,$name,$mobile,$address,$locality,$city,$state,$pincode,$address_type,$vendor_id);
             
                  $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getstates')
       {
              $chk = $this->user->getstates();
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='gethomepagecities')
       {
              $chk = $this->user->getHomeCities();
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getselectedcities')
       {
              $state = $this->post('state');
              $chk = $this->user->getSelectedCities($state);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
        else if($this->post('action')=='getlocationpincodes')
       {
              $city_id = $this->post('city');
              $chk = $this->user->getselectedPincodes($city_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       
       else if($this->post('action')=='saveUserhomeLocation')
       {
              $user_id = $this->post('user_id');
              $state = $this->post('state');
              $city_id = $this->post('city_id');
              $pincode = $this->post('pincode');
              $chk = $this->user->saveUserHomeLocation($user_id,$city_id,$state,$pincode);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='checkLocationpageCondition')
       {
              $user_id = $this->post('user_id');
              $chk = $this->user->checkLocationCondition($user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       
       else if($this->post('action')=='getcities1')
       {
              $state_id = $this->post('state_id');
              $shopId = $this->post('shopId');
              $chk = $this->user->getCities1($state_id,$shopId);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='geteditcities1')
       {
              $state_id = $this->post('state_id');
              $chk = $this->user->getEditCities1($state_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       
       else if($this->post('action')=='getpincodes')
       {
              $state_id = $this->post('state_id');
              $city_id = $this->post('city_id');
              $vendor_id = $this->post('vendor_id');
              $chk = $this->user->getPincodes($state_id,$city_id,$vendor_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getuserpincodes')
       {
              $state_id = $this->post('state_id');
              $city_id = $this->post('city_id');
              $chk = $this->user->getuserPincodes($state_id,$city_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getareas')
       {
              $state_id = $this->post('state_id');
              $city_id = $this->post('city_id');
              $vendor_id = $this->post('vendor_id');
              $pincode = $this->post('pincode');
              $chk = $this->user->getAreas($state_id,$city_id,$vendor_id,$pincode);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getuserareas')
       {
              $state_id = $this->post('state_id');
              $city_id = $this->post('city_id');
              $pincode = $this->post('pincode');
              $chk = $this->user->getuserAreas($state_id,$city_id,$pincode);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='user_addresslist')
       {
              $user_id = $this->post('user_id');
              $pincode = $this->post('pincode');
              $chk = $this->user->getAddress($user_id,$pincode);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='user_cartaddresslist')
       {
              $user_id = $this->post('user_id');
              $vendor_id = $this->post('vendor_id');
              $chk = $this->user->getCartAddress($user_id,$vendor_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='usersavedAddress')
       {
              $user_id = $this->post('user_id');
              $chk = $this->user->getAddress1($user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
        else if($this->post('action')=='userhomesavedAddress')
       {
              $user_id = $this->post('user_id');
              $chk = $this->user->userSavedAddress($user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getbanner')
       {

              $user_id = $this->post('user_id'); 
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $chk = $this->user->getBanners($user_id,$lat,$lng);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getvendorbanner')
       {  
              $vendor_id = $this->post('vendor_id');
              $user_id = $this->post('user_id');
              $chk = $this->user->getVendorBanners($vendor_id,$user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       
      else if($this->post('action')=='getcategories')
       {
              $chk = $this->user->getCategories();
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='gethome_categories1')
       {
              $user_id = $this->post('user_id');
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $chk = $this->user->getHomeLimitCategories($user_id,$lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='gethome_categories')
       {
              $chk = $this->user->getHomeCategories();
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='selected_categories')
       {
              $catid = $this->post('catid');
              $chk = $this->user->getseleHomeCategories($catid);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       
       else if($this->post('action')=='getshopsWithcategory')
       {
              $cat_id = $this->post('cat_id');
              $user_id = $this->post('user_id');
              $subcatid = $this->post('subcatid');
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $chk = $this->user->getshopsWithcategoryID($cat_id,$subcatid,$user_id,$lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }

       else if($this->post('action')=='getallshopsWithoutcategory')
       {
              $user_id = $this->post('user_id');
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $start_from = $this->post('start_from');
              $perpage = $this->post('perpage');
              $chk = $this->user->getAllshopsWithoutcategory($user_id,$lat,$lng,$start_from,$perpage);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getcategoryWithshop')
       {
              $shop_id = $this->post('shop_id');
              $chk = $this->user->getcategoryWithshopID($shop_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getSubcategories1')
       {
              $shop_id = $this->post('shop_id');
              $cat_id = $this->post('cat_id');
              $user_id = $this->post('user_id');
              $chk = $this->user->getSublimitCategories($shop_id,$cat_id,$user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getSubcategories')
       {
              $shop_id = $this->post('shop_id');
              $cat_id = $this->post('cat_id');
              $user_id = $this->post('user_id');
              $chk = $this->user->getSubCategories($shop_id,$cat_id,$user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getProducts')
       {
              $cat_id = $this->post('cat_id');
              $shop_id = $this->post('shop_id');
              $user_id = $this->post('user_id');
              $subcat_id = $this->post('subcat_id');
              $start_from = $this->post('start_from');
              $perpage = $this->post('perpage');
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $chk = $this->user->getProducts($cat_id,$shop_id,$user_id,$subcat_id,$start_from,$perpage,$lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='shopwiseProductSearch')
       {
              $cat_id = $this->post('cat_id');
              $shop_id = $this->post('shop_id');
              $user_id = $this->post('user_id');
              $subcat_id = $this->post('subcat_id');
              $start_from = $this->post('start_from');
              $perpage = $this->post('perpage');
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $keyword = $this->post('keyword');
              $chk = $this->user->shopWiseProductSearch($cat_id,$shop_id,$user_id,$subcat_id,$start_from,$perpage,$lat,$lng,$keyword);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='fetchsubcategories')
       {
              $cat_id = $this->post('cat_id');
              $subcat_id = $this->post('subcat_id');
              $shopId = $this->post('shopId');
              $chk = $this->user->fetchsubcategories($cat_id,$subcat_id,$shopId);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='search_products')
       {
              $cat_id = $this->post('cat_id');
              $shop_id = $this->post('shop_id');
              $user_id = $this->post('user_id');
              $keyword = $this->post('keyword');
              $chk = $this->user->searchProducts($cat_id,$shop_id,$user_id,$keyword);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='filterProducts')
       {
              $type = $this->post('type');
              $shopId = $this->post('shop_id'); 
              $catId = $this->post('catId'); 
              $user_id= $this->post('user_id'); 
              $subcat_id= $this->post('subcat_id'); 
              $lat= $this->post('lat'); 
              $lng= $this->post('lng'); 
              $chk = $this->user->filterProductslist($type,$shopId,$catId,$user_id,$subcat_id,$lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='searchProducts')
       {
              $keyword = $this->post('keyword');
              $chk = $this->user->fetchProducts($keyword);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='productDetails')
       {
              $product_id = $this->post('product_id');
              $user_id = $this->post('user_id');
              $chk = $this->user->getProductDetails($product_id,$user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
        else if($this->post('action')=='productDetails_filter')
       {
              $product_id = $this->post('product_id');
              $json_data = $this->post('json_data');
              $chk = $this->user->productDetailsFilter($product_id,$json_data);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getDeals')
       {
              $chk = $this->user->getDeals();
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='addToCart')
       {
       		$session_id = $this->post('sid');
       		$variant_id = $this->post('variant_id');
       		$vendor_id = $this->post('vendor_id');
       		$user_id = $this->post('user_id');
       		$price = $this->post('price');
       		$quantity = $this->post('quantity');
              $chk = $this->user->addToCart($session_id,$variant_id,$vendor_id,$user_id,$price,$quantity);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='cartList')
       {
       		    $session_id = $this->post('sid');
              $chk = $this->user->getCartList($session_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='increment_quantity')
       {
          $cart_id = $this->post('cart_id');
          $sid = $this->post('sid');
              $chk = $this->user->incrementQuantity($cart_id,$sid);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='decrement_quantity')
       {
          $cart_id = $this->post('cart_id');
          $sid = $this->post('sid');
              $chk = $this->user->decrementQuantity($cart_id,$sid);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='removeCart')
       {
       		  $cart_id = $this->post('cart_id');
              $chk = $this->user->removeCart($cart_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='coupon_codes')
       {
            $shop_id = $this->post('shop_id');
            $user_id = $this->post('user_id');
              $chk = $this->user->getCouponcodes($shop_id,$user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='apply_coupon')
       {
       		  $coupon_code = $this->post('coupon_code');
       		  $session_id = $this->post('sid');
       		  $coupon_status= $this->post('coupon_status');
            $grand_total= $this->post('grand_total');
              $chk = $this->user->applyCoupon($coupon_code,$session_id,$coupon_status,$grand_total);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='apply_user_coupon')
       {
            $coupon_code = $this->post('coupon_code');
            $session_id = $this->post('sid');
            $coupon_status= $this->post('coupon_status');
            $grand_total= $this->post('grand_total');
            $user_id= $this->post('user_id');
              $chk = $this->user->applyUserCoupon($coupon_code,$session_id,$coupon_status,$grand_total,$user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='apply_manualcoupon')
       {
            $coupon_code = $this->post('coupon_code');
            $session_id = $this->post('sid');
            $coupon_status= $this->post('coupon_status');
            $grand_total= $this->post('grand_total');
              $chk = $this->user->applyManualCoupon($coupon_code,$session_id,$coupon_status,$grand_total);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       /*else if($this->post('action')=='remove_coupon')
       {
              $chk = $this->user->removeCoupon();
              $this->response($chk, REST_Controller::HTTP_OK);  
       }*/
       else if($this->post('action')=='doOrder')
       {
       		$session_id = $this->post('sid');
       		$user_id = $this->post('user_id');
       		$vendor_id = $this->post('vendor_id');
       		$deliveryaddress_id = $this->post('deliveryaddress_id');
       		$payment_option = $this->post('payment_option');

       		$sub_total = $this->post('sub_total');
       		$delivery_amount = $this->post('delivery_amount');
       		$grand_total = $this->post('grand_total');
       		$coupon_id= $this->post('coupon_id');
       		$coupon_code= $this->post('coupon_code');
       		$coupon_disount= $this->post('coupon_disount');
          $gst= $this->post('gst');
       		$created_at = time();
       		$order_status = 1;

            $chk = $this->user->doOrder($session_id,$user_id,$vendor_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$sub_total,$delivery_amount,$grand_total,$coupon_id,$coupon_code,$coupon_disount,$gst);
            $this->response($chk, REST_Controller::HTTP_OK);  
       }
       

       else if($this->post('action')=='gettime_slots')
       {
       		  $date =  $this->post('date');
              $chk = $this->user->timeSlots($date);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='myorders')
       {
            $user_id =  $this->post('user_id');
            $order_status =  $this->post('order_status');
              $chk = $this->user->orderList($user_id,$order_status);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='completed_orders')
       {
            $user_id =  $this->post('user_id');
              $chk = $this->user->completedOrders($user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='orderDetails')
       {
              $order_id =  $this->post('order_id');
              $chk = $this->user->orderDetails($order_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='add_removewhishlist')
       {
              $product_id =  $this->post('product_id');
              $user_id =  $this->post('user_id');
              $chk = $this->user->add_removewhishList($product_id,$user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='whishlist')
       {
              $user_id =  $this->post('user_id');
              $chk = $this->user->whishList($user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='profile_details')
       {
              $user_id =  $this->post('user_id');
              $chk = $this->user->profileDetails($user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='upload_file')
       {
            $user_id =  $this->post('user_id');
            $chk = $this->user->browse_file($user_id);
            $this->response($chk, REST_Controller::HTTP_OK); 
       }
       else if($this->post('action')=='update_profile')
       {
              $user_id =  $this->post('user_id');
              $fullname =  $this->post('fullname');
              $email =  $this->post('email');
              $gender =  $this->post('gender');
              //$image =  $this->post('image');
              $chk = $this->user->updateProfile($user_id,$fullname,$email,$gender);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='attributeswithCategory')
       {
              $cat_id =  $this->post('cat_id');
              $chk = $this->user->attributesWithCategory($cat_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='attributeValues')
       {
              $attribute_id =  $this->post('attribute_id');
              $chk = $this->user->fetchattributeValues($attribute_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='userreviews')
       {
              $user_id =  $this->post('user_id');
              $order_id =  $this->post('order_id');
              $vendor_id =  $this->post('vendor_id');
              $review =  $this->post('review');
              $rating =  $this->post('rating');
              $createdat =  time();
              $chk = $this->user->userReviews($user_id,$order_id,$vendor_id,$review,$rating,$createdat);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getNearByShops')
       {
              $lat =  $this->post('lat');
              $lng =  $this->post('lng');

              $chk = $this->user->getNearByShops($lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getVenodorShopswithCatId')
       {
              $catid =  $this->post('catid');

              $chk = $this->user->getVenodorShopswithCatId($catid);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='searchNearByShops')
       {
              $title =  $this->post('title');
              $lat =  $this->post('lat');
              $lng =  $this->post('lng');
              $chk = $this->user->searchByNearByShops($title,$lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='searchNearByShopswithloadmore')
       {
              $title =  $this->post('title');
              $lat =  $this->post('lat');
              $lng =  $this->post('lng');

              $start_from = $this->post('start_from');
              $perpage = $this->post('perpage');
              $chk = $this->user->searchByNearByShopswithloadmore($title,$lat,$lng,$start_from,$perpage);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getshopsLogo')
       {
              $lat =  $this->post('lat');
              $lng =  $this->post('lng');

              $chk = $this->user->shopsLogo($lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getvendorDetails')
       {
             $vendor_id =  $this->post('vendor_id'); 
             $chk = $this->user->getVendorProfile($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='add_removeFavorites')
       {
              $shop_id =  $this->post('shop_id');
              $user_id =  $this->post('user_id');
              $chk = $this->user->add_removeFavorites($shop_id,$user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getOrdersdetails')
       {
            $oid =  $this->post('oid'); 
             $chk = $this->user->getOrdersDetails($oid);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       
       else if($this->post('action')=='favoritelist')
       {
              $user_id =  $this->post('user_id');
              $chk = $this->user->favoriteList($user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='deleteCartDetails')
       {
              $session_id =  $this->post('sid');
              $user_id =  $this->post('user_id');
              $chk = $this->user->deleteCartData($session_id,$user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='exchange_refund')
       {
              $session_id =  $this->post('order_id');
              $product_id =  $this->post('product_id');
              $user_id =  $this->post('user_id');
              $vendor_id  =  $this->post('vendor_id');
              $cartid  =  $this->post('cartid');
              $delivery_type  =  $this->post('delivery_type');
              $reson = $this->post('message');

              $chk = $this->user->exchangeRefund($session_id,$product_id,$user_id,$vendor_id,$cartid,$delivery_type,$reson);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }

       else if($this->post('action')=='delivery_slots')
       {
               $shop_id  =  $this->post('shop_id');
               $date  =  $this->post('date'); 
              $chk = $this->user->delivery_slots($shop_id,$date);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='razerpay_orderId')
       {
              /* $razorpay_keyid = 'rzp_live_yUSgYWYRFXcTeI';
               $razorpay_secret = 't1r8cnK1pXGzcL3nmGrUeoum';*/
	          $razorpay_keyid = 'rzp_test_DFLA3IuxBE958i';
            $razorpay_secret = 'jLjGiWZbt6WZ6XAS7kxff6eq';
            
            $total_amount = $this->post('grand_total');

              $final = (int)round($total_amount * 100);
              
            $data = array(
                'amount' => $final, 
                'currency' => 'INR'
            );
            $payload = json_encode($data);
            $ch = curl_init('https://api.razorpay.com/v1/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$razorpay_keyid:$razorpay_secret");  
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
            ); 
            $result = curl_exec($ch);
             $order_id = json_decode($result)->id; 

            $session_id = $this->post('sid');
          $user_id = $this->post('user_id');
          $vendor_id = $this->post('vendor_id');
          $deliveryaddress_id = $this->post('deliveryaddress_id');

          $sub_total = $this->post('sub_total');
          $delivery_amount = $this->post('delivery_amount');
          $grand_total = $this->post('grand_total');
          $coupon_id= $this->post('coupon_id');
          $coupon_code= $this->post('coupon_code');
          $coupon_disount= $this->post('coupon_disount');
          $gst= $this->post('gst');
          $created_at = time();
          $order_status = 1;

         $chk = $this->user->dorazerpayOrder($session_id,$user_id,$vendor_id,$deliveryaddress_id,$created_at,$order_status,$sub_total,$delivery_amount,$grand_total,$coupon_id,$coupon_code,$coupon_disount,$order_id,$gst);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='razerpay_doOrder')
       {
          $order_id = $this->post('order_id');
          $razerpay_orderid = $this->post('razerpay_orderid');
          $razerpay_txnid = $this->post('razerpay_txnid');
          $payment_option = $this->post('payment_option');
           $chk = $this->user->dorazerpaysuccessOrder($order_id,$razerpay_orderid,$razerpay_txnid,$payment_option);
         $this->response($chk, REST_Controller::HTTP_OK); 
      }
      else if($this->post('action')=='getmostViewedProducts')
      {
              $user_id = $this->post('user_id');
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $chk = $this->user->getmostViewedProducts($user_id,$lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
    
      else if($this->post('action')=='top_deals')
      {
              $user_id = $this->post('user_id');
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $chk = $this->user->getTopDeals($user_id,$lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='view_alltop_deals')
      {
              $user_id = $this->post('user_id');
              $start_from = $this->post('start_from');
              $perpage = $this->post('perpage');
              $lat = $this->post('lat');
              $lng = $this->post('lng');
              $chk = $this->user->viewAlltopDeals($user_id,$start_from,$perpage,$lat,$lng);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      
      else if($this->post('action')=='products_filters')
      {
              $shop_id  =  $this->post('shop_id');
              $json_data = $this->post('json_data');
              $cat_id= $this->post('cat_id');
              $subcat_id= $this->post('subcat_id');
              $chk = $this->user->getproductsFilters($json_data,$shop_id,$cat_id,$subcat_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='socialshare')
      {
          $chk = $this->user->socialShare();
           $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='getDistance')
      {
          $clat  =  $this->post('clat');
          $clng  =  $this->post('clng');
          $userlat  =  $this->post('userlat');
          $userlng  =  $this->post('userlng');
          $chk = $this->user->getDistance($clat,$clng,$userlat,$userlng);
          $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='userTransactions')
      {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->getTransactions($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='getuserWallet')
      {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->getUserWallet($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='wallet_razerpay_orderID')
       {
            //$razorpay_keyid = 'rzp_test_ywjRok0nPJdn2M';
            //$razorpay_secret = 'peGeVXRIW7EM4Kn0gBuxUqYP';
			$razorpay_keyid = 'rzp_live_yUSgYWYRFXcTeI';
            $razorpay_secret = 't1r8cnK1pXGzcL3nmGrUeoum';
            
            $user_id = $this->post('user_id');
            $total_amount = $this->post('wallet_amount');
            $data = array(
                'amount' => (int)round($total_amount * 100), 
                'currency' => 'INR'
            );
            $payload = json_encode($data);
            $ch = curl_init('https://api.razorpay.com/v1/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$razorpay_keyid:$razorpay_secret");  
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
            ); 
            $result = curl_exec($ch);
            $order_id = json_decode($result)->id;

          
          $created_at = time();
          $order_status = 1;

          $chk = $this->user->getWalletRazerpayOrderId($user_id,$total_amount,$order_id);

          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='addamounttoWallet')
       {
          $user_id  =  $this->post('user_id');
          $payment_id  =  $this->post('payment_id');
          $razerpay_orderid  =  $this->post('razerpay_orderid');
          $order_id  =  $this->post('order_id');
          $chk = $this->user->addAmountToWallet($user_id,$payment_id,$razerpay_orderid,$order_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getUserbonupoints')
       {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->getUserBonuPoints($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='doredeemAmount')
       {
          $user_id  =  $this->post('user_id');
          $redeem_amount  =  $this->post('redeem_amount');
          $chk = $this->user->doRedeemAmount($user_id,$redeem_amount);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getcities')
       {
          $chk = $this->user->getCities();
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='select_useraddress')
       {
          $user_id  =  $this->post('user_id');
          $address_id  =  $this->post('address_id');
          $chk = $this->user->selectUseraddress($user_id,$address_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getOrderCoins')
       {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->fetchOrderCoins($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='createUserBid')
       {
          $user_id  =  $this->post('user_id');
          $ar = array(
                  'user_id'=>$this->post('user_id'),
                  'session_id'=>$this->post('sid'),
                  'vendor_id'=>$this->post('vendor_id'),
                  'sub_total'=>$this->post('sub_total'),
                  'delivery_amount'=>$this->post('delivery_amount'),
                  'grand_total'=>$this->post('grand_total'),
                  'gst'=>$this->post('gst'),
                  'created_at'=>time()
                );

          $chk = $this->user->createUserBid($ar);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getBidList')
       {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->fetchOrderCoins($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='mybids')
       {
          $user_id  =  $this->post('user_id');
          $order_status  =  $this->post('order_status');
          $chk = $this->user->mybids($user_id,$order_status);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='cancelBid')
       {
          $bid  =  $this->post('bid');
          $chk = $this->user->cancelBid($bid);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getcontent')
       {
          $cid  =  $this->post('cid');
          $chk = $this->user->getcontent($cid);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='contact_details')
       {
          $chk = $this->user->contactDetails();
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='add_contact_us')
       {
          $name  =  $this->post('name');
          $email  =  $this->post('email');
          $mobile  =  $this->post('mobile');
          $message  =  $this->post('message');
           $created_date  =  date("Y-m-d H:i:s");
           $ar = array('name'=>$name,'email'=>$email,'mobile'=>$mobile,'message'=>$message,'created_date'=>$created_date);
          $chk = $this->user->addContactUs($ar);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='delete_address')
       {
          $user_id  =  $this->post('user_id');
          $aid  =  $this->post('aid');
          $chk = $this->user->deleteAddress($user_id,$aid);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='userNotifications')
       {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->userNotifications($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='updateNotifications')
       {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->updateNotifications($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='sendPushnotification')
       {
          $user_id  =  $this->post('user_id');
          $chk = $this->user->sendPushnotification($user_id);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='become_vendor')
       {
              $shopname = $this->post('shopname');
              $ownername = $this->post('ownername');
              $email = $this->post('email');
              $mobile = $this->post('mobile');
              $state = $this->post('state');
              $city = $this->post('city');
              $location = $this->post('location');

              $data = array('shopname' =>$shopname, 'ownername' =>$ownername, 'email' =>$email,'mobile'=>$mobile,'state'=>$state,'city'=>$city,'location'=>$location,'created_at'=>time());

               $chk = $this->user->becomeVendor($data);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='checkLocation')
       {
          $area_id  =  $this->post('area_id');
          $city_id  =  $this->post('city_id');
          $vendor_id  =  $this->post('vendor_id');
          $pincode  =  $this->post('pincode');
          $chk = $this->user->checkLocation($area_id,$city_id,$vendor_id,$pincode);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='updateUserToken')
       {
          $user_id  =  $this->post('user_id');
          $tokenId  =  $this->post('tokenId');
          $chk = $this->user->updateUserToken1($user_id,$tokenId);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='updatecustomeraddress')
       {
          $user_id  =  $this->post('user_id');
          $lat  =  $this->post('lat');
          $lng  =  $this->post('lng');
          $address  =  $this->post('address');
          $chk = $this->user->updateCustomerAddress($user_id,$lat,$lng,$address);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='distanceinkm')
       {
          $chk = $this->user->distanceInKm();
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if ($this->post('action') == 'version_control') {
            $chk = $this->user->versionControl();
            $this->response($chk, REST_Controller::HTTP_OK);  
        }
        else if ($this->post('action') == 'promotional_notifications') {
            $user_id  =  $this->post('user_id');
            $chk = $this->user->promotionalNotifications($user_id);
            $this->response($chk, REST_Controller::HTTP_OK);  
        }


       /*else if($this->post('action')=='getPincodes')
       {
          $from_pincode  =  $this->post('from_pincode');
          $to_pincode  =  $this->post('to_pincode');

          $chk = $this->user->calc_distance($from_pincode,$to_pincode);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }*/

        else if ($this->post('action') == 'change_seo_url') {
            $chk = $this->user->changeSeoUrl();
            $this->response($chk, REST_Controller::HTTP_OK);  
        }
        else if($this->post('action')=='test_razerpay_orderId')
       {
               /*$razorpay_keyid = 'rzp_live_yUSgYWYRFXcTeI';
               $razorpay_secret = 't1r8cnK1pXGzcL3nmGrUeoum';*/
            $razorpay_keyid = 'rzp_test_DFLA3IuxBE958i';
            $razorpay_secret = 'jLjGiWZbt6WZ6XAS7kxff6eq';
            
            $total_amount = $this->post('grand_total');

              $final = (int)round($total_amount * 100);
              
            $data = array(
                'amount' => $final, 
                'currency' => 'INR'
            );
            $payload = json_encode($data);
            $ch = curl_init('https://api.razorpay.com/v1/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$razorpay_keyid:$razorpay_secret");  
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
            ); 
            $result = curl_exec($ch);
             $order_id = json_decode($result)->id;    

            $session_id = $this->post('sid');
          $user_id = $this->post('user_id');
          $vendor_id = $this->post('vendor_id');
          $deliveryaddress_id = $this->post('deliveryaddress_id');

          $sub_total = $this->post('sub_total');
          $delivery_amount = $this->post('delivery_amount');
          $grand_total = $this->post('grand_total');
          $coupon_id= $this->post('coupon_id');
          $coupon_code= $this->post('coupon_code');
          $coupon_disount= $this->post('coupon_disount');
          $gst= $this->post('gst');
          $created_at = time();
          $order_status = 1;

         $chk = $this->user->test_dorazerpayOrder($session_id,$user_id,$vendor_id,$deliveryaddress_id,$created_at,$order_status,$sub_total,$delivery_amount,$grand_total,$coupon_id,$coupon_code,$coupon_disount,$order_id,$gst);
          $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='test_razerpay_doOrder')
       {
          $order_id = $this->post('order_id');
          $razerpay_orderid = $this->post('razerpay_orderid');
          $razerpay_txnid = $this->post('razerpay_txnid');
          $payment_option = $this->post('payment_option');
           $chk = $this->user->test_dorazerpaysuccessOrder($order_id,$razerpay_orderid,$razerpay_txnid,$payment_option);
         $this->response($chk, REST_Controller::HTTP_OK); 
      }
      else if($this->post('action')=='bid_show_status')
      {
          $chk = $this->user->bidShowStatus();
          $this->response($chk, REST_Controller::HTTP_OK);
      }

      // Get terms and conditions for user subscriptions
      else if($this->post('action')=='get_user_subscription_terms')
      {
          $plan_id = $this->post('plan_id');
          
          $this->load->model('admin/terms_conditions_model');
          $terms = $this->terms_conditions_model->get_terms_with_sections('user', $plan_id);
          
          if ($terms) {
              $this->response([
                  'status' => TRUE,
                  'data' => $terms
              ], REST_Controller::HTTP_OK);
          } else {
              $this->response([
                  'status' => FALSE,
                  'message' => 'No terms and conditions found'
              ], REST_Controller::HTTP_OK);
          }
      }

      // Accept terms and conditions
      else if($this->post('action')=='accept_user_terms')
      {
          $user_id = $this->post('user_id');
          $terms_id = $this->post('terms_id');
          $subscription_id = $this->post('subscription_id');
          
          $this->load->model('admin/terms_conditions_model');
          
          if ($this->terms_conditions_model->log_terms_acceptance($user_id, 'user', $terms_id, $subscription_id)) {
              $this->response([
                  'status' => TRUE,
                  'message' => 'Terms accepted successfully'
              ], REST_Controller::HTTP_OK);
          } else {
              $this->response([
                  'status' => FALSE,
                  'message' => 'Unable to log terms acceptance'
              ], REST_Controller::HTTP_OK);
          }
      }

      


       

       

       

       

       


       

       

       

       



       
    }
}



?>