<?php
$conn = mysqli_connect('localhost', 'root', '', 'doctto');
if (!$conn) die("Fail");

echo "All Doctors:\n";
$r = mysqli_query($conn, "SELECT id, doctor_name FROM doctors WHERE doctor_name LIKE '%NAGARJUN%'");
while($row = mysqli_fetch_assoc($r)) {
    print_r($row);
}

mysqli_close($conn);
?>
