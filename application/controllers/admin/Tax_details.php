<?php



defined('BASEPATH') OR exit('No direct script access allowed');
 


class Tax_details extends CI_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }

    }



    function index() {
        $this->data['page_name'] = 'payment_invoice';
        $qry = $this->db->query("select * from tax_details order by id desc");
        $data['tax_details'] = $qry->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/tax_details', $data);
        $this->load->view('admin/includes/footer');
    }



    function add() {
        
        $this->data['title'] = 'Add Payment Invoice';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/addtax_details', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function insert() {

        $gst = $this->input->post('gst');
        $pan = $this->input->post('pan');
        $company_address = $this->input->post('address');
        $company_email = $this->input->post('email');
        $company_phone = $this->input->post('mobile');
        $company_website = $this->input->post('website');
        $invoice_number = rand(99999999999999, 999999999999999);  
        $customer_name = $this->input->post('cust_name');
        $customer_phone = $this->input->post('cust_email');
        $customer_email = $this->input->post('cust_phone');
        $customer_place = $this->input->post('cust_place');
        $customer_address = $this->input->post('cust_address');
        // $bank_name = $this->input->post('bank_name');
        // $account_number = $this->input->post('account_number');
        // $ifsc_code = $this->input->post('ifsc');
        // $branch = $this->input->post('branch');

                     
        // $qrcode = $this->upload_file('qrcode');

        $data = array(
            'gst' => $gst,
            'pan' => $pan,
            'company_address' => $company_address,
            'company_email' => $company_email,
            'company_phone' => $company_phone,
            'company_website' => $company_website,
            'invoice_number' => $invoice_number,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone,
            'customer_email' => $customer_email,
            'customer_place' => $customer_place,
            'customer_address' => $customer_address,
            // 'bank_name' => $bank_name,
            // 'account_number' => $account_number,
            // 'ifsc_code' => $ifsc_code,
            // 'branch' => $branch,
            // 'qr_code' => $qrcode
        );

        $insert_query = $this->db->insert('tax_details', $data);
        if ($insert_query) 
        {
            redirect('admin/tax_details');
            die();
        } 
        else
        {
            redirect('admin/tax_details/add');
            die();
        }

    }



    function edit($id) {

        $data['title'] = 'Edit Payment Invoice ';

        $data['tax_details'] = $this->db->get_where('tax_details', ['id' => $id])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/edittax_details', $data);

        $this->load->view('admin/includes/footer');

    }



    function update() {
        $id=$this->input->post('id');
        $qry=$this->db->query("select * from tax_details where id='".$id."'");
        $row = $qry->row();
        
        
        $gst = $this->input->post('gst');
        $pan = $this->input->post('pan');
        $company_address = $this->input->post('address');
        $company_email = $this->input->post('email');
        $company_phone = $this->input->post('mobile');
        $company_website = $this->input->post('website');
        $invoice_number = rand(99999999999999, 999999999999999);  
        $customer_name = $this->input->post('cust_name');
        $customer_phone = $this->input->post('cust_email');
        $customer_email = $this->input->post('cust_phone');
        $customer_place = $this->input->post('cust_place');
        $customer_address = $this->input->post('cust_address');
        // $bank_name = $this->input->post('bank_name');
        // $account_number = $this->input->post('account_number');
        // $ifsc_code = $this->input->post('ifsc');
        // $branch = $this->input->post('branch');
        
        // if($this->upload_file('qrcode')!='')
        // {
        //     $qrcode=$this->upload_file('qrcode');
        // }
        // else
        // {
        //     $qrcode=$row->qrcode;
        // }

      
       $data = array(
            'gst' => $gst,
            'pan' => $pan,
            'company_address' => $company_address,
            'company_email' => $company_email,
            'company_phone' => $company_phone,
            'company_website' => $company_website,
            'invoice_number' => $invoice_number,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone,
            'customer_email' => $customer_email,
            'customer_place' => $customer_place,
            'customer_address' => $customer_address,
            // 'bank_name' => $bank_name,
            // 'account_number' => $account_number,
            // 'ifsc_code' => $ifsc_code,
            // 'branch' => $branch,
            // 'qr_code' => $qrcode
        );
        
        
        $this->db->where('id', $id);
        $update_query = $this->db->update('tax_details', $data);

        if ($update_query) 
        {
            redirect('admin/tax_details');
            die();
        } 
        else 
        {
            redirect('admin/tax_details/edit/' . $id);
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
            $upload_path1 = "./uploads/pdf_logo/";
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
        if ($this->db->delete('tax_details')) {
                $this->session->set_flashdata('success_message', 'Banner Deleted Successfully');
                redirect('admin/tax_details');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/tax_details');
         }

    }



}

