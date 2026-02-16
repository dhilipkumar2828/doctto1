<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Subsymptoms extends MY_Controller {
    public $data;
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
        $this->load->model("admin_model");
    }

    function index() {

        $this->data['page_name'] = 'vendors_shops';
        $this->data['title'] = 'Vendors/Shops';

        $q = $this->input->get_post('q');

        $status = $this->input->get_post('status');

        $city_id = $this->input->get_post('city_id');

        $vm_id = $this->input->get_post('vm_id');

        $cat_id = $this->input->get_post('category_id');

        $deals_of_the_day = $this->input->get_post('deals_of_the_day');

        $this->data['q'] = $q;

        $this->data['status'] = $status;

        $this->data['city_id'] = $city_id;

        $this->data['vm_id'] = $vm_id;

        $this->data['cat_id'] = $cat_id;

        $this->data['deals_of_the_day'] = $deals_of_the_day;

        $this->data['cities'] = $this->admin_model->get_table_data('cities');


        $this->data['categories'] = $this->admin_model->get_table_data('categories');

        $this->db->where('status', 1);
        $this->db->order_by('id', 'desc');
        $this->data['vendor_shops'] = $this->db->get('vendor_shop')->result();

        foreach ($this->data['vendor_shops'] as $v) {
            

            $total_products = $this->db->get_where('products', ['shop_id' => $v->id])->result();

            $v->total_products = count($total_products);

            $total_categories = $this->db->get_where('admin_comissions', ['shop_id' => $v->id])->result();

            $v->total_categories = count($total_categories);
        }

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/vendors_shops', $this->data);

        $this->load->view('admin/includes/footer');
    }




    function delete($shop_id) {
        /*$this->db->where('id', $shop_id);
       $del = $this->db->delete('vendor_shop');*/

        //echo$del = $this->db->last_query(); die;

       $del = $this->db->update("vendor_shop",array('status'=>1,'delete_status' =>1),array('id' =>$shop_id));
        if($del)
        {
            $this->db->delete('products',array('shop_id'=>$shop_id));
            $this->db->delete('products',array('shop_id'=>$shop_id));
            $this->session->set_flashdata('success_message', 'Shop Deleted Successfully');
            redirect('admin/vendors_shops');
        }
        else
        {
            $this->session->set_flashdata('error_message', 'Something went wrong, Unable to delete');
            redirect('admin/vendors_shops');
        }
    }



    function changeStatus($shop_id,$status)
    {
        $upd = $this->db->update("vendor_shop",array('status' =>$status,'vendor_verification_status'=>0),array('id' =>$shop_id));
        if($upd)
        {
            redirect('admin/vendors_shops');
        }
    }


    function add() {
        
         $this->data['page_name'] = 'vendors_shops/add';
         
        $this->data['title'] = 'Add Vendor/Shop';

        $this->data['cities'] = $this->db->get('cities')->result();

        $this->data['categories'] = $this->db->get('categories')->result();

        $this->data['visual_merchant'] = "";

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/add_vendor_shop', $this->data);

        $this->load->view('admin/includes/footer');

    }

function send_message($message = "", $mobile_number) {


         $message = urlencode($message);

         $URL = "http://login.smsmoon.com/API/sms.php"; // connecting url 

         $post_fields = ['username' => 'Rocket', 'password' => 'vizag@123', 'from' => 'Rocket', 'to' => $mobile_number, 'msg' => $message, 'type' => 1, 'dnd_check' => 0];

         //file_get_contents("http://login.smsmoon.com/API/sms.php?username=colourmoonalerts&password=vizag@123&from=WEBSMS&to=$mobile_number&msg=$message&type=1&dnd_check=0");

         $ch = curl_init();

         curl_setopt($ch, CURLOPT_URL, $URL);

         curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

         curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

         curl_exec($ch);

         return true;

      }

      function update()
      {
        $sid = $this->input->get_post('sid');
        $shop_name = $this->input->get_post('shop_name');
        $owner_name = $this->input->get_post('owner_name');
        $description = $this->input->get_post('description');
        $email = $this->input->get_post('email');
        $mobile = $this->input->get_post('mobile');
       $gender = $this->input->get_post('gender');
        
        $state_id= $this->input->get_post('state_id');
        $city_id = $this->input->get_post('city_id');
        $pincodes = $this->input->get_post('pincodes');

        $address = $this->input->get_post('address');
        
        $status = $this->input->get_post('status');

        $delivery_time = $this->input->get_post('delivery_time');
        $min_order_amount = $this->input->get_post('min_order_amount');


        $qry = $this->db->query("select * from vendor_shop where id='".$sid."'");
        $row = $qry->row();
        if($this->upload_file('shop_image')!='')
        {
            $shop_image = $this->upload_file('shop_image');
        }
        else
        {
            $shop_image = $row->shop_logo;
        }

        if($this->upload_file('shop_logo')!='')
        {
             $shop_logo = $this->upload_file('shop_logo');
        }
        else
        {
             $shop_logo = $row->logo;
        }
        


        $seo_url = $this->make_seo_name($shop_name);
        if($this->input->get_post('vendor_verification_status')==0)
        {
            $shop_status = 0;
        }
        else
        {
            $shop_status = $row->status;
        }


        $check_qry = $this->db->query("select * from vendor_shop where id!='".$sid."' and ( mobile='".$this->input->get_post('mobile')."' or  email='".$this->input->get_post('email')."' )");
        if($check_qry->num_rows()>0)
        {
            $this->session->set_flashdata('error_message', 'Already Exist ( Email address or Mobile Number )');
            redirect('admin/vendors_shops/edit');
            die();
        }


        $check_pincode_qry = $this->db->query("select * from vendor_shop where id!='".$sid."' and vendor_pincodes='".$this->input->get_post('mobile')."'");
        if($check_pincode_qry->num_rows()>0)
        {
            $this->session->set_flashdata('error_message', 'Pincode already assigned');
            redirect('admin/vendors_shops/edit');
            die();
        }

        $data = array(
            'shop_name' => $shop_name,
            'owner_name' => $owner_name,
            'email' => $email,
            'mobile' => $mobile,
            'gender' => $gender,

            'shop_logo' => $shop_image,
            'logo' => $shop_logo,
            'address' => $address,
            'state_id' => $state_id,
            'city_id' => $city_id,

            'vendor_pincodes'=>$this->input->get_post('pincodes'),
            'status' =>$status,
            'delivery_time'=>$delivery_time,
            'min_order_amount' => $this->input->get_post('min_order_amount'),
            'description' =>$description,
            'youtube_id' => $this->input->get_post('youtube_id'),
            'seo_url'=>$seo_url,
            'vendor_verification_status'=>$this->input->get_post('vendor_verification_status')
        );
        
          $insert_query = $this->db->update('vendor_shop', $data,array('id'=>$sid));
        
        if ($insert_query) {

            $work_hrs_data = $this->work_hours($this->db->insert_id());

            foreach ($work_hrs_data as $w) {

                $this->db->insert('shop_work_hours', $w);

            }

            $this->session->set_flashdata('success_message', 'Vendor Updated Successfully');

            redirect('admin/vendors_shops');

            die();

        } else {

            $this->session->set_flashdata('error_message', 'Unable to add');

            redirect('admin/vendors_shops/edit');

            die();

        }

    

      }

    function make_seo_name($title) {
        return preg_replace('/[^a-z0-9_-]/i', '', strtolower(str_replace(' ', '-', trim($title))));
    }

    function insert() 
    {

        $password = $this->input->get_post('password');

        $shop_name = $this->input->get_post('shop_name');
        $seo_url = $this->make_seo_name($shop_name);

        $shop_image = $this->upload_file('shop_image');
        $shop_logo = $this->upload_file('shop_logo');

       

        $pincodes = $this->input->get_post('pincodes');
        $youtube_id = $this->input->get_post('youtube_id');

        $check_qry = $this->db->query("select * from vendor_shop where ( mobile='".$this->input->get_post('mobile')."' or  email='".$this->input->get_post('email')."' )");
        if($check_qry->num_rows()>0)
        {
            $this->session->set_flashdata('error_message', 'Already Exist ( Email address or Mobile Number )');
            redirect('admin/vendors_shops/add');
            die();
        }


        $check_pincode_qry = $this->db->query("select * from vendor_shop where vendor_pincodes='".$this->input->get_post('mobile')."'");
        if($check_pincode_qry->num_rows()>0)
        {
            $this->session->set_flashdata('error_message', 'Pincode already assigned');
            redirect('admin/vendors_shops/add');
            die();
        }


        $data = array(
            'shop_name' => $this->input->get_post('shop_name'),
            'owner_name' => $this->input->get_post('owner_name'),
            'email' => $this->input->get_post('email'),
            'mobile' => $this->input->get_post('mobile'),
            'gender' => $this->input->get_post('gender'),
            'youtube_id'=>$youtube_id,
            'password' => md5($password),
            'shop_logo' => $shop_image,
            'logo' => $shop_logo,
            'address' => $this->input->get_post('address'),
            'state_id' => $this->input->get_post('state_id'),
            'city_id' => $this->input->get_post('city_id'),
            'vendor_pincodes'=>$pincodes,
            'seo_url'=>$seo_url,
            'status' => $this->input->get_post('status'),
            'vendor_verification_status'=>$this->input->get_post('vendor_verification_status'),
            'delivery_time'=>$this->input->get_post('delivery_time'),
            'min_order_amount' => $this->input->get_post('min_order_amount'),
            'created_date'=>date('Y-m-d H:i:s'),
            'description' =>$this->input->get_post('description')
        );

        
        //$otp_message = 'Your Account created by A3 Services, LINK: http://rocketwheel.in/vendors/login  &  USERNAME: '.$this->input->get_post('email').'  & PASSWORD : '.$password;

        /*if($this->send_message($otp_message,$mobile))
        {*/
          $insert_query = $this->db->insert('vendor_shop', $data);

          //echo $this->db->last_query(); die;
        /*}*/

        if ($insert_query) 
        {
            $this->session->set_flashdata('success_message', 'Vendor Created Successfully');
            redirect('admin/vendors_shops');
            die();
        } 
        else 
        {
            $this->session->set_flashdata('error_message', 'Unable to add');
            redirect('admin/vendors_shops/add');
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
                    $upload_path1 = "./uploads/shops/";
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
                    return 'default_shop_logo.png';
                }
            }
            else
            {
                return '';
            }
    }
    



    function edit($id) {
        
        $qry = $this->db->query("select * from vendor_shop where id='".$id."'");
        $row = $qry->row();
        $this->data['vendor_data']=$row;

         $this->data['cities'] = $this->db->get('cities')->result();

        $this->data['categories'] = $this->db->get('categories')->result();

        $this->data['visual_merchant'] = "";

        $this->data['title'] = 'Vendors/Shops';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/edit_vendor_shops', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function manage_categories() {

        $shop_id = $this->input->get_post('shop_id');
        if(!$shop_id) 
        {
            redirect('admin/vendors_shops/');
            die();
        }

        $this->data['shop_id'] = $shop_id;

        $this->data['shop_status'] = 'add';

        $this->data['shop_name'] = $this->admin_model->get_table_row('vendor_shop', 'id', $shop_id)->shop_name;

        $this->data['categories'] = $this->admin_model->get_table_data('categories', 'id', 'desc');



        $this->db->select('ad_com.*, c.category_name');

        $this->db->from('admin_comissions ad_com');

        $this->db->join('categories c', 'c.id=ad_com.cat_id');

        $this->db->where('ad_com.shop_id', $shop_id);

        $res = $this->db->get()->result();

        if (count($res) > 0) 
        {
            $this->data['admin_comissions'] = $res;
        }

        $this->data['title'] = 'Manage Categories';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/vendor_manage_categories', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function edit_manage_categories($shop_id,$com_id) {

        if (!$shop_id) 
        {
            redirect('admin/vendors_shops/');
            die();
        }

        $this->data['shop_id'] = $shop_id;
        $this->data['com_id'] = $com_id;
        $this->data['shop_status'] = 'edit';


        $shop_qry = $this->db->query("select * from admin_comissions where id='".$com_id."'");
        $this->data['admin_edit_comissions'] = $shop_qry->row();

        $this->data['shop_name'] = $this->admin_model->get_table_row('vendor_shop', 'id', $shop_id)->shop_name;

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

        /*$shop_id = $this->input->get_post('shop_id');

        if (!$shop_id) 
        {
            redirect('admin/vendors_shops/');
            die();
        }

        $this->data['shop_id'] = $shop_id;

       

        $this->data['page_name'] = 'pincodes';
        $this->data['title'] = 'Pincodes';
        $qry = $this->db->query("select * from pincodes where shop_id='".$shop_id."'");
        $this->data['pincodes'] = $qry->result();

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/pincodes', $this->data);
        $this->load->view('admin/includes/footer');*/


$shop_id = $this->input->get_post('shop_id');

        if (!$shop_id) 
        {
            redirect('admin/vendors_shops/');
            die();
        }
        $this->data['shop_id'] = $shop_id;

        $this->data['page_name'] = 'cities';
        $this->data['title'] = 'Cities';
        $qry = $this->db->query("select * from cities where vendor_id='".$shop_id."'");
        $this->data['cities'] = $qry->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/cities', $this->data);
        $this->load->view('admin/includes/footer');

    

    }

    function manage_pincodes() {

        $shop_id = $this->input->get_post('shop_id');

        if (!$shop_id) 
        {
            redirect('admin/vendors_shops/');
            die();
        }

        $this->data['shop_id'] = $shop_id;

       

        $this->data['page_name'] = 'pincodes';
        $this->data['title'] = 'Pincodes';
        $qry = $this->db->query("select * from pincodes where shop_id='".$shop_id."'");
        $this->data['pincodes'] = $qry->result();

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/pincodes', $this->data);
        $this->load->view('admin/includes/footer');


    

    }

    function manage_areas() {

        $shop_id = $this->input->get_post('shop_id');
        if (!$shop_id) 
        {
            redirect('admin/vendors_shops/');
            die();
        }

        $this->data['shop_id'] = $shop_id;

       
        $this->data['page_name'] = 'locations';
        $this->data['title'] = 'Areas';

        $qry = $this->db->query("select * from areas where vendor_id='".$shop_id."'");
        $this->data['locations'] = $qry->result();

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/locations', $this->data);
        $this->load->view('admin/includes/footer');

    

    }



    function insert_cat_comission() {

        $shop_id = $this->input->get_post('shop_id');

        $cat_id = $this->input->get_post('cat_id');
        $sub_categories = $this->input->get_post('sub_categories');
        $subcategory_ids = implode(",", $sub_categories);
        $admin_commission = $this->input->get_post('admin_comission');
        $gst = $this->input->get_post('gst');




        $shop_qry = $this->db->query("SELECT * FROM vendor_shop WHERE id='".$shop_id."'");
        $shop_row = $shop_qry->row();

        $pincode = $shop_row->vendor_pincodes;


        $category_qry = $this->db->query("SELECT admin_comissions.* FROM `admin_comissions` INNER JOIN vendor_shop ON vendor_shop.id=admin_comissions.shop_id WHERE admin_comissions.cat_id='".$cat_id."' and vendor_shop.vendor_pincodes='".$pincode."' ");
        if($category_qry->num_rows()>0)
        {
                $this->session->set_flashdata('error_message', 'Category already assigned to vendor with same pincode');
                redirect('admin/vendors_shops/manage_categories?shop_id=' . $shop_id);
                die();
        }
        else
        {
                $data = array(
                    'shop_id' => $shop_id,
                    'cat_id' => $cat_id,
                    'subcategory_ids' =>$subcategory_ids,
                    'admin_comission' => $admin_commission,
                    'gst' =>$gst,
                    'status' => 1,
                    'created_at' => time());

                $insert = $this->db->insert('admin_comissions', $data);
                if ($insert) {
                    redirect('admin/vendors_shops/manage_categories?shop_id=' . $shop_id);
                    die();
                } else {
                    redirect('admin/vendors_shops/');
                    die();
                }
        }



    }


    function loadSubcategories()
    {
       $cid = $this->input->get_post('cid');
        $shop_id= $this->input->get_post('shop_id');
       //$chk = $this->vendor_model->subcategories($cid,$shop_id);

        $qry = $this->db->query("select * from sub_categories where cat_id='".$cid."'");
          $query=$qry->result();
          $output = '<option value="">Select SubCategories</option>';
          foreach($query as $row)
          {
              $output .= '<option value="'.$row->id.'">'.$row->sub_category_name.'</option>';
          }

          print_r($output); die;

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
        if($status==1)
        {
            $ars = array('status'=>1);
            $wrr = array('shop_id'=>$shop_id,'cat_id'=>$cat_id);
            $this->db->update('products', $ars,$wrr);
            //echo $this->db->last_query(); die;
        }
        else if($status==0)
        {
            $ars = array('status'=>0);
            $wrr = array('shop_id'=>$shop_id,'cat_id'=>$cat_id);
            $this->db->update('products', $ars,$wrr);
            //echo $this->db->last_query(); die;
        }
        
        $wr=array("id"=>$com_id);
            $data = array(
                'shop_id' => $shop_id,
                'cat_id' => $cat_id,
                'subcategory_ids' =>$subcategory_ids,
                'admin_comission' => $admin_commission,
                'gst' =>$gst,
                'status' => $status,
                'updated_at' => time());

            $insert = $this->db->update('admin_comissions', $data,$wr);
            if ($insert) {
                redirect('admin/vendors_shops/manage_categories?shop_id=' . $shop_id);
                die();
            } else {
                redirect('admin/vendors_shops/');
                die();
            }

    }



    function delete_vendor_admin_comission() {

        $admin_com_id = $this->input->get_post('admin_com_id');

        $shop_id = $this->input->get_post('shop_id');

            $qry = $this->db->query("select * from admin_comissions where id='".$admin_com_id."'");
            $admin_row = $qry->row();

            $cat_id = $admin_row->cat_id;
            
            $shop_qry = $this->db->query("select * from products where cat_id='".$cat_id."' and shop_id='".$shop_id."'");
            $shop_nums = $shop_qry->num_rows();
            if($shop_nums>0)
            {
                    $this->session->set_flashdata('error_message', 'Unable to delete,this category already assigned to products');
                    redirect('admin/vendors_shops/manage_categories?shop_id=' . $shop_id);
                    die();
            }
            else
            {
                if($this->admin_model->delete_vendor_admin_comission($admin_com_id)) 
                {
                    $this->session->set_flashdata('success_message', 'Comission Deleted Successfully');
                    redirect('admin/vendors_shops/manage_categories?shop_id=' . $shop_id);
                    die();
                } 
                else 
                {
                    $this->session->set_flashdata('error_message', 'Unable to delete');
                    redirect('admin/vendors_shops/manage_categories?shop_id=' . $shop_id);
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
    
    
    function loadSubSymptoms() {
        
        
        $cid = $this->input->post('cid');

        $qry = $this->db->query("select * from doctor_sub_categories where cat_id='".$cid."'");
          $query=$qry->result();
          $output = '<option value="">Select SubCategories</option>';
          foreach($query as $row)
          {
              $output .= '<option value="'.$row->id.'">'.$row->sub_category_name.'</option>';
          }

          print_r($output); die;
          
        
    }


}

