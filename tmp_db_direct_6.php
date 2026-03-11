<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'localhost';

define('ENVIRONMENT', 'development');

require_once 'index.php';
// But this would load the default controller. I don't want that.

// Instead we can just do raw DB query to test if there's any weird typing issue.
