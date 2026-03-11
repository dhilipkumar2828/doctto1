<?php
$lines = file('C:/xampp/apache/logs/error.log');
$last = array_slice($lines, -50);
file_put_contents('tmp_apache_err_utf8.txt', implode("", $last));
