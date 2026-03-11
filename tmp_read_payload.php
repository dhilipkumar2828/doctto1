<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
$sql = "SELECT request_payload FROM payment_logs WHERE provider='phonepe_autopay' ORDER BY id DESC LIMIT 1";
$res = mysqli_query($conn, $sql);
if($row = mysqli_fetch_assoc($res)) {
    echo $row['request_payload'];
}
mysqli_close($conn);
