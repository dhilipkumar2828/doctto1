<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctors extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) { 
            redirect('admin/login');
        }

        $this->load->model('admin/Doctors_model', 'doctors_model');  

        $this->load->model("Admin_model", "admin_model");
        $this->load->model("admin/Doctor_manage_category_model", "doctor_manage_category_model");
        $this->data['page_name'] = 'doctors';
    }

    function index() {
        $this->data['page_title'] = 'Doctors';
        $doctors = $this->doctors_model->get_doctors();
        
    
        $this->data['doctors'] = $doctors;  
        $this->admin_view('doctors');
    }

    function inactive() {
        $this->data['page_title'] = 'Doctors';
        $doctors = $this->doctors_model->get_in_inactive_doctors();
        // print_r($doctors);
        // die;
        $this->data['doctors'] = $doctors;
        $this->admin_view('doctors');
    }
     function active() {
        $this->data['page_title'] = 'Doctors';
        $doctors = $this->doctors_model->get_in_active_doctors();
        // print_r($doctors);
        // die;
        $this->data['doctors'] = $doctors;
        $this->admin_view('doctors');
    }
    

    function delete($id) {
        if ((isset($id)) && ($id != '')) {
            $parameters = array();
            $parameters['id'] = $id;

            if ($this->doctors_model->delete($parameters)) {

                $this->session->set_flashdata('success_message', "'Deleted Successfully', 'Success'");
                redirect('admin/doctors');
            } else {
                $this->session->set_flashdata('error_message', "'Please Try Again', 'Error'");
                redirect('admin/doctors');
            }
        } else {
            redirect('admin/doctors');
        }
    }

    function changeStatus($shop_id, $status) {
        $upd = $this->db->update("doctors", array('status' => $status, 'vendor_verification_status' => 0), array('id' => $shop_id)); 
        if ($upd) {
            redirect('admin/doctors');
        }
    }

    // function add() {
    //      $this->data['page_name'] = 'doctors/add';
    //     $this->data['title'] = 'Add Vendor/Shop';
    //     $this->data['cities'] = $this->db->get('cities')->result();
    //     $this->data['categories'] = $this->db->get('categories')->result();
    //     $this->data['visual_merchant'] = "";
    //     $this->load->view('admin/includes/header', $this->data);
    //     $this->load->view('admin/add_doctor', $this->data);
    //     $this->load->view('admin/includes/footer');
    // }

    function add() {//print_r($_FILES);exit;
        $this->data['page_title'] = 'Add Doctors'; 
        $this->data['title'] = 'Add Doctor'; 
        
           $specialisation = $this->doctors_model->get_specialisations();
        // print_r($specialisation);
        // die;
         $this->data['specialisation'] = $specialisation;
        //  print_r($this->data['specialisation']);die;
        $this->admin_view('add_doctor');
    }
    
    
     function getspecialist()
    {
        $specialisation = $this->input->post('specialisation');
        //get state name
        //$state = $this->db->get_where('states',['id'=>$state_id])->row()->state_name;
              $this->db->where('id', $specialisation);
              $query = $this->db->get('specialist_in');
              $output = '<option value="">Select Specialist In</option>';
              foreach($query->result() as $row)
              {
               $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
              }
              echo $output; 
            exit;
    }

    function send_message($message = "", $mobile_number) { 


        $message = urlencode($message);

        $URL = "http://login.smsmoon.com/API/sms.php"; // connecting url 

        $post_fields = ['username' => 'a3services', 'password' => 'vizag@123', 'from' => 'Rocket', 'to' => $mobile_number, 'msg' => $message, 'type' => 1, 'dnd_check' => 0];

        //file_get_contents("http://login.smsmoon.com/API/sms.php?username=colourmoonalerts&password=vizag@123&from=WEBSMS&to=$mobile_number&msg=$message&type=1&dnd_check=0");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $URL);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

        curl_exec($ch);

        return true;
    }

    function update() {
    	
        $sid = $this->input->post('id');
        $row = $this->db->where(array('id'=>$sid))->get('doctors')->row();
        // echo "<pre>";
        // print_r($row);die;
        if ($this->upload_file('hospital_image') != '') {
            $hospital_image = $this->upload_file('hospital_image');
        } else {
            $hospital_image = $row->hospital_image;
        }

        if ($this->upload_file('doctor_image') != '') {
            $doctor_image = $this->upload_file('doctor_image');
        } else {
            $doctor_image = $row->doctor_image;
        }
        
        if ($this->upload_file('digital_signature') != '') {
            $digital_signature = $this->upload_file('digital_signature');
        } else {
            $digital_signature = $row->digital_signature;
        }

       /* $check_qry = $this->db->query("select * from doctors where id!='" . $sid . "' and ( mobile_number='" . $this->input->post('mobile_number') . "')");*/
       /*$wr= array('id!='=>$sid,'mobile_number'=>$this->input->post('mobile_number'));
        $check_num_row = $this->db->where($wr)->get('doctors')->num_rows();
        

        if ($check_num_row > 0) {
            $this->session->set_flashdata('error_message', 'Already Exist ( Mobile Number )');
            redirect('admin/doctors/edit');
            die();
        }*/


        // $check_pincode_qry = $this->db->query("select * from doctors where id!='".$sid."' and vendor_pincodes='".$this->input->get_post('mobile')."'");
        // if($check_pincode_qry->num_rows()>0)
        // {
        //     $this->session->set_flashdata('error_message', 'Pincode already assigned');
        //     redirect('admin/doctors/edit');
        //     die();
        // }
        
        if($this->input->post('specialist')!= ''){
            $splecialist= implode(',', $this->input->post('specialist'));
        }
        else{
            $splecialist= $row->specialist_in;
        }


//print_r($this->input->post()); die;
        $data = array(
            'hospital_name' => $this->input->post('hospital_name'),
            'doctor_name' => $this->input->post('doctor_name'),
            'hospital_image' => $hospital_image,
            'doctor_image' => $doctor_image,
            'digital_signature' => $digital_signature,
            'designations' => implode(',', $this->input->post('designations')),
            'youtube_channel_id  ' => $this->input->post('youtube_channel_id'),
            'address' => $this->input->post('address'),
            'mobile_number' => $this->input->post('mobile_number'),
            'specialisation' => $this->input->post('specialisation'),
            'specialist_in' => $splecialist,       
            'state' => $this->input->post('state_id'),
            'city' => $this->input->post('city_id'),
            'pincode' => $this->input->post('pincodes'),
            'experience' => $this->input->post('experience'),
            'voice_call' => $this->input->get_post('consultant_fee_voice_call'),
            'video_call' => $this->input->get_post('consultant_fee_video_call'),
            'chat_price' => $this->input->get_post('consultant_fee_chat'),
            'consultant_fee' => $this->input->get_post('consultant_fee_chat'),
            'aboutus' => $this->input->post('aboutus'),
            'tags' => $this->input->post('tags'),
             'blue_tick' => $this->input->get_post('bluetick_status'),
             'rating'  => $this->input->get_post('doctor_rating'),
             'rating_count' => $this->input->get_post('users_count'),
            'doctor_show_status' => $this->input->post('doctor_show_status'),
            'doctor_login_status' => $this->input->post('doctor_login_status'),
            // 'gst_number' => $this->input->post('gst_number'),
            // 'pan_number' => $this->input->post('pan_number'), 
            'morning_start_time' => $this->input->post('morning_start_time'),
            'morning_end_time' => $this->input->post('morning_end_time'),
            'afternoon_start_time' => $this->input->post('afternoon_start_time'),
            'afternoon_end_time' => $this->input->post('afternoon_end_time'),
            'evening_end_time' => $this->input->post('evening_end_time'),
            'evening_start_time' => $this->input->post('evening_start_time'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude')
        );
        // echo "<pre>";
        //  print_r($data);die;
        	
        $insert_query = $this->db->update('doctors', $data,array('id' => $sid));
        // echo $this->db->last_query(); die;
        if ($insert_query) {

            $this->session->set_flashdata('success_message', 'Doctor Updated Successfully');

            redirect('admin/doctors');

            die();
        } else {

            $this->session->set_flashdata('error_message', 'Unable to edit');

            redirect('admin/doctors/edit/' . $sid);

            die();
        }
    }

    function make_seo_name($title) {
        return preg_replace('/[^a-z0-9_-]/i', '', strtolower(str_replace(' ', '-', trim($title))));
    }

    function insert() {

        $hospital_name = $this->input->post('hospital_name');
        $doctor_name = $this->input->post('doctor_name');
        $hospital_image = $this->upload_file('hospital_image');
        $doctor_image = $this->upload_file('doctor_image');
        $designations = implode(',', $this->input->get_post('designations'));
        $youtube_channel_id = $this->input->get_post('youtube_channel_id');
        $password = md5($this->input->get_post('password'));
        $address = $this->input->get_post('address');
        $mobile_number = $this->input->get_post('mobile_number');
        $email = $this->input->get_post('email');
        $specialisation = $this->input->get_post('specialisation');
        $specialist = implode(',', $this->input->get_post('specialist'));
        $license = $this->input->get_post('license');
        $digital_signature = $this->upload_file('digital_signature');
        $state = $this->input->get_post('state_id');
        $city = $this->input->get_post('city_id');
        $pincode = $this->input->get_post('pincodes');
        $experience = $this->input->get_post('experience');
        $consultant_fee_voice_call = $this->input->get_post('consultant_fee_voice_call');
        $consultant_fee_video_call = $this->input->get_post('consultant_fee_video_call');
        $consultant_fee_chat = $this->input->get_post('consultant_fee_chat');
        $aboutus = $this->input->get_post('aboutus');
        $tags = $this->input->get_post('tags');
        $latitude = $this->input->get_post('latitude');
        $longitude = $this->input->get_post('longitude');
      
        $doctor_show_status = $this->input->get_post('doctor_show_status');
        $bluetick_status = $this->input->get_post('bluetick_status');
        $doctor_rating = $this->input->get_post('doctor_rating');
        $users_count = $this->input->get_post('users_count');
        $doctor_login_status = $this->input->get_post('doctor_login_status');
        // $gst_number = $this->input->get_post('gst_number');
        // $pan_number = $this->input->get_post('pan_number'); 
        $morning_start_time = $this->input->get_post('morning_start_time');
        $morning_end_time = $this->input->get_post('morning_end_time');
        $afternoon_start_time = $this->input->get_post('afternoon_start_time');
        $afternoon_end_time = $this->input->get_post('afternoon_end_time');
        $evening_start_time = $this->input->get_post('evening_start_time');
        $evening_end_time = $this->input->get_post('evening_end_time');
        $created_date = date('Y-m-d H:i:s');

        $ins = $this->doctors_model->insertData($hospital_name, $doctor_name, $hospital_image, $doctor_image, $designations, $youtube_channel_id, $password, $address,$email,$specialisation,$specialist,$license,$digital_signature, $mobile_number, $state, $city, $pincode, $experience, $consultant_fee_voice_call,$consultant_fee_video_call,$consultant_fee_chat, $aboutus, $tags, $doctor_show_status,$bluetick_status,$doctor_rating,$users_count, $doctor_login_status, $morning_start_time, $morning_end_time, $afternoon_start_time, $afternoon_end_time, $evening_start_time, $evening_end_time, $latitude, $longitude);
        
        // echo $this->db->last_query();die;
      
        if ($ins == true) {
            $this->session->set_flashdata('success_message', 'Doctors  added Successfully');
            redirect('admin/doctors');
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, please try again');
            redirect('admin/doctors');
        }
    }

    private function upload_file($file_name) {
// echo $file_ext = pathinfo($_FILES[$file_name]["name"], PATHINFO_EXTENSION);
// die;
        if ($_FILES[$file_name]['name'] != '') {

            if ($_FILES[$file_name]["size"] < '5114374') {
                $upload_path1 = "./uploads/doctors/";
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
            } else {
                return 'default_shop_logo.png';
            }
        } else {
            return '';
        }
    }

    function edit($id) {

        $qry = $this->db->query("select * from doctors where id='" . $id . "'");
        $row = $qry->row();
        $this->data['doctors_data'] = $row;
        $this->data['cities'] = $this->db->get('cities')->result();
        $this->data['pincodes'] = $this->db->get('pincodes')->result();
        $specialisation = $this->doctors_model->get_specialisations();
        $this->data['specialisation'] = $specialisation;
        
        $this->data['title'] = 'Edit Doctor';
        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/edit_doctor', $this->data);

        $this->load->view('admin/includes/footer');
    }

    function manage_doctor_categories() {

        $doctor_id = $this->input->get_post('id');
        if (!$doctor_id) {
            redirect('admin/doctors/');
            die();
        }

        $this->data['doctor_id'] = $doctor_id;

        //$this->data['shop_status'] = 'add';

        $this->data['doctor_data'] = $this->doctor_manage_category_model->get_doctor_category($doctor_id);
        $this->data['categories'] = $this->db->get('doctor_categories')->result();

        // echo "<pre>";
        // print_r($this->data['doctor_data']); die;
        //  echo "</pre>";
        // if (count($res) > 0) 
        // {
        //     $this->data['admin_comissions'] = $res;
        // }

        $this->data['title'] = 'Manage Doctor Categories';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/doctor_manage_category', $this->data);

        $this->load->view('admin/includes/footer');
    }

    function manage_doctor_symptoms($doctor_id) {
        if (!$doctor_id) {
            redirect('admin/doctors/');
            die();
        }

        $this->data['doctor_id'] = $doctor_id;
        $this->data['symptom'] = $this->doctor_manage_category_model->getSymptoms();
        $chekc= $this->doctor_manage_category_model->get_doctor_symptoms($doctor_id);
        if(!empty($chekc))
        { 
            $this->data['doctor_data'] = $chekc;
    
        $this->data['doctor_data_symptom_ids'] = array_column($this->data['doctor_data'], 'symptom_id');
        }
        else
        {
            $this->data['doctor_data'] = [];
            $this->data['doctor_data_symptom_ids'] = [];
        }
        
        $this->data['title'] = 'Manage Doctor Categories';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/doctor_manage_symptoms', $this->data);

        $this->load->view('admin/includes/footer');
    }
    
    
     function doctor_bank_details($doctor_id) {
        if (!$doctor_id) {
            redirect('admin/doctors/');
            die();
        }

        $this->data['doctor_id'] = $doctor_id;
        
        $this->data['doctor_bank_details'] = $this->db->where('doctor_id',$this->data['doctor_id'])->get('doctor_bank_details')->row();
        
        $this->data['doc_name']= $this->db->where('id',$this->data['doctor_bank_details']->doctor_id)->get('doctors')->row();
        
        $this->data['title'] = 'Doctor Bank Details';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/doctor_bank_details', $this->data);

        $this->load->view('admin/includes/footer');
    }
    
  

    function insert_cat_comission() {
        $doctor_id = $this->input->post('doctor_id');
        $Category = $this->input->post('cat_id');
        $cat_qry = $this->db->query("select * from doctor_admin_comission where cat_id='" . $Category . "' and doctor_id='" . $doctor_id . "'");
        if ($cat_qry->num_rows() > 0) {

            $this->session->set_flashdata('error_message', 'Already exist ( Doctor Name )');
            redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
            die();
        }
        $admin_comission = $this->input->post('admin_comission');
        $gst = $this->input->post('gst');

//             $this->form_validation->set_rules('cat_id', 'Category Name', 'required|trim|is_unique[doctor_admin_comission.cat_id]');
//          if ($this->form_validation->run() == FALSE) { 
// echo"done";
//          } 
//          else { 
//             echo"no done";
//          }
// exit;

        $ins = $this->doctor_manage_category_model->insert($this->input->post('doctor_id'), $Category);
        if ($ins == true) {
            $this->session->set_flashdata('success_message', 'Doctors   added Successfully');
            redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, please try again');
            redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
        }
    }

    function edit_comission($id, $doctor_id) {
        $this->data['doctor_id'] = $doctor_id;
        $this->data['com'] = $this->doctor_manage_category_model->get_doctor_commision($id);
        $this->data['categories'] = $this->db->get('doctor_categories')->result();
        // $this->data['comission'] = $this->db->get('doctor_admin_comission')->row();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/edit_doctors_manage', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function update_comission() {
        $id = $this->input->post('id');
        $doctor_id = $this->input->post('doctor_id');
        $category = $this->input->post('cat_id');
        $admin_comission = $this->input->post('admin_comission');
        $gst = $this->input->post('gst');

        $cat_qry = $this->db->query("select * from doctor_admin_comission where cat_id ='" . $category . "' and doctor_id='" . $doctor_id . "'");
        if ($cat_qry->num_rows() > 0) {
            $exist_cat = $cat_qry->row();
            if ($exist_cat->doctor_id == $doctor_id) {
                $data = array(
                    'cat_id' => $category
                    //'admin_comission' => $admin_comission,
                    //'gst' => $gst,
                        // 'status' => $status,
                );

                $update_query = $this->doctor_manage_category_model->get_doctor_manage_category($id, $data);
            } else {
                $this->session->set_flashdata('error_message', 'Already exist ( Doctor Name )');
                redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
                die();
            }
        } else {
            $data = array(
                'cat_id' => $category,
                'admin_comission' => $admin_comission,
                'gst' => $gst,
                    // 'status' => $status,
            );

            $update_query = $this->doctor_manage_category_model->get_doctor_manage_category($id, $data);
        }


        //echo $this->db->last_query(); die;
        if ($update_query) {
            $this->session->set_flashdata('success_message', "'Updated Successfully', 'Success'");
            redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
            die();
        } else {
            $this->session->set_flashdata('error_message', "'Please Try Again', 'Error'");
            redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
            die();
        }
    }

    function delete_commission() {
        $id = $this->input->get_post('id');
        $doctor_id = $this->input->get_post('doctor_id');
        if ((isset($id)) && ($id != '')) {
            $parameters = array();
            $parameters['id'] = $id;

            if ($this->doctor_manage_category_model->delete($parameters)) {
                //echo "delete0";exit;
                $this->session->set_flashdata('success_message', "'Deleted Successfully', 'Success'");
                //  redirect('admin/doctors');
                redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
            } else {
                $this->session->set_flashdata('error_message', "'Please Try Again', 'Error'");
                redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
            }
        } else {
            redirect('admin/doctors/manage_doctor_categories?id=' . $doctor_id);
        }
    }

    //  function delete_admin_comission($id) {
    //     if ((isset($id)) && ($id != '')) {
    //         $parameters = array();
    //         $parameters['id'] = $id;
    //         if ($this->doctors_model->delete_commission($parameters)) {
    //             $this->session->set_flashdata('success_message', "'Deleted Successfully', 'Success'");
    //            redirect('admin/doctors/manage_doctor_categories?id='.$id);
    //         } else {
    //             $this->session->set_flashdata('error_message', "'Please Try Again', 'Error'");
    //              redirect('admin/doctors/manage_doctor_categories?id='.$id);
    //         }
    //     } else {
    //         redirect('admin/doctors/manage_doctor_categories/' .$id);
    //     }
    // }



    function manage_categories() {

        $shop_id = $this->input->get_post('shop_id');
        if (!$shop_id) {
            redirect('admin/doctors/');
            die();
        }

        $this->data['shop_id'] = $shop_id;

        $this->data['shop_status'] = 'add';

        $this->data['shop_name'] = $this->admin_model->get_table_row('doctors', 'id', $shop_id)->shop_name;

        $this->data['categories'] = $this->admin_model->get_table_data('categories', 'id', 'desc');

        $this->db->select('ad_com.*, c.category_name');

        $this->db->from('admin_comissions ad_com');

        $this->db->join('categories c', 'c.id=ad_com.cat_id');

        $this->db->where('ad_com.shop_id', $shop_id);

        $res = $this->db->get()->result();

        if (count($res) > 0) {
            $this->data['admin_comissions'] = $res;
        }

        $this->data['title'] = 'Manage Categories';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/vendor_manage_categories', $this->data);

        $this->load->view('admin/includes/footer');
    }

    function edit_manage_categories($shop_id, $com_id) {

        if (!$shop_id) {
            redirect('admin/doctors/');
            die();
        }

        $this->data['shop_id'] = $shop_id;
        $this->data['com_id'] = $com_id;
        $this->data['shop_status'] = 'edit';

        $shop_qry = $this->db->query("select * from admin_comissions where id='" . $com_id . "'");
        $this->data['admin_edit_comissions'] = $shop_qry->row();

        $this->data['shop_name'] = $this->admin_model->get_table_row('doctors', 'id', $shop_id)->shop_name;

        $this->data['categories'] = $this->admin_model->get_table_data('categories', 'id', 'desc');

        $this->data['subcategories'] = $this->admin_model->get_table_data('sub_categories', 'id', 'desc');

        $this->db->select('ad_com.*, c.category_name');

        $this->db->from('admin_comissions ad_com');

        $this->db->join('categories c', 'c.id=ad_com.cat_id');

        $this->db->where('ad_com.shop_id', $shop_id);

        $res = $this->db->get()->result();

        if (count($res) > 0) {

            $this->data['admin_comissions'] = $res;
        }

        $this->data['title'] = 'Manage Categories';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/vendor_manage_categories', $this->data);

        $this->load->view('admin/includes/footer');
    }

    function manage_locations() {

        /* $shop_id = $this->input->get_post('shop_id');

          if (!$shop_id)
          {
          redirect('admin/doctors/');
          die();
          }

          $this->data['shop_id'] = $shop_id;



          $this->data['page_name'] = 'pincodes';
          $this->data['title'] = 'Pincodes';
          $qry = $this->db->query("select * from pincodes where shop_id='".$shop_id."'");
          $this->data['pincodes'] = $qry->result();

          $this->load->view('admin/includes/header', $this->data);
          $this->load->view('admin/pincodes', $this->data);
          $this->load->view('admin/includes/footer'); */


        $shop_id = $this->input->get_post('shop_id');

        if (!$shop_id) {
            redirect('admin/doctors/');
            die();
        }
        $this->data['shop_id'] = $shop_id;

        $this->data['page_name'] = 'cities';
        $this->data['title'] = 'Cities';
        $qry = $this->db->query("select * from cities where vendor_id='" . $shop_id . "'");
        $this->data['cities'] = $qry->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/cities', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function manage_pincodes() {

        $shop_id = $this->input->get_post('shop_id');

        if (!$shop_id) {
            redirect('admin/doctors/');
            die();
        }

        $this->data['shop_id'] = $shop_id;

        $this->data['page_name'] = 'pincodes';
        $this->data['title'] = 'Pincodes';
        $qry = $this->db->query("select * from pincodes where shop_id='" . $shop_id . "'");
        $this->data['pincodes'] = $qry->result();

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/pincodes', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function manage_areas() {

        $shop_id = $this->input->get_post('shop_id');
        if (!$shop_id) {
            redirect('admin/doctors/');
            die();
        }

        $this->data['shop_id'] = $shop_id;

        $this->data['page_name'] = 'locations';
        $this->data['title'] = 'Areas';

        $qry = $this->db->query("select * from areas where vendor_id='" . $shop_id . "'");
        $this->data['locations'] = $qry->result();

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/locations', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function loadSubcategories() {
        $cid = $this->input->get_post('cid');
        $shop_id = $this->input->get_post('shop_id');
        //$chk = $this->vendor_model->subcategories($cid,$shop_id);

        $qry = $this->db->query("select * from sub_categories where cat_id='" . $cid . "'");
        $query = $qry->result();
        $output = '<option value="">Select SubCategories</option>';
        foreach ($query as $row) {
            $output .= '<option value="' . $row->id . '">' . $row->sub_category_name . '</option>';
        }

        print_r($output);
        die;
    }

    function update_cat_comission() {

        $com_id = $this->input->get_post('com_id');
        $shop_id = $this->input->get_post('shop_id');

        $cat_id = $this->input->get_post('cat_id');
        $sub_categories = $this->input->get_post('sub_categories');
        $subcategory_ids = implode(",", $sub_categories);
        $admin_commission = $this->input->get_post('admin_comission');
        $gst = $this->input->get_post('gst');
        $status = $this->input->get_post('status');
        if ($status == 1) {
            $ars = array('status' => 1);
            $wrr = array('shop_id' => $shop_id, 'cat_id' => $cat_id);
            $this->db->update('products', $ars, $wrr);
            //echo $this->db->last_query(); die;
        } else if ($status == 0) {
            $ars = array('status' => 0);
            $wrr = array('shop_id' => $shop_id, 'cat_id' => $cat_id);
            $this->db->update('products', $ars, $wrr);
            //echo $this->db->last_query(); die;
        }

        $wr = array("id" => $com_id);
        $data = array(
            'shop_id' => $shop_id,
            'cat_id' => $cat_id,
            'subcategory_ids' => $subcategory_ids,
            'admin_comission' => $admin_commission,
            'gst' => $gst,
            'status' => $status,
            'updated_at' => time());

        $insert = $this->db->update('admin_comissions', $data, $wr);
        if ($insert) {
            redirect('admin/doctors/manage_categories?shop_id=' . $shop_id);
            die();
        } else {
            redirect('admin/doctors/');
            die();
        }
    }

    function delete_vendor_admin_comission() {

        $admin_com_id = $this->input->get_post('admin_com_id');

        $shop_id = $this->input->get_post('shop_id');

        $qry = $this->db->query("select * from admin_comissions where id='" . $admin_com_id . "'");
        $admin_row = $qry->row();

        $cat_id = $admin_row->cat_id;

        $shop_qry = $this->db->query("select * from products where cat_id='" . $cat_id . "' and shop_id='" . $shop_id . "'");
        $shop_nums = $shop_qry->num_rows();
        if ($shop_nums > 0) {
            $this->session->set_flashdata('error_message', 'Unable to delete,this category already assigned to products');
            redirect('admin/doctors/manage_categories?shop_id=' . $shop_id);
            die();
        } else {
            if ($this->admin_model->delete_vendor_admin_comission($admin_com_id)) {
                $this->session->set_flashdata('success_message', 'Comission Deleted Successfully');
                redirect('admin/doctors/manage_categories?shop_id=' . $shop_id);
                die();
            } else {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/doctors/manage_categories?shop_id=' . $shop_id);
                die();
            }
        }
    }

    function manage_shop_banners() {

        $shop_id = $this->input->get_post('shop_id');

        $shop_banners = $this->db->get_where('vendor_shop_banners', ['shop_id' => $shop_id, 'status' => 1])->result();

        $this->data['shop_banners'] = $shop_banners;

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/manage_shop_banners', $this->data);

        $this->load->view('admin/includes/footer');
    }

    function add_shop_banner() {

        $title = $this->input->get_post('title');
    }

    function manage_work_hours($shop_id) {

        $this->data['title'] = 'Edit Shop Work Hours';

        $this->data['work_hours'] = $this->db->get_where('shop_work_hours', ['shop_id' => $shop_id])->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/manage_work_hours', $this->data);

        $this->load->view('admin/includes/footer');
    }

    private function work_hours($shop_id) {

        $work_hrs_data[] = array(
            'week_name' => 'Monday',
            'is_working_day' => 'Yes',
            'open_time' => '10:00:00',
            'close_time' => '20:00:00',
            'shop_id' => $shop_id,
            'status' => 1
        );

        $work_hrs_data[] = array(
            'week_name' => 'Tuesday',
            'is_working_day' => 'Yes',
            'open_time' => '10:00:00',
            'close_time' => '20:00:00',
            'shop_id' => $shop_id,
            'status' => 1
        );

        $work_hrs_data[] = array(
            'week_name' => 'Wednesday',
            'is_working_day' => 'Yes',
            'open_time' => '10:00',
            'close_time' => '20:00',
            'shop_id' => $shop_id,
            'status' => 1
        );

        $work_hrs_data[] = array(
            'week_name' => 'Thursday',
            'is_working_day' => 'Yes',
            'open_time' => '10:00',
            'close_time' => '20:00',
            'shop_id' => $shop_id,
            'status' => 1
        );

        $work_hrs_data[] = array(
            'week_name' => 'Friday',
            'is_working_day' => 'Yes',
            'open_time' => '10:00',
            'close_time' => '20:00',
            'shop_id' => $shop_id,
            'status' => 1
        );

        $work_hrs_data[] = array(
            'week_name' => 'Saturday',
            'is_working_day' => 'Yes',
            'open_time' => '10:00',
            'close_time' => '20:00',
            'shop_id' => $shop_id,
            'status' => 1
        );

        $work_hrs_data[] = array(
            'week_name' => 'Sunday',
            'is_working_day' => 'Yes',
            'open_time' => '10:00',
            'close_time' => '20:00',
            'shop_id' => $shop_id,
            'status' => 1
        );

        return $work_hrs_data;
    }

    public function insert_symptoms() {

        $data = [];
        $data['doctor_id'] = $this->input->post('doctor_id');
        $data['symptom_id'] = $this->input->post('symptom_id');
        // $data['sub_symptom_id'] = implode(',', ($this->input->post('sub_symptoms')));
        $data['status'] = 1;

        $insert = $this->db->insert('doctor_symptoms_subsymptoms', $data);
        if ($insert) {
            $this->session->set_flashdata('success_message', 'Symptoms  added Successfully');
            redirect('admin/doctors/manage_doctor_symptoms/' . $data['doctor_id']);
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, please try again');
            redirect('admin/doctors/manage_doctor_symptoms/' . $data['doctor_id']);
            die();
        }
    }

    public function edit_symptoms($id, $doctor_id) {
        $this->data['doctor_id'] = $doctor_id;
        $this->data['doctor_symptoms'] = $this->db->where('id', $id)->get('doctor_symptoms_subsymptoms')->row();
        $this->data['symptom'] = $this->doctor_manage_category_model->getSymptoms();
        $doctor_data = $this->db->where('doctor_id', $doctor_id)->get('doctor_symptoms_subsymptoms')->result();
        $this->data['doctor_data_symptom_ids'] = array_column($doctor_data, 'symptom_id');
        if (($key = array_search($this->data['doctor_symptoms']->symptom_id, $this->data['doctor_data_symptom_ids'])) !== false) {
            unset($this->data['doctor_data_symptom_ids'][$key]);
        }
        
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/doctor_update_symptoms', $this->data);
        $this->load->view('admin/includes/footer');
    }

    public function update_symptoms() {
        $data = [];
        $id = $this->input->post('id');
        $data['doctor_id'] = $this->input->post('doctor_id');
        $data['symptom_id'] = $this->input->post('symptom_id');
        // $data['sub_symptom_id'] = implode(',', ($this->input->post('sub_symptoms')));
        $data['status'] = $this->input->post('status');

        $this->db->where('id', $id);
        $update = $this->db->update('doctor_symptoms_subsymptoms', $data);
        if ($update) {
            $this->session->set_flashdata('success_message', 'Symptoms updated Successfully');
            redirect('admin/doctors/manage_doctor_symptoms/' . $data['doctor_id']);
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, please try again');
            redirect('admin/doctors/manage_doctor_symptoms/' . $data['doctor_id']);
            die();
        }
    }

    public function delete_doctor_symptom() {
        $id = $this->input->get_post('id');
        $doctor_id = $this->input->get_post('doctor_id');
        if ((isset($id)) && ($id != '')) {
            $this->db->where(['id' => $id, 'doctor_id' => $doctor_id]);
            $delete = $this->db->delete('doctor_symptoms_subsymptoms');
            if ($delete) {
                $this->session->set_flashdata('success_message', 'Deleted Successfully');
                redirect('admin/doctors/manage_doctor_symptoms/' . $doctor_id);
            } else {
                $this->session->set_flashdata('error_message', 'Please Try Again');
                redirect('admin/doctors/manage_doctor_symptoms/' . $doctor_id);
            }
        } else {
            redirect('admin/doctors/manage_doctor_symptoms/' . $doctor_id);
        }
    }

}
