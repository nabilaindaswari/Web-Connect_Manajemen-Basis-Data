<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tokosembako";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    // Arahkan ke halaman error kustom jika koneksi gagal
    header('Location: ../public/error.php');
    exit;
}
?>