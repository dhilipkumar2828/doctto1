<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Web extends MY_Controller {

    public $data;

    function __construct() {


      header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept,Access-Control-Request-Method, Authorization");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        $this->load->helper(array('url','html','form'));
        $this->load->model('Web_model');

        $this->load->library("pagination");

      if($_SESSION['userdata']['logged_in']!=true)
      {
        $url = $this->uri->segment('2'); 
        if($url=='contact_us')
        {
            $data['contactinfo'] = $this->Web_model->getContactDetails();
            $data['categories'] = $this->Web_model->getHomeLimitCategories();
            $data['title'] = 'Contact Us';
            $this->load->view('web/contact_us.php',$data);

            //redirect('web/contact_us');
        }
        else if($url=='about_us')
        {
           $data['categories'] = $this->Web_model->getHomeLimitCategories();
           $data['content'] = $this->Web_model->getSMSContent('4');
            $data['title'] = 'About Us';
            $this->load->view('web/about_us.php',$data);
            //redirect('web/about_us');
        }
        else if($url=='privacy_policy')
        {
          $data['categories'] = $this->Web_model->getHomeLimitCategories();
          $data['content'] = $this->Web_model->getSMSContent('2');
             $data['title'] = 'Privacy Policy';
             $this->load->view('web/privacy_policy.php',$data);
             //redirect('web/privacy_policy');
        }
        else if($url=='refund_policy')
        {
             $data['categories'] = $this->Web_model->getHomeLimitCategories();
             $data['content'] = $this->Web_model->getSMSContent('8');
             $data['title'] = 'Refund Policy';
             $this->load->view('web/refund_policy.php',$data);
             //redirect('web/refund_policy');
        }
        else if($url=='terms_and_conditions')
        {
          $data['categories'] = $this->Web_model->getHomeLimitCategories();
          $data['content'] = $this->Web_model->getSMSContent('1');
             $data['title'] = 'Terms and Conditions';
             $this->load->view('web/terms_and_conditions.php',$data);
            // redirect('web/terms_and_conditions');
        }
        else if($url=='delivery_partner')
        {
          $data['categories'] = $this->Web_model->getHomeLimitCategories();
          $data['content'] = $this->Web_model->getSMSContent('9');
             $data['title'] = 'Delivery Partner';
             $this->load->view('web/terms_and_conditions.php',$data);
            // redirect('web/terms_and_conditions');
        }
        else if($url=='shipping_policy')
        {
          $data['categories'] = $this->Web_model->getHomeLimitCategories();
          $data['content'] = $this->Web_model->getSMSContent('10');
             $data['title'] = 'Shipping Policy';
             $this->load->view('web/terms_and_conditions.php',$data);
            // redirect('web/terms_and_conditions');
        }
        else
        {
          /*$data['title'] = 'Dashboard';
          $this->load->view('web/index.php',$data);*/

          if($_SESSION['userdata']['guest_logged_in']==false)
          {
            $data['categories'] = $this->Web_model->getHomeLimitCategories();

            $data['location'] = $_SERVER['REQUEST_URI'];
             $data['title'] = 'Location';
             $this->load->view('web/selectlocation.php',$data);
          }
          
         
        }
               
      }
    }

    

    function index() 
    {
         $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {

              $user_id = $_SESSION['userdata']['user_id'];
              $data['banners'] = $this->Web_model->getBanners($user_id);
               $data['categories'] = $this->Web_model->getHomeLimitCategories();

              $data['topdeals'] = $this->Web_model->getTopDeals($user_id);

              //echo "<pre>";  print_r($data['topdeals']); die;

               $data['trending'] = $this->Web_model->getmostViewedProducts($user_id);

              $data['bannerads'] = $this->Web_model->getBannerads($user_id);

              $data['lastbannerads'] = $this->Web_model->getLastBannerads($user_id);

             

              $data['shops'] = $this->Web_model->getAllshopsWithoutcategory($user_id);
              

             
              //print_r($data['trending']); die;
              $data['title'] = 'Dashboard';
              $this->load->view('web/index.php',$data); 
         }
         else
         {
          $guest_user_id = $_SESSION['userdata']['guest_user_id']; 

          if($_SESSION['userdata']['guest_logged_in']==true)
          {
              $user_id = 'guest'; 
              $data['banners'] = $this->Web_model->getBanners($user_id);
              //echo "<pre>";  print_r($data['banners']); die;
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
              $data['topdeals'] = $this->Web_model->getTopDeals($user_id);

             
              $data['trending'] = $this->Web_model->getmostViewedProducts($user_id);

              $data['bannerads'] = $this->Web_model->getBannerads($user_id);

              $data['lastbannerads'] = $this->Web_model->getLastBannerads($user_id);

              

              $data['shops'] = $this->Web_model->getAllshopsWithoutcategory($user_id);


              //echo "<pre>"; print_r($data['shops']); die;
              $data['title'] = 'Dashboard';
              $this->load->view('web/index.php',$data); 
          }
          
              

         }
    }

    function store($seo_url,$cateurl)
    {
        
       if($this->uri->segment(5)!='')
       {
          $data['search_title'] =$this->uri->segment(5);
       }
       else
       {
          $data['search_title'] = 'nodata';
       }

       $data['search'] = 'show';
      $qry = $this->db->query("select * from vendor_shop where seo_url='".$seo_url."'");
      $vendor_row = $qry->row();
      $vendor_id=$vendor_row->id;
      
      $data['shop_id']=$vendor_id;
      $data['shop_name']=$vendor_row->shop_name;
      $data['cat_url']=$cateurl;
      $data['subcat_url']="nosubcat";
        $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['vendor_id']=$vendor_id;
              $data['vendor_seo_url']=$vendor_row->seo_url;
               $chk = $this->Web_model->getVendorBanners($vendor_id,$user_id);
               $data['banners']=$chk['bannerslist'];

              $data['categories'] = $this->Web_model->getHomeLimitCategories();
             
             
              $data['banneradd1']=$chk['banneradd1'];
              $data['banneradd2']=$chk['banneradd2'];

              $data['subcategories'] = $this->Web_model->getcategoryWithshopID($vendor_id);
              
              $data['best_selling_products'] = $this->Web_model->bestSeller($vendor_id,$user_id);
              //print_r($data['best_selling_products']); die;
              $data['title'] = 'Store details';
              $this->load->view('web/store_view.php',$data); 
         }
         else
         {
              $guest_user_id = $_SESSION['userdata']['guest_user_id']; 
              if($guest_user_id!='')
              {
                  $user_id = 'guest'; 
                  $data['vendor_id']=$vendor_id;
                  $data['vendor_seo_url']=$vendor_row->seo_url;
                  $data['categories'] = $this->Web_model->getHomeLimitCategories();
                  $chk = $this->Web_model->getVendorBanners($vendor_id,$user_id);
                  $data['banners']=$chk['bannerslist'];
                  //print_r($data['banners']); die;
                  $data['banneradd1']=$chk['banneradd1'];
                  $data['banneradd2']=$chk['banneradd2'];

                  $data['subcategories'] = $this->Web_model->getcategoryWithshopID($vendor_id);
                  $data['best_selling_products'] = $this->Web_model->bestSeller($vendor_id,$user_id);
                  //print_r($data['best_selling_products']); die;
                  $data['title'] = 'Store details';
                  $this->load->view('web/store_view.php',$data); 
              }
              else
              {
                  $data['location'] = $_SERVER['REQUEST_URI']; 
                  $data['title'] = 'Location';
                  $this->load->view('web/selectlocation.php',$data);
              }
         }
    }

    function checkout() 
    {
       $user_id = $_SESSION['userdata']['user_id'];
       
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $session_id = $_SESSION['session_data']['session_id'];
                    $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
                    $del_b = $qry->row();
                      $data['shop_id']=$del_b->vendor_id;
                      $shop_qry = $this->db->query("select * from vendor_shop where id='".$del_b->vendor_id."'");
                      $shop_row = $shop_qry->row();
                       $data['shop_name'] = $shop_row->shop_name;
                       $data['city'] = $shop_row->city;
                       $data['seo_url'] = $shop_row->seo_url;
                    if($qry->num_rows()>0)
                    {
                      $data['categories'] = $this->Web_model->getHomeLimitCategories();
                      $data['coupons'] = $this->Web_model->getCouponcodes($user_id,$session_id);
                        $data['addresslist'] = $this->Web_model->getAddress($user_id);
                        $data['states'] = $this->Web_model->getstates();
                        $data['title'] = 'Check Out';
                        $this->load->view('web/cart.php',$data); 
                        //$this->load->view('web/checkout.php',$data); 
                    }
                    else
                    {

                        $data['banners'] = $this->Web_model->getBanners($user_id);
                        $data['categories'] = $this->Web_model->getHomeLimitCategories();
                        $data['shops'] = $this->Web_model->getAllshopsWithoutcategory($user_id);
                        $data['topdeals'] = $this->Web_model->getTopDeals($user_id);
                        $data['trending'] = $this->Web_model->getmostViewedProducts($user_id);
                       $data['title'] = 'Dashboard';
                       $this->load->view('web/index.php',$data); 
                    }
         }
         else
         {
              $data['location'] = $_SERVER['REQUEST_URI'];

              $data['title'] = 'Dashboard';
              //$this->load->view('web/index.php',$data); 
              redirect('web');
              //$this->load->view('web/selectlocation.php',$data); 
         }
    }
   
   function changeLocation()
   {

      if($_SESSION['userdata']['logged_in']==true)
      {
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
      }
   }
    function updateCart()
    {
        $user_id = $_SESSION['userdata']['user_id'];
        $cartid =  $this->input->post('cartid'); 
        $quantity =   $this->input->post('quantity');
        $chk = $this->Web_model->updateCart($cartid,$quantity);
        die;
    }

    function removeCart()
    {
        $user_id = $_SESSION['userdata']['user_id'];
        $cartid =  $this->input->post('cartid'); 
        $quantity =   $this->input->post('quantity');
        $chk = $this->Web_model->descrementCart($cartid,$quantity);
        die;
    }

    

    function applycoupon()
    {
       $user_id = $_SESSION['userdata']['user_id'];
       $session_id = $_SESSION['session_data']['session_id'];
       $coupon_code = $this->input->post('couponcode'); 
       $grand_total = $this->input->post('carttotal'); 
       $total_amount= $this->input->post('total_amount'); 
       $chk = $this->Web_model->applyCoupon($coupon_code,$session_id,$grand_total,$total_amount,$user_id);
    }


    function goaddress_page() 
    {
      $data['coupon_id'] = $this->input->post('coupon_id');
      $data['coupon_code'] = $this->input->post('applied_coupon_code');
      $data['coupon_discount'] = $this->input->post('coupon_discount');
        //print_r($data); die;
       $user_id = $_SESSION['userdata']['user_id'];
       $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $session_id = $_SESSION['session_data']['session_id'];
              $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
              // print_r($qry->result()); die;
                        $del_b = $qry->row();
                    if($qry->num_rows()>0)
                    {
                        $data['addresslist'] = $this->Web_model->getAddress($user_id);
                        $data['states'] = $this->Web_model->getstates();
                        $data['title'] = 'Check Out';
                        $this->load->view('web/checkout.php',$data); 
                    }
                    else
                    {
                        $data['banners'] = $this->Web_model->getBanners($user_id);
                        
                        $data['shops'] = $this->Web_model->getAllshopsWithoutcategory($user_id);
                        $data['topdeals'] = $this->Web_model->getTopDeals($user_id);
                        $data['trending'] = $this->Web_model->getmostViewedProducts($user_id);
                       $data['title'] = 'Dashboard';
                       $this->load->view('web/index.php',$data); 
                    }
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }
    }

    function goaddress_bidpage(){
      $user_id = $_SESSION['userdata']['user_id'];
      $data['categories'] = $this->Web_model->getHomeLimitCategories();
      $chk = $this->Web_model->checkValidLocation($user_id);
      if($chk==true){
        $session_id = $_SESSION['session_data']['session_id'];
        $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
        $del_b = $qry->row();
      }
    }

     function about_us()
    {
      if($_SESSION['userdata']['logged_in']==true)
      {
       $data['categories'] = $this->Web_model->getHomeLimitCategories();
       $data['content'] = $this->Web_model->getSMSContent('4');

        $data['title'] = 'About Us';
        $this->load->view('web/about_us.php',$data); 
      }
    }

    function contact_us()
    {
      if($_SESSION['userdata']['logged_in']==true)
      {
      $data['categories'] = $this->Web_model->getHomeLimitCategories();
      $data['contactinfo'] = $this->Web_model->getContactDetails();

        $data['title'] = 'Contact Us';
        $this->load->view('web/contact_us.php',$data);
      }
    }

    function privacy_policy()
    {
      if($_SESSION['userdata']['logged_in']==true)
      {
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $data['content'] = $this->Web_model->getSMSContent('2');
        $data['title'] = 'Privacy Policy';
        $this->load->view('web/privacy_policy.php',$data);
      }
    }

    function refund_policy()
    {
      if($_SESSION['userdata']['logged_in']==true)
      {
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $data['content'] = $this->Web_model->getSMSContent('8');
        $data['title'] = 'Refund Policy';
        $this->load->view('web/refund_policy.php',$data);
      }
    }

    function terms_and_conditions()
    {
      if($_SESSION['userdata']['logged_in']==true)
      {
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $data['content'] = $this->Web_model->getSMSContent('1');
        $data['title'] = 'Terms and Conditions';
        $this->load->view('web/terms_and_conditions.php',$data);
     }
    }

     function delivery_partner()
    {
      if($_SESSION['userdata']['logged_in']==true)
      {
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $data['content'] = $this->Web_model->getSMSContent('9');
        $data['title'] = 'Delivery Partner';
        $this->load->view('web/terms_and_conditions.php',$data);
     }
    }

     function shipping_policy()
    {
      if($_SESSION['userdata']['logged_in']==true)
      {
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $data['content'] = $this->Web_model->getSMSContent('10');
        $data['title'] = 'Shipping Policy';
        $this->load->view('web/terms_and_conditions.php',$data);
     }
    }

    function userRegister()
    {
              $first_name = $this->input->post('first_name');
              $last_name = $this->input->post('last_name');
              $email = $this->input->post('email');
              $password = md5($this->input->post('password'));
              $phone = $this->input->post('mobile');
              $token = "";

              
              $data = array('first_name' =>$first_name,'last_name' =>$last_name, 'email' =>$email, 'password' =>$password,'phone'=>$phone,'token'=>$token);
              //print_r($data);
               $chk = $this->Web_model->doRegister($data);
        die;
              
    }


    function OTPVerification()
    {
              $user_id = $this->input->post('user_id');
              $otp = $this->input->post('otp');
               $chk = $this->Web_model->verify_OTP($user_id,$otp);
               die;
    }


    function logout()
    {
      
        $this->session->unset_userdata('session_data');

            $this->session->unset_userdata('admin_login');
             $this->session->unset_userdata('userdata');
        $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
    }

    function userLogin()
    {
              $username = $this->input->post('username');
              $password = md5($this->input->post('password'));
              $token = "";
               $chk = $this->Web_model->checkLogin($username,$password,$token);
               die;
    }

    function resendOTP()
    {
               $user_id = $this->input->post('user_id');
               $chk = $this->Web_model->resendOTP($user_id);
               die;
    }
    
    function forgotPassword()
    {
              $phone = $this->input->post('username');
               $chk = $this->Web_model->checkForgot($phone);
               die;
    }

    function getStates()
    {
              $state_id = $this->input->post('state_id');
               $chk = $this->Web_model->fetchCities($state_id);
               die;
    }

    function getpincodes()
    {
              $state_id = $this->input->post('state_id');
              $city_id = $this->input->post('city_id');
              $vendor_id= $this->input->post('vendor_id');
               $chk = $this->Web_model->getPincodes($state_id,$city_id,$vendor_id);
               die;
    }


    function getaddresspincodes()
    {
              $state_id = $this->input->post('state_id');
              $city_id = $this->input->post('city_id');
               $chk = $this->Web_model->getaddresspincodes($state_id,$city_id);
               die;
    }




    function resetPassword()
    {
              $otp = $this->input->post('otp');
              $password = $this->input->post('password');
              $user_id = $this->input->post('user_id');
              $chk = $this->Web_model->resetPassword($user_id,$otp,$password);
               die;
    }

    function myprofile()
    {
      $data['categories'] = $this->Web_model->getHomeLimitCategories();
      if($_SESSION['userdata']['logged_in']==true)
      {
        $user_id= $_SESSION['userdata']['user_id'];
        $data['title'] = 'My Profile';
        $data['profiledata'] = $this->Web_model->profileDetails($user_id);
        $data['page'] = 'myprofile';
        $this->load->view('web/my_profile.php',$data);
      }
      else
      {
         $data['title'] = 'Home';
         $this->load->view('web/index.php',$data);
      }
        
    }

    function updateUserdata()
    {
       $user_id= $_SESSION['userdata']['user_id'];
       $first_name =  $this->input->post('first_name'); 
       $last_name =   $this->input->post('last_name');
       $chk = $this->Web_model->updateProfile($user_id,$first_name,$last_name);
       die;
    }

    function myaccount()
    {
        $data['title'] = 'My Account';
        $data['page'] = 'myaccount';
        $user_id= $_SESSION['userdata']['user_id'];
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $data['data'] = $this->Web_model->myAccount($user_id);
        $this->load->view('web/my_account.php',$data);
    }
    function my_orders()
    {
    	//total_orders
    	if($this->uri->segment(3))
    	{
    		$order_status = $this->uri->segment(3);
    	} 
    	else
    	{
    		$order_status = 'total_orders';
    	}
        $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['title'] = 'My Orders';
              $data['page'] = 'myorders';
              $user_id =  $_SESSION['userdata']['user_id'];
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
              $data['orders'] = $this->Web_model->orderList($user_id,$order_status);
              $this->load->view('web/my_orders.php',$data);
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }
    }

    function orderview($oid)
    {
      $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['title'] = 'View Order';
              $data['data']= $this->Web_model->orderDetails($oid);
              $this->load->view('web/order_view.php',$data);
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }
    }
 
    function my_wishlist()
    {
         $user_id = $_SESSION['userdata']['user_id'];
         $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['title'] = 'My Wishlist';
              $data['page'] = 'mywishlist';
              $user_id =  $_SESSION['userdata']['user_id'];
              $data['data'] = $this->Web_model->whishList($user_id);
              $this->load->view('web/my_wishlist.php',$data);
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }
    }

    function my_addressbook()
    {

         $user_id = $_SESSION['userdata']['user_id'];
         $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['addresslist'] = $this->Web_model->getAddress($user_id);
              $data['states'] = $this->Web_model->getstates();

              $data['title'] = 'My Addressbook';
              $data['page'] = 'myaddressbook';
              $this->load->view('web/my_addressbook.php',$data);
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }
    }

    function become_a_vendor()
    {
         $user_id = $_SESSION['userdata']['user_id'];
         $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['title'] = 'Become A Vendor';
              $data['page'] = 'becomeavendor';
              $this->load->view('web/become_a_vendor.php',$data);
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }
    }

     function googlemap() 
    {
      $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
               $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data);
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }

         
    }

    function getuserLocation()
    {
        $user_id= $_SESSION['userdata']['user_id'];
        $selectedlocation =  $this->input->post('selectedlocation'); 
        if($user_id=='')
        {
            
            $chk = $this->Web_model->getUserLocation('guest',$selectedlocation);
        }
        else
        {
           
           $chk = $this->Web_model->getUserLocation($user_id,$selectedlocation);
        }
        
       die;
    }


    function product_view($seo_url)
    {
      if($this->uri->segment(4)!='')
       {
          $data['search_title'] =$this->uri->segment(4);
       }
       else
       {
          $data['search_title'] = 'nodata';
       }

       $session_id = $_SESSION['session_data']['session_id'];
      $qry = $this->db->query("select * from products where seo_url='".$seo_url."'");
      $product_row = $qry->row();

      $product_id=$product_row->id;
       $data['seo_url'] = $seo_url;
         $user_id = $_SESSION['userdata']['user_id'];
         $data['user_id'] = $user_id;
         $data['product_id'] =$product_id;
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['product_qry'] = $this->Web_model->checkProductQTY($product_id,$session_id,$user_id);


              $data['categories'] = $this->Web_model->getHomeLimitCategories();
              $data['product_details'] = $this->Web_model->getProductDetails($product_id,$user_id);
              
              //echo "<pre>"; print_r($data['product_details']['link_variants']); die;
              $data['linkvarinats']=$data['product_details']['link_variants'][0]['jsondata'];
              $data['title'] = 'Product Details';
              $data['searchbar'] = 'hide';
              $this->load->view('web/product_view.php',$data); 
              
         }
         else
         {
             
          $guest_user_id = $_SESSION['userdata']['guest_user_id']; 

          if($guest_user_id!='')
          {
              $user_id = 'guest'; 
              $data['product_qry'] = $this->Web_model->checkProductQTY($product_id,$session_id,$user_id);
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
              $data['product_details'] = $this->Web_model->getProductDetails($product_id,$user_id);

              $data['title'] = 'Product Details';
              $data['searchbar'] = 'hide';
              $this->load->view('web/product_view.php',$data);
          }
          else
          {
            $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Location';
              $this->load->view('web/selectlocation.php',$data);
          }    
         }
    }

    function remove_whishlist($product_id)
    {
      

       $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['title'] = 'My Wishlist';
              $data['page'] = 'mywishlist';
              $user_id =  $_SESSION['userdata']['user_id'];

              $chk = $this->Web_model->removewhishList($product_id,$user_id);
              if($chk==true)
              {
                
                 $this->session->set_flashdata('success_whishlist_message', 'Removed from the Favourites');
              }

              

              $data['data'] = $this->Web_model->whishList($user_id);
              $this->load->view('web/my_wishlist.php',$data);
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }



      
    }

    function productfilters($product_id)
    {

       $user_id = $_SESSION['userdata']['user_id'];
         $data['user_id'] = $user_id;
         $data['product_id'] =$product_id;

         $seo_url = $this->input->post('seo_url');

         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
                $data['product_qry'] = $this->Web_model->checkProductQTY($product_id,$session_id,$user_id);
                $data['categories'] = $this->Web_model->getHomeLimitCategories();

                $total_count = $this->input->post('total_count');
                $json_data=[];
                for ($i=0; $i<$total_count; $i++) 
                { 
                   $attribute_type = $this->input->post('attribute_type'.$i);
                   $attribute_value = $this->input->post('attribute_value'.$i); 
                   $json_data[] = array('attribute_type'=>$attribute_type,'attribute_value'=>$attribute_value);
                }
                $product_id = $this->input->post('product_id');
                $jsondata = json_encode($json_data);
                //echo "<pre>"; print_r($jsondata); die;
                $dat = $this->Web_model->productDetailsFilter($product_id,$jsondata);
                if($dat=='false')
                {
                    $this->session->set_flashdata('success_message', 'There is no products,Please select another variant');$data['title'] = 'Product Details';
                    redirect('web/product_view/'.$seo_url);
                      
                }
                else
                {
                    $data['product_details'] = $dat;
                    $data['linkvarinats'] = $data['product_details']['link_variants'][0]['jsondata'];
                    //echo "<pre>"; print_r($data['product_details']['link_variants']); die;
                    $data['title'] = 'Product Details';
                    $data['searchbar'] = 'hide';
                    $this->load->view('web/product_view.php',$data); 
                }
                
              
         }
         else
         {
             
          $guest_user_id = $_SESSION['userdata']['guest_user_id']; 

          if($guest_user_id!='')
          {
              $user_id = 'guest'; 
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
               $data['product_qry'] = $this->Web_model->checkProductQTY($product_id,$session_id,$user_id);
               $total_count = $this->input->post('total_count');
                $json_data=[];
                for ($i=0; $i<$total_count; $i++) 
                { 
                   $attribute_type = $this->input->post('attribute_type'.$i);
                   $attribute_value = $this->input->post('attribute_value'.$i); 
                   $json_data[] = array('attribute_type'=>$attribute_type,'attribute_value'=>$attribute_value);
                }
                $product_id = $this->input->post('product_id');
                $jsondata = json_encode($json_data);
                $data['product_details'] = $this->Web_model->productDetailsFilter($product_id,$jsondata);
                $data['linkvarinats'] = $data['product_details']['link_variants'][0]['jsondata'];

              $data['title'] = 'Product Details';
              $data['searchbar'] = 'hide';
              $this->load->view('web/product_view.php',$data);
          }
          else
          {
            $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Location';
              $this->load->view('web/selectlocation.php',$data);
          }    
         }

    }


    function addtocart()
    {
       $user_id= $_SESSION['userdata']['user_id'];
       $variant_id =  $this->input->post('variant_id'); 
       $vendor_id =   $this->input->post('vendor_id');
       $price =   $this->input->post('saleprice');
       $quantity =   $this->input->post('quantity');
       $session_id =   $this->input->post('session_id');
       $chk = $this->Web_model->addToCart($variant_id,$vendor_id,$user_id,$price,$quantity,$session_id);
       die;
    }

    function addtocartWithoutLogin()
    {
       $user_id= $this->input->post('user_id');
       $variant_id =  $this->input->post('variant_id'); 
       $vendor_id =   $this->input->post('vendor_id');
       $price =   $this->input->post('saleprice');
       $quantity =   $this->input->post('quantity');
       $session_id =   $this->input->post('session_id');
       $chk = $this->Web_model->addToCart($variant_id,$vendor_id,$user_id,$price,$quantity,$session_id);
       die;
    }




    function product_categories($catseo_url,$shop_seourl,$category_seo_url)
    {
      $qry = $this->db->query("select * from vendor_shop where seo_url='".$shop_seourl."'");
      $vendor_row = $qry->row();
      $shop_id=$vendor_row->id;

if($category_seo_url=='shop')
{
  $catid = 'shop';
}
else
{
  $catqry = $this->db->query("select * from categories where seo_url='".$category_seo_url."'");
  $cat_row = $catqry->row();
  $catid = $cat_row->id;
}
  


   $subcatqry = $this->db->query("select * from sub_categories where seo_url='".$catseo_url."'");
   $subcatqry_row = $subcatqry->row();
   $subcatid = $subcatqry_row->id;

$data['search'] = 'show';
  $data['subcatseo_url'] = $subcatqry_row->seo_url;

$data['catseo_url'] = $catseo_url;
$data['shop_seourl'] = $shop_seourl;


      $data['cat_url']=$category_seo_url;
      $data['subcat_url']=$catseo_url;


$data['subcategory_title'] = $subcatqry_row->sub_category_name;
$data['shop_name'] = $vendor_row->shop_name;;
         $user_id = $_SESSION['userdata']['user_id']; 
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['attributes'] = $this->Web_model->attributesWithCategory($catid);

             // echo "<pre>"; print_r($data['attributes']); die;
              $data['product_details'] = $this->Web_model->getProducts($catid,$shop_id,$user_id,$subcatid);
              //echo "<pre>"; print_r($data['product_details']); die;
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
               $shop_data = $this->Web_model->getShopdata($shop_id);
               $data['shop_name'] = $shop_data->shop_name;
              $data['shop_id'] = $shop_id;
              $data['catid'] = $catid;
              $data['subcatid'] = $subcatid;
              $data['title'] = 'Products';
              $this->load->view('web/product_categories.php',$data); 
              
         }
         else
         {
              $guest_user_id = $_SESSION['userdata']['guest_user_id']; 
              if($guest_user_id!='')
              {
                  $user_id = 'guest'; 
                  $data['categories'] = $this->Web_model->getHomeLimitCategories();
                  $data['product_details'] = $this->Web_model->getProducts($catid,$shop_id,$user_id,$subcatid);
                  $data['shop_id'] = $shop_id;
                  $data['catid'] = $catid;
                  $data['subcatid'] = $subcatid;
                  $data['title'] = 'Products';
                  $this->load->view('web/product_categories.php',$data);
              }
              else
              {
                $data['location'] = $_SERVER['REQUEST_URI'];
                  $data['title'] = 'Location';
                  $this->load->view('web/selectlocation.php',$data);
              }    
         }
    }



function search_shop_report()
    {
              $shop_id = $this->input->post('shop_id');
              $category_seo_url = $this->input->post('cat_url');
              $catseo_url = $this->input->post('subcat_url');
              $searchdata = $this->input->post('searchdata');
          //$catseo_url,$shop_seourl,$category_seo_url
      $qry = $this->db->query("select * from vendor_shop where id='".$shop_id."'");
      $vendor_row = $qry->row();
      $shop_id=$vendor_row->id;
      $shop_seourl = $vendor_row->seo_url;

if($category_seo_url=='shop')
{
  $catid = 'shop';
}
else
{
  $catqry = $this->db->query("select * from categories where seo_url='".$category_seo_url."'");
  $cat_row = $catqry->row();
  $catid = $cat_row->id;
}
  
    if($catseo_url=='nosubcat')
    {
        $subcatid = 'nosubcat';
    }
    else
    {
       $subcatqry = $this->db->query("select * from sub_categories where seo_url='".$catseo_url."'");
       $subcatqry_row = $subcatqry->row();
       $subcatid = $subcatqry_row->id;
    }

   

$data['search'] = 'show';
  $data['subcatseo_url'] = $subcatqry_row->seo_url;

$data['catseo_url'] = $catseo_url;
$data['shop_seourl'] = $shop_seourl;


      $data['cat_url']=$category_seo_url;
      $data['subcat_url']=$catseo_url;


$data['subcategory_title'] = $subcatqry_row->sub_category_name;
$data['shop_name'] = $vendor_row->shop_name;;
         $user_id = $_SESSION['userdata']['user_id']; 
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['attributes'] = $this->Web_model->attributesWithCategory($catid);

             // echo "<pre>"; print_r($data['attributes']); die;
              $data['product_details'] = $this->Web_model->getshopwiseProducts($catid,$shop_id,$user_id,$subcatid,$searchdata);
              //echo "<pre>"; print_r($data['product_details']); die;
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
               $shop_data = $this->Web_model->getShopdata($shop_id);
               $data['shop_name'] = $shop_data->shop_name;
              $data['shop_id'] = $shop_id;
              $data['catid'] = $catid;
              $data['subcatid'] = $subcatid;
              $data['title'] = 'Products';
              $this->load->view('web/product_categories.php',$data); 
              
         }
         else
         {
              $guest_user_id = $_SESSION['userdata']['guest_user_id']; 
              if($guest_user_id!='')
              {
                  $user_id = 'guest'; 
                  $data['categories'] = $this->Web_model->getHomeLimitCategories();
                  $data['product_details'] = $this->Web_model->getProducts($catid,$shop_id,$user_id,$subcatid);
                  $data['shop_id'] = $shop_id;
                  $data['catid'] = $catid;
                  $data['subcatid'] = $subcatid;
                  $data['title'] = 'Products';
                  $this->load->view('web/product_categories.php',$data);
              }
              else
              {
                $data['location'] = $_SERVER['REQUEST_URI'];
                  $data['title'] = 'Location';
                  $this->load->view('web/selectlocation.php',$data);
              }    
         }
    }

function product_filter()
{
   // echo "<pre>"; print_r($this->input->post()); die;
               $attributes_counts=$this->input->post('attributes_counts');
               $json_data=[];
                for($i=0; $i<$attributes_counts; $i++) 
                { 
                   $attribute_type = $this->input->post('attribute_title_id'.$i);
                   $attribute_values = $this->input->post('atributes_value'.$i); 
                   foreach ($attribute_values as $value) 
                   {
                    if($value!='')
                    {
                      $json_data[] = array('attribute_type'=>$attribute_type,'attribute_value'=>$value);
                    }
                   }
                }

                $jsondata = json_encode($json_data);

                $shop_id = $this->input->post('shop_id'); 
                $catid = $this->input->post('catid');
               $user_id = $_SESSION['userdata']['user_id']; 

               $catseo_url = $this->input->post('catseo_url'); 
               $shop_seourl = $this->input->post('shop_seourl'); 
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['attributes'] = $this->Web_model->attributesWithCategory($catid);
              $data['product_details'] = $this->Web_model->getfilterProducts($jsondata,$shop_id,$catid,$user_id);

              //echo "<pre>"; print_r($data['product_details']); die;
              $data['categories'] = $this->Web_model->getHomeLimitCategories();

              //redirect('web/product_categories/'.$catseo_url."/".$shop_seourl);

               $data['shop_id'] = $shop_id;
               $data['catid'] = $catid;
               $data['title'] = 'Products';
               $this->load->view('web/product_categories.php',$data); 
              
         }
         else
         {
              $guest_user_id = $_SESSION['userdata']['guest_user_id']; 
              if($guest_user_id!='')
              {
                  $user_id = 'guest'; 
                   $data['attributes'] = $this->Web_model->attributesWithCategory($catid);
                  $data['categories'] = $this->Web_model->getHomeLimitCategories();
                   $data['product_details'] = $this->Web_model->getfilterProducts($catid,$shop_id,$user_id,$brands,$start_price,$end_price);
                   //redirect('web/product_categories/'.$catid."/".$shop_id);
                  $data['shop_id'] = $shop_id;
               $data['catid'] = $catid;
               $data['title'] = 'Products';
               $this->load->view('web/product_categories.php',$data); 
              }
              else
              {
                $data['location'] = $_SERVER['REQUEST_URI'];
                  $data['title'] = 'Location';
                  $this->load->view('web/selectlocation.php',$data);
              }    
         }


}


    function sort_products()
    {
         $catid = $this->input->post('catid');
         $shop_id = $this->input->post('shop_id');
         $subcatid = $this->input->post('subcatid');
         $type = $this->input->post('type');
         $user_id = $_SESSION['userdata']['user_id']; 
         $chk = $this->Web_model->checkValidLocation($user_id);
         
              $data= $this->Web_model->getsortProducts($catid,$shop_id,$user_id,$type,$subcatid);

              die;
    

    }


    function addaddress()
    {
       $user_id= $_SESSION['userdata']['user_id'];
       $name =  $this->input->post('name'); 
       $mobile =  $this->input->post('mobile'); 
       $address =  $this->input->post('address'); 
       $state =  $this->input->post('state'); 
       $cities =  $this->input->post('cities'); 
       $pincode =  $this->input->post('pincode'); 
       $landmark =  $this->input->post('landmark'); 
       $address_type =  $this->input->post('address_type'); 
        $chk = $this->Web_model->addAddress($user_id,$name,$mobile,$address,$state,$cities,$pincode,$landmark,$address_type);
        die;
    }

    function addbookaddress()
    {
       $user_id= $_SESSION['userdata']['user_id'];
       $name =  $this->input->post('name'); 
       $mobile =  $this->input->post('mobile'); 
       $address =  $this->input->post('address'); 
       $state =  $this->input->post('state'); 
       $cities =  $this->input->post('cities'); 
       $pincode =  $this->input->post('pincode'); 
       $landmark =  $this->input->post('landmark'); 
       $address_type =  $this->input->post('address_type'); 
        $chk = $this->Web_model->addBookAddress($user_id,$name,$mobile,$address,$state,$cities,$pincode,$landmark,$address_type);
        die;
    }

    function updateaddress()
    {
       $user_id= $_SESSION['userdata']['user_id'];
       $aid =  $this->input->post('aid'); 
       $name =  $this->input->post('name'); 
       $mobile =  $this->input->post('mobile'); 
       $address =  $this->input->post('address'); 
       $state =  $this->input->post('state'); 
       $cities =  $this->input->post('cities'); 
       $pincode =  $this->input->post('pincode'); 
       $landmark =  $this->input->post('landmark'); 
       $address_type =  $this->input->post('address_type'); 
       $chk = $this->Web_model->updateAddress($aid,$user_id,$name,$mobile,$address,$state,$cities,$pincode,$landmark,$address_type);
        die;
    }

    function updatebookaddress()
    {
       $user_id= $_SESSION['userdata']['user_id'];
       $aid =  $this->input->post('aid'); 
       $name =  $this->input->post('name'); 
       $mobile =  $this->input->post('mobile'); 
       $address =  $this->input->post('address'); 
       $state =  $this->input->post('state'); 
       $cities =  $this->input->post('cities'); 
       $pincode =  $this->input->post('pincode'); 
       $landmark =  $this->input->post('landmark'); 
       $address_type =  $this->input->post('address_type'); 
       $chk = $this->Web_model->updateBookAddress($aid,$user_id,$name,$mobile,$address,$state,$cities,$pincode,$landmark,$address_type);
        die;
    }

    function deleteaddress()
    {
      $aid = $this->input->post('address_id');
       $data['coupon_id'] = $this->input->post('coupon_id');
      $data['coupon_code'] = $this->input->post('coupon_code');
      $data['coupon_discount'] = $this->input->post('coupon_discount');
      $user_id= $_SESSION['userdata']['user_id'];
       $chk = $this->Web_model->deleteAddress($user_id,$aid);
              if($chk=='true')
              {
                  $this->session->set_flashdata('success_message', 'Address deleted successfully');
                  $data['addresslist'] = $this->Web_model->getAddress($user_id);
                  $data['states'] = $this->Web_model->getstates();
                  $data['title'] = 'Check Out';
                  $this->load->view('web/checkout.php',$data); 
                  
              }
              else
              {
                $this->session->set_flashdata('error_message', 'Address deleted successfully');
                  $data['addresslist'] = $this->Web_model->getAddress($user_id);
                  $data['states'] = $this->Web_model->getstates();
                  $data['title'] = 'Check Out';
                  $this->load->view('web/checkout.php',$data);
              }
              
    }



     function deletebidaddress()
    {

        $data['bid_id'] = $this->input->post('bid_id');;
        $data['vendor_id'] = $this->input->post('vendor_id');

      
      $bid_id = $this->input->post('bid_id');
      $vendor_id = $this->input->post('vendor_id');

       $aid = $this->input->post('address_id');
       $data['coupon_id'] = $this->input->post('coupon_id');
      $data['coupon_code'] = $this->input->post('coupon_code');
      $data['coupon_discount'] = $this->input->post('coupon_discount');
      $user_id= $_SESSION['userdata']['user_id'];
       $chk = $this->Web_model->deleteAddress($user_id,$aid);
              if($chk=='true')
              {
                  $this->session->set_flashdata('success_message', 'Address deleted successfully');
                  $data['addresslist'] = $this->Web_model->getAddress($user_id);
                  $data['states'] = $this->Web_model->getstates();
                  $data['title'] = 'Check Out';


                  redirect('web/my_bidder_full_details/'.$bid_id."/".$vendor_id);
                  //$this->load->view('web/bidder_checkout.php',$data); 
                  
              }
              else
              {
                $this->session->set_flashdata('error_message', 'Address deleted successfully');
                  $data['addresslist'] = $this->Web_model->getAddress($user_id);
                  $data['states'] = $this->Web_model->getstates();
                  $data['title'] = 'Check Out';
                  redirect('web/my_bidder_full_details/'.$bid_id."/".$vendor_id);
                  //$this->load->view('web/bidder_checkout.php',$data);
              }
              
    }


    function deleteaddressinbook($aid)
    {
      $user_id= $_SESSION['userdata']['user_id'];
       $chk = $this->Web_model->deleteAddress($user_id,$aid);
              if($chk=='true')
              {
                  $this->session->set_flashdata('success_message', 'Address deleted successfully');
                  $data['addresslist'] = $this->Web_model->getAddress($user_id);
                  $data['states'] = $this->Web_model->getstates();

                  $data['title'] = 'My Addressbook';
                  $data['page'] = 'myaddressbook';
                  $this->load->view('web/my_addressbook.php',$data);
              }
              else
              {
                $this->session->set_flashdata('error_message', 'Address deleted successfully');
                  $data['addresslist'] = $this->Web_model->getAddress($user_id);
                  $data['states'] = $this->Web_model->getstates();
                  $data['title'] = 'Check Out';
                  $this->load->view('web/checkout.php',$data); 
              }
              
    }


    function payment()
    {
        
         $user_id = $_SESSION['userdata']['user_id']; 


    $session_id = $_SESSION['session_data']['session_id'];
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

            
                $var1 = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
                $link = $var1->row();

                 $pro1 = $this->db->query("select * from  products where id='".$link->product_id."'");
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

                    

           $unitprice = $value->unit_price+$unitprice;
            $gst = $class_percentage+$gst;
          }

          $grand_t =  $min_order_amount+$unitprice+$gst;
                /*close */


      $data['coupon_id'] = $this->input->post('coupon_id');
      $data['coupon_code'] = $this->input->post('coupon_code');
      $data['coupon_discount'] = $this->input->post('coupon_discount');

 $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $user_address = $this->db->query("select * from users where id='".$user_id."'");
         $user_ddata =  $user_address->row();
         $data['phone'] =$user_ddata->phone;
         $data['email'] =$user_ddata->email;
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $session_id = $_SESSION['session_data']['session_id'];
              $qry = $this->db->query("select * from cart where session_id='".$session_id."' and user_id='".$user_id."'");
                        $del_b = $qry->row();
                    if($qry->num_rows()>0)
                    {
                            $data['aid'] = $this->input->post('aid');
                            $data['total_price'] = $grand_t; //$this->input->post('total_price');

                            $data['title'] = 'Payment';
                            $this->load->view('web/payment.php',$data); 
                    }else{

                $data['banners'] = $this->Web_model->getBanners($user_id);


               

                $data['shops'] = $this->Web_model->getAllshopsWithoutcategory($user_id);
                $data['topdeals'] = $this->Web_model->getTopDeals($user_id);

                $data['trending'] = $this->Web_model->getmostViewedProducts($user_id);
               $data['title'] = 'Dashboard';
               $this->load->view('web/index.php',$data); 
            
                    }
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
             $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data);    
         }
    }


 function razorPaySuccess()
    { 

          $user_id = $_SESSION['userdata']['user_id'];
          $session_id = $_SESSION['session_data']['session_id'];
          $deliveryaddress_id = $this->input->post('address_id');
          $payment_option = "ONLINE";
          $grand_total = $this->input->post('totalAmount');
          $razorpay_payment_id = $this->input->post('razorpay_payment_id');
          $coupon_id= $this->input->post('coupon_id');
          $coupon_code= $this->input->post('coupon_code');
          $coupon_disount= $this->input->post('coupon_discount');
          $created_at = time();
          $order_status = 1;

            $chk = $this->Web_model->doOrder($session_id,$user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$grand_total,$razorpay_payment_id,$coupon_id,$coupon_code,$coupon_disount);

         // $arr = array('msg' => 'Payment successfully credited', 'status' => true);  
    }


    function bidrazorPaySuccess()
    { 

          $user_id = $_SESSION['userdata']['user_id'];
          $session_id = $_SESSION['session_data']['session_id'];
          $deliveryaddress_id = $this->input->post('address_id');
          $payment_option = "ONLINE";
          $grand_total = $this->input->post('totalAmount');
          $razorpay_payment_id = $this->input->post('razorpay_payment_id');
          $coupon_id= $this->input->post('coupon_id');
          $coupon_code= $this->input->post('coupon_code');
          $coupon_disount= $this->input->post('coupon_discount');
          $vendor_id= $this->input->post('vendor_id');
          $bid_id= $this->input->post('bid_id');
          $created_at = time();
          $order_status = 1;

            $chk = $this->Web_model->biddoOrder($user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$grand_total,$razorpay_payment_id,$coupon_id,$coupon_code,$coupon_disount,$vendor_id,$bid_id);

         // $arr = array('msg' => 'Payment successfully credited', 'status' => true);  
    }


    

    function doOrder()
    { 

          $user_id = $_SESSION['userdata']['user_id'];
          $session_id = $_SESSION['session_data']['session_id'];
          $deliveryaddress_id = $this->input->post('address_id');
          $payment_option = "COD";

          $grand_total = $this->input->post('totalAmount');


          $coupon_id= $this->input->post('coupon_id');
          $coupon_code= $this->input->post('coupon_code');
          $coupon_disount= $this->input->post('coupon_discount');
          /*$gst= $this->post('gst');*/
          $created_at = time();
          $order_status = 1;

          $chk = $this->Web_model->doOrderCOD($session_id,$user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$grand_total,$coupon_id,$coupon_code,$coupon_disount);

         // $arr = array('msg' => 'Payment successfully credited', 'status' => true);  
    }

    function doBidOrder()
    { 

          $user_id = $_SESSION['userdata']['user_id'];
          $session_id = $_SESSION['session_data']['session_id'];
          $deliveryaddress_id = $this->input->post('address_id');
          $payment_option = "COD";

          $grand_total = $this->input->post('totalAmount');
          $vendor_id= $this->input->post('totalAmount');

          $coupon_id= $this->input->post('coupon_id');
          $coupon_code= $this->input->post('coupon_code');
          $coupon_disount= $this->input->post('coupon_discount');
          $bid_id = $this->input->post('bid_id');
         $vendor_id = $this->input->post('vendor_id'); 
          /*$gst= $this->post('gst');*/
          $created_at = time();
          $order_status = 1;

          $chk = $this->Web_model->doBidOrderCOD($user_id,$deliveryaddress_id,$payment_option,$created_at,$order_status,$grand_total,$coupon_id,$coupon_code,$coupon_disount,$bid_id,$vendor_id);

         // $arr = array('msg' => 'Payment successfully credited', 'status' => true);  
    }

    function RazorThankYou()
    {
       $data['categories'] = $this->Web_model->getHomeLimitCategories();
       $data['title'] = 'Dashboard';
            $this->load->view('web/order_confirm.php',$data);
    }


    function become_vendor()
    {
              $shopname = $this->input->post('shopname');
              $ownername = $this->input->post('ownername');
              $email = $this->input->post('email');
              $mobile = $this->input->post('mobile');
              $state = $this->input->post('state');
              $city = $this->input->post('city');
              $location = $this->input->post('location');

              $data = array('shopname' =>$shopname, 'ownername' =>$ownername, 'email' =>$email,'mobile'=>$mobile,'state'=>$state,'city'=>$city,'location'=>$location,'created_at'=>time());
              $chk = $this->Web_model->becomeVendor($data);
              die;
    }


    
    function add_remove_topdeal_whishList()
    {
      $product_id = $this->input->post('pid');
      $user_id = $_SESSION['userdata']['user_id'];

      $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         { 
              $user_id = $_SESSION['userdata']['user_id'];
         }
         else
         {

              $user_id = 'guest';
         }
         
      $chk = $this->Web_model->addRemoveTopdealWhishList($product_id,$user_id);
      die;
    }

     function add_most_viewed_removewhishList()
    {
      $product_id = $this->input->post('pid');
      $user_id = $_SESSION['userdata']['user_id'];

      $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         { 
              $user_id = $_SESSION['userdata']['user_id'];
         }
         else
         {

              $user_id = 'guest';
         }
         
      $chk = $this->Web_model->add_most_viewed_removewhishList($product_id,$user_id);
      die;
    }




    

    function searchdata()
    {
      $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         { 
              $user_id = $_SESSION['userdata']['user_id'];
         }
         else
         {

              $user_id = 'guest';
         }
          $keyword = $this->input->post('keyword');
          $chk = $this->Web_model->fetchProducts($keyword,$user_id);
               die;
      
    }

    function storesearchdata()
    {
      $user_id = $_SESSION['userdata']['user_id'];
      $keyword = $this->input->post('keyword');
      $shop_id = $this->input->post('shop_id');
      $chk = $this->Web_model->fetchstorewisesearchProducts($keyword,$user_id,$shop_id);
      die;
    }

    

 function deleteCartItem()
 {
     $user_id = $_SESSION['userdata']['user_id'];
     $cart_id = $this->input->post('cartid');
      $chk = $this->Web_model->deleteCartRow($cart_id,$user_id);
      die;
 }

 function cancelOrder()
 {
    $user_id = $_SESSION['userdata']['user_id'];
    $orderid = $this->input->post('order_id');
    $chk = $this->Web_model->docancelOrder($user_id,$orderid);
    die;
 }

 function order_refund()
 {
      $session_id =  $this->input->post('order_id');
      $product_id =  $this->input->post('product_id');
      $user_id   =  $_SESSION['userdata']['user_id'];
      $vendor_id  =  $this->input->post('vendor_id');
      $cartid  =  $this->input->post('cart_id');
      $delivery_type  =  1;
      $reson = $this->input->post('message');
      $chk = $this->Web_model->exchangeRefund($session_id,$product_id,$user_id,$vendor_id,$cartid,$delivery_type,$reson);
    die;
 }

function store_categories($seo_url)
{
  $qry = $this->db->query("select * from categories where seo_url='".$seo_url."'");
  $cat_row =$qry->row();
  $cat_id = $cat_row->id;
  $data['seo_url'] = $seo_url;
        $det = $this->Web_model->getHomeCategories($cat_id);
        
        $data['sub_category_list'] = $det['sub_category_list'];
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $subcat = $det['sub_category_list'][0]['id']; 
         $chk = $this->Web_model->checkValidLocation($_SESSION['userdata']['user_id']);
         if($chk==true)
         { 
              $user_id = $_SESSION['userdata']['user_id'];
         }
         else
         {

              $user_id = 'guest';
         }

            $data['category_name'] = $det['category_name'];
        $data['subcatid'] = $subcat;
        $data['catid'] = $cat_id;


         $config = array();
        $config["base_url"] = base_url()."web/store_categories/".$seo_url;
        $config["total_rows"] = $this->Web_model->getshopsWithcategoryID_count($cat_id,$subcat,$user_id);
        $config["per_page"] = 8;
        $config["uri_segment"] = 4;
          //print_r($config); die;
        
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;  

        $data["links"] = $this->pagination->create_links();

        $data['shops'] = $this->Web_model->getshopsWithcategoryID_storecat($config["per_page"], $page,$cat_id,$subcat,$user_id);



        //echo "<pre>"; print_r($data['shops']); die;
        $data['title'] = 'Stores';
        $this->load->view('web/store_categories.php',$data);
}


function store_wise_categories($cat_seo,$subcat_seo)
{
  $qry = $this->db->query("select * from categories where seo_url='".$cat_seo."'");
  $cat_row =$qry->row();


  $subqry = $this->db->query("select * from sub_categories where seo_url='".$subcat_seo."'");
  $subqry_row =$subqry->row();

  $cat_id = $cat_row->id;
  $subcat = $subqry_row->id;

        $det = $this->Web_model->getHomeCategories($cat_id);
        //print_r($det); 
        $data['sub_category_list'] = $det['sub_category_list'];
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
  
        $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($_SESSION['userdata']['user_id']);
        if($chk==true)
         { 
              $user_id = $_SESSION['userdata']['user_id'];
         }
         else
         {

              $user_id = 'guest';
         }

        //$chk = $this->Web_model->getshopsWithcategoryID($cat_id,$subcat,$user_id);
        $data['category_name'] = $det['category_name'];
        $data['subcatid'] = $subcat;
        $data['catid'] = $cat_id;

        $data['seo_url'] = $cat_seo;





        $config = array();
        $config["base_url"] = base_url()."web/store_wise_categories/".$cat_seo."/".$subcat_seo;
        $config["total_rows"] = $this->Web_model->getshopsWithcategoryID_count($cat_id,$subcat,$user_id);
        $config["per_page"] = 8;
        $config["uri_segment"] = 5;
          //print_r($config); die;
        
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;  

        $data["links"] = $this->pagination->create_links();

        $data['shops'] = $this->Web_model->getshopsWithcategoryID_storecat($config["per_page"], $page,$cat_id,$subcat,$user_id);


        //$data['shops'] = $chk['shop_list'];

        //echo "<pre>"; print_r($data['shops']); die;
        $data['title'] = 'Stores';
        $this->load->view('web/store_categories.php',$data);
}

function savecontactus()
{
  $name =  $this->input->post('name');
  $mobile =  $this->input->post('mobile');
  $email =  $this->input->post('email');
  $message =  $this->input->post('message');
  $ar=array('name'=>$name,'mobile'=>$mobile,'email'=>$email,'message'=>$message);
  $ins = $this->Web_model->saveContact($ar);
  die;
            
}



function viewAllCategories()
{
            $data['categories'] = $this->Web_model->getHomeAllCategories();
            $data['title'] = 'View All Categories';
            $this->load->view('web/allcategories.php',$data);
}

function viewallProducts($url)
{
  $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $user_id = $_SESSION['userdata']['user_id'];
         }
         else
         {
            $user_id = 'guest'; 
         }

  if($url=='topdeals')
  {
        $data['title'] = 'TOP DEALS';
        $config = array();
        $config["base_url"] = base_url()."web/viewallProducts/topdeals/";
        $config["total_rows"] = $this->Web_model->getTopDealscount($user_id);
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
          //print_r($config); die;
        
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;  

        $data["links"] = $this->pagination->create_links();

        $data['products'] = $this->Web_model->getAllTopDeals($config["per_page"], $page,$user_id);

         $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $this->load->view('web/viewallproducts.php',$data);
        

  }
  else if($url=='trending')
  {
          $data['title'] = 'TRENDING OFFERS';
          $config = array();
          $config["base_url"] = base_url()."web/viewallProducts/trending/";
          $config["total_rows"] = $this->Web_model->getmostViewedProducts_count($user_id);
          $config["per_page"] = 10;
          $config["uri_segment"] = 4;
          //print_r($config); die;
        
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;  

        $data["links"] = $this->pagination->create_links();

        $data['products'] = $this->Web_model->getmostViewedProductsAll($config["per_page"], $page,$user_id);



         $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $this->load->view('web/viewallproducts.php',$data);
  }
            
            
}
function viewallshops()
{
  
  $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $user_id = $_SESSION['userdata']['user_id'];
         }
         else
         {
            $user_id = 'guest'; 
         }



        $config = array();
        $config["base_url"] = base_url()."web/viewallshops/";
        $config["total_rows"] = $this->Web_model->get_count($user_id);
        $config["per_page"] = 8;
        $config["uri_segment"] = 3;
          //print_r($config); die;
        
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;  

        $data["links"] = $this->pagination->create_links();

        $data['shops'] = $this->Web_model->getAllshopsWithoutcategoriesList($config["per_page"], $page,$user_id);

         $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $data['title'] = 'View All Shops';
        $this->load->view('web/viewallshops.php',$data);
          
}


function add_remove_favourite()
    {
      $product_id = $this->input->post('pid');
      $user_id = $_SESSION['userdata']['user_id'];
      $chk = $this->Web_model->add_removewhishList($product_id,$user_id);
      die;
    }



function search_report()
{
      
  $searchdata = $this->input->post('searchdata'); 

  $user_id = $_SESSION['userdata']['user_id'];
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         { 
              $user_id = $_SESSION['userdata']['user_id'];
         }
         else
         {

              $user_id = 'guest';
         }
  $keyword = $this->input->post('keyword');
  $data['categories'] = $this->Web_model->getHomeLimitCategories();
  $chk = $this->Web_model->searchAllProductsandShops($searchdata,$user_id);
  $data['products']=$chk['products'];
  $data['shops']=$chk['shops'];
  $data['search_count']=$chk['search_count'];
  
//echo "<pre>"; print_r($chk); die;
  $data['title'] = $searchdata;
  $this->load->view('web/search_result.php',$data);
          
}


function createUserBid()
{
  $user_id  =  $_SESSION['userdata']['user_id'];


/*Start */
    $session_id = $_SESSION['session_data']['session_id'];
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

            
                $var1 = $this->db->query("select * from link_variant where id='".$value->variant_id."'");
                $link = $var1->row();

                 $pro1 = $this->db->query("select * from  products where id='".$link->product_id."'");
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
            $unitprice = $value->unit_price+$unitprice;
            $gst = $class_percentage+$gst;
          }

          $grand_t =  $min_order_amount+$unitprice+$gst;
                /*close */

  $ar = array(
                  'user_id'=>$user_id,
                  'session_id'=>$_SESSION['session_data']['session_id'],
                  'vendor_id'=>$this->input->post('vendor_id'),
                  'sub_total'=>$unitprice,
                  'delivery_amount'=>$min_order_amount,
                  'grand_total'=>$grand_t,
                  'gst'=>$gst,
                  'created_at'=>time()
                );
  $ins = $this->Web_model->createUserBid($ar);
die;
}



 function my_bids()
    {
         $user_id = $_SESSION['userdata']['user_id'];
         $order_status  =  'ongoing';  //$this->input->post('order_status')
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $data['title'] = 'My Orders';
              $data['page'] = 'my_bids';
              $user_id =  $_SESSION['userdata']['user_id'];
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
              $data['bids'] = $this->Web_model->mybids($user_id);
              $this->load->view('web/my_bids.php',$data);
         }
         else
         {
              $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }
    }


    function my_bids_details($bid)
    {
        $user_id = $_SESSION['userdata']['user_id'];
         $order_status  =  'ongoing';  //$this->input->post('order_status')
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              
              $data['title'] = 'My Orders';
              $data['page'] = 'my_bids';
              $user_id =  $_SESSION['userdata']['user_id'];
              $data['categories'] = $this->Web_model->getHomeLimitCategories();
              $data['bids'] = $this->Web_model->mybidDetails($user_id,$bid);

              $check_stat = $this->db->where(array("id"=>$bid,"user_id"=>$user_id))->get("user_bids")->result()[0];
              $data['bid_da'] = $check_stat->id;
              $data['is_delivered'] = $check_stat->bid_status;
              

              // print_r($data); die;
              $this->load->view('web/my_bids_details.php',$data);
         }
         else
         {
              $data['location'] = $_SERVER['REQUEST_URI'];
              $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data); 
         }
    }


    //my_bidder_full_details

    function my_bidder_full_details($bid,$vendor_id){
        $user_id = $_SESSION['userdata']['user_id'];
        $chk = $this->Web_model->checkValidLocation($user_id);
        $data['title'] = 'View Orders';
        $data['page'] = 'view_selected_bid';
        $data['categories'] = $this->Web_model->getHomeLimitCategories();
        $current_bid = $this->db->where(array("id"=>$bid,"user_id"=>$user_id))->get("user_bids");
        $data['bid_data'] = $current_bid;
        $data['bid_id'] = $bid;
        $data['vendor_id'] = $vendor_id;

        $bid_ven = $this->db->query("select * from vendor_bids where bid_id='".$bid."' and vendor_id='".$vendor_id."'");
        $bid_ven_row = $bid_ven->row();
        $data['total_price'] = $bid_ven_row->total_price;

        $data['bid_products'] = $this->Web_model->mybidDetails($user_id,$bid)['products'];
        

        // print_r($data['bid_products']);
                
        $data['addresslist'] = $this->Web_model->getAddress($user_id);
        $data['states'] = $this->Web_model->getstates();
        $data['title'] = 'Check Out';
        // $this->load->view('web/checkout.php',$data); 

        $this->load->view('web/bidder_checkout.php',$data);
    }


    function bid_payment($bid){
      $vendor_id = $this->input->post('vendor_id'); 
      $data['bid_id'] = $bid;
      $user_id = $_SESSION['userdata']['user_id'];
       
        $session_id = $_SESSION['session_data']['session_id'];
    $qry = $this->Web_model->mybidDetails($user_id,$bid)['products'];
    $del_b = $qry[0];


    
    $shop = $this->db->query("select * from vendor_shop where id='".$del_b['vendor_id']."'");
    $shopdat = $shop->row();
    $min_order_amount = $shopdat->min_order_amount;

    $result = $qry;
          $bid_ven = $this->db->query("select * from vendor_bids where bid_id='".$bid."' and vendor_id='".$vendor_id."'");
          $bid_ven_row = $bid_ven->row();



      $data['coupon_id'] = $this->input->post('coupon_id');
      $data['coupon_code'] = $this->input->post('coupon_code');
      $data['coupon_discount'] = $this->input->post('coupon_discount');
      $data['vendor_id'] = $vendor_id;
 $data['categories'] = $this->Web_model->getHomeLimitCategories();
         $user_address = $this->db->query("select * from users where id='".$user_id."'");
         $user_ddata =  $user_address->row();
         $data['phone'] =$user_ddata->phone;
         $data['email'] =$user_ddata->email;
         $chk = $this->Web_model->checkValidLocation($user_id);
         if($chk==true)
         {
              $session_id = $_SESSION['session_data']['session_id'];
             $qry = $this->Web_model->mybidDetails($user_id,$bid)['products'];
                    /*if(sizeof($qry)>0)
                    {*/
                            $data['aid'] = $this->input->post('aid');
                            $data['total_price'] = $bid_ven_row->total_price; //$this->input->post('total_price');
                            $data['title'] = 'Payment';
                            $this->load->view('web/bid_payment.php',$data); 
                    /*}else{*/

                /*$data['banners'] = $this->Web_model->getBanners($user_id);


               

                $data['shops'] = $this->Web_model->getAllshopsWithoutcategory($user_id);
                $data['topdeals'] = $this->Web_model->getTopDeals($user_id);

                $data['trending'] = $this->Web_model->getmostViewedProducts($user_id);
               $data['title'] = 'Dashboard';
               $this->load->view('web/index.php',$data); */
            
                    //}
         }
         else
         {
          $data['location'] = $_SERVER['REQUEST_URI'];
             $data['title'] = 'Dashboard';
              $this->load->view('web/selectlocation.php',$data);    
         }
    }


    function createBid()
    {
    $bidid = $this->input->post('bidid');
    $chk = $this->Web_model->createBid($bidid);
    die;
    }

}
