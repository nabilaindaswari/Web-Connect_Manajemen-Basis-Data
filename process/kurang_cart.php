<?php

session_start();

/* ======================================================
   CEK SESSION KERANJANG
====================================================== */

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
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
   KEMBALI KE HALAMAN KASIR
====================================================== */

header("Location: ../public/kasir_home.php");
exit;
?>