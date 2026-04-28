<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Doctors_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        //load database libraryf
        $this->load->database();
    }

    function notificationCount($user_id)
    {
        $this->db->where("view_status", 0);
        $this->db->where("recieved_id", $user_id);
        $cnt = $this->db->count_all_results("doctor_notifications");
        return array('status' => TRUE, 'count' => $cnt);
    }

    function prescription($appointment_id)
    {


        $this->db->select('id');
        $this->db->where('appointment_id', $appointment_id);
        $this->db->where('prescription_type', 'prescription');
        $res = $this->db->get('patient_prescription')->row();

        if ($res) {
            $pres_id = $res->id;
            $this->db->where('patient_prescription_id', $pres_id);
            $prescription = $this->db->get('eprescription')->result();



            foreach ($prescription as $value) {
                $value->time_of_the_day = explode(',', $value->time_of_the_day);
            }


            $this->db->select("id,lab_test_name,lab_test_description,lab_test_created_at");
            $this->db->where('patient_prescription_id', $pres_id);
            $lab_Tests = $this->db->get('lab_tests')->result();

        }

        $this->db->where('appointment_id', $appointment_id);
        $this->db->where('prescription_type', 'prescription');
        $manual_prescription = $this->db->get('patient_prescription')->row();

        if ($manual_prescription->manual_prescription == "") {
            $manual_prescription = "";
        }
        else {
            $manual_prescription = base_url() . "uploads/prescription/" . $manual_prescription->manual_prescription;
        }


        if (!$prescription) {
            $prescription = array();
        }

        if (!$lab_Tests) {
            $lab_Tests = array();
        }
        $data = array('eprescription' => $prescription, 'manual_prescription' => $manual_prescription, 'lab_Tests' => $lab_Tests);

        return array('status' => TRUE, 'data' => $data);
    }



    function validateLocation($user_id, $latitude, $longitude, $location)
    {

        $distance = $this->db->where('id', 1)->get("admin")->row()->distance;


        $this->db->select("*,( 3959 * acos ( cos ( radians('" . $latitude . "') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('" . $longitude . "') ) + sin ( radians('" . $latitude . "') ) * sin( radians( latitude ) ) ) ) * 1.60934 AS distance");
        $this->db->having('distance<', $distance, false);
        $this->db->where("doctor_show_status", "active");
        $data = $this->db->get('doctors')->num_rows();
        // echo $this->db->last_query(); die;
        if ($data > 0) {
            $upd = array('home_location' => $location, 'lat' => $latitude, 'lng' => $longitude);
            $wr = array('id' => $user_id);
            $upddata = $this->db->update('users', $upd, $wr);
            return array('status' => TRUE, 'message' => "Location Validated");
        }
        else {
            return array('status' => FALSE, 'message' => "Currently we are not serving in this location");
        }
    }


    function getWelcomeScreens()
    {
        $this->db->order_by("id", "desc");
        $user_result = $this->db->get("welcome_page")->result();
        if (count($user_result) > 0) {
            foreach ($user_result as $value) {
                $value->app_image = base_url() . "uploads/welcome_page/" . $value->app_image;
            }
            return array("status" => TRUE, 'data' => $user_result);
        }
        else {
            return array("status" => FALSE, 'message' => "No data found");
        }

    }


    function userDashboard($user_id)
    {

        $this->db->where('id', $user_id);
        $home_location_row = $this->db->get('users')->row();
        if (!empty($home_location_row->home_location)) {
            $home_location = $home_location_row->home_location;
        }
        else {
            $home_location = "";
        }


        //  $distance = $this->db->where('id',1)->get("admin")->row()->distance;
        // $this->db->select("id,( 3959 * acos ( cos ( radians('".$latitude."') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('".$longitude."') ) + sin ( radians('".$latitude."') ) * sin( radians( latitude ) ) ) ) * 1.60934 AS distance");
        //                 $this->db->having('distance<',$distance, false);



        $this->db->select('doctors.*');
        $this->db->from('doctors');
        $this->db->join('doctor_subscriptions ds', 'ds.doctor_id = doctors.id');
        $this->db->where('ds.status', 'active');
        $this->db->where('ds.featured_status', 1);
        $this->db->where('ds.end_at >=', date('Y-m-d H:i:s'));
        $this->db->where("doctors.doctor_show_status", "active");
        $doctors_data = $this->db->get()->result();




        //  echo $this->db->last_query();die;
        //   print_r($doctors_data);die;
        $banner_array = [];
        foreach ($doctors_data as $doc_value) {
            $banners_rows = $this->db->where("doctor_id", $doc_value->id)->get("doctor_banners");
            //  

            if ($banners_rows->num_rows() > 0) {
                $banners_result = $banners_rows->row();

                $app_image = base_url() . "uploads/doctor_banners/" . $banners_result->app_image;

                $banner_array[] = array('id' => $banners_result->id, 'title' => $banners_result->title, 'app_image' => $banners_result->app_image, 'status' => $banners_result->status, 'banner_type' => $banners_result->banner_type, 'doctor_id' => $banners_result->doctor_id, 'category_id' => $banners_result->category_id, 'app_image' => $app_image);
            }

        }

        if (count($banner_array) > 0) {
            $banners = $banner_array;
        }
        else {
            $banners = array();
        }

        $this->db->order_by("id", "desc");
        $banners = $this->db->get("doctor_banners")->result();
        //echo $this->db->last_query(); die;
        if (count($banners) > 0) {
            foreach ($banners as $value) {
                $value->app_image = base_url() . "uploads/doctor_banners/" . $value->app_image;
            }
        }
        else {
            $banners = array();
        }


        $this->db->select("id,name,image");
        $this->db->order_by("priority", "ASC");
        $this->db->where('status', 1);
        $this->db->limit("15");
        $categories = $this->db->get("symptom")->result();


        if (count($categories) > 0) {
            foreach ($categories as $value) {
                $value->app_image = base_url() . "uploads/doctor_categories/" . $value->image;
            }
        }
        else {
            $categories = array();

        }


        //$table = "doctors";
        //  $this->db->select("id,hospital_name,hospital_image,doctor_name,doctor_image,designations,consultant_fee,( 3959 * acos ( cos ( radians('".$latitude."') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('".$longitude."') ) + sin ( radians('".$latitude."') ) * sin( radians( latitude ) ) ) ) * 1.60934 AS distance");
        // $this->db->having('distance<',$distance, false);
        // LEFT JOIN so ALL active doctors appear (subscribed OR not subscribed)
        $this->db->select('doctors.*');
        $this->db->from('doctors');
        $this->db->join('doctor_subscriptions ds', 'ds.doctor_id = doctors.id AND ds.status = \'active\'', 'left');
        $this->db->where("doctors.doctor_show_status", "active");
        $this->db->group_by('doctors.id');
        $this->db->order_by("doctors.id", "desc");
        $this->db->limit("6");
        $data = $this->db->get()->result();

        // print_r($data);die;
        if (count($data) > 0) {
            $available_doctors = [];
            foreach ($data as $value) {

                //     $this->db->select('round(AVG(rating),2) as rating,COUNT(user_id) as totalusers');
                //                             $this->db->where('vendor_id',$value->id);
                //   $value->doctor_reviews = $this->db->get('user_reviews')->row();

                $blue_tick = $value->blue_tick;

                $doctor_rating = $value->rating;
                $total_users_reviewed = $value->rating_count;

                // $doctor_review = $value->doctor_reviews; 

                $hospital_name = $value->hospital_name;
                $doctor_name = $value->doctor_name;
                $designations = $value->designations;
                //$hospital_image = base_url()."uploads/doctors/".$value->hospital_image;
                //$doctor_image = base_url()."uploads/doctors/".$value->doctor_image;

                if (!empty($value->doctor_image)) {
                    $hospital_image = base_url() . "uploads/doctors/" . $value->hospital_image;
                }
                else {
                    $doctor_image = base_url() . "uploads/profile-icon-3.png";
                }
                if (!empty($value->doctor_image)) {
                    $doctor_image = base_url() . "uploads/doctors/" . $value->doctor_image;
                }
                else {
                    $doctor_image = base_url() . "uploads/profile-icon-3.png";
                }

                $designations = $this->get_designation_names_csv($designations);
                $des_explode = explode(",", $designations);
                // print_r($des_explode);die;  
                $available_doctors[] = array('id' => $value->id, 'hospital_name' => $hospital_name, 'hospital_image' => $hospital_image, 'doctor_name' => $doctor_name, 'doctor_image' => $doctor_image, 'designations' => $designations, 'consultant_fee' => $value->consultant_fee, 'blue_tick' => $blue_tick, 'doctor_rating' => $doctor_rating, 'total_users_reviewed' => $total_users_reviewed);
            }
            if (count($available_doctors) > 0) {
                $available_doctors_list = $available_doctors;
            }
            else {
                $available_doctors_list = array();
            }
        }
        else {
            $available_doctors_list = array();
        }


        $this->db->select("id,link");
        $this->db->order_by("id", "desc");
        $data = $this->db->get("doctor_videos")->result();
        if (count($data) > 0) {
            $videos = $data;
        }
        else {
            $videos = array();
        }


        $this->db->order_by("id", "desc");
        $this->db->where("status", 1);
        $adslist = $this->db->get("ads")->result();
        //echo $this->db->last_query(); die;
        if (count($adslist) > 0) {
            foreach ($adslist as $value) {
                $value->app_image = base_url() . "uploads/doctor_banners/" . $value->app_image;
            }
        }
        else {
            $adslist = array();
        }

        $data = array('home_location' => $home_location, 'banners' => $banners, 'categories' => $categories, 'available_doctors' => $available_doctors_list, 'videos' => $videos, 'ads' => $adslist);

        return array('status' => TRUE, 'data' => $data);
    }

    function categories_list($status)
    {

        if ($status == 'home') {
            $table = "doctor_categories";
            $this->db->select("id,category_name,app_image");
            $this->db->order_by("id", "desc");
            $this->db->limit("9");
            $data = $this->db->get($table)->result();
            if (count($data) > 0) {
                foreach ($data as $value) {
                    $value->app_image = base_url() . "uploads/doctor_categories/" . $value->app_image;
                }
                $ar = array('status' => TRUE, 'data' => $data);
                return $ar;
            }
            else {
                $ar = array('status' => FALSE, 'message' => "No data found");
                return $ar;
            }
        //$sql = $this->db->query("select id,category_name,app_image from doctor_categories order by id desc LIMIT 9");
        }
        else if ($status == 'view_all') {
            $table = "doctor_categories";
            $this->db->select("id,category_name,app_image");
            $this->db->order_by("id", "desc");
            $data = $this->db->get($table)->result();
            if (count($data) > 0) {
                foreach ($data as $value) {
                    $value->app_image = base_url() . "uploads/doctor_categories/" . $value->app_image;
                }
                $ar = array('status' => TRUE, 'data' => $data);
                return $ar;
            }
            else {
                $ar = array('status' => FALSE, 'message' => "No data found");
                return $ar;
            }
        }

    }

    function doctor_videos_list()
    {

        $table = "doctor_videos";
        $this->db->select("id,link");
        $this->db->order_by("id", "desc");
        $data = $this->db->get($table)->result();
        if (count($data) > 0) {
            $ar = array('status' => TRUE, 'data' => $data);
            return $ar;
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    function doctors_banners_list()
    {
        $table = "doctor_banners";
        $this->db->select("*");
        $this->db->order_by("id", "desc");
        $data = $this->db->get($table)->result();
        if (count($data) > 0) {
            foreach ($data as $value) {
                $value->app_image = base_url() . "uploads/doctor_banners/" . $value->app_image;
            }
            $ar = array('status' => TRUE, 'data' => $data);
            return $ar;
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    function symptom_list()
    {
        $table = "symptom";
        $this->db->select("id,name,image");
        $this->db->order_by("priority", "asc");
        $this->db->where('status', 1);
        $data = $this->db->get($table)->result();
        if (count($data) > 0) {
            foreach ($data as $value) {
                $value->image = base_url() . "uploads/doctor_categories/" . $value->image;
            }
            $ar = array('status' => TRUE, 'data' => $data);
            return $ar;
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    function getConsultions()
    {
        $this->db->select("id,name,image");
        $this->db->where("consultation", 'yes');
        $this->db->where('status', 1);
        $this->db->order_by("priority", "asc");
        $data = $this->db->get("symptom")->result();
        if (count($data) > 0) {
            foreach ($data as $value) {
                $value->image = base_url() . "uploads/doctor_categories/" . $value->image;
            }
            return array('status' => TRUE, 'data' => $data);
        }
        else {
            return array('status' => FALSE, 'message' => "No data found");
        }
    }

    function searchConsultionsSymptoms($keyword)
    {
        $this->db->select("id,name,image");
        $this->db->where("consultation", 'yes');
        $this->db->where('status', 1);
        $this->db->like('name', $keyword, 'both', false);
        $this->db->order_by("priority", "asc");
        $data = $this->db->get("symptom")->result();
        if (count($data) > 0) {
            foreach ($data as $value) {
                $value->image = base_url() . "uploads/doctor_categories/" . $value->image;
            }
            return array('status' => TRUE, 'data' => $data);
        }
        else {
            return array('status' => FALSE, 'message' => "No data found");
        }
    }

    function sub_symptom_list($symptom_id)
    {

        $where = array('cat_id' => $symptom_id, 'status' => 1);
        $table = "doctor_sub_categories";
        $this->db->select("id,sub_category_name,app_image,description");
        $this->db->where($where);
        $this->db->order_by("id", "desc");
        $data = $this->db->get($table)->result();
        if (count($data) > 0) {
            foreach ($data as $value) {
                $value->app_image = base_url() . "uploads/doctor_sub_categories/" . $value->app_image;
            }
            $ar = array('status' => TRUE, 'data' => $data);
            return $ar;
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    //doctors list
    function doctors_list()
    {

        //   $distance = $this->db->where('id',1)->get("admin")->row()->distance;
        // $this->db->select("id,hospital_name,hospital_image,doctor_name,doctor_image,designations,consultant_fee,experience,( 3959 * acos ( cos ( radians('".$latitude."') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('".$longitude."') ) + sin ( radians('".$latitude."') ) * sin( radians( latitude ) ) ) ) * 1.60934 AS distance");
        //     $this->db->having('distance<',$distance, false);
        // LEFT JOIN so ALL active doctors appear (subscribed OR not subscribed)
        $this->db->select('doctors.*');
        $this->db->from('doctors');
        $this->db->join('doctor_subscriptions ds', 'ds.doctor_id = doctors.id AND ds.status = \'active\'', 'left');
        $this->db->where("doctors.doctor_show_status", "active");
        $this->db->group_by('doctors.id');
        $this->db->order_by("doctors.id", "desc");
        $data = $this->db->get()->result();

        // print_r($data);die;

        if (count($data) > 0) {
            $array = [];
            foreach ($data as $value) {

                //                             $this->db->select('round(AVG(rating),2) as rating,COUNT(user_id) as totalusers');
                //                             $this->db->where('vendor_id',$value->id);
                $value->doctor_reviews = $this->db->get('user_reviews')->row();




                $blue_tick = $value->blue_tick;

                $doctor_rating = $value->rating;
                $total_users_reviewed = $value->rating_count;

                $doctor_review = $value->doctor_reviews;
                $experience = $value->experience;
                $hospital_name = $value->hospital_name;
                $doctor_name = $value->doctor_name;
                $designations = $value->designations;

                if (!empty($value->hospital_image)) {
                    $hospital_image = base_url() . "uploads/doctors/" . $value->hospital_image;
                }
                else {
                    $hospital_image = base_url() . "uploads/profile-icon-3.png";
                }
                if (!empty($value->doctor_image)) {
                    $doctor_image = base_url() . "uploads/doctors/" . $value->doctor_image;
                }
                else {
                    $doctor_image = base_url() . "uploads/profile-icon-3.png";
                }

                $designations = $this->get_designation_names_csv($designations);
                $array[] = array('id' => $value->id, 'hospital_name' => $hospital_name, 'hospital_image' => $hospital_image, 'doctor_name' => $doctor_name, 'doctor_image' => $doctor_image, 'designations' => $designations, 'consultant_fee' => $value->consultant_fee, 'experience' => $experience, 'blue_tick' => $blue_tick, 'doctor_rating' => $doctor_rating, 'total_users_reviewed' => $total_users_reviewed);


            }
            if (count($array) > 0) {
                $ar = array('status' => TRUE, 'data' => $array);
                return $ar;
            }
            else {
                $ar = array('status' => FALSE, 'data' => $data);
                return $ar;
            }
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }

    }


    function get_designation_names_csv($designations_ids)
    {
        $designations_ids_ar = explode(",", $designations_ids);

        $designations_array = [];

        foreach ($designations_ids_ar as $d_id) {
            $this->db->select("name");
            $this->db->where("id", $d_id);
            $designations_array[] = $this->db->get("designations")->row()->name;
        }
        $designations_implode = implode(',', $designations_array);

        return $designations_implode;

    }

    function online_appointment($appointment_id, $razerpay_order_id, $transaction_id)
    {
        $appointment_row = $this->db->where(array('id' => $appointment_id))->get('online_doctor_appointments');
        if ($appointment_row->num_rows() > 0) {
            $row = $appointment_row->row();
            $array = array('patient_id' => $row->patient_id, 'doctor_id' => $row->doctor_id, 'date' => $row->date, 'time_slot_name' => $row->time_slot_name, 'time_slot_value' => $row->time_slot_value, 'patient_name' => $row->patient_name, 'patient_mobile' => $row->patient_mobile, 'patient_age' => $row->patient_age, 'patient_gender' => $row->patient_gender, 'patient_visiting_purpose' => $row->patient_visiting_purpose, 'consultation_fee' => $row->consultation_fee, 'order_id' => $row->order_id, 'payment_mode' => 'ONLINE', 'order_id' => $razerpay_order_id, 'transaction_id' => $transaction_id, 'appointment_type' => $row->type, 'created_date' => date('Y-m-d H:i:s'));

            $ins = $this->db->insert("doctor_appointments", $array);

            if ($ins) {
                $appointment_id = $this->db->insert_id();
                $message = "You have new appointment";
                $this->doNotifications($appointment_id, $row->patient_id, $row->doctor_id, $message);

                $message = "Your appointment Created successfully";
                $this->doNotifications($appointment_id, $row->doctor_id, $row->patient_id, $message);

                $this->db->select("id,first_name");
                $this->db->where("id", $row->patient_id);
                $data_user = $this->db->get("users")->row();
                $first_name = $data_user->first_name;

                $this->db->select("id,doctor_name");
                $this->db->where("id", $row->doctor_id);
                $this->db->where("doctor_show_status", 'active');
                $data_doctor = $this->db->get("doctors")->row();
                $doctor_name = $data_doctor->doctor_name;
                //echo $this->db->last_query(); die;
                if ($row->time_slot_name == 'morning') {
                    $time_slot_value1 = $row->time_slot_value . " :00 AM";
                }
                else if ($row->time_slot_name == 'afternoon') {
                    $time_slot_value1 = $row->time_slot_value . " :00 PM";
                }
                elseif ($row->time_slot_name == 'evening') {
                    $time_slot_value1 = $row->time_slot_value . " :00 PM";
                }
                $date = date("d M,Y", strtotime($row->date));


                $otp_message = "Dear " . $row->patient_name . " your booking no." . $appointment_id . " is successfully placed, awaiting for doctor confirmation. Thanks & Regards...! DOCTTO";
                $template_id = '1407168691886113081';

                $this->User->send_message($otp_message, $row->patient_mobile, $template_id);


                return array('status' => TRUE, 'message' => 'Appointment Success', 'first_name' => $first_name, 'doctor_name' => $doctor_name, 'patient_name' => $row->patient_name, 'date' => $date, 'time_slot_value' => $row->time_slot_value);
            }
        }
        else {
            $ar = array('status' => FALSE, 'message' => "Something went wrong, please try again");
            return $ar;
        }

    }


    //doctors search option list
    function doctors_full_details_list($title, $doctor_id)
    {
        $table = "doctors";
        $this->db->select("*");
        $this->db->select('doctors.*');
        $this->db->from('doctors');
        $this->db->join('doctor_subscriptions ds', 'ds.doctor_id = doctors.id');
        $this->db->where('ds.status', 'active');
        $this->db->where('ds.featured_status', 1);
        $this->db->where('ds.end_at >=', date('Y-m-d H:i:s'));
        $this->db->where('doctors.id', $doctor_id);
        $this->db->where("doctors.doctor_show_status", 'active');
        $this->db->group_start();
        $this->db->like('doctors.doctor_name', $title, 'both', false);
        $this->db->or_like('doctors.designations', $title);
        $this->db->group_end();
        $this->db->order_by("doctors.id", "desc");
        $data = $this->db->get()->result();
        //echo $this->db->last_query(); die;
        if (count($data) > 0) {


            foreach ($data as $value) {

                $value->blue_tick = $value->blue_tick;

                $value->doctor_rating = $value->rating;
                $value->total_users_reviewed = $value->rating_count;
                // $value->doctor_id = $this->db->select('review','rating')->where('vendor_id',$data->id)->get('user_reviews')->row();
                $value->hospital_image = base_url() . "uploads/doctors/" . $value->hospital_image;
                $value->doctor_image = base_url() . "uploads/doctors/" . $value->doctor_image;
                $value->designations = $this->get_designation_names_csv($value->designations);
            }
            $ar = array('status' => TRUE, 'data' => $data);
            return $ar;
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    //doctors details
    function doctors_details($doctor_id)
    {

        $this->db->select('doctors.*');
        $this->db->from('doctors');
        $this->db->where('doctors.id', $doctor_id);
        $this->db->where("doctors.doctor_show_status", 'active');
        $data = $this->db->get()->result();



        if (count($data) > 0) {
            foreach ($data as $value) {


                //                             $this->db->select('round(AVG(rating),2) as rating,COUNT(user_id) as totalusers,review');
                //                             $this->db->where('vendor_id',$value->id);
                $value->doctor_reviews = $this->db->get('user_reviews')->row();


                $value->blue_tick = $value->blue_tick;

                $value->doctor_rating = $value->rating;
                $value->total_users_reviewed = $value->rating_count;


                $this->db->select('user_id');
                $this->db->where('vendor_id', $value->id);
                $user_details = $this->db->get('user_reviews')->result();
                foreach ($user_details as $ud) {
                    $this->db->select('image,first_name');
                    $this->db->where('id', $ud->user_id);
                    $user = $this->db->get('users')->row();
                    if ($user->image != '') {
                        $ud->image = base_url() . "uploads/users/" . $user->image;
                    }
                    else {
                        $ud->image = base_url() . "uploads/users/" . $user->image;
                    }

                    $ud->first_name = $user->first_name;
                }



                $value->user_profile = $user_details;


                $value->hospital_image = base_url() . "uploads/doctors/" . $value->hospital_image;
                $value->doctor_image = base_url() . "uploads/doctors/" . $value->doctor_image;
                $value->designations = $this->get_designation_names_csv($value->designations);

            }
            $ar = array('status' => TRUE, 'data' => $data);
            return $ar;
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    // Date list
    function date_list()
    {
        $cdate = date("Y-m-d");
        $end_date = date("Y-m-d", strtotime("$cdate +20 days"));
        $begin = new DateTime($cdate);
        $end = new DateTime($end_date);

        $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
        $dates_list = [];
        foreach ($daterange as $date) {
            $list = $date->format("Y-m-d");
            $dates_list[] = array('dates' => $list);
        }

        $ar = array('status' => TRUE, 'data' => $dates_list);
        return $ar;
    }

    function doctors_page($cat_id, $title)
    {

        $table = "doctor_admin_comission";
        $this->db->select("id,doctor_id");
        $this->db->where('cat_id', $cat_id);
        $this->db->where('status', 1);
        $this->db->order_by("id", "desc");
        $data = $this->db->get($table)->result();

        if (count($data) > 0) {
            $array = [];
            foreach ($data as $value) {
                $doctor_id = $value->doctor_id;

                $table1 = "doctors";
                $this->db->select("id,hospital_name,hospital_image,doctor_name,doctor_image,designations,consultant_fee");
                $this->db->where("id", $doctor_id);
                $this->db->like('doctor_name', $title);
                $this->db->or_like('designations', $title);
                $data1 = $this->db->get($table1)->row();
                $hospital_name = $data1->hospital_name;
                $hospital_image = base_url() . "uploads/doctors/" . $data1->hospital_image;
                $doctor_name = $data1->doctor_name;
                $doctor_image = base_url() . "uploads/doctors/" . $data1->doctor_image;
                $designations = $data1->designations;
                $consultant_fee = $data1->consultant_fee;

                $des_explode = explode(",", $designations);
                // print_r($des_explode);die;  
                $designations_array = [];
                foreach ($des_explode as $ex_des_vl) {
                    $this->db->where("id", $ex_des_vl);
                    $ex_des_vl_row = $this->db->get("designations")->row();
                    $designations_array[] = $ex_des_vl_row->name;
                }
                $designations_implode = implode(',', $designations_array);

                $this->db->get($table1)->num_rows();
                // if(count($data1)>0){
                $array[] = array('id' => $value->id, 'hospital_name' => $hospital_name, 'hospital_image' => $hospital_image, 'doctor_name' => $doctor_name, 'doctor_image' => $doctor_image, 'designations' => $designations_implode, 'consultant_fee' => $consultant_fee);
            //}

            }

            if (count($array) > 0) {
                $ar = array('status' => TRUE, 'data' => $array);
                return $ar;
            }
            else {
                $ar = array('status' => FALSE, 'message' => "No data found");
                return $ar;
            }
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    function searchAvailableDoctors($keyword)
    {
        // $distance = $this->db->where('id',1)->get("admin")->row()->distance;
        // $this->db->select("id,experience,hospital_name,hospital_image,doctor_name,doctor_image,designations,consultant_fee,( 3959 * acos ( cos ( radians('".$latitude."') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('".$longitude."') ) + sin ( radians('".$latitude."') ) * sin( radians( latitude ) ) ) ) * 1.60934 AS distance");
        //                 $this->db->having('distance<',$distance, false);
        // LEFT JOIN so ALL active doctors appear (subscribed OR not)
        $this->db->select('doctors.*');
        $this->db->from('doctors');
        $this->db->join('doctor_subscriptions ds', 'ds.doctor_id = doctors.id AND ds.status = \'active\'', 'left');
        $this->db->group_start();
        $this->db->like('doctors.hospital_name', $keyword, 'both', false);
        $this->db->or_like('doctors.doctor_name', $keyword, 'both', false);
        $this->db->group_end();
        $this->db->where("doctors.doctor_show_status", "active");
        $this->db->group_by('doctors.id');
        $this->db->order_by("doctors.id", "desc");
        $data = $this->db->get()->result();


        if (count($data) > 0) {
            $array = [];
            foreach ($data as $value) {


                //                         $this->db->select('round(AVG(rating),2) as rating,COUNT(user_id) as totalusers');
                //                         $this->db->where('vendor_id',$value->id);
                //   $value->doctor_reviews = $this->db->get('user_reviews')->row(); 


                // echo $this->db->last_query();die;



                // $doctor_review =  $value->doctor_reviews;




                $blue_tick = $value->blue_tick;

                $doctor_rating = $value->rating;
                $total_users_reviewed = $value->rating_count;

                $hospital_name = $value->hospital_name;
                $doctor_name = $value->doctor_name;
                $designations = $value->designations;
                $hospital_image = base_url() . "uploads/doctors/" . $value->hospital_image;
                $doctor_image = base_url() . "uploads/doctors/" . $value->doctor_image;
                $designations = $this->get_designation_names_csv($designations);
                $array[] = array('id' => $value->id, 'experience' => $value->experience, 'hospital_name' => $hospital_name, 'hospital_image' => $hospital_image, 'doctor_name' => $doctor_name, 'doctor_image' => $doctor_image, 'designations' => $designations, 'consultant_fee' => $value->consultant_fee, 'blue_tick' => $blue_tick, 'doctor_rating' => $doctor_rating, 'total_users_reviewed' => $total_users_reviewed);




            }
            if (count($array) > 0) {
                $ar = array('status' => TRUE, 'data' => $array);
                return $ar;
            }
            else {
                $ar = array('status' => FALSE, 'data' => $data);
                return $ar;
            }
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    function symptomWiseDoctors($symptom_id)
    {
        $this->db->where("symptom_id", $symptom_id);
        $doctor_ids_list = $this->db->get("doctor_symptoms_subsymptoms")->result();

        if (empty($doctor_ids_list)) {
            return array('status' => FALSE, 'message' => "No data found");
        }

        $doct_id = [];
        foreach ($doctor_ids_list as $doc_value) {
            $doct_id[] = $doc_value->doctor_id;
        }
        $doctor_ids = implode(",", $doct_id);

        $now = date('Y-m-d H:i:s');
        // Changed to LEFT JOIN to show doctors even if they don't have a featured active subscription
        $data = $this->db->query("select doctors.id,doctors.hospital_name,doctors.hospital_image,doctors.doctor_name,doctors.doctor_image,doctors.designations,doctors.consultant_fee,doctors.blue_tick,doctors.rating,doctors.rating_count from doctors left join doctor_subscriptions ds on ds.doctor_id = doctors.id and ds.status='active' and ds.featured_status=1 and ds.end_at >= '$now' where doctors.doctor_show_status='active' and find_in_set(doctors.id,'" . $doctor_ids . "')")->result();


        if (count($data) > 0) {
            $array = [];
            foreach ($data as $value) {

                $array[] = array(
                    'id' => $value->id,
                    'hospital_name' => $value->hospital_name,
                    'hospital_image' => base_url() . "uploads/doctors/" . $value->hospital_image,
                    'doctor_name' => $value->doctor_name,
                    'doctor_image' => base_url() . "uploads/doctors/" . $value->doctor_image,
                    'designations' => $this->get_designation_names_csv($value->designations),
                    'consultant_fee' => $value->consultant_fee,
                    'blue_tick' => $value->blue_tick,
                    'doctor_rating' => $value->rating,
                    'total_users_reviewed' => $value->rating_count
                );

            }
            return array('status' => TRUE, 'data' => $array);
        }
        else {
            return array('status' => FALSE, 'message' => "No data found");
        }

    }

    function symptomSubsymptomWiseDoctors($symptom_id, $subsymptom_id = null)
    {
        if ($subsymptom_id != '') {
            $where = "find_in_set('" . $subsymptom_id . "',sub_symptom_id) and status=1 and symptom_id=" . $symptom_id;
        }
        else {
            $where = "status=1 and symptom_id=" . $symptom_id;
        }



        $doctor_ids_list = $this->db->where($where)->get("doctor_symptoms_subsymptoms")->result();

        if (empty($doctor_ids_list)) {
            return array('status' => FALSE, 'message' => "No data found");
        }

        $doct_id = [];
        foreach ($doctor_ids_list as $doc_value) {
            $doct_id[] = $doc_value->doctor_id;
        }
        $doctor_ids = implode(",", $doct_id);

        $now = date('Y-m-d H:i:s');
        // Changed to LEFT JOIN to show doctors even if they don't have a featured active subscription
        $data = $this->db->query("select doctors.id,doctors.hospital_name,doctors.hospital_image,doctors.doctor_name,doctors.doctor_image,doctors.designations,doctors.consultant_fee,doctors.blue_tick,doctors.rating,doctors.rating_count from doctors left join doctor_subscriptions ds on ds.doctor_id = doctors.id and ds.status='active' and ds.featured_status=1 and ds.end_at >= '$now' where doctors.doctor_show_status='active' and find_in_set(doctors.id,'" . $doctor_ids . "')")->result();

        if (count($data) > 0) {
            $array = [];
            foreach ($data as $value) {
                $array[] = array(
                    'id' => $value->id,
                    'hospital_name' => $value->hospital_name,
                    'hospital_image' => base_url() . "uploads/doctors/" . $value->hospital_image,
                    'doctor_name' => $value->doctor_name,
                    'doctor_image' => base_url() . "uploads/doctors/" . $value->doctor_image,
                    'designations' => $this->get_designation_names_csv($value->designations),
                    'consultant_fee' => $value->consultant_fee,
                    'blue_tick' => $value->blue_tick,
                    'doctor_rating' => $value->rating,
                    'total_users_reviewed' => $value->rating_count
                );
            }
            return array('status' => TRUE, 'data' => $array);
        }
        else {
            return array('status' => FALSE, 'message' => "No data found");
        }
    }

    function initiatePhonephePayment($total_amount, $patient_id, $doctor_id, $date, $time_slot_name, $time_slot_value, $patient_name, $patient_mobile, $patient_age, $patient_gender, $patient_visiting_purpose, $type)
    {
        // Load PhonePe OAuth Service
        $this->load->library('PhonePeOAuthService');


        $table = "doctor_appointments";
        $this->db->select("*");
        $this->db->where('date', $date);
        $this->db->where('time_slot_name', $time_slot_name);
        $this->db->where('time_slot_value', $time_slot_value);
        $this->db->where('doctor_status!=', 'reject');
        $this->db->order_by("id", "desc");
        $data1 = $this->db->get($table)->result();
        if (!empty($data1)) {
            return array('status' => FALSE, 'message' => 'Slot already Booked');
        }

        // Insert appointment record
        $data = array('patient_id' => $patient_id, 'doctor_id' => $doctor_id, 'date' => $date, 'time_slot_name' => $time_slot_name, 'time_slot_value' => $time_slot_value, 'patient_name' => $patient_name, 'patient_mobile' => $patient_mobile, 'patient_age' => $patient_age, 'patient_gender' => $patient_gender, 'patient_visiting_purpose' => $patient_visiting_purpose, 'consultation_fee' => $total_amount, 'type' => $type);

        $res = $this->db->insert("online_doctor_appointments", $data);
        //echo $this->db->last_query();die;
        if ($res) {
            $order_id = $this->db->insert_id();
            $merchant_transaction_id = 'APT_' . $order_id . '_' . time();
            $amount_in_paise = intval($total_amount * 100);

            // Step 1: Get OAuth Bearer Token (Standard Checkout Integration)
            $token_result = $this->phonepeoauthservice->getBearerToken();
            if (!$token_result['status']) {
                return array(
                    'status' => FALSE,
                    'message' => 'Failed to get OAuth token: ' . ($token_result['message'] ?? 'Unknown error'),
                    'order_id' => $order_id
                );
            }

            // Step 2: Create SDK Order Token using OAuth token
            // Set order expiry to 30 minutes (1800 seconds)
            $expireAfter = 1800;

            // Optional: Add metadata for tracking
            $metaInfo = array(
                'udf1' => 'appointment_booking',
                'udf2' => 'patient_id_' . $patient_id,
                'udf3' => 'doctor_id_' . $doctor_id,
                'udf4' => 'appointment_type_' . $type
            );

            $order_result = $this->phonepeoauthservice->createSDKOrder(
                $merchant_transaction_id,
                $amount_in_paise,
                $token_result['accessToken'] ?? $token_result['access_token'],
                $expireAfter,
                $metaInfo
            );

            if (!$order_result['status']) {
                return array(
                    'status' => FALSE,
                    'message' => 'Failed to create SDK order: ' . ($order_result['message'] ?? 'Unknown error'),
                    'order_id' => $order_id
                );
            }

            // Update appointment record with transaction details
            $this->db->where('id', $order_id);
            $this->db->update('online_doctor_appointments', array(
                'phonepe_transaction_id' => $merchant_transaction_id,
                'phonepe_order_id' => $order_result['orderId'],
                'phonepe_order_token' => $order_result['token']
            ));

            // Return order details with PhonePe SDK configuration
            return array(
                'status' => TRUE,
                'order_id' => $order_id,
                'transaction_id' => $merchant_transaction_id,
                'amount' => $total_amount,
                'phonepe_config' => array(
                    'orderId' => $order_result['orderId'],
                    'token' => $order_result['token'],
                    'merchantOrderId' => $merchant_transaction_id,
                    'amount' => $amount_in_paise,
                    'state' => $order_result['state'] ?? 'PENDING',
                    'expireAt' => $order_result['expireAt'] ?? null,
                    'merchantId' => defined('PHONEPE_CLIENT_ID') ? PHONEPE_CLIENT_ID : 'M1Y5YWMA86HR'
                )
            );
        }
        else {
            return FALSE;
        }
    }





    function razerpayOrderidAppointment($patient_id, $doctor_id, $date, $time_slot_name, $time_slot_value, $patient_name, $patient_mobile, $patient_email, $patient_age, $patient_gender, $patient_visiting_purpose, $consultation_fee, $order_id, $razorpay_keyid, $type)
    {

        $table = "doctor_appointments";
        $this->db->select("*");
        $this->db->where('date', $date);
        $this->db->where('time_slot_name', $time_slot_name);
        $this->db->where('time_slot_value', $time_slot_value);
        $this->db->where('doctor_status!=', 'reject');
        $this->db->order_by("id", "desc");
        $data1 = $this->db->get($table)->result();
        if (count($data1) > 0) {
            return array('status' => FALSE, 'message' => 'Slot already Booked');
        }
        if ($order_id != '') {
            $order_id = $order_id;
        }
        else {
            $order_id = '';
        }
        $table = "doctor_appointments";
        $data = array('patient_id' => $patient_id, 'doctor_id' => $doctor_id, 'date' => $date, 'time_slot_name' => $time_slot_name, 'time_slot_value' => $time_slot_value, 'patient_name' => $patient_name, 'patient_mobile' => $patient_mobile, 'patient_email' => $patient_email, 'patient_age' => $patient_age, 'patient_gender' => $patient_gender, 'patient_visiting_purpose' => $patient_visiting_purpose, 'appointment_type'=>$type,'consultation_fee' => $consultation_fee, 'order_id' => $order_id, 'type' => $type);

        $res = $this->db->insert("doctor_appointments", $data);
        // echo $this->db->last_query();die;

        if ($res) {
            $appointment_id = $this->db->insert_id();

            return array('status' => TRUE, 'appointment_id' => $appointment_id, 'razerpay_order_id' => $order_id, 'razorpay_keyid' => $razorpay_keyid);
        }

        else {
            return FALSE;
        }
    }


    function doctor_appointments($patient_id, $doctor_id, $date, $time_slot_name, $time_slot_value, $patient_name, $patient_mobile, $patient_email, $patient_age, $patient_gender, $patient_visiting_purpose, $consultation_fee, $appointment_type)
    {

        //                      $this->db->where("id",$consultation_fees_id);
        // $consultation_data = $this->db->count_all_results("consultation_fees");
        // if($consultation_data==0)
        // {
        //      return array('status'=>FALSE,'message'=>'Please select Messaging or Voice Call or Video Call '); 
        // }

        $this->db->where('doctor_show_status', 'active');
        $chk = $this->db->count_all_results('doctors');
        if ($chk == 0) {
            return array('status' => FALSE, 'message' => 'Doctor is not available');
        }
        $table = "doctor_appointments";
        $this->db->select("*");
        $this->db->where('date', $date);
        $this->db->where('time_slot_name', $time_slot_name);
        $this->db->where('time_slot_value', $time_slot_value);
        $this->db->where('doctor_status!=', 'reject');
        $this->db->order_by("id", "desc");
        $data1 = $this->db->get($table)->result();
        if (count($data1) > 0) {
            return array('status' => FALSE, 'message' => 'Slot already Booked');
        }

        $table = "doctor_appointments";
        $data = array('patient_id' => $patient_id, 'doctor_id' => $doctor_id, 'date' => $date, 'time_slot_name' => $time_slot_name, 'time_slot_value' => $time_slot_value, 'patient_name' => $patient_name, 'patient_mobile' => $patient_mobile, 'patient_email' => $patient_email, 'patient_age' => $patient_age, 'patient_gender' => $patient_gender, 'patient_visiting_purpose' => $patient_visiting_purpose, 'consultation_fee' => $consultation_fee, 'appointment_type' => $appointment_type);

        $res = $this->db->insert("doctor_appointments", $data);

        // echo $this->db->last_query();die;


        // echo $this->db->last_query(); die;
        if ($res) {

            $appointment_id = $this->db->insert_id();
            $message = "You have new appointment";




            $table_user = "users";
            $this->db->select("id,first_name");
            $this->db->where("id", "$patient_id");
            $data_user = $this->db->get($table_user)->row();
            $first_name = $data_user->first_name;

            $table_doctor = "doctors";
            $this->db->select("id,doctor_name");
            $this->db->where("id", $doctor_id);
            $this->db->where("doctor_show_status", 'active');
            $data_doctor = $this->db->get($table_doctor)->row();
            $doctor_name = $data_doctor->doctor_name;
            //echo $this->db->last_query(); die;
            /*if($time_slot_name=='morning'){
             $time_slot_value1=$time_slot_value." :00 AM";
             }   
             else if($time_slot_name=='afternoon'){
             $time_slot_value1=$time_slot_value." :00 PM";
             } 
             elseif ($time_slot_name=='evening') {
             $time_slot_value1=$time_slot_value." :00 PM";
             }*/
            $message1 = "You have new appointment";
            $this->doNotifications($appointment_id, $doctor_id, $patient_id, $message1);
            $message = "Dear " . $patient_name . " your booking no." . $appointment_id . " is successfully placed, awaiting for doctor confirmation. Thanks & Regards...! DOCTTO";
            $this->doNotifications($appointment_id, $patient_id, $doctor_id, $message);



            $otp_message = "Dear " . $patient_name . " your booking no." . $appointment_id . " is successfully placed, awaiting for doctor confirmation. Thanks & Regards...! DOCTTO";
            $template_id = '1407168691886113081';

            $this->User->send_message($otp_message, $patient_mobile, $template_id);

            $date = date("d M,Y", strtotime($date));
            return array('status' => TRUE, 'message' => 'Appointment Success', 'first_name' => $first_name, 'doctor_name' => $doctor_name, 'patient_name' => $patient_name, 'date' => $date, 'time_slot_value' => $time_slot_value);
        }

        else {
            return FALSE;
        }
    }

    function consultationFees()
    {
        $this->db->order_by("id", "asc");
        $data = $this->db->get("consultation_fees")->result();
        if (count($data) > 0) {
            foreach ($data as $value) {
                if ($value->icon != '') {
                    $value->icon = base_url() . "uploads/icons/" . $value->icon;
                }
            }
            return array("status" => TRUE, "data" => $data);
        }
        else {
            return array("status" => FALSE, "message" => "No Data Found");
        }

    }

    function doNotifications($appointment_id, $sender_id, $recieved_id, $message)
    {
        $title = "New Appointment";
        $date = date('Y-m-d');
        $doc_array = array('appointment_id' => $appointment_id, 'sender_id' => $sender_id, 'recieved_id' => $recieved_id, 'message' => $message, 'created_date' => $date, 'created_at' => time(), 'title' => $title);
        $ins = $this->db->insert("doctor_notifications", $doc_array);
        if ($ins) {
            return TRUE;
        }
    }

    function todayDoctorNot($user_id)
    {
        $date = date("Y-m-d");
        $this->db->select("id,message,title,created_at,created_date");
        $this->db->where("recieved_id", $user_id);
        $this->db->where("created_date", $date);
        $this->db->order_by("id", "desc");
        $result = $this->db->get('doctor_notifications')->result();
        //echo $this->db->last_query(); die;
        if (count($result) > 0) {
            foreach ($result as $value) {
                $value->created_at = date('h:s A d M, Y', $value->created_at);
            }

        }
        else {
            $result = array();
        }


        return $result;
    }

    function get_doctor_notifications($user_id)
    {

        $todaydoctor = $this->todayDoctorNot($user_id);

        $date = date("Y-m-d");
        $this->db->select("id,message,title,created_at,created_date");
        $this->db->where("recieved_id", $user_id);
        $this->db->where("created_date!=", $date);
        $result = $this->db->get('doctor_notifications')->result();
        //echo $this->db->last_query(); die;
        if (count($result) > 0) {
            foreach ($result as $value) {
                $value->created_at = date('h:s A d M, Y', $value->created_at);
            }

            $previous = $result;
        }
        else {
            $previous = array();
        }
        $data = array('today' => $todaydoctor, 'previous' => $previous);
        return array('status' => TRUE, 'data' => $data);

    }

    function updateNotifications($user_id)
    {
        $update = $this->db->update("doctor_notifications", array('view_status' => 1), array('recieved_id' => $user_id));
        if ($update) {
            return array('status' => TRUE, 'message' => "Notification Updated");
        }
        else {
            return array('status' => FALSE, 'message' => "Server might be problem, please try again later");
        }
    }

    function appointment_status($user_id, $status)
    {
        $this->cancelledTheAppointment($status);
        $table = "doctor_appointments";

        // Fetch offline appointments
        $this->db->select("id, doctor_id, doctor_status, date, time_slot_name, time_slot_value, rejected_by, reason, comments, patient_name, patient_mobile, patient_age, patient_gender, consultation_fee, appointment_type, 'offline' as source");
        if ($status == 'active') {
            // "pending" tab: show both "active" and "pending" statuses
            $this->db->group_start();
            $this->db->where('doctor_status', 'active');
            $this->db->or_where('doctor_status', 'pending');
            $this->db->group_end();
        }
        else {
            $this->db->where('doctor_status', $status);
        }
        $this->db->where("patient_id", $user_id);
        $this->db->order_by("id", "desc");
        $offline_data = $this->db->get($table)->result();

        // Fetch ONLY from doctor_appointments table.
        // Reason: When PhonePe payment is verified, we INSERT into doctor_appointments
        // (with doctor_status='active'). So all appointments (online & offline) are in
        // doctor_appointments. Fetching from online_doctor_appointments causes DUPLICATES.
        $online_data = [];

        // EXCEPTION: For 'completed' status only, also fetch online appointments
        // that were NOT copied to doctor_appointments (edge case / old data).
        if ($status == 'completed') {
            // Get IDs already fetched from doctor_appointments to avoid duplicates
            $existing_ids = array_column(array_map(function ($v) {
                return (array)$v;
            }, $offline_data), 'id');

            $this->db->select("oda.id, oda.doctor_id, oda.payment_status as doctor_status, oda.date, oda.time_slot_name, oda.time_slot_value, '' as rejected_by, '' as reason, '' as comments, oda.patient_name, oda.patient_mobile, oda.patient_age, oda.patient_gender, oda.consultation_fee, oda.type as appointment_type, 'online' as source");
            $this->db->from('online_doctor_appointments oda');
            $this->db->where('oda.patient_id', $user_id);
            $this->db->group_start();
            $this->db->where('oda.payment_status', 'completed');
            $this->db->or_where('oda.payment_status', 'COMPLETED');
            $this->db->group_end();
            // Exclude ones already present in doctor_appointments via phonepe_transaction_id
            $this->db->join('doctor_appointments da', 'da.patient_id = oda.patient_id AND da.date = oda.date AND da.time_slot_value = oda.time_slot_value AND da.doctor_id = oda.doctor_id', 'left');
            $this->db->where('da.id IS NULL', null, false);
            $this->db->order_by("oda.id", "desc");
            $online_data = $this->db->get()->result();
        }

        $data = array_merge($offline_data, $online_data);


        if (count($data) > 0) {
            $array = [];
            foreach ($data as $value) {
                $orig_status = $value->doctor_status;
                if ($orig_status == 'active' || $orig_status == 'pending') {
                    $status_text = "Waiting for Doctor Acceptancy";
                }
                elseif ($orig_status == 'accept') {
                    $status_text = "Doctor Accepted your Booking";
                }
                elseif ($orig_status == 'completed' || $orig_status == 'COMPLETED') {
                    $status_text = "completed";
                }
                elseif ($orig_status == 'reject') {
                    $status_text = "Cancelled";

                    if (isset($value->rejected_by) && $value->rejected_by == "patient") {
                        $value->rejected_by = "You";
                    }
                    else {
                        $value->rejected_by = "Doctor";
                    }
                }
                else {
                    $status_text = $orig_status;
                }

                $doctor_id = $value->doctor_id;
                $date = date("d M,Y", strtotime($value->date));
                $time_slot_value = $value->time_slot_value;

                $data1 = $this->db->where(array('id' => $doctor_id))->get("doctors")->row();
                if ($data1) {
                    $hospital_name = $data1->hospital_name;
                    $doctor_name = $data1->doctor_name;
                    $doctor_image = !empty($data1->doctor_image)
                        ? base_url() . "uploads/doctors/" . $data1->doctor_image
                        : base_url() . "uploads/profile-icon-3.png";
                    $hospital_image = !empty($data1->hospital_image)
                        ? base_url() . "uploads/doctors/" . $data1->hospital_image
                        : base_url() . "uploads/profile-icon-3.png";
                    $designations_implode = $this->get_designation_names_csv($data1->designations);

                    $array[] = array(
                        'id' => $value->id,
                        'appointment_id' => $value->id,
                        'hospital_name' => $hospital_name,
                        'hospital_image' => $hospital_image,
                        'doctor_name' => $doctor_name,
                        'doctor_image' => $doctor_image,
                        'designations' => $designations_implode,
                        'date' => $date,
                        'time_slot_value' => $time_slot_value,
                        'time_slot_name' => $value->time_slot_name,
                        'doctor_status' => $status_text,
                        'rejected_by' => isset($value->rejected_by) ? $value->rejected_by : '',
                        'reason' => isset($value->reason) ? $value->reason : '',
                        'comments' => isset($value->comments) ? $value->comments : '',
                        'patient_name' => isset($value->patient_name) ? $value->patient_name : '',
                        'patient_mobile' => isset($value->patient_mobile) ? $value->patient_mobile : '',
                        'patient_age' => isset($value->patient_age) ? $value->patient_age : '',
                        'patient_gender' => isset($value->patient_gender) ? $value->patient_gender : '',
                        'consultation_fee' => isset($value->consultation_fee) ? $value->consultation_fee : '',
                        'appointment_type' => isset($value->appointment_type) ? $value->appointment_type : '',
                        'source' => isset($value->source) ? $value->source : 'offline',
                        'doctor_id' => $doctor_id,
                        'doctor_mobile' => $data1->mobile_number,
                        'experience' => $data1->experience,
                        'doctor_rating' => $data1->rating,
                    );
                }
            }
            if (count($array) > 0) {
                return array('status' => TRUE, 'data' => $array);
            }
        }

        return array('status' => FALSE, 'message' => "No data found");
    }


    function cancelledTheAppointment($status)
    {
        if ($status == 'active' || $status != 'accept') {
            $this->db->where("doctor_status", "active");
            $this->db->or_where("doctor_status", "accept");
            $data = $this->db->get("doctor_appointments")->result();
            foreach ($data as $value) {
                $patient_id = $value->patient_id;
                $appointment_id = $value->id;
                $reason = "Cancelled by admin";
                $comments = "Appointment Cancelled by admin";

                $created_date = $value->date;

                $end_date = date('Y-m-d', strtotime($created_date . ' + 3 days'));
                $c_date = date("Y-m-d");

                if ($end_date < $c_date) {

                    $data = array('reason' => $reason, 'comments' => $comments, 'doctor_status' => 'reject', 'rejected_by' => 'admin');
                    $where = array('id' => $appointment_id, 'date<' => $c_date);
                    $table = "doctor_appointments";
                    $res = $this->db->update($table, $data, $where);
                    //echo $this->db->last_query(); die;
                    if ($res) {
                        $patient_row = $this->db->where("id", $appointment_id)->get("doctor_appointments")->row();

                        $doctor_row = $this->db->where("id", $patient_row->doctor_id)->get("doctors")->row();

                   $otp_message = "Dear ".$doctor_row->doctor_name." your booking no.".$appointment_id." is cancelled by patient Thank and regards DOCTTO Thanks & Regards...! DOCTTO";
                     $template_id = '1407168691897786773';
                     $this->User->send_message($otp_message,$doctor_row->mobile_number,$template_id);

                     $arr = array('status'=>TRUE,'message'=>"Appointment cancelled successfully");
                     return $arr;   
                    }

                }
            }
        }
        return TRUE;

    }

    function appointment_details($appointment_id, $user_id)
    {

        // Try offline first
        $this->db->select("id,doctor_id,consultation_fee,date,time_slot_value,time_slot_name,patient_name,patient_mobile,patient_age,doctor_status,rejected_by, reason,comments,patient_gender,appointment_type,created_date,completed_date, 'offline' as source");
        $this->db->where("id", $appointment_id);
        $this->db->where('patient_id', $user_id);
        $data = $this->db->get("doctor_appointments")->result();

        if (count($data) == 0) {
            // Try online
            $this->db->select("id,doctor_id,consultation_fee,date,time_slot_value,time_slot_name,patient_name,patient_mobile,patient_age,payment_status as doctor_status, '' as rejected_by, '' as reason, '' as comments, patient_gender, type as appointment_type, created_date, '' as completed_date, 'online' as source");
            $this->db->where("id", $appointment_id);
            $this->db->where('patient_id', $user_id);
            $this->db->where('payment_status', 'completed');
            $data = $this->db->get("online_doctor_appointments")->result();
        }

        if (count($data) > 0) {
            $array = [];
            foreach ($data as $value) {
                $doctor_id = $value->doctor_id;

                $date = date("d M,Y", strtotime($value->date));
                $time_slot_value = $value->time_slot_value;
                $time_slot_name = $value->time_slot_name;
                $time_slot_value = $value->time_slot_value;

                //                           $this->db->select('round(AVG(rating),2) as rating,COUNT(user_id) as totalusers');
                //                           $this->db->where('vendor_id',$value->doctor_id);
                // $value->doctor_reviews =   $this->db->get('user_reviews')->row(); 

                // $doctor_rating = $value->doctor_reviews;

                // print_r($doctor_rating);die;

                // echo $this->db->last_query();die;

                /*if($value->time_slot_name=='morning'){
                 $time_slot_value=$time_slot_value.":00 AM";
                 }   
                 else if($value->time_slot_name=='afternoon'){
                 $time_slot_value=$time_slot_value.":00 PM";
                 } 
                 elseif ($value->time_slot_name=='evening') {
                 $time_slot_value=$time_slot_value.":00 PM";
                 } */




                if ($value->rejected_by == "patient") {
                    $value->rejected_by = "You";
                }

                $patient_name = $value->patient_name;
                $patient_mobile = $value->patient_mobile;
                $patient_age = $value->patient_age;
                $doctor_status = $value->doctor_status;

                $this->db->select("id,hospital_name,hospital_image,doctor_name,doctor_image,designations,consultant_fee,address,latitude,longitude,gender,experience,mobile_number,blue_tick,rating,rating_count");
                $this->db->where("id", $doctor_id);
                $data1 = $this->db->get("doctors")->row();

                $blue_tick = $data1->blue_tick;

                $doctor_rating = $data1->rating;
                $total_users_reviewed = $data1->rating_count;

                // echo $this->db->last_query();die;

                $hospital_name = $data1->hospital_name;
                if (!empty($data1->doctor_image)) {
                    $doctor_image = base_url() . "uploads/doctors/" . $data1->doctor_image;
                }
                else {
                    $doctor_image = base_url() . "uploads/profile-icon-3.png";
                }

                // $hospital_image = base_url()."uploads/doctors/".$data1->hospital_image;
                $doctor_name = $data1->doctor_name;
                if (!empty($data1->hospital_image)) {
                    $hospital_image = base_url() . "uploads/doctors/" . $data1->hospital_image;
                }
                else {
                    $hospital_image = base_url() . "uploads/profile-icon-3.png";
                }

                // $doctor_image = base_url()."uploads/doctors/".$data1->doctor_image;
                $designations = $data1->designations;
                $consultant_fee = $data1->consultant_fee;

                $des_explode = explode(",", $designations);
                // print_r($des_explode);die;  
                $designations_array = [];
                if (!empty($des_explode)) {
                    foreach ($des_explode as $ex_des_vl) {
                        $this->db->where("id", $ex_des_vl);
                        $ex_des_vl_row = $this->db->get("designations")->row();
                        $designations_array[] = $ex_des_vl_row->name;
                    }
                    $designations_implode = implode(',', $designations_array);
                }
                else {
                    $designations_implode = "";
                }


                if ($value->rejected_by != '') {
                    $rejected_by = $value->rejected_by;
                }
                else {
                    $rejected_by = "";
                }

                if ($value->doctor_status == 'active') {
                    $appointment_status = "Waiting for Doctor Acceptancy";
                }
                else if ($value->doctor_status == 'accept') {
                    $appointment_status = "Appointment Accepted";
                }
                else if ($value->doctor_status == 'completed') {
                    $appointment_status = "Appointment Completed";
                }
                else if ($value->doctor_status == 'reject') {
                    $appointment_status = "Appointment Rejected";
                }
                $created_date = $value->created_date;



                $this->db->where("vendor_id", $doctor_id);
                $user_reviews = $this->db->count_all_results("user_reviews");
                if ($user_reviews > 0) {
                    $is_review_completed = true;
                }
                else {
                    $is_review_completed = false;
                }

                if ($value->completed_date != "0000-00-00 00:00:00") {
                    $completed_date = $value->completed_date;
                }
                else {
                    $completed_date = "";
                }

                $booking_date = $value->date;

                $last_date = strtotime($booking_date . ' +3 days');
                $cdate = time();

                if ($last_date > $cdate) {
                    $chat_permission = 'Yes';
                    $video_permission = 'Yes';
                    $audio_permission = 'Yes';

                }
                else {
                    $chat_permission = 'No';
                    $video_permission = 'No';
                    $audio_permission = 'No';
                }




                $array[] = array('id' => $value->id, 'hospital_name' => $hospital_name, 'hospital_image' => $hospital_image, 'doctor_name' => $doctor_name, 'doctor_image' => $doctor_image, 'doctor_reviews' => $doctor_rating, 'doctor_status' => $doctor_status, 'designations' => $designations_implode, 'consultant_fee' => $value->consultation_fee, 'date' => $date, 'time_slot_value' => $time_slot_value, 'time_slot_name' => $time_slot_name, 'patient_name' => $patient_name, 'patient_mobile' => $patient_mobile, 'patient_age' => $patient_age, 'patient_age' => $patient_age, 'rejected_by' => $rejected_by, 'reason' => $value->reason, 'comments' => $value->comments, 'appointment_status' => $appointment_status, 'hospital_address' => $data1->address, 'gender' => $value->patient_gender, 'latitude' => $data1->latitude, 'experience' => $data1->experience, 'longitude' => $data1->longitude, 'appointment_type' => $value->appointment_type, 'doctor_mobile_number' => $data1->mobile_number, 'created_date' => $created_date, 'doctor_id' => $doctor_id, 'is_review_completed' => $is_review_completed, 'completed_date' => $completed_date, 'blue_tick' => $blue_tick, 'doctor_rating' => $doctor_rating, 'total_users_reviewed' => $total_users_reviewed, 'chat_permission' => $chat_permission, 'video_permission' => $video_permission, 'audio_permission' => $audio_permission);
                if (count($array) > 0) {
                    $ar = array('status' => TRUE, 'data' => $array);
                    return $ar;
                }
                else {
                    $ar = array('status' => FALSE, 'message' => "No data found");
                    return $ar;
                }
            }
        }
        else {
            $ar = array('status' => FALSE, 'message' => "No data found");
            return $ar;
        }
    }

    function userSelectedAppointment_type($doctor_id)
    {
        $this->db->select("id,voice_call,video_call,chat_price");
        $this->db->where("id", $doctor_id);
        $data = $this->db->get("doctors")->row();
        if (!empty($data)) {
            return array('status' => TRUE, 'data' => $data);
        }
        else {
            return array('status' => FALSE, 'message' => "No data found");
        }
    }

    function appointment_cancel($patient_id, $appointment_id, $reason, $comments)
    {

        $data = array('reason' => $reason, 'comments' => $comments, 'doctor_status' => 'reject', 'rejected_by' => 'patient');
        $where = array('id' => $appointment_id);
        $table = "doctor_appointments";
        $res = $this->db->update($table, $data, $where);

        if ($res) {
            $patient_row = $this->db->where("id", $appointment_id)->get("doctor_appointments")->row();

            $doctor_row = $this->db->where("id", $patient_row->doctor_id)->get("doctors")->row();

            $otp_message = "Dear " . $doctor_row->doctor_name . " your booking no." . $appointment_id . " is cancelled by patient Thank and regards DOCTTO Thanks & Regards...! DOCTTO";
            $template_id = '1407168691897786773';
            $this->User->send_message($otp_message, $doctor_row->mobile_number, $template_id);

            $arr = array('status' => TRUE, 'message' => "Appointment cancelled successfully");
            return $arr;
        }
        else {
            $arr = array('status' => FALSE, 'message' => "Something went wrong");
            return $arr;
        }
    }

    function upload_images()
    {

        $images = $this->upload_file('images');
        $fullpath = base_url() . "uploads/user_reports/" . $images;
        $arr = array('status' => TRUE, 'path' => $images, 'fullpath' => $fullpath);
        return $arr;
    }

    private function upload_file($file_name)
    {

        $file_ext = pathinfo($_FILES[$file_name]["name"], PATHINFO_EXTENSION);

        if ($_FILES[$file_name]['name'] != '') {
            if ($_FILES[$file_name]["size"] < '11114374') {
                $upload_path1 = "./uploads/user_reports";
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
            else {
                return 'false';
            }
        }
        else {
            return 'false';
        }
    }

    function report_issue($fullname, $mobile, $subject, $message, $user_id)
    {

        $data = array('fullname' => $fullname, 'mobile' => $mobile, 'subject' => $subject, 'message' => $message, 'user_id' => $user_id);
        $table = "user_reports";
        $sql = $this->db->insert($table, $data);
        if ($sql) {
            $arr = array('status' => TRUE, 'message' => "Report submitted successfully");
            return $arr;
        }
        else {
            $arr = array('status' => FALSE, 'message' => "Something went wrong");
            return $arr;
        }
    }

    function user_contact()
    {
        $table = "contact_us";
        $this->db->select("id,email,mobile");
        $data = $this->db->get($table)->row();
        //if(count($data)>0){
        $email = $data->email;
        $mobile = $data->mobile;
        $arr = array('status' => TRUE, 'email' => $email, 'mobile' => $mobile);
        return $arr;
    //}

    }

    function timings($doctor_id, $date, $session)
    {

        $this->db->select("id,morning_start_time,morning_end_time,afternoon_start_time,afternoon_end_time,evening_start_time,evening_end_time");
        $this->db->where("id", $doctor_id);
        $this->db->where("doctor_show_status", 'active');
        $data = $this->db->get("doctors")->row();
        $interval = 20;
        $cdate = strtotime(date('Y-m-d'));
        $pdate = strtotime($date);

        $doc_id = $data->id;

        if ($pdate == $cdate) {
            $chour = date('H');

            // $chourm = date('i');


            $chour1 = $chour + 1;

            $currenthour = $chour1 . ":00:00";
            if ($session == 'morning') {



                $start_time = $date . ' ' . $data->morning_start_time;
                $end_time = $date . ' ' . $data->morning_end_time;

                $time_slot = $this->time_slot($interval, $start_time, $end_time, $doctor_id, $date, $session);
                if (empty($time_slot)) {
                    $ar = array('status' => FALSE, 'message' => "No time slots");
                    return $ar;
                }

            }
            else if ($session == 'afternoon') {

                $start_time = $date . ' ' . $data->afternoon_start_time;
                $end_time = $date . ' ' . $data->afternoon_end_time;
                $time_slot = $this->time_slot($interval, $start_time, $end_time, $doctor_id, $date, $session);
                if (empty($time_slot)) {
                    $ar = array('status' => FALSE, 'message' => "No time slots");
                    return $ar;
                }
            }
            else if ($session == 'evening') {


                $start_time = $date . ' ' . $data->evening_start_time;
                $end_time = $date . ' ' . $data->evening_end_time;
                $time_slot = $this->time_slot($interval, $start_time, $end_time, $doctor_id, $date, $session);
                if (empty($time_slot)) {
                    $ar = array('status' => FALSE, 'message' => "No time slots");
                    return $ar;
                }

            }
            $current_time_format = time();
            $time_slot_time = [];
            foreach ($time_slot as $value) {
                $time_format = strtotime($value['time_slot']);
                if ($time_format > $current_time_format) {
                    $time_slot_time[] = array('time_slot' => $value['time_slot'], 'is_available' => $value['is_available']);
                }
            }

            if (!empty($time_slot_time)) {
                $ar = array('status' => TRUE, 'data' => $time_slot_time);
            }
            else {
                $ar = array('status' => FALSE, 'message' => "No time slots");
                return $ar;
            }



            return $ar;
        }
        else {
            if ($session == 'morning') {

                $start_time = $date . ' ' . $data->morning_start_time;
                $end_time = $date . ' ' . $data->morning_end_time;


                $time_slot = $this->time_slot($interval, $start_time, $end_time, $doctor_id, $date, $session);



            }
            else if ($session == 'afternoon') {

                $start_time = $date . ' ' . $data->afternoon_start_time;
                $end_time = $date . ' ' . $data->afternoon_end_time;
                $time_slot = $this->time_slot($interval, $start_time, $end_time, $doctor_id, $date, $session);


            }
            else if ($session == 'evening') {
                $start_time = $date . ' ' . $data->evening_start_time;
                $end_time = $date . ' ' . $data->evening_end_time;
                $time_slot = $this->time_slot($interval, $start_time, $end_time, $doctor_id, $date, $session);

            }

            $ar = array('status' => TRUE, 'data' => $time_slot);
            return $ar;
        }
    }
    function time_slot($interval, $start_time, $end_time, $doctor_id, $date, $session)
    {

        $start = new DateTime($start_time);
        $end = new DateTime($end_time);

        $startTime = date('Y-m-d') . $start->format('h:i A');
        $endTime = date('Y-m-d') . $end->format('h:i A');
        $i = 0;
        $time = [];
        $timeslot = [];

        $this->db->where('date', $date);
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('time_slot_value', $start->format('h:i A'));
        $this->db->where('time_slot_name', $session);
        $num_rows = $this->db->count_all_results('doctor_appointments');
        //echo $this->db->last_query(); die;
        if ($num_rows > 0) {
            $is_available = FALSE;
        }
        else {
            $is_available = TRUE;
        }
        $timeslot[0] = array('time_slot' => $start->format('h:i A'), 'is_available' => $is_available);
        while (strtotime($startTime) <= strtotime($endTime)) {

            $start = $startTime;
            $interval1 = 0;
            $end = date('h:i A', strtotime('+' . $interval . ' minutes', strtotime($startTime)));
            $startTime = date('Y-m-d h:i A', strtotime('+' . $interval . ' minutes', strtotime($startTime)));

            $i++;
            if (strtotime($startTime) <= strtotime($endTime)) {
                // $time[$i]['slot_start_time'] = $start;
                //$time[$i]['slot_end_time'] = $end;


                $this->db->where('date', $date);
                $this->db->where('doctor_id', $doctor_id);
                $this->db->where('time_slot_value', $end);
                $this->db->where('time_slot_name', $session);
                $num_rows = $this->db->count_all_results('doctor_appointments');
                //echo $this->db->last_query(); die;

                if ($num_rows > 0) {
                    $is_available = FALSE;
                }
                else {
                    $is_available = TRUE;
                }



                $timeslot[] = array('time_slot' => $end, 'is_available' => $is_available);
            }
        }
        //$timeslot[0]=date('Y-m-d ') . $start->format('h:i A');


        return $timeslot;
    }

    // ==================== DOCTOR SUBSCRIPTION PAYMENT METHODS ====================

    function getDoctorSubscriptionPlans()
    {
        $this->db->select('*');
        $this->db->where('is_active', 1);
        $this->db->order_by('price', 'ASC');
        $result = $this->db->get('doctor_subscription_plans')->result();

        if ($result) {
            // Format perks for better display
            foreach ($result as $plan) {
                if (isset($plan->perks)) {
                    $plan->perks = str_replace(['\r\n', '\n', '\r'], "\n", $plan->perks);
                }
            }
            return array('status' => TRUE, 'data' => $result);
        }
        else {
            return array('status' => FALSE, 'message' => 'No subscription plans available');
        }
    }

    function initiatePhonePeSubscriptionPayment($doctor_id, $plan_id)
    {
        // Validate doctor_id - use same validation as login API
        $this->db->select('id, doctor_login_status');
        $this->db->where('id', $doctor_id);
        $this->db->where('doctor_login_status', 'active');
        $doctor = $this->db->get('doctors')->row();

        if (!$doctor) {
            return array('status' => FALSE, 'message' => 'Invalid doctor ID or doctor not eligible for subscriptions');
        }

        // Get plan details
        $this->db->select('*');
        $this->db->where('id', $plan_id);
        $this->db->where('is_active', 1);
        $plan = $this->db->get('doctor_subscription_plans')->row();

        if (!$plan) {
            return array('status' => FALSE, 'message' => 'Invalid subscription plan');
        }

        // Check if doctor already has active subscription
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('status', 'active');
        $this->db->where('end_at >', date('Y-m-d H:i:s'));
        $existing = $this->db->get('doctor_subscriptions')->num_rows();

        if ($existing > 0) {
            return array('status' => FALSE, 'message' => 'Doctor already has an active subscription');
        }

        // Check if doctor has accepted current terms and conditions
        $this->load->model('admin/terms_conditions_model');
        $current_terms = $this->terms_conditions_model->get_active_terms('doctor', $plan_id);

        if ($current_terms) {
            $terms_accepted = $this->terms_conditions_model->check_terms_acceptance($doctor_id, 'doctor', $current_terms->id);
            if (!$terms_accepted) {
                return array(
                    'status' => FALSE,
                    'message' => 'Please accept the terms and conditions first',
                    'requires_terms_acceptance' => true,
                    'terms_id' => $current_terms->id
                );
            }
        }

        // Create pending subscription record
        $subscription_data = array(
            'doctor_id' => $doctor_id,
            'doctor_subscription_plan_id' => $plan_id,
            'start_at' => date('Y-m-d H:i:s'),
            'end_at' => date('Y-m-d H:i:s', strtotime('+' . $plan->duration_days . ' days')),
            'status' => 'pending',
            'auto_renew' => 0,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('doctor_subscriptions', $subscription_data);
        $subscription_id = $this->db->insert_id();

        // Create payment record
        $payment_data = array(
            'doctor_id' => $doctor_id,
            'subscription_id' => $subscription_id,
            'payment_amount' => $plan->price,
            'payment_method' => 'phonepe',
            'payment_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('doctor_subscription_payments', $payment_data);
        $payment_id = $this->db->insert_id();

        // Load PhonePe OAuth Service for production SDK flow
        $this->load->library('PhonePeOAuthService');

        $merchant_transaction_id = 'SUB_' . $subscription_id . '_' . time();
        $amount_in_paise = intval($plan->price * 100);

        // Step B: Get OAuth Bearer Token
        $token_result = $this->phonepeoauthservice->getBearerToken();
        if (!$token_result['status']) {
            // Update payment status to failed
            $this->db->where('id', $payment_id);
            $this->db->update('doctor_subscription_payments', array(
                'payment_status' => 'failed',
                'error_message' => 'Failed to get OAuth token: ' . $token_result['message']
            ));

            return array(
                'status' => FALSE,
                'message' => 'Failed to get OAuth token: ' . $token_result['message']
            );
        }

        // Step C: Create Subscription Setup Order
        // Mapping duration_days to PhonePe frequency
        $frequency = 'ON_DEMAND';
        if ($plan->duration_days == 1) {
            $frequency = 'DAILY';
        }
        elseif ($plan->duration_days == 7) {
            $frequency = 'WEEKLY';
        }
        elseif ($plan->duration_days == 30 || $plan->duration_days == 31) {
            $frequency = 'MONTHLY';
        }
        elseif ($plan->duration_days == 90 || $plan->duration_days == 93) {
            $frequency = 'QUARTERLY';
        }
        elseif ($plan->duration_days == 365) {
            $frequency = 'YEARLY';
        }

        $subscriptionDetails = array(
            'subscriptionType' => 'RECURRING',
            'merchantSubscriptionId' => 'SUB_DOC_' . $doctor_id . '_' . $subscription_id,
            'authWorkflowType' => 'TRANSACTION',
            'amountType' => 'VARIABLE', // Changed to VARIABLE for better bank compatibility
            'amount' => $amount_in_paise,
            'maxAmount' => $amount_in_paise * 2, // Allow up to 2x for safety
            'frequency' => $frequency,
            'expireAt' => (time() + (365 * 24 * 60 * 60)) * 1000
        );

        // Optional: Add metadata for tracking
        $metaInfo = array(
            'udf1' => 'doctor_subscription',
            'udf2' => 'doctor_id_' . $doctor_id,
            'udf3' => 'plan_id_' . $plan_id,
            'udf4' => 'subscription_id_' . $subscription_id
        );

        $order_result = $this->phonepeoauthservice->createSubscriptionSetupOrder(
            $merchant_transaction_id,
            $amount_in_paise,
            $token_result['accessToken'] ?? $token_result['access_token'],
            $subscriptionDetails,
            $metaInfo
        );

        if (!$order_result['status']) {
            // Update payment status to failed
            $this->db->where('id', $payment_id);
            $this->db->update('doctor_subscription_payments', array(
                'payment_status' => 'failed',
                'error_message' => 'Failed to initiate AutoPay setup: ' . ($order_result['message'] ?? 'Unknown error')
            ));

            return array(
                'status' => FALSE,
                'message' => 'Failed to initiate AutoPay setup: ' . ($order_result['message'] ?? 'Unknown error'),
                'debug' => $order_result['response'] ?? null
            );
        }

        // Update payment record with AutoPay details
        $this->db->where('id', $payment_id);
        $this->db->update('doctor_subscription_payments', array(
            'transaction_id' => $merchant_transaction_id,
            'phonepe_transaction_id' => $order_result['orderId'] ?? null,
            'payment_status' => 'initiated'
        ));

        // Return response for frontend redirect
        return array(
            'status' => TRUE,
            'order_id' => $subscription_id,
            'transaction_id' => $merchant_transaction_id,
            'amount' => $plan->price,
            'plan_name' => $plan->name,
            'redirect_url' => $order_result['redirectUrl'],
            'phonepe_config' => array(
                'redirectUrl' => $order_result['redirectUrl'],
                'merchantOrderId' => $merchant_transaction_id,
                'amount' => $amount_in_paise
            )
        );
    }

    function verifyPhonePeSubscriptionPayment($merchant_transaction_id)
    {
        // Load PhonePe OAuth Service
        $this->load->library('PhonePeOAuthService');

        // Get OAuth token
        $token_result = $this->phonepeoauthservice->getBearerToken();
        if (!$token_result['status'])
            return array('status' => FALSE, 'message' => 'Token failed');

        $verification_result = $this->phonepeoauthservice->verifyOrderStatus($merchant_transaction_id, $token_result['access_token']);

        if ($verification_result['status']) {
            $state = $verification_result['state']; // COMPLETED, FAILED, PENDING

            // Find the subscription payment record
            $this->db->where('transaction_id', $merchant_transaction_id);
            $payment = $this->db->get('doctor_subscription_payments')->row();

            if ($payment) {
                $final_status = ($state == 'COMPLETED') ? 'PAYMENT_SUCCESS' : (($state == 'FAILED') ? 'PAYMENT_FAILED' : 'PAYMENT_PENDING');

                // Update payment record
                $this->db->where('id', $payment->id);
                $this->db->update('doctor_subscription_payments', array(
                    'payment_status' => $final_status,
                    'updated_at' => date('Y-m-d H:i:s')
                ));

                if ($state == 'COMPLETED') {
                    // Activate subscription
                    $this->db->where('id', $payment->subscription_id);
                    $this->db->update('doctor_subscriptions', array(
                        'status' => 'active',
                        'autopay_enabled' => 1,
                        'autopay_agreement_id' => 'SUB_DOC_' . $payment->doctor_id . '_' . $payment->subscription_id,
                        'activated_at' => date('Y-m-d H:i:s')
                    ));

                    // Update doctor record
                    $this->db->where('id', $payment->doctor_id);
                    $this->db->update('doctors', array('has_active_subscription' => 1));

                    // Schedule next renewal
                    $this->schedule_next_renewal($payment->subscription_id);
                }
            }
            return array('status' => TRUE, 'payment_status' => $state, 'data' => $verification_result);
        }
        return array('status' => FALSE, 'message' => 'Verification failed');
    }

    private function schedule_next_renewal($subscription_id)
    {
        $sub = $this->db->where('id', $subscription_id)->get('doctor_subscriptions')->row();
        if ($sub) {
            // Schedule for the exact end_at date
            $renewal_data = array(
                'subscription_id' => $subscription_id,
                'doctor_id' => $sub->doctor_id,
                'renewal_date' => $sub->end_at,
                'status' => 'scheduled',
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->db->insert('subscription_renewals', $renewal_data);
        }
    }

    /**
     * Executes all due subscription renewals (The "Auto" part)
     * Can be called from MY_Controller constructor to act as a pseudo-cron
     */
    public function execute_due_renewals()
    {
        // We look for subscriptions that are expiring in exactly 24 hours to NOTIFY them.
        // For V2, Notify + autoDebit:true is the most reliable "No Cron" way.
        $tomorrow_limit = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->db->select('ds.*, dsp.price, dsp.duration_days');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id');
        $this->db->where('ds.status', 'active');
        $this->db->where('ds.autopay_enabled', 1);
        $this->db->where('ds.end_at <=', $tomorrow_limit);
        $due_subs = $this->db->get()->result();

        if (empty($due_subs))
            return;

        $this->load->library('PhonePeOAuthService');
        $token_result = $this->phonepeoauthservice->getBearerToken();
        if (!$token_result['status'])
            return;

        foreach ($due_subs as $sub) {
            // Check if already notified for this cycle
            $cycle_id = 'NOTIFY_' . $sub->id . '_' . date('Ymd', strtotime($sub->end_at));
            $exists = $this->db->where('transaction_id', $cycle_id)->get('doctor_subscription_payments')->row();
            if ($exists)
                continue;

            $amount_in_paise = intval($sub->price * 100);

            // Call Notify API with auto_debit: true
            $notify_result = $this->phonepeoauthservice->notifyRedemption(
                $cycle_id,
                $sub->autopay_agreement_id,
                $amount_in_paise,
                $token_result['access_token'] ?? $token_result['accessToken'],
                true // auto_debit = true means PhonePe handles the execute after 24h
            );

            if ($notify_result['status']) {
                // Success! Create a record to track the upcoming debit
                $this->db->insert('doctor_subscription_payments', array(
                    'doctor_id' => $sub->doctor_id,
                    'subscription_id' => $sub->id,
                    'payment_amount' => $sub->price,
                    'payment_status' => 'notified',
                    'transaction_id' => $cycle_id,
                    'is_renewal' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ));

                // Extension will be handled by Webhook when PhonePe actually debits after 24h.
                // Or we can pre-extend if we trust the notify, but better wait for callback.
                log_message('info', "AutoPay Notified: scheduled debit for Sub ID " . $sub->id);
            }
            else {
                log_message('error', "AutoPay Notify Failed: Sub ID " . $sub->id . " Error: " . ($notify_result['message'] ?? 'Unknown'));
            }
        }
    }

    function verifyPhonePePayment($merchant_transaction_id, $details = false, $errorContext = false)
    {
        // Load PhonePe OAuth Service for production SDK flow
        $this->load->library('PhonePeOAuthService');

        // Get OAuth token for verification
        $token_result = $this->phonepeoauthservice->getBearerToken();
        if (!$token_result['status']) {
            return array(
                'status' => FALSE,
                'message' => 'Failed to get OAuth token for verification: ' . $token_result['message']
            );
        }

        // Step G: Verify order status using OAuth (Check Order Status API)
        $accessToken = $token_result['accessToken'] ?? $token_result['access_token'];
        $verification_result = $this->phonepeoauthservice->verifyOrderStatus(
            $merchant_transaction_id,
            $accessToken,
            $details,
            $errorContext
        );

        if (!$verification_result['status']) {
            // Check if it's an error response (invalid order ID)
            if (isset($verification_result['code']) && $verification_result['code'] === 'MERCHANT_ORDER_MAPPING_NOT_FOUND') {
                return array(
                    'status' => FALSE,
                    'message' => 'Order not found: ' . $verification_result['message'],
                    'code' => $verification_result['code']
                );
            }

            return array(
                'status' => FALSE,
                'message' => $verification_result['message'] ?? 'Failed to verify order status'
            );
        }

        // Extract order data from response (new format doesn't have 'data' wrapper)
        $order_state = $verification_result['state'] ?? 'PENDING';

        // Map PhonePe state to payment status
        // PhonePe states: PENDING, COMPLETED, FAILED
        $payment_status = 'pending';
        if ($order_state === 'COMPLETED') {
            $payment_status = 'success';
        }
        elseif ($order_state === 'FAILED') {
            $payment_status = 'failed';
        }

        // Update payment record
        $this->db->where('transaction_id', $merchant_transaction_id);
        $this->db->update('doctor_subscription_payments', array(
            'payment_status' => $payment_status,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // If payment successful, activate subscription
        if ($payment_status === 'success') {
            $payment = $this->db->where('transaction_id', $merchant_transaction_id)
                ->get('doctor_subscription_payments')
                ->row();

            if ($payment) {
                // Update subscription status
                $this->db->where('id', $payment->subscription_id);
                $this->db->update('doctor_subscriptions', array(
                    'status' => 'active',
                    'updated_at' => date('Y-m-d H:i:s')
                ));
            }
        }

        return array(
            'status' => TRUE,
            'payment_status' => $payment_status,
            'order_data' => $verification_result
        );
    }

    function initiateRazorpaySubscriptionPayment($doctor_id, $plan_id)
    {
        // Validate doctor_id - use same validation as login API
        $this->db->select('id, doctor_login_status');
        $this->db->where('id', $doctor_id);
        $this->db->where('doctor_login_status', 'active');
        $doctor = $this->db->get('doctors')->row();

        if (!$doctor) {
            return array('status' => FALSE, 'message' => 'Invalid doctor ID or doctor not eligible for subscriptions');
        }

        // Get plan details
        $this->db->select('*');
        $this->db->where('id', $plan_id);
        $this->db->where('is_active', 1);
        $plan = $this->db->get('doctor_subscription_plans')->row();

        if (!$plan) {
            return array('status' => FALSE, 'message' => 'Invalid subscription plan');
        }

        // Check if doctor already has active subscription
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('status', 'active');
        $this->db->where('end_at >', date('Y-m-d H:i:s'));
        $existing = $this->db->get('doctor_subscriptions')->num_rows();

        if ($existing > 0) {
            return array('status' => FALSE, 'message' => 'Doctor already has an active subscription');
        }

        // Check if doctor has accepted current terms and conditions
        $this->load->model('admin/terms_conditions_model');
        $current_terms = $this->terms_conditions_model->get_active_terms('doctor', $plan_id);

        if ($current_terms) {
            $terms_accepted = $this->terms_conditions_model->check_terms_acceptance($doctor_id, 'doctor', $current_terms->id);
            if (!$terms_accepted) {
                return array(
                    'status' => FALSE,
                    'message' => 'Please accept the terms and conditions first',
                    'requires_terms_acceptance' => true,
                    'terms_id' => $current_terms->id
                );
            }
        }

        // Create pending subscription record
        $subscription_data = array(
            'doctor_id' => $doctor_id,
            'doctor_subscription_plan_id' => $plan_id,
            'start_at' => date('Y-m-d H:i:s'),
            'end_at' => date('Y-m-d H:i:s', strtotime('+' . $plan->duration_days . ' days')),
            'status' => 'pending',
            'auto_renew' => 0,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('doctor_subscriptions', $subscription_data);
        $subscription_id = $this->db->insert_id();

        // Create payment record
        $payment_data = array(
            'doctor_id' => $doctor_id,
            'subscription_id' => $subscription_id,
            'payment_amount' => $plan->price,
            'payment_method' => 'razorpay',
            'payment_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('doctor_subscription_payments', $payment_data);
        $payment_id = $this->db->insert_id();

        // Generate Razorpay order (using existing credentials)
        $razorpay_keyid = 'rzp_live_yUSgYWYRFXcTeI'; // Same production key as regular appointments
        $razorpay_secret = 't1r8cnK1pXGzcL3nmGrUeoum'; // Same production secret as regular appointments

        $amount_in_paise = intval($plan->price * 100);

        $data = array(
            'amount' => $amount_in_paise,
            'currency' => 'INR',
            'receipt' => 'SUB_' . $subscription_id
        );

        $payload = json_encode($data);
        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$razorpay_keyid:$razorpay_secret");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ));

        $result = curl_exec($ch);
        $order_data = json_decode($result);
        curl_close($ch);

        if (isset($order_data->id)) {
            // Update payment record with order ID
            $this->db->where('id', $payment_id);
            $this->db->update('doctor_subscription_payments', array(
                'razorpay_order_id' => $order_data->id
            ));

            // Use same simple format as appointment payments
            return array(
                'status' => TRUE,
                'order_id' => $subscription_id, // Use order_id like appointment payments
                'transaction_id' => $order_data->id,
                'amount' => $plan->price,
                'plan_name' => $plan->name
            );
        }
        else {
            return array('status' => FALSE, 'message' => 'Failed to create Razorpay order');
        }
    }

    function processPhonePeSubscriptionPayment($transaction_id, $payment_status, $amount)
    {
        // Find payment record - try multiple fields
        $this->db->where('phonepe_transaction_id', $transaction_id);
        $payment = $this->db->get('doctor_subscription_payments')->row();

        // If not found by phonepe_transaction_id, try transaction_id field
        if (!$payment) {
            $this->db->where('transaction_id', $transaction_id);
            $payment = $this->db->get('doctor_subscription_payments')->row();
        }

        if (!$payment) {
            return array('status' => FALSE, 'message' => 'Payment record not found for transaction: ' . $transaction_id);
        }

        // Update payment status
        $this->db->where('id', $payment->id);
        $this->db->update('doctor_subscription_payments', array(
            'payment_status' => $payment_status,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // Handle different payment status formats
        $success_statuses = ['success', 'PAYMENT_SUCCESS', 'captured', 'completed'];

        if (in_array(strtolower($payment_status), array_map('strtolower', $success_statuses))) {
            // Activate subscription
            $this->db->where('id', $payment->subscription_id);
            $this->db->update('doctor_subscriptions', array(
                'status' => 'active',
                'activated_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ));

            return array('status' => TRUE, 'message' => 'Subscription activated successfully');
        }
        else {
            // Mark subscription as failed
            $this->db->where('id', $payment->subscription_id);
            $this->db->update('doctor_subscriptions', array(
                'status' => 'failed',
                'updated_at' => date('Y-m-d H:i:s')
            ));

            return array('status' => FALSE, 'message' => 'Payment failed');
        }
    }

    function processRazorpaySubscriptionPayment($transaction_id, $payment_status, $amount)
    {
        // Find payment record - try multiple fields
        $this->db->where('razorpay_order_id', $transaction_id);
        $payment = $this->db->get('doctor_subscription_payments')->row();

        // If not found by razorpay_order_id, try transaction_id field
        if (!$payment) {
            $this->db->where('transaction_id', $transaction_id);
            $payment = $this->db->get('doctor_subscription_payments')->row();
        }

        if (!$payment) {
            return array('status' => FALSE, 'message' => 'Payment record not found for transaction: ' . $transaction_id);
        }

        // Update payment status
        $this->db->where('id', $payment->id);
        $this->db->update('doctor_subscription_payments', array(
            'payment_status' => $payment_status,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // Handle different payment status formats
        $success_statuses = ['success', 'captured', 'PAYMENT_SUCCESS', 'completed'];

        if (in_array(strtolower($payment_status), array_map('strtolower', $success_statuses))) {
            // Activate subscription
            $this->db->where('id', $payment->subscription_id);
            $this->db->update('doctor_subscriptions', array(
                'status' => 'active',
                'activated_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ));

            return array('status' => TRUE, 'message' => 'Subscription activated successfully');
        }
        else {
            // Mark subscription as failed
            $this->db->where('id', $payment->subscription_id);
            $this->db->update('doctor_subscriptions', array(
                'status' => 'failed',
                'updated_at' => date('Y-m-d H:i:s')
            ));

            return array('status' => FALSE, 'message' => 'Payment failed');
        }
    }

    function setupDoctorAutopay($doctor_id, $subscription_id, $autopay_agreement_id)
    {
        // Check if subscription exists and is active or pending
        $this->db->where('id', $subscription_id);
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where_in('status', array('active', 'pending'));
        $subscription = $this->db->get('doctor_subscriptions')->row();

        if (!$subscription) {
            return array('status' => FALSE, 'message' => 'Subscription not found or not eligible for autopay');
        }

        // Get plan details for renewal amount
        $this->db->where('id', $subscription->doctor_subscription_plan_id);
        $plan = $this->db->get('doctor_subscription_plans')->row();

        // Create autopay agreement record
        $autopay_data = array(
            'doctor_id' => $doctor_id,
            'subscription_id' => $subscription_id,
            'phonepe_agreement_id' => $autopay_agreement_id,
            'agreement_status' => 'active',
            'recurring_amount' => $plan->price,
            'recurring_frequency' => 'monthly',
            'next_debit_date' => $subscription->end_at,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('doctor_autopay_agreements', $autopay_data);

        // Update subscription with autopay details
        $this->db->where('id', $subscription_id);
        $this->db->update('doctor_subscriptions', array(
            'autopay_agreement_id' => $autopay_agreement_id,
            'autopay_status' => 'active',
            'auto_renew' => 1,
            'next_renewal_date' => $subscription->end_at
        ));

        return array('status' => TRUE, 'message' => 'Autopay setup successfully');
    }

    function getDoctorSubscriptions($doctor_id)
    {
        $this->db->select('ds.*, dsp.name as plan_name, dsp.price, dsp.duration_days, dsp.perks');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id');
        $this->db->where('ds.doctor_id', $doctor_id);
        $this->db->order_by('ds.created_at', 'DESC');
        $result = $this->db->get()->result();

        if ($result) {
            return array('status' => TRUE, 'data' => $result);
        }
        else {
            return array('status' => FALSE, 'message' => 'No subscriptions found');
        }
    }

    function cancelDoctorSubscription($doctor_id, $subscription_id)
    {
        // Check if subscription exists and belongs to doctor
        $this->db->where('id', $subscription_id);
        $this->db->where('doctor_id', $doctor_id);
        $subscription = $this->db->get('doctor_subscriptions')->row();

        if (!$subscription) {
            return array('status' => FALSE, 'message' => 'Subscription not found');
        }

        // Cancel autopay if active
        if ($subscription->autopay_status == 'active') {
            $this->db->where('subscription_id', $subscription_id);
            $this->db->update('doctor_autopay_agreements', array(
                'agreement_status' => 'cancelled',
                'updated_at' => date('Y-m-d H:i:s')
            ));
        }

        // Update subscription status
        $this->db->where('id', $subscription_id);
        $this->db->update('doctor_subscriptions', array(
            'status' => 'cancelled',
            'auto_renew' => 0,
            'autopay_status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s')
        ));

        return array('status' => TRUE, 'message' => 'Subscription cancelled successfully');
    }

}
?>