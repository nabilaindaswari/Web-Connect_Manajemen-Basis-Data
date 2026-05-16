<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tokosembako";

try {
    $dsn = "mysql:host=$host;dbname=$db";
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    echo "Koneksi Gagal" . $e->getMessage();
}
?>