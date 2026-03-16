<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Sub_categories extends MY_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }

    }



    function index() {
        $this->data['page_name'] = 'sub_categories';
        $this->data['title'] = 'Sub Categories';

//        $this->db->order_by('id', 'desc');

//        $this->data['categories'] = $this->db->get('categories')->result();

        $this->data['sub_categories'] = $this->db->query('SELECT subcat.*, cat.category_name FROM sub_categories subcat INNER JOIN categories cat ON cat.id = subcat.cat_id order by subcat.id desc')->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/sub_categories', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function add() {

        $this->data['title'] = 'Add Sub Category';

        $this->data['categories'] = $this->db->get('categories')->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/add_sub_category', $this->data);

        $this->load->view('admin/includes/footer');

    }

function make_seo_name($title) {
        return preg_replace('/[^a-z0-9_-]/i', '', strtolower(str_replace(' ', '-', trim($title))));
    }

    function insert() {

        $sub_category_name = $this->input->get_post('sub_category_name');

        $description = $this->input->get_post('description');

        $status = $this->input->get_post('status');

        $cat_id = $this->input->get_post('cat_id');

        $brand = $this->input->get_post('brand');


        $config['upload_path'] = './uploads/sub_categories/';

        $config['allowed_types'] = 'gif|jpg|png';

        $config['max_size'] = 5000;

        $config['max_width'] = 5000;

        $config['max_height'] = 5000;



        $this->load->library('upload', $config);

//        if ($this->upload->do_upload('image')) {

//            $imageDetailArray = $this->upload->data();

//            $image_name = $imageDetailArray['file_name'];

//        } else {

//            $image_name = '';

//        }

        /*if ($this->upload->do_upload('app_image')) {

            $imageDetailArray2 = $this->upload->data();

            $app_image_name = $imageDetailArray2['file_name'];

        } else {

            $app_image_name = 'no';

        }*/

        $seo_url = $this->make_seo_name($sub_category_name);

        $data = array(

            'sub_category_name' => $sub_category_name,

            //'description' => $description,

            'cat_id' => $cat_id,
            //'brand' => $brand,
            'seo_url'=>$seo_url,
            'status' => $status,

            'created_at' => time()

            //'app_image' => $app_image_name

        );

        $insert_query = $this->db->insert('sub_categories', $data);

        if ($insert_query) {

            redirect('admin/sub_categories');

            die();

        } else {

            redirect('admin/sub_categories/add');

            die();

        }

    }



    function edit_subcategory($sub_catid) {

        $this->data['title'] = 'Edit Sub Category';

        $this->data['categories'] = $this->db->get('categories')->result();

        $this->data['sub_category'] = $this->db->get_where('sub_categories', ['id' => $sub_catid])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/edit_sub_category', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function update() {

        $cat_id = $this->input->get_post('cat_id');

        $sub_cat_id = $this->input->get_post('sub_cat_id');

        $sub_category_name = $this->input->get_post('sub_category_name');

        $description = $this->input->get_post('description');

        $status = $this->input->get_post('status');

        $brand = $this->input->get_post('brand');

        $seo_url = $this->make_seo_name($sub_category_name);
        $data = array(
            'sub_category_name' => $sub_category_name,
            //'description' => $description,
            'cat_id' => $cat_id,
           // 'brand' => $brand,
            'seo_url'=>$seo_url,
            'status' => $status,
            'updated_at' => time(),
        );


        $this->db->where('id', $sub_cat_id);

        $update_query = $this->db->update('sub_categories', $data);

        if ($update_query) {

            redirect('admin/sub_categories');

            die();

        } else {

            redirect('admin/sub_categories/edit_subcategory/' . $sub_cat_id);

            die();

        }

    }



    function delete($subcat_id) {
        $check = $this->db->query("select * from products where sub_cat_id='".$subcat_id."'");

        if($check->num_rows()>0)
        {
                 $this->session->set_flashdata('error_message', "Some products are assigned, Unable to delete");
                redirect('admin/sub_categories');
        }
        else
        {
                $this->db->where('id', $subcat_id);
                if ($this->db->delete('sub_categories')) {
                    $this->session->set_flashdata('success_message', 'Sub Category Deleted Successfully');
                    redirect('admin/sub_categories');
                } else {
                    $this->session->set_flashdata('error_message', 'Unable to delete');
                    redirect('admin/sub_categories');
                }
        }
        

    }



}

