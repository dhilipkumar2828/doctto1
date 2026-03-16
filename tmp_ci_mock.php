<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/admin/subscription_plans/manage_doctors';
$_SERVER['HTTP_HOST'] = 'localhost';

define('ENVIRONMENT', 'development');

try {
    // We can't easily load CI this way without side effects if it redirects
    // But let's try reading the response headers of a curl request to it
} catch (Exception $e) {}
