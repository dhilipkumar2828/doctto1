<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
$sql = "SELECT id, request_payload FROM payment_logs WHERE request_payload LIKE '%deviceContext%' ORDER BY id DESC LIMIT 1";
$res = mysqli_query($conn, $sql);
if($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . "\n";
    echo $row['request_payload'];
} else {
    echo "NO DEVICECONTEXT FOUND IN LOGS";
}
mysqli_close($conn);
