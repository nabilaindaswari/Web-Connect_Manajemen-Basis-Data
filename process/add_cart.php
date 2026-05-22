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
   AMBIL DATA POST
====================================================== */

$id_barang = $_POST['id_barang'] ?? null;

if ($id_barang === null) {
    header("Location: ../public/kasir_home.php");
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
    header("Location: ../public/kasir_home.php");
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
   KEMBALI KE HALAMAN KASIR
====================================================== */

header("Location: ../public/kasir_home.php");
exit;