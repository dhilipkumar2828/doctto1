<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Qualifications extends CI_Controller {
    public $data;
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            redirect('admin/login');
        }
    }



    function index() {
        $this->data['page_name'] = 'qualification';

        $this->data['data'] = $this->db->get("designations")->result();
        
        //print_r($this->data);die;
        $this->load->view('admin/includes/header', $this->$data);
        $this->load->view('admin/qualifications', $this->data);
        $this->load->view('admin/includes/footer', $this->data);
    }



    function add() {
        $this->data['page_name'] = 'qualifications';
        $this->data['title'] = 'Add Doctors Specialist In Page';
        
        $qry = $this->db->query("select * from specialisation order by id desc");
        $this->data['specialisation'] = $qry->result(); 
        
        // print_r($data['specialisation']);die;
        
        // echo $this->db->last_query();die;

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/addqualifications', $this->data);

        $this->load->view('admin/includes/footer');

    }


    function getspecialist_in()
    {
        $specialisation_id = $this->input->post('specialisation_id');
        //get state name
        //$state = $this->db->get_where('states',['id'=>$state_id])->row()->state_name;
              $this->db->where('specialisation_id', $specialisation_id);
              $query = $this->db->get('specialist_in');
              $output = '<option value="">Select Specialist In</option>';
              foreach($query->result() as $row)
              {
               $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
              }
              echo $output;
            exit;
    }


    function insert() {

        $name = $this->input->post('name');
        $status = $this->input->post('status');
       
         $data = array(
            'name' => $name,
            'status' => $status
        );

        $insert_query = $this->db->insert('designations', $data);
        if ($insert_query) 
        {
            redirect('admin/qualifications');
            die();
        } 
        else
        {
            redirect('admin/qualifications/add');
            die();
        }

    }



    function edit($id) {

        $data['title'] = 'Edit Designations';

        $data['data'] = $this->db->get_where('designations', ['id' => $id])->row();
        
        // print_r($data['specialist_in']);die;

        $this->load->view('admin/includes/header');

        $this->load->view('admin/editqualifications', $data);

        $this->load->view('admin/includes/footer');

    }



    function update() {
        $id=$this->input->post('id');
        $row = $this->db->where("id",$id)->get("designations")->row();
        
        $status = $this->input->post('status');
        $name = $this->input->post('name');
       
        $data = array(
            'status' => $status,
            'name' => $name
        );
        $this->db->where('id', $id);
        $update_query = $this->db->update('designations', $data);

        if ($update_query) 
        {
            redirect('admin/qualifications');
            die();
        } 
        else 
        {
            redirect('admin/qualifications/edit/' . $id);
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
        if ($this->db->delete('designations')) {
                $this->session->set_flashdata('success_message', 'Deleted Successfully');
                redirect('admin/qualifications');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/qualifications');
         }

    }



}

