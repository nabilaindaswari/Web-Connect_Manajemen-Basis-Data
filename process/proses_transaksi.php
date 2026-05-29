<?php
session_start();
require_once '../config/database.php';

$id_kasir        = $_SESSION['user_id'];
$id_metode       = (int)($_POST['id_metode'] ?? 0);
$total_bayar     = (int)($_POST['total_bayar'] ?? 0);
$total_harga     = (int)($_POST['total_harga'] ?? 0);

# Pembuatan JSON Keranjang berasal dari ID Barang dan Jumlah yang dikirim dari form checkout

$p_keranjang     = [];
foreach ($_SESSION['keranjang'] as $item) {
    $p_keranjang[] = [
        'id_barang' => (int)$item['id_barang'],
        'jumlah'    => (int)$item['jumlah_barang']
    ];
}
$p_json_keranjang = json_encode($p_keranjang);


/* Ambil nama_lengkap untuk ditampilkan di struk */
$stmtUser = $pdo->prepare("SELECT nama_lengkap FROM kasir WHERE id_kasir = ? LIMIT 1");
$stmtUser->execute([$id_kasir]);
$userRow       = $stmtUser->fetch(PDO::FETCH_ASSOC);
$kasir = $userRow ? $userRow['nama_lengkap'] : $username_session;



$stmtTransaksi = $pdo->prepare("CALL SP_Checkout_Kasir(:p_id_kasir, :p_id_metode, :p_total_bayar, :p_json_keranjang, @kembalian)");
var_dump($id_metode);
die();
$stmtTransaksi->execute([
    ':p_id_kasir'       => $id_kasir,
    ':p_id_metode'      => $id_metode,
    ':p_total_bayar'    => $total_bayar,
    ':p_json_keranjang' => $p_json_keranjang,
]);

?>