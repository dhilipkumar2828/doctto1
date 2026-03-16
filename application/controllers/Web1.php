<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Web1 extends MY_Controller {

    public $data;

    function __construct() {

         header('Access-Control-Allow-Origin: *');
      header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        $this->load->model('Web_model');
        
      if($_SESSION['userdata']['user_id']!=true)
      {
        $data['title'] = 'Dashboard';
        $this->load->view('web/index.php',$data); 

      }
    }

    function index() 
    {
        $data['title'] = 'Dashboard';
        $this->load->view('web/index.php',$data); 
        $this->load->view('web/welcome_message.php',$data); 
    }

    function checkout() 
    {
        $data['title'] = 'Check Out';
        $this->load->view('web/checkout.php',$data); 
    }

    function about_us()
    {
        $data['title'] = 'About Us';
        $this->load->view('web/about_us.php',$data); 
    }

    function contact_us()
    {
        $data['title'] = 'Contact Us';
        $this->load->view('web/contact_us.php',$data);
    }

    function privacy_policy()
    {
        $data['title'] = 'Privacy Policy';
        $this->load->view('web/privacy_policy.php',$data);
    }

    function refund_policy()
    {
        $data['title'] = 'Refund Policy';
        $this->load->view('web/refund_policy.php',$data);
    }

    function terms_and_conditions()
    {
        $data['title'] = 'Terms and Conditions';
        $this->load->view('web/terms_and_conditions.php',$data);
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
            $this->session->unset_userdata('admin_login');
             $this->session->unset_userdata('userdata');
        redirect('web');
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
      if($_SESSION['userdata']['user_id']==true)
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
        $data['data'] = $this->Web_model->myAccount($user_id);
        $this->load->view('web/my_account.php',$data);
    }
    function my_orders()
    {
        $data['title'] = 'My Orders';
        $data['page'] = 'myorders';
         $user_id =  $_SESSION['userdata']['user_id'];
         $data['orders'] = $this->Web_model->orderList($user_id);

        $this->load->view('web/my_orders.php',$data);
    }

    function orderview($oid)
    {
      $data['title'] = 'View Order';
      $data['data']= $this->Web_model->orderDetails($oid);
      $this->load->view('web/order_view.php',$data);
    }
 
    function my_wishlist()
    {
        $data['title'] = 'My Wishlist';
        $data['page'] = 'mywishlist';

         $user_id =  $_SESSION['userdata']['user_id'];
         $data['data'] = $this->Web_model->whishList($user_id);

        $this->load->view('web/my_wishlist.php',$data);
    }

    function my_addressbook()
    {
        $data['title'] = 'My Addressbook';
        $data['page'] = 'myaddressbook';
        $this->load->view('web/my_addressbook.php',$data);
    }

    function become_a_vendor()
    {
        $data['title'] = 'Become A Vendor';
        $data['page'] = 'becomeavendor';
        $this->load->view('web/become_a_vendor.php',$data);
    }
    

}
