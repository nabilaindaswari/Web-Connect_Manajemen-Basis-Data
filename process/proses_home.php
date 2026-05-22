<?php

require_once '../config/database.php';

/* =========================
   AMBIL DATA BARANG
========================= */
$stmtBarangList = $pdo->prepare("
    SELECT 
        barang.id_barang,
        barang.nama_barang,
        barang.id_kategori,
        kategori.nama_kategori,
        barang.harga,
        barang.stok,
        barang.pict
    FROM barang
    JOIN kategori 
        ON barang.id_kategori = kategori.id_kategori
");

$stmtBarangList->execute();

$barang_list = $stmtBarangList->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   AMBIL DATA KATEGORI
========================= */
$stmtKategori = $pdo->prepare("
    SELECT *
    FROM kategori
");

$stmtKategori->execute();

$kategori_list = $stmtKategori->fetchAll(PDO::FETCH_ASSOC);

?>