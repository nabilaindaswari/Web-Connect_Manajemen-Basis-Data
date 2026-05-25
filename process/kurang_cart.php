<?php

session_start();

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
   AMBIL ID BARANG
====================================================== */

$id_barang = $_POST['id_barang'] ?? null;

if ($id_barang !== null) {

    foreach ($_SESSION['keranjang'] as $index => &$item) {

        if ($item['id_barang'] == $id_barang) {

            /* Kurangi jumlah */
            $item['jumlah_barang']--;

            /* Jika sudah 0 -> hapus dari keranjang */
            if ($item['jumlah_barang'] <= 0) {

                unset($_SESSION['keranjang'][$index]);
            }

            break;
        }
    }

    unset($item);

    /* Rapikan index array */
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
}


/* ======================================================
   KEMBALI KE HALAMAN ASAL
====================================================== */

header("Location: $redirect_page");
exit;
?>