<?php

session_start();

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}


/* ======================================================
   AMBIL DATA DARI FORM
====================================================== */

$id_barang     = $_POST['id_barang'] ?? 0;
$harga         = $_POST['harga'] ?? 0;
$jumlah_barang = $_POST['jumlah_barang'] ?? 1;


/* ======================================================
   CEK APAKAH BARANG SUDAH ADA
====================================================== */

$barang_sudah_ada = false;

foreach ($_SESSION['keranjang'] as &$item) {

    if ($item['id_barang'] == $id_barang) {

        $item['jumlah_barang'] += $jumlah_barang;

        $barang_sudah_ada = true;

        break;
    }
}

unset($item);


/* ======================================================
   JIKA BELUM ADA -> TAMBAH BARU
====================================================== */

if (!$barang_sudah_ada) {

    $_SESSION['keranjang'][] = [
        'id_barang'     => $id_barang,
        'harga'         => $harga,
        'jumlah_barang' => $jumlah_barang
    ];
}


/* ======================================================
   KEMBALI KE HALAMAN KASIR
====================================================== */

header("Location: ../public/kasir_home.php");
exit;
?>