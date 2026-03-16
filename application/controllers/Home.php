<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    function __construct() {
        parent::__construct();
        // $this->load->model('web_model');
        
    }

    function index(){
      $this->data['page_title']='home';
      $this->load->view('index',$this->data);
    }

    function invoice(){
      $this->data['page_title'] = 'invoice';
      $this->load->view('invoice',$this->data);
    }

    function labtest_invoice(){
      $this->data['page_title'] = 'labtest_invoice';
      $this->load->view('labtest_invoice',$this->data);
    }

    function labtest_pdf(){
      $this->data['page_title'] = 'labtest_pdf';
      $this->load->view('labtest_pdf',$this->data);
    }

    function mypdf(){
      $this->data['page_title'] = 'mypdf';
      $this->load->view('mypdf',$this->data);
    }

    function payment_invoice(){
      $this->data['page_title'] = 'payment_invoice';
      $this->load->view('payment_invoice',$this->data);
    }

    function privacy(){
      $this->data['page_title'] = 'privacy';
      $this->load->view('privacy',$this->data);
    }

    function privacypolicy(){
      $this->data['page_title'] = 'privacypolicy';
      $this->load->view('privacypolicy',$this->data);
    }

    function terms(){
      $this->data['page_title'] = 'terms';
      $this->load->view('terms',$this->data);
    }

    function submit_fom(){
      $name=$this->input->post('name');
      $phone=$this->input->post('phone');
      $email=$this->input->post('email');
      $subject=$this->input->post('subject');
      $message=$this->input->post('message');

      $data = array(
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
            );
      // print_r($data);die;

      if($data){

        $to = 'doctto108@gmail.com';
        $subject="Doctto Contact Us";
        $message = "Name :".$name."\n Phone:".$phone." \n Email:".$email."\n Subject: ".$subject."\n Message: ".$message." ";
        $headers = "From: ".$email." " . "\r\n" .
                    "CC: ".$to." ";

            if (mail($to,$subject,$message,$headers)) {
                 $this->session->set_flashdata("success_message", 'Your Response submitted successfully');
                redirect('https://www.doctto.com/');
            } else {
                $this->session->set_flashdata("error_message", 'something went wrong');
                redirect('https://www.doctto.com/');
            }     
               
        }
    }

}


