<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Doctors_specialisation extends CI_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }

    }



    function index() {
        $this->data['page_name'] = 'Doctors Specialisation';
        $this->data['page_name'] = 'doctors_specialisation';
        $qry = $this->db->query("select * from specialisation order by id desc");
        $data['specialisation'] = $qry->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/specialisation', $data);
        $this->load->view('admin/includes/footer');
    }



    function add() {
        
        $this->data['title'] = 'Add Specialisation Page';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/addspecialisation', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function insert() {
        
        // echo "test";die;

        $name = $this->input->post('name');
        $status = $this->input->post('status');
   
        $data = array(
            'name' => $name,
            'status' => $status
           
        );

        $insert_query = $this->db->insert('specialisation', $data);
        if ($insert_query) 
        {
            redirect('admin/doctors_specialisation');
            die();
        } 
        else
        {
            redirect('admin/doctors_specialisation/add');
            die();
        }

    }



    function edit($id) {

        $data['title'] = 'Edit Specialisation Page';

        $data['specialisation'] = $this->db->get_where('specialisation', ['id' => $id])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/editspecialisation', $data);

        $this->load->view('admin/includes/footer');

    }



    function update() {
        $id=$this->input->post('id');
        $qry=$this->db->query("select * from specialisation where id='".$id."'");
        $row = $qry->row();
        $name = $this->input->post('name');
        $status = $this->input->post('status');
       
        $data = array(
             'name' => $name,
            'status' => $status
            
        );
        $this->db->where('id', $id);
        $update_query = $this->db->update('specialisation', $data);

        if ($update_query) 
        {
            redirect('admin/doctors_specialisation');
            die();
        } 
        else 
        {
            redirect('admin/doctors_specialisation/edit/' . $id);
            die();
        }

    }



//  private function upload_file($file_name) {
// // echo $file_ext = pathinfo($_FILES[$file_name]["name"], PATHINFO_EXTENSION);
// // die;
//     if($_FILES[$file_name]['name']!='')
//     {

//         if($_FILES[$file_name]["size"]<'5114374')
//         {
//             $upload_path1 = "./uploads/shops/";
//             $config1['upload_path'] = $upload_path1;
//             $config1['allowed_types'] = "*";
//             // $config1['allowed_types'] = "*";
//             $config1['max_size'] = "204800000";
//             $img_name1 = strtolower($_FILES[$file_name]['name']);
//             $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
//             $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
//             $this->load->library('upload', $config1);
//             $this->upload->initialize($config1);
//             $this->upload->do_upload($file_name);
//             $fileDetailArray1 = $this->upload->data();
//             // echo $this->upload->display_errors();
//             // die;
//             return $fileDetailArray1['file_name'];
//         }
//         else
//         {
//             return 'false';
//         }
//     }
//     else
//     {
//         return '';
//     }
//     }



    function delete($id) {

        $this->db->where('id', $id);
        if ($this->db->delete('specialisation')) {
                $this->session->set_flashdata('success_message', 'Deleted Successfully');
                redirect('admin/doctors_specialisation');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/doctors_specialisation');
         }

    }



}

