<?php

session_start();
require_once '../config/database.php';

/* ======================================================
   CEK SESSION KERANJANG
====================================================== */

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

/* ======================================================
   TENTUKAN HALAMAN REDIRECT (KASIR / ADMIN)
====================================================== */

$redirect_page = '../public/kasir_home.php';

if (isset($_SESSION['access_level']) && $_SESSION['access_level'] >= 11) {
    $redirect_page = '../public/admin_home.php';
}

/* ======================================================
   AMBIL DATA POST
====================================================== */

$id_barang = $_POST['id_barang'] ?? null;

if ($id_barang === null) {
    header("Location: $redirect_page");
    exit;
}

/* ======================================================
   AMBIL DATA BARANG DARI DATABASE
====================================================== */

$stmt = $pdo->prepare("
    SELECT id_barang, nama_barang, harga
    FROM barang
    WHERE id_barang = ?
");

$stmt->execute([$id_barang]);
$barang = $stmt->fetch(PDO::FETCH_ASSOC);

/* ======================================================
   CEK BARANG ADA
====================================================== */

if (!$barang) {
    header("Location: $redirect_page");
    exit;
}

/* ======================================================
   CEK APAKAH SUDAH ADA DI KERANJANG
====================================================== */

$found = false;

foreach ($_SESSION['keranjang'] as &$item) {
    if ($item['id_barang'] == $id_barang) {
        $item['jumlah_barang']++;
        $found = true;
        break;
    }
}
unset($item);

/* ======================================================
   JIKA BELUM ADA -> TAMBAHKAN
====================================================== */

if (!$found) {
    $_SESSION['keranjang'][] = [
        'id_barang'     => $barang['id_barang'],
        'nama_barang'   => $barang['nama_barang'],
        'harga'         => $barang['harga'],
        'jumlah_barang' => 1
    ];
}

/* ======================================================
   RESET TOKEN CHECKOUT (TAMBAHAN PENTING)
   Karena isi keranjang berubah, kita harus mereset token
   agar transaksi dianggap sebagai transaksi baru yang valid.
====================================================== */
unset($_SESSION['token_checkout']);


/* ======================================================
   KEMBALI KE HALAMAN ASAL
====================================================== */

header("Location: $redirect_page");
exit;