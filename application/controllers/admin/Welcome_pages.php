<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Welcome_pages extends CI_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }

    }



    function index() {
        $this->data['page_name'] = 'welcome_pages';
        $qry = $this->db->query("select * from welcome_page order by id desc");
        $data['welcome_pages'] = $qry->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/welcome_pages', $data);
        $this->load->view('admin/includes/footer');
    }



    function add() {
        
        $this->data['title'] = 'Add Welcome page';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/addwelcome_page', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function insert() {

        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $appimage = $this->upload_file('appimage');

        $data = array(
            'title' => $title,
            'description' => $description,
            'app_image' => $appimage
        );

        $insert_query = $this->db->insert('welcome_page', $data);
        if ($insert_query) 
        {
            redirect('admin/welcome_pages');
            die();
        } 
        else
        {
            redirect('admin/welcome_pages/add');
            die();
        }

    }



    function edit($id) {

        $data['title'] = 'Edit Welcome Page';

        $data['welcome_page'] = $this->db->get_where('welcome_page', ['id' => $id])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/editwelcome_page', $data);

        $this->load->view('admin/includes/footer');

    }



    function update() {
        $id=$this->input->post('id');
        $qry=$this->db->query("select * from welcome_page where id='".$id."'");
        $row = $qry->row();
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        if($this->upload_file('appimage')!='')
        {
            $appimage=$this->upload_file('appimage');
        }
        else
        {
            $appimage=$row->app_image;
        }

      
        $data = array(
            'title' => $title,
            'description' => $description,
            'app_image' => $appimage
        );
        $this->db->where('id', $id);
        $update_query = $this->db->update('welcome_page', $data);

        if ($update_query) 
        {
            redirect('admin/welcome_pages');
            die();
        } 
        else 
        {
            redirect('admin/welcome_pages/edit/' . $id);
            die();
        }

    }



 private function upload_file($file_name) {
// echo $file_ext = pathinfo($_FILES[$file_name]["name"], PATHINFO_EXTENSION);
// die;
    if($_FILES[$file_name]['name']!='')
    {

        if($_FILES[$file_name]["size"]<'5114374')
        {
            $upload_path1 = "./uploads/welcome_page/";
            $config1['upload_path'] = $upload_path1;
            $config1['allowed_types'] = "*";
            // $config1['allowed_types'] = "*";
            $config1['max_size'] = "204800000";
            $img_name1 = strtolower($_FILES[$file_name]['name']);
            $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
            $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);
            $this->upload->do_upload($file_name);
            $fileDetailArray1 = $this->upload->data();
            // echo $this->upload->display_errors();
            // die;
            return $fileDetailArray1['file_name'];
        }
        else
        {
            return 'false';
        }
    }
    else
    {
        return '';
    }
    }



    function delete($id) {

        $this->db->where('id', $id);
        if ($this->db->delete('welcome_page')) {
                $this->session->set_flashdata('success_message', 'Banner Deleted Successfully');
                redirect('admin/welcome_pages');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/welcome_pages');
         }

    }



}

