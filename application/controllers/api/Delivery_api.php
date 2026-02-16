<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Delivery_api extends REST_Controller {

    public function __construct() { 
      header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        
        //load user model
        $this->load->model('delivery');
        //$this->load->library('email'); 
    }

    public function user_post() {
       $userData = array();

       if($this->post('action')=='login')
       {
              $mobile = $this->post('mobile');
              $password = md5($this->post('password'));
              $token = $this->post('token');
               $chk = $this->delivery->checkLogin($mobile,$password,$token);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='myorders')
       {
            $db_id =  $this->post('db_id');
              $chk = $this->delivery->orderList($db_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='completed_orders')
       {
            $db_id =  $this->post('db_id');
              $chk = $this->delivery->completedOrders($db_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='orderDetails')
       {
              $order_id =  $this->post('order_id');
              $chk = $this->delivery->orderDetails($order_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='confirm_pickup')
       {
              $order_id =  $this->post('order_id');
              $db_id =  $this->post('db_id');
              $chk = $this->delivery->confirmPickup($order_id,$db_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='pickup_orders')
       {
              $db_id =  $this->post('db_id');
              $chk = $this->delivery->pickupOrders($db_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='confirm_delivery')
       {
              $db_id =  $this->post('db_id');
              $order_id =  $this->post('order_id');
              $chk = $this->delivery->confirmDelivery($order_id,$db_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='orders_count')
       {
              $db_id =  $this->post('db_id');
              $chk = $this->delivery->ordersCount($db_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
        else if($this->post('action')=='forgotpassword')
       {
              $phone = $this->post('phone');
               $chk = $this->delivery->checkForgot($phone);
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
               $chk = $this->delivery->resetPassword($phone,$otp,$password);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='changePassword')
       {
           $user_id =  $this->post('db_id');
           $current_password =  $this->post('current_password');
           $new_password =  $this->post('new_password');

           $chk = $this->delivery->updatePassword($user_id,$current_password,$new_password);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='profile_details')
       {
              $user_id =  $this->post('db_id');
              $chk = $this->delivery->profileDetails($user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='upload_file')
       {
            $chk = $this->delivery->browse_file();
            if($chk=='false')
            {
               $ss = array('status'=>false,'message'=>"please upload below 5mb");
            }
            else
            {
              $img = base_url()."uploads/delivery_boy/".$chk;
              $ss = array('status'=>true,'file'=>$chk,'fullpath'=>$img);
            }
            $this->response($ss);
       }
       else if($this->post('action')=='earnings')
       {
              $user_id =  $this->post('db_id');
              $chk = $this->delivery->getearnings($user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if ($this->post('action') == 'version_control') {
            $chk = $this->delivery->versionControl();
            $this->response($chk, REST_Controller::HTTP_OK);  
        }


       

       
       

    }
}

?>