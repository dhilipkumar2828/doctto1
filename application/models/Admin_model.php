<?php

class Admin_model extends CI_Model {

    public $table = 'admin';
    public $logs = 'logs';
    public $ip_check = 'ip_check';
    public $banners = 'banners';
    public $services = 'services';

    function __construct() {

        //load the parent constructor

        parent::__construct();
    }

    public function ip_checking($ip) {
        $this->db->where('ip', $ip);
        $ipcheck = $this->db->get('ipcheck');
        return $ipcheck->row();
    }

    public function ip_insert($ip, $date) {
        $data['date1'] = date('d-m-Y h:i:s');
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->db->insert('ipcheck', $data);
        // return $query->row();
    }

    public function admin_login($username, $password) {

        $this->db->where('email', $username);
        $this->db->where('password', $password);

        $query = $this->db->get('admin');
        if ($query->num_rows() == 1) {

             $ip = $_SERVER['REMOTE_ADDR'];
                // $details = json_decode(@file_get_contents("http://ipinfo.io/{$ip}/json"));
                // $city = isset($details->city) ? $details->city : 'Local';
                $city = 'Local';
                    /*$ip = 'test';
                    $city = 'kakinada';*/

                    $adm = $this->db->query("select * from admin where id=1");
                    $adm_row = $adm->row();

                $to_mail = $adm_row->email;
                $from_email = 'A3 Services';
                $site_name = 'A3 Services';
                $email_message = "IP Address : ".$ip." <br>
                                  City : ".$city." ";
                // $this->load->library('email');
                // require_once (APPPATH . 'libraries/vendor/autoload.php');
                // require_once (APPPATH . 'libraries/vendor/phpmailer/phpmailer/src/PHPMailer.php');
                // require_once (APPPATH . 'libraries/vendor/phpmailer/phpmailer/src/SMTP.php');
                // require_once (APPPATH . 'libraries/vendor/phpmailer/phpmailer/src/Exception.php');

                // $mail = new PHPMailer\PHPMailer\PHPMailer();
                // $mail->From = $from_email;
                // $mail->FromName = $site_name;
                // $mail->addAddress($to_mail); 
                // $mail->addReplyTo($from_email, "Reply");
                // $mail->isHTML(true);
                // $mail->Sender = $from_email;
                // $mail->Subject = "$site_name Login IP Address";
                // $mail->Body = $email_message;
                // $sucess = $mail->send();


//            $data['admin'] = 'admin';
//            $data['date'] = date('d-m-Y h:i:s');
//            $data['ip'] = $ip;
//            $this->db->insert('logs', $data);
            return $query->row();
        } else {
            return 'false';
        }
    }

    public function verify_password_by_user_id($id, $password) {

        return $this->db->get_where('admin', ['id' => $id, 'password' => $password])->num_rows();
    }

    public function set_password_by_user_id($id, $new_password) {

        $this->db->set('password', $new_password);
        $this->db->where('id', $id);
        return $this->db->update('admin');
    }

    // Get all table rows order by
    public function get_table_data($table_name, $order_col = null, $order_val = null) {
        if ($order_col && $order_val) {
            $this->db->order_by($order_col, $order_val);
        }
        return $this->db->get($table_name)->result();
    }

    // Get row
    public function get_table_row($table_name, $col_name, $val) {
        $this->db->where($col_name, $val);
        return $this->db->get($table_name)->row();
    }

    // Get rows of where clause
    public function get_table_data_by_value($table_name, $col_name, $val) {
        $this->db->order_by('id', 'desc');
        $this->db->where($col_name, $val);
        return $this->db->get($table_name)->result();
    }

    public function get_table_rows_count($table_name) {
        return $this->db->get($table_name)->num_rows();
    }

    // Delete Product
    public function delete_product($id) {
        return $this->db->query("delete from products where id='" . $id . "'");
    }

    // Delete Vendor Admin Comission
    public function delete_vendor_admin_comission($id) {
        return $this->db->query("delete from admin_comissions where id='" . $id . "'");
    }


    function getCities($state_id)
    {
         $qry = $this->db->query("select * from cities where state_id='".$state_id."'");
          $query=$qry->result();
          $output = '<option value="">Select Cities</option>';
          foreach($query as $row)
          {
           $output .= '<option value="'.$row->id.'">'.$row->city_name.'</option>';
          }

          print_r($output); die;
          // /return $output;
    }

    function getLocation($city_id)
    {
         $qry = $this->db->query("select * from locations where city_id='".$city_id."'");
          $query=$qry->result();
          $output = '<option value="">Select Location</option>';
          foreach($query as $row)
          {
           $output .= '<option value="'.$row->id.'">'.$row->location_name.'</option>';
          }

          print_r($output); die;
          // /return $output;
    }

    

}
