<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/admin/subscription_plans/manage_doctors';
$_SERVER['HTTP_HOST'] = 'localhost';

define('ENVIRONMENT', 'development');

require_once 'index.php';
