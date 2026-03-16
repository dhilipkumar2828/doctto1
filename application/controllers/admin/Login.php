<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    private $data;
    function __construct() {
        parent::__construct();        
        $this->load->model("admin_model");
//        if ($this->session->userdata('logged_in') == true) {
//            redirect('admin/dashboard');
//        }
//        $this->data['site_details'] = $this->admin_model->get_row_by_id('1', 'profile');
    }

    public function index() {   
//        $this->data['username'] = 'admin';
//        $this->data['password'] = 'Wido@5454';
        $this->load->view('admin/login', $this->data);
    }

    public function admin_login() {
           
         $ip = $_SERVER['REMOTE_ADDR'];

//        $ipcheck = $this->admin_model->ip_checking($ip);
        //log_message('error', json_encode($ipcheck));

//        if ($ipcheck != '') {
//         $ip_add = $_SERVER['REMOTE_ADDR'];
//         $details = json_decode(@file_get_contents("http://ipinfo.io/{$ip_add}/json"));
//         $city = isset($details->city) ? $details->city : '';
//         if($city=='Hyder?b?d')
//         {
//             $city1="Hyderabad";
//         }
//         else
//         {
//             $city1=$city;
//         }
        $log = array(
            "ip_address"=> $_SERVER['REMOTE_ADDR'],
            "city" => "Localhost",
            "login_time" => time()
        );



         $login_type = $this->input->get_post('login_type');
         if($login_type=='admin')
         {   


            $log['user_type'] = 'admin';


            $username = $this->input->get_post('email', TRUE);
            $password = $this->input->get_post('password', TRUE);
            $password = md5($password);
            $row = $this->admin_model->admin_login($username, $password); 
            log_message('error', "Login attempt for $username: " . ($row == "false" ? 'Failed' : 'Success'));
            if ($row=="false")
            {
                 $this->session->set_flashdata('login_error_message', 'Invalid Email Or Password');
                 redirect('admin/login');
            } else {

                $sess_arr = array(
                    'user_type'=>'admin',
                    'id' => $row->id,
                    'username' => $row->username,
                    'logged_in' => true
                ); 

                $logger = $this->db->insert("admin_login_logs",$log);

                $this->session->set_flashdata('msg', 'Welcome');
                $this->session->set_userdata('admin_login', $sess_arr);

                redirect('admin/dashboard');
            }
        }
        else if($login_type=='subadmin')
        {


            $log['user_type'] = 'sub admin';


            $username = $this->input->get_post('email', TRUE);
            $password = $this->input->get_post('password', TRUE);
            $password = md5($password);
            $chk_login = $this->db->query("select * from sub_admin where username='".$username."' and password='".$password."'");
            if($chk_login->num_rows()>0)
            {
                $subadmin_row = $chk_login->row();

                $sess_arr = array(
                    'user_type'=>'subadmin',
                    'id' => $subadmin_row->id,
                    'username' => $subadmin_row->username,
                    'logged_in' => true
                ); 

                $logger = $this->db->insert("admin_login_logs",$log);

                $this->session->set_flashdata('msg', 'Welcome');
                $this->session->set_userdata('admin_login', $sess_arr);
                redirect('admin/dashboard');
            }
            else{
                 $this->session->set_flashdata('login_error_message', 'Invalid Email Or Password');
                 redirect('admin/login');
            }
        }
    }

}
