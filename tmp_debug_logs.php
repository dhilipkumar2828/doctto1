<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
$sql = "SELECT id, provider, request_payload FROM payment_logs ORDER BY id DESC LIMIT 3";
$res = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . " | PROVIDER: " . $row['provider'] . "\n";
    echo "PAYLOAD: " . substr($row['request_payload'], 0, 1000) . "\n\n";
}
mysqli_close($conn);
