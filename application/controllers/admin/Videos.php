<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Videos extends MY_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }
        $this->data['page_name'] = 'videos';

    }



    function index() {
        $this->data['title'] = 'Doctor Videos';
        $this->data['title1'] = 'Doctor Videos';
       // $this->db->order_by('id', 'desc');

       $this->data['videos'] = $this->db->get('doctor_videos')->result();

        // $this->data['categories'] = $this->db->query('SELECT subcat.*, cat.category_name FROM sub_categories subcat INNER JOIN categories cat ON cat.id = subcat.cat_id order by subcat.id desc')->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/videos', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function add() {

        $this->data['title'] = 'Add Videos';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/add_video', $this->data);

        $this->load->view('admin/includes/footer');

    }

function make_seo_name($title) {
        return preg_replace('/[^a-z0-9_-]/i', '', strtolower(str_replace(' ', '-', trim($title))));
    }

    function insert() {

        
        $status = $this->input->get_post('status');

        $link = $this->input->get_post('link');

        $priority = $this->input->get_post('priority');


        $data = array(

            'link' => $link,

            'priority' => $priority,
            
           
            'status' => $status,

            'created_at' => time()

        );



        $insert_query = $this->db->insert('doctor_videos', $data);

        if ($insert_query) {

            redirect('admin/videos');

            die();

        } else {

            redirect('admin/videos/add');

            die();

        }

    }



    function edit_video($video_id) {

        $this->data['title'] = 'Edit Sub Category';

        $this->data['video'] = $this->db->get_where('doctor_videos', ['id' => $video_id])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/edit_video', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function update() {

        $id = $this->input->get_post('id');

        $status = $this->input->get_post('status');

        $link = $this->input->get_post('link');

        $priority = $this->input->get_post('priority');


        // $seo_url = $this->make_seo_name($sub_category_name);
        $data = array(
            'link' => $link,
            'priority' => $priority,
            'status' => $status,
            'updated_at' => time(),
        );

        

        $this->db->where('id', $id);

        $update_query = $this->db->update('doctor_videos', $data);

        if ($update_query) {

            redirect('admin/videos');

            die();

        } else {

            redirect('admin/videos/edit_video/' . $id);

            die();

        }

    }



    function delete($id) {
        // $check = $this->db->query("select * from products where sub_cat_id='".$subcat_id."'");

        // if($check->num_rows()>0)
        // {
        //          $this->session->set_flashdata('error_message', "Some products are assigned, Unable to delete");
        //         redirect('admin/sub_categories');
        // }
        
                $this->db->where('id', $id);
                if ($this->db->delete('doctor_videos')) {
                    $this->session->set_flashdata('success_message', 'Video Deleted Successfully');
                    redirect('admin/videos');
                } else {
                    $this->session->set_flashdata('error_message', 'Unable to delete');
                    redirect('admin/videos');
                
        }
        

    }



}

