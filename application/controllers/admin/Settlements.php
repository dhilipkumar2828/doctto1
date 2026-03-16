<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Settlements extends MY_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }

    }


    function index() {
        $this->data['page_name'] = 'settlements';

        $vendor_req = $this->db->query("select * from request_payment order by id desc");
        $this->data['vendor_requests']=$vendor_req->result();

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/settlements', $this->data);
        $this->load->view('admin/includes/footer');
    }



    function add($id) {
        $this->data['page_name'] = 'settlements';
        $this->data['title'] = 'Settlements';

        

        $chk_vend = $this->db->query("select * from request_payment where id='".$id."'");
        $vendor_row = $chk_vend->row();

        $ve = $this->db->query("select * from vendor_shop where id='".$vendor_row->vendor_id."'");
        $vendror_data = $ve->row();
        $this->data['shop_name']=$vendror_data->shop_name;
        $this->data['id']=$id;
        $this->data['vendor_amount']=$vendor_row->vendor_amount;
        $this->data['request_amount']=$vendor_row->request_amount;

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/add_settlement', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function insert() 
    {
         $id = $this->input->post('id');
        $vendor_amount = $this->input->post('vendor_amount');
        $requested_amount = $this->input->post('requested_amount');
        $mode_payment = $this->input->post('mode_payment');
         $sender_name = $this->input->post('sender_name');
         $receiver_name = $this->input->post('receiver_name');
         $transaction_id = $this->input->post('transaction_id');
        $description = $this->input->post('description');

        if($this->upload_file('image')!='')
        {
            $transation_img=$this->upload_file('image');
        }
        else
        {
            $transation_img="";
        }
        $vv = $this->db->query("select * from request_payment where id='".$id."'");
                    $vendr = $vv->row();
        $gel = $this->db->query("select *  from vendor_payements where vendor_id='".$vendr->vendor_id."'");
                    $gel_t = $gel->row();

                     $total_payment = $gel_t->requested_amount;
                    $fin_am = $total_payment+$requested_amount;
                     $data1 = array('requested_amount'=>$fin_am);
                        $wrr1 = array('vendor_id'=>$vendr->vendor_id);
                     $this->db->update('vendor_payements', $data1,$wrr1);
                  



        $data = array(
            'transaction_id'=>$transaction_id,
            'sender_name'=> $sender_name,
            'receiver_name'=> $receiver_name,
            'mode_payment'=> $mode_payment,
            'admin_description'=> $description,
            'image'=>$transation_img,
            'status'=>1,
            'updated_at' => time()
        );
        $wr = array('id'=>$id);
        $insert_query = $this->db->update('request_payment', $data,$wr);
        //echo $this->db->last_query(); die;
        if ($insert_query) {

                        
                    


                       


           $qrr = $this->db->query("select * from vendor_shop where id='".$vendr->vendor_id."'");
            $vend_row = $qrr->row();

                            $msg = "Admin Settlement the amount ".$requested_amount." to ".$vend_row->shop_name;
                        $trans_ar=array('sender_name'=>'Admin','receiver_name'=>$vend_row->shop_name,'amount'=>$requested_amount,'message'=>$msg,'created_at'=>time());
                        $this->db->insert('transactions', $trans_ar);

            $this->session->set_flashdata('success_message', 'Settlement added Successfully');
            redirect('admin/settlements');
            die();
        } else {
            redirect('admin/settlements/add');
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
            $upload_path1 = "./uploads/payments/";
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
        if ($this->db->delete('request_payment')) {
                $this->session->set_flashdata('error_message', 'Request Payment Deleted Successfully');
                redirect('admin/request_payment');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/request_payment');
         }

    }



}

