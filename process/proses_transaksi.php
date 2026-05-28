<?php
session_start();
require_once '../config/database.php';

$id_user          = $_SESSION['user_id'];
$id_metode       = (int)($_POST['id_metode'] ?? 0);
$total_bayar     = (int)($_POST['total_bayar'] ?? 0);
$total_harga     = (int)($_POST['total_harga'] ?? 0);

/* Ambil nama_lengkap untuk ditampilkan di struk */
$stmtUser = $pdo->prepare("SELECT nama_lengkap FROM users WHERE id_user = ? LIMIT 1");
$stmtUser->execute([$id_user]);
$userRow       = $stmtUser->fetch(PDO::FETCH_ASSOC);
$kasir = $userRow ? $userRow['nama_lengkap'] : $username_session;

$stmtTransaksi = $pdo->prepare("CALL SP_Checkout_Kasir(:p_id_user, :p_id_metode, :p_total_bayar, :p_json_keranjang, @kembalian)");
$stmtTransaksi->execute([
    ':p_id_user'        => $id_user,
    ':p_id_metode'      => $id_metode,
    ':p_total_bayar'    => $total_bayar,
    ':p_json_keranjang' => $p_json_keranjang,
]);

?>