<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
    }
 function push_notification_android($device_id,$message,$title){

    //API URL of FCM
    $url = 'https://fcm.googleapis.com/fcm/send';

    /*api_key available in:
    Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/    
    $api_key = 'AAAAkpaJlU0:APA91bFtR5W87oDNlnaW4dgXbTXZAENAiPOx7D9to-h-GujBPC0Eo5bKsvVz2RkdNA5pTs8ffAZXgyOESL59o6IDwdci5dvmHJCd6B4ppbg5vPHxxw6tr3wnaEB9sbtsOcPIk9E8J9mf';
                
    $fields = array (
        'registration_ids' => array (
                $device_id
        ),
        'data' => array (
                "title" => $title,
                "body" => $message,
				 'sound'=>'vendor_delivery_notification'
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
    print_r($result); die;
    return $result;
} 
    function index() {
		
		$device_id="ef02nnHmRoi11vgMmhLK7U:APA91bGdKB1hhTsjrl4xeTmKxSYu5TCL-iwv2os8RQNlQSDBmBr63fwmWR-BvIhcEz2QevQEQgSpbUSOQRH19qgjUYvp_v6fHfUfSdHcrgwXA1oSJgWZ_lUbdFeARBjUvqH8yhZfCTmh";
		$message="Testing from Shaik ok";
		$title="Test notify";
		//$this->push_notification_android($device_id,$message,$title);
        $data['page_name'] = 'notifications';
        $qry=$this->db->query("SELECT orders.*,admin_notifications.* FROM orders LEFT JOIN admin_notifications ON orders.id=admin_notifications.order_id WHERE admin_notifications.status=0 and orders.order_status=2");
        $data['transactions']=$qry->result();

        $this->load->view('admin/includes/header', $data);

        $this->load->view('admin/notifications', $data);
        $this->load->view('admin/includes/footer');

    }

     function delete($user_id) {
        $this->db->where('id', $user_id);
       $del = $this->db->delete('users');

        //echo$del = $this->db->last_query(); die;
        if($del)
        {
            $this->session->set_flashdata('success_message', 'User Deleted Successfully');
            redirect('admin/users');
        }
        else
        {
            $this->session->set_flashdata('error_message', 'Something went wrong, Unable to delete');
            redirect('admin/users');
        }
    }



}

