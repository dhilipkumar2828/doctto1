<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_db2 extends CI_Controller {
    public function index() {
        $fields = $this->db->list_fields('users');
        echo "Columns in users: " . implode(', ', $fields) . "\n";
    }
}
