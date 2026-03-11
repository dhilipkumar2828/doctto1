<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'doctto';
$conn = mysqli_connect($hostname, $username, $password, $database);
$res = mysqli_query($conn, "SHOW TABLES");
while($row = mysqli_fetch_array($res)) {
    echo $row[0] . "\n";
}
