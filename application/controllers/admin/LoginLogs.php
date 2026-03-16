<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class LoginLogs extends MY_Controller {
    public $data;
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
        $this->load->model("admin_model");
    }

    function index() {

        $this->data['page_name'] = 'login_logs';
        $this->data['title'] = 'Login Logs';
        $qry = $this->db->query("SELECT * FROM `admin_login_logs` ORDER BY id DESC");
        $this->data['logs'] = $qry->result();

       // print_r($this->data['logs']); die;
        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/login_logs', $this->data);

        $this->load->view('admin/includes/footer');
    }
}