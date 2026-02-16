<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Symptom extends CI_Controller {
    public $data;
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
    }

    function index() {
        $this->data['page_name'] = 'symptom';
        $this->data['title'] = 'Symptom Category';
                                  $this->db->order_by("priority","asc");
        $this->data['symptoms'] = $this->db->get('symptom')->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/symptom', $this->data);
        $this->load->view('admin/includes/footer');
    }



    function add() 
    {
        $this->data['title'] = 'Add Symptom Category';
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/add_symptom', $this->data);
        $this->load->view('admin/includes/footer');
    }

function make_seo_name($title) {
        return preg_replace('/[^a-z0-9_-]/i', '', strtolower(str_replace(' ', '-', trim($title))));
    }

    function insert() {

        $category_name = $this->input->get_post('name');

        $cat_qry=$this->db->query("select * from symptom where name='".$category_name."'");
        if($cat_qry->num_rows()>0)
        {
            $this->session->set_flashdata('error_message', 'Already exist ( Name )');
            redirect('admin/symptom/add');
            die();
        }
        $status = $this->input->get_post('status');
        $priority = $this->input->get_post('priority');

            if(!empty($_FILES["app_image"]["name"]))
            {
                $img_k_image = "Doc_category_".$i."_".date('YmdHis').".jpg";
                if (file_exists("./uploads/doctor_categories/" . $img_k_image))
                {
                     $k_image= $img_k_image;
                }
                else
                {
                  move_uploaded_file($_FILES["app_image"]["tmp_name"], "./uploads/doctor_categories/" . $img_k_image);
                 $k_image=$img_k_image;
                }
            }
            else
            {
                $k_image="";
            }
            $consultation = $this->input->get_post('consultation');
            $seo_url = $this->make_seo_name($category_name);
        $data = array(
            'consultation'=>$consultation,
            'name' => $category_name,
            'image' => $k_image,
            'status' => $status,
            'priority'=>$priority
        );

        $insert_query = $this->db->insert('symptom', $data);
        if ($insert_query) {
             $this->session->set_flashdata('success_message', 'Symptom Category added Successfully');
            redirect('admin/symptom');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, please try again');
            redirect('admin/symptom/add');
            die();
        }
    }



    function edit($cat_id) {

        $this->data['title'] = 'Edit Symptom Category';

        $this->data['doctor_category'] = $this->db->get_where('symptom', ['id' => $cat_id])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/edit_symptom', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function update() 
    {
        $cat_id = $this->input->get_post('cat_id');
        $category_name = $this->input->get_post('name');
        $status = $this->input->get_post('status');
        $priority = $this->input->get_post('priority');
        $seo_url = $this->make_seo_name($category_name);


         $cat_qry=$this->db->query("select * from symptom where id!='".$cat_id."' and name='".$category_name."'");
        if($cat_qry->num_rows()>0)
        {
            $this->session->set_flashdata('error_message', 'Already exist ( Name )');
            redirect('admin/symptom/edit/' . $cat_id);
            die();
        }
         $consultation = $this->input->get_post('consultation');
        $data = array(
            'consultation'=>$consultation,
            'name' => $category_name,
            'status' => $status,
             'priority'=>$priority
        );
            if(!empty($_FILES["app_image"]["name"]))
            {
                $img_k_image = "symptoms_".date('YmdHis').".jpg";
                if (file_exists("./uploads/doctor_categories/" . $img_k_image))
                {
                     $k_image= $img_k_image;
                }
                else
                {
                  move_uploaded_file($_FILES["app_image"]["tmp_name"], "./uploads/doctor_categories/" . $img_k_image);
                  $k_image=$img_k_image;
                }
                $data['image'] = $k_image;
                $this->db->where('id', $cat_id);
                $update_query = $this->db->update('symptom', $data);
            }
            else
            {
                $this->db->where('id', $cat_id);
                $update_query = $this->db->update('symptom', $data);
            }

        if ($update_query) {
            $this->session->set_flashdata('success_message', 'Symptom Category updated Successfully');
            redirect('admin/symptom');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, please try again');
            redirect('admin/symptom/edit/' . $cat_id);
            die();
        }

    }



    function delete($cat_id) {



            $this->db->where('id', $cat_id);

            if ($this->db->delete('symptom')) {

                $this->session->set_flashdata('success_message', 'Symptom Category Deleted Successfully');

                redirect('admin/symptom');

            } else {

                $this->session->set_flashdata('error_message', 'Unable to delete');

                redirect('admin/symptom');

            }

    }



}

