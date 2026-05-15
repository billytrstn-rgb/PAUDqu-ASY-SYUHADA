<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "paudqu_db";

// Mematikan exception otomatis MySQLi agar bisa kita handle manual
mysqli_report(MYSQLI_REPORT_OFF);

// Koneksi awal ke server MySQL (tanpa memilih database)
$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("Koneksi ke server MySQL gagal: " . mysqli_connect_error());
}

// Cek apakah database ada, jika tidak buat otomatis
if (!mysqli_select_db($conn, $db)) {
    $createDbQuery = "CREATE DATABASE IF NOT EXISTS `$db`";
    mysqli_query($conn, $createDbQuery);
    mysqli_select_db($conn, $db);
}

// Pastikan tabel admin selalu ada
$createTableQuery = "CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $createTableQuery);

// Cek apakah akun admin default sudah ada
$checkAdmin = mysqli_query($conn, "SELECT id, password FROM admin WHERE username = 'admin'");
if ($checkAdmin && mysqli_num_rows($checkAdmin) == 0) {
    // Simpan password dengan hash bcrypt yang aman
    $hashedPassword = password_hash('paud2026', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO `admin` (`username`, `password`) VALUES (?, ?)");
    $stmt->bind_param("ss", $adminUsername, $hashedPassword);
    $adminUsername = 'admin';
    $stmt->execute();
    $stmt->close();
} else if ($checkAdmin && mysqli_num_rows($checkAdmin) > 0) {
    // Migrasi: jika password masih plain text, hash sekarang
    $row = mysqli_fetch_assoc($checkAdmin);
    if (!password_get_info($row['password'])['algo']) {
        $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE admin SET password = '$hashedPassword' WHERE username = 'admin'");
    }
}
?>
