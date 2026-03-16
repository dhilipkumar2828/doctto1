<?php
// Mocking the environment
define('BASEPATH', 'dummy');
define('APPPATH', 'application/');
require 'application/config/constants.php';

// Mocking CodeIgniter
class MY_Controller {
    public $db;
    public function __construct() {
        require 'application/config/database.php';
        $db_config = $db['default'];
        $this->db = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
    }
}

function redirect($url) {
    echo "REDIRECT: $url\n";
    exit;
}

require 'application/controllers/Phonephe.php';

$p = new Phonephe();
$p->paymentview(968);
?>
