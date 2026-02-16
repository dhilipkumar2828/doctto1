<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Doctor_sub_categories extends MY_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }
        $this->data['page_name'] = 'doctor_sub_categories';

    }



    function index() {
        $this->data['page_name'] = 'doctor_sub_categories';
        $this->data['title'] = 'Sub categories ( Symptoms )';
        $this->data['title1'] = 'Sub category ( Symptom )';
       // $this->db->order_by('id', 'desc');

       $this->data['categories'] = $this->db->get('doctor_sub_categories')->result();

        // $this->data['categories'] = $this->db->query('SELECT subcat.*, cat.category_name FROM sub_categories subcat INNER JOIN categories cat ON cat.id = subcat.cat_id order by subcat.id desc')->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/doctor_sub_categories', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function add() {

        $this->data['title'] = 'Add Doctor Sub Category';

        $this->data['categories'] = $this->db->get('symptom')->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/add_doctor_sub_category', $this->data);

        $this->load->view('admin/includes/footer');

    }

function make_seo_name($title) {
        return preg_replace('/[^a-z0-9_-]/i', '', strtolower(str_replace(' ', '-', trim($title))));
    }

    function insert() {

        $sub_category_name = $this->input->get_post('sub_category_name');


        $status = $this->input->get_post('status');

        $cat_id = $this->input->get_post('cat_id');

        $status = $this->input->get_post('status');
        $seo_url = $this->make_seo_name($sub_category_name);

        $data = array(
            'sub_category_name'=>$sub_category_name,
            'cat_id'=>$cat_id,
            'status'=>$status,
            'created_at'=>time()
        );
        $insert_query = $this->db->insert('doctor_sub_categories', $data);
        if ($insert_query) {
            redirect('admin/doctor_sub_categories');
            die();
        } else {
            redirect('admin/doctor_sub_categories/add');
            die();
        }

    }



    function edit_subcategory($sub_catid) {

        $this->data['title'] = 'Edit Sub Category';

        $this->data['categories'] = $this->db->get('symptom')->result();

        $this->data['sub_category'] = $this->db->get_where('doctor_sub_categories', ['id' => $sub_catid])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/edit_doctor_sub_category', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function update() {

        $cat_id = $this->input->get_post('cat_id');

        $sub_cat_id = $this->input->get_post('sub_cat_id');

        $sub_category_name = $this->input->get_post('sub_category_name');

       

        $status = $this->input->get_post('status');


        // $seo_url = $this->make_seo_name($sub_category_name);
        $data = array(
            'sub_category_name' => $sub_category_name,
            'cat_id' => $cat_id,
            'status' => $status,
            'updated_at' => time(),
        );


        $this->db->where('id', $sub_cat_id);
        $update_query = $this->db->update('doctor_sub_categories', $data);
        if ($update_query) {

            redirect('admin/doctor_sub_categories');

            die();

        } else {

            redirect('admin/doctor_sub_categories/edit_subcategory/' . $sub_cat_id);

            die();

        }

    }



    function delete($subcat_id) {
        // $check = $this->db->query("select * from products where sub_cat_id='".$subcat_id."'");

        // if($check->num_rows()>0)
        // {
        //          $this->session->set_flashdata('error_message', "Some products are assigned, Unable to delete");
        //         redirect('admin/sub_categories');
        // }
        
                $this->db->where('id', $subcat_id);
                if ($this->db->delete('doctor_sub_categories')) {
                    $this->session->set_flashdata('success_message', 'Sub Category Deleted Successfully');
                    redirect('admin/doctor_sub_categories');
                } else {
                    $this->session->set_flashdata('error_message', 'Unable to delete');
                    redirect('admin/doctor_sub_categories');
                
        }
        

    }



}

