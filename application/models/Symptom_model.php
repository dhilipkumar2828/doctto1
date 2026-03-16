<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Symptom_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        
        //load database library
        $this->load->database();
    }
    
    function get_row()
    {
    	return $this->db->get('symptom')->result();
    }
    

}
?>