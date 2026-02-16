<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Specialist_in extends CI_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }

    }



    function index() {
        $this->data['page_name'] = 'specialist_in';
        $qry = $this->db->query("select * from specialist_in order by id desc");
        $this->data['specialist_in'] = $qry->result(); 
        
        // print_r( $data['specialist_in']);die;
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/specialist_in', $this->data);
        $this->load->view('admin/includes/footer',$this->data);
    }



    function add() {
        
        $this->data['title'] = 'Add Doctors Specialist In Page';
        
        $qry = $this->db->query("select * from specialisation order by id desc");
        $this->data['specialisation'] = $qry->result(); 
        
        // print_r($data['specialisation']);die;
        
        // echo $this->db->last_query();die;

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/addspecialist_in', $this->data);

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

        $specialisation_id = $this->input->post('specialisation_id');
        $status = $this->input->post('status');
        $specialist_in = $this->input->post('specialist_in');
       
         $data = array(
            'specialisation_id' => $specialisation_id,
            'status' => $status,
            'specialist_in' => $specialist_in
            
        );

        $insert_query = $this->db->insert('specialist_in', $data);
        if ($insert_query) 
        {
            redirect('admin/specialist_in');
            die();
        } 
        else
        {
            redirect('admin/specialist_in/add');
            die();
        }

    }



    function edit($id) {

        $data['title'] = 'Edit Doctors Specialist In Page';

        $data['specialist_in'] = $this->db->get_where('specialist_in', ['id' => $id])->row();
        
        // print_r($data['specialist_in']);die;

        $this->load->view('admin/includes/header');

        $this->load->view('admin/editspecialist_in', $data);

        $this->load->view('admin/includes/footer');

    }



    function update() {
        $id=$this->input->post('id');
        $qry=$this->db->query("select * from specialist_in where id='".$id."'");
        $row = $qry->row();
        
        $specialisation_id = $this->input->post('specialisation_id');
        $status = $this->input->post('status');
        $name = $this->input->post('name');
       
        $data = array(
            'specialisation_id' => $specialisation_id,
            'status' => $status,
           
            'name' => $name
            
        );
        $this->db->where('id', $id);
        $update_query = $this->db->update('specialist_in', $data);

        if ($update_query) 
        {
            redirect('admin/specialist_in');
            die();
        } 
        else 
        {
            redirect('admin/specialist_in/edit/' . $id);
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
        if ($this->db->delete('specialist_in')) {
                $this->session->set_flashdata('success_message', 'Deleted Successfully');
                redirect('admin/specialist_in');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/specialist_in');
         }

    }



}

