<?php
require 'koneksi.php';
$res = mysqli_query($conn, 'SELECT * FROM admin');
if ($res) {
    print_r(mysqli_fetch_all($res, MYSQLI_ASSOC));
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
