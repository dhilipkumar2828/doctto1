<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
    public function set() {
        $this->session->set_flashdata('test_msg', 'Hello World ' . time());
        echo "Set flashdata. <a href='".base_url()."admin/test/get'>Get it</a>";
    }

    public function get() {
        $msg = $this->session->flashdata('test_msg');
        echo "Message: " . $msg;
        echo "<br><a href='".base_url()."admin/test/get_again'>Get again</a>";
    }

    public function get_again() {
        $msg = $this->session->flashdata('test_msg');
        echo "Message (should be empty): " . $msg;
    }
}
