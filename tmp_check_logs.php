<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
$sql = "SELECT id, request_payload, created_at FROM payment_logs ORDER BY id DESC LIMIT 5";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . "\n";
    echo "TIME: " . $row['created_at'] . "\n";
    echo "PAYLOAD: " . $row['request_payload'] . "\n\n";
}
mysqli_close($conn);
