-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Bulan Mei 2026 pada 14.17
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tokosembako`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_Checkout_Kasir` (IN `p_id_kasir` INT, IN `p_id_metode` INT, IN `p_total_bayar` INT, IN `p_json_keranjang` JSON, OUT `kembalian` INT, OUT `idtransaksix` INT)   BEGIN
DECLARE v_id_transaksi INT;
DECLARE i INT DEFAULT 0;
DECLARE v_jml_item INT;    
DECLARE v_id_barang INT;
DECLARE v_jumlah INT;
DECLARE v_subtotal INT;
DECLARE v_total_harga INT DEFAULT 0;
DECLARE v_hargabarang INT;
START TRANSACTION;
INSERT INTO transaksi (tanggal_transaksi, total_harga, total_bayar, statuss, id_kasir, id_metode)
VALUES (NOW(), 0, p_total_bayar, 1, p_id_kasir, p_id_metode);    
SET v_id_transaksi = LAST_INSERT_ID();
SET v_jml_item = JSON_LENGTH(p_json_keranjang);
WHILE i < v_jml_item DO
SET v_id_barang = JSON_EXTRACT(p_json_keranjang, CONCAT('$[', i, '].id_barang'));
SET v_hargabarang = (SELECT harga FROM barang WHERE id_barang = v_id_barang FOR UPDATE);        
SET v_jumlah = JSON_EXTRACT(p_json_keranjang, CONCAT('$[', i, '].jumlah'));        
SET v_subtotal = f_hitung_subtotal(v_hargabarang, v_jumlah);
SET v_total_harga = v_total_harga + v_subtotal;
INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah_barang, subtotal)
VALUES (v_id_transaksi, v_id_barang, v_jumlah, v_subtotal);        
SET i = i + 1;
END WHILE;
IF p_total_bayar < v_total_harga THEN
ROLLBACK;
SET kembalian = 0;
SET idtransaksix = v_id_transaksi;
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Transaksi Batal: Uang pembayaran kurang!';        
ELSE
UPDATE transaksi 
SET total_harga = v_total_harga
WHERE id_transaksi = v_id_transaksi;
SET kembalian = f_kembalian(v_total_harga, p_total_bayar);
SET idtransaksix = v_id_transaksi;
COMMIT;
END IF;
END$$

--
-- Fungsi
--
CREATE DEFINER=`root`@`localhost` FUNCTION `f_hitung_subtotal` (`harga` INT, `jumlah` INT) RETURNS INT(11) DETERMINISTIC BEGIN
RETURN harga * jumlah;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `f_kembalian` (`harga` INT, `bayar` INT) RETURNS INT(11) DETERMINISTIC BEGIN
RETURN bayar-harga;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `pict` varchar(255) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `harga`, `stok`, `pict`, `id_kategori`) VALUES
(1, 'Beras BMW 5kg', 75000, 30, 'beras_bmw_5kg.jpg', 1),
(2, 'Beras Sania 5kg', 74000, 20, 'beras_sania_5kg.jpg', 1),
(3, 'Beras Maknyuss 5kg', 76000, 15, 'beras_maknyuss_5kg.jpg', 1),
(4, 'Beras Merah 1kg', 20000, 10, 'beras_merah_1kg.jpg', 1),
(5, 'Kacang Hijau 500g', 12000, 25, 'kacang_hijau_500g.jpg', 1),
(6, 'Kacang Tanah 500g', 14000, 25, 'kacang_tanah_500g.jpg', 1),
(7, 'Beras Ketan Putih 500g', 11000, 15, 'ketan_putih_500g.jpg', 1),
(8, 'Kedelai 500g', 13000, 20, 'kedelai_500g.jpg', 1),
(9, 'Bimoli Pouch 1L', 20000, 40, 'bimoli_1l.jpg', 2),
(10, 'Bimoli Pouch 2L', 38000, 35, 'bimoli_2l.jpg', 2),
(11, 'Sunco Pouch 1L', 21000, 30, 'sunco_1l.jpg', 2),
(12, 'Sunco Pouch 2L', 39000, 25, 'sunco_2l.jpg', 2),
(13, 'Filma Pouch 2L', 37000, 20, 'filma_2l.jpg', 2),
(14, 'Tropical Pouch 2L', 38500, 25, 'tropical_2l.jpg', 2),
(15, 'Blue Band Serbaguna 200g', 10500, 40, 'blueband_200g.jpg', 2),
(16, 'Forvita Margarin 200g', 8500, 30, 'forvita_200g.jpg', 2),
(17, 'Minyak Kelapa Barco 1L', 32000, 15, 'barco_1l.jpg', 2),
(18, 'Gulaku Kuning 1kg', 16500, 50, 'gulaku_kuning_1kg.jpg', 3),
(19, 'Gulaku Putih 1kg', 17000, 45, 'gulaku_putih_1kg.jpg', 3),
(20, 'Gula Pasir Curah 1kg', 15500, 100, 'gula_curah_1kg.jpg', 3),
(21, 'Gula Merah Aren 500g', 18000, 20, 'gula_aren_500g.jpg', 3),
(22, 'Garam Dolphin Halus 250g', 3000, 80, 'garam_dolphin_250g.jpg', 3),
(23, 'Garam Refina Halus 250g', 3500, 60, 'garam_refina_250g.jpg', 3),
(24, 'Garam Kasar Curah 500g', 4000, 40, 'garam_kasar_500g.jpg', 3),
(25, 'Kecap Bango 275ml', 13000, 40, 'bango_275ml.jpg', 4),
(26, 'Kecap Bango 520ml', 24000, 30, 'bango_520ml.jpg', 4),
(27, 'Kecap ABC 520ml', 22000, 35, 'kecap_abc_520ml.jpg', 4),
(28, 'Saus Sambal ABC 340ml', 15000, 40, 'sambal_abc_340ml.jpg', 4),
(29, 'Saus Tomat ABC 340ml', 14500, 30, 'tomat_abc_340ml.jpg', 4),
(30, 'Royco Ayam 100g', 5000, 100, 'royco_ayam_100g.jpg', 4),
(31, 'Royco Sapi 100g', 5000, 100, 'royco_sapi_100g.jpg', 4),
(32, 'Masako Ayam 100g', 4500, 100, 'masako_ayam_100g.jpg', 4),
(33, 'Masako Sapi 100g', 4500, 100, 'masako_sapi_100g.jpg', 4),
(34, 'Ladaku Merica Bubuk', 1500, 150, 'ladaku.jpg', 4),
(35, 'Ketumbar Bubuk Desaku', 1500, 150, 'ketumbar_desaku.jpg', 4),
(36, 'Terasi ABC Sachet', 1000, 200, 'terasi_abc.jpg', 4),
(37, 'Segitiga Biru 1kg', 12500, 50, 'segitiga_biru_1kg.jpg', 5),
(38, 'Cakra Kembar 1kg', 13500, 40, 'cakra_kembar_1kg.jpg', 5),
(39, 'Kunci Biru 1kg', 11500, 40, 'kunci_biru_1kg.jpg', 5),
(40, 'Tepung Tapioka Rose Brand 500g', 7500, 45, 'tapioka_rose_brand_500g.jpg', 5),
(41, 'Tepung Beras Rose Brand 500g', 8000, 40, 'beras_rose_brand_500g.jpg', 5),
(42, 'Tepung Maizena 150g', 5500, 30, 'maizena_150g.jpg', 5),
(43, 'Ragi Fermipan 11g', 5000, 60, 'fermipan.jpg', 5),
(44, 'Baking Powder Koepoe 45g', 6000, 40, 'baking_powder_koepoe.jpg', 5),
(45, 'SP Koepoe 30g', 6500, 35, 'sp_koepoe.jpg', 5),
(46, 'Indomie Goreng', 3100, 300, 'indomie_goreng.jpg', 6),
(47, 'Indomie Kuah Ayam Bawang', 3000, 250, 'indomie_ayam_bawang.jpg', 6),
(48, 'Indomie Kuah Soto', 3000, 250, 'indomie_soto.jpg', 6),
(49, 'Mie Sedap Goreng', 3000, 280, 'miesedap_goreng.jpg', 6),
(50, 'Mie Sedap Soto', 2900, 200, 'miesedap_soto.jpg', 6),
(51, 'Supermi Ayam Bawang', 2800, 150, 'supermi_ayam_bawang.jpg', 6),
(52, 'Sarimi Isi 2 Goreng', 4000, 120, 'sarimi_isi_2_goreng.jpg', 6),
(53, 'Bihun Jagung Padamu 320g', 7500, 60, 'bihun_jagung_padamu.jpg', 6),
(54, 'Mie Telur Cap 3 Ayam 200g', 5500, 80, 'mie_telur_3ayam.jpg', 6),
(55, 'La Fonte Macaroni 225g', 9500, 30, 'la_fonte_macaroni.jpg', 6),
(56, 'La Fonte Spaghetti 225g', 10500, 30, 'la_fonte_spaghetti.jpg', 6),
(57, 'Kopi Kapal Api Mix 10s', 13500, 100, 'kapal_api_mix.jpg', 7),
(58, 'Kopi Nescafe Classic 50g', 16000, 40, 'nescafe_classic.jpg', 7),
(59, 'Kopi Luwak White Koffie 10s', 14000, 80, 'luwak_white_koffie.jpg', 7),
(60, 'Teh Sariwangi Celup 25s', 6500, 120, 'sariwangi_25s.jpg', 7),
(61, 'Teh Tong Tji Celup 25s', 8000, 90, 'tong_tji_25s.jpg', 7),
(62, 'Sirup Marjan Cocopandan 460ml', 21000, 40, 'marjan_cocopandan.jpg', 7),
(63, 'Sirup ABC Squash Orange 460ml', 15000, 50, 'abc_squash_orange.jpg', 7),
(64, 'SKM Frisian Flag Putih 370g', 12500, 80, 'skm_frisian_putih.jpg', 7),
(65, 'SKM Frisian Flag Cokelat 370g', 12500, 70, 'skm_frisian_cokelat.jpg', 7),
(66, 'Susu Dancow Fortigro 400g', 45000, 30, 'dancow_fortigro.jpg', 7),
(67, 'Milo Active-Go 300g', 35000, 40, 'milo_300g.jpg', 7),
(68, 'Nutrisari Jeruk Peras 10s', 15000, 100, 'nutrisari_jeruk.jpg', 7),
(69, 'Roma Kelapa 300g', 11000, 60, 'roma_kelapa.jpg', 8),
(70, 'Oreo Vanilla 133g', 8500, 75, 'oreo_vanilla.jpg', 8),
(71, 'Biskuat Coklat 140g', 8000, 50, 'biskuat_coklat.jpg', 8),
(72, 'Taro Net Seaweed 65g', 5500, 80, 'taro_seaweed.jpg', 8),
(73, 'Chitato Sapi Panggang 68g', 10500, 60, 'chitato_sapi.jpg', 8),
(74, 'Qtela Singkong Original 60g', 6000, 70, 'qtela_singkong.jpg', 8),
(75, 'Kerupuk Udang Finna 380g', 22000, 20, 'kerupuk_finna.jpg', 8),
(76, 'Beng-Beng Share It 300g', 35000, 25, 'bengbeng_shareit.jpg', 8),
(77, 'Silverqueen 62g', 16000, 40, 'silverqueen_62g.jpg', 8),
(78, 'Lifebuoy Sabun Cair 450ml', 25000, 40, 'lifebuoy_cair.jpg', 9),
(79, 'Nuvo Sabun Batang 76g', 3500, 120, 'nuvo_batang.jpg', 9),
(80, 'Pepsodent White 190g', 13500, 80, 'pepsodent_190g.jpg', 9),
(81, 'Ciptadent 190g', 10000, 60, 'ciptadent_190g.jpg', 9),
(82, 'Clear Men Shampoo 160ml', 24000, 35, 'clear_men_160ml.jpg', 9),
(83, 'Sunsilk Black Shine 160ml', 22000, 45, 'sunsilk_black_160ml.jpg', 9),
(84, 'Rexona Roll On Women 50ml', 17000, 30, 'rexona_women.jpg', 9),
(85, 'Sikat Gigi Formula 3s', 12000, 50, 'formula_3s.jpg', 9),
(86, 'Sunlight Jeruk Nipis 755ml', 18500, 60, 'sunlight_755ml.jpg', 10),
(87, 'Mama Lemon 780ml', 17000, 50, 'mama_lemon_780ml.jpg', 10),
(88, 'Rinso Anti Noda 800g', 22000, 40, 'rinso_800g.jpg', 10),
(89, 'Daia Bunga 850g', 18000, 45, 'daia_850g.jpg', 10),
(90, 'Soklin Liquid 750ml', 19000, 50, 'soklin_liquid.jpg', 10),
(91, 'Wipol Karbol 750ml', 20000, 35, 'wipol_750ml.jpg', 10),
(92, 'Super Pell Apel 770ml', 16000, 40, 'super_pell.jpg', 10),
(93, 'Baygon Aerosol 600ml', 38000, 25, 'baygon_600ml.jpg', 10),
(94, 'Telur Ayam Ras 1kg', 28000, 30, 'telur_ayam_1kg.jpg', 11),
(95, 'Telur Puyuh 500g', 18000, 15, 'telur_puyuh_500g.jpg', 11),
(96, 'Telur Bebek Asin 1pcs', 3500, 50, 'telur_asin.jpg', 11),
(97, 'Bawang Merah 250g', 12000, 20, 'bawang_merah_250g.jpg', 11),
(98, 'Bawang Putih 250g', 10000, 20, 'bawang_putih_250g.jpg', 11),
(99, 'Tisu Paseo 250s', 18000, 60, 'paseo_250s.jpg', 12),
(100, 'Korek Api Tokai', 3000, 100, 'korek_tokai.jpg', 12);

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah_barang` int(11) DEFAULT NULL,
  `subtotal` int(11) DEFAULT NULL,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp(),
  `nama_kasir` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_transaksi`, `id_barang`, `jumlah_barang`, `subtotal`, `tanggal_transaksi`, `nama_kasir`) VALUES
(1001, 101, 1, 15000, '2026-05-28 08:46:26', ''),
(1001, 103, 1, 5000, '2026-05-28 08:46:26', ''),
(1002, 102, 1, 12000, '2026-05-28 08:46:26', ''),
(1002, 103, 1, 5000, '2026-05-28 08:46:26', ''),
(1003, 101, 2, 30000, '2026-05-28 08:46:26', ''),
(1004, 103, 1, 5000, '2026-05-28 08:46:26', ''),
(1004, 105, 1, 10000, '2026-05-28 08:46:26', ''),
(1005, 101, 2, 30000, '2026-05-28 08:46:26', ''),
(1005, 103, 3, 15000, '2026-05-28 08:46:26', ''),
(1010, 101, 4, 60000, '2026-05-28 08:46:26', ''),
(1010, 102, 2, 24000, '2026-05-28 08:46:26', ''),
(1010, 103, 1, 5000, '2026-05-28 08:46:26', ''),
(1010, 104, 2, 16000, '2026-05-28 08:46:26', '');

--
-- Trigger `detail_transaksi`
--
DELIMITER $$
CREATE TRIGGER `trg_kurangi_stok_otomatis` AFTER INSERT ON `detail_transaksi` FOR EACH ROW BEGIN
UPDATE barang 
SET stok = stok - NEW.jumlah_barang
WHERE id_barang = NEW.id_barang;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir`
--

CREATE TABLE `kasir` (
  `id_kasir` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `access_level` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir`
--

INSERT INTO `kasir` (`id_kasir`, `nama_lengkap`, `username`, `password_hash`, `access_level`, `created_at`) VALUES
(1, 'Rizky Hadi', 'rizkyhadi', '$2y$10$YP9XEHqB7DGvaDg0Xs50yOvRURl5d0TYe/b0ncqHqdk1V3MlrR6Zi', 20, '2026-05-28 11:17:59'),
(2, 'Azkafalah Cendikia Suryatmaja', 'azkafalahcendikia', '$2y$10$cnCLuWRBOUKrmwjk14.XIuonam0e9A.YRzf3cwB5Q7H10h/cI6bPS', 0, '2026-05-28 11:17:59'),
(3, 'Rizky Hadi', 'rizky', '$2y$10$YP9XEHqB7DGvaDg0Xs50yOvRURl5d0TYe/b0ncqHqdk1V3MlrR6Zi', 10, '2026-05-28 11:17:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Beras & Biji-bijian'),
(2, 'Minyak & Margarin'),
(3, 'Gula & Garam'),
(4, 'Bumbu Dapur'),
(5, 'Tepung & Bahan Kue'),
(6, 'Mie Instan & Pasta'),
(7, 'Minuman & Bubuk'),
(8, 'Makanan Ringan'),
(9, 'Perawatan Diri'),
(10, 'Pembersih Rumah'),
(11, 'Telur & Bahan Segar'),
(12, 'Lain-lain');

-- --------------------------------------------------------

--
-- Struktur dari tabel `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode` int(11) NOT NULL,
  `nama_metode` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode`, `nama_metode`) VALUES
(1, 'Cash'),
(2, 'Debit Card'),
(3, 'QRIS'),
(4, 'E-Wallet');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `tanggal_transaksi` datetime DEFAULT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `total_bayar` int(11) DEFAULT NULL,
  `statuss` tinyint(1) DEFAULT NULL,
  `id_kasir` int(11) DEFAULT NULL,
  `id_metode` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tanggal_transaksi`, `total_harga`, `total_bayar`, `statuss`, `id_kasir`, `id_metode`) VALUES
(1001, '2026-03-10 10:15:00', 20000, 20000, 1, 1, 1),
(1002, '2026-03-10 11:00:00', 17000, 20000, 1, 2, 3),
(1003, '2026-03-11 14:20:00', 30000, 50000, 1, 3, 2),
(1004, '2026-03-11 16:45:00', 15000, 20000, 1, 1, 4),
(1005, '2026-03-29 16:12:02', 45000, 100000, 1, 1, 1),
(1010, '2026-04-01 05:23:47', 105000, 500000, 1, 2, 1);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_barang_kategori`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_barang_kategori` (
`nama_barang` varchar(100)
,`harga` int(11)
,`nama_kategori` varchar(50)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `view_barang_kategori`
--
DROP TABLE IF EXISTS `view_barang_kategori`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_barang_kategori`  AS SELECT `b`.`nama_barang` AS `nama_barang`, `b`.`harga` AS `harga`, `k`.`nama_kategori` AS `nama_kategori` FROM (`barang` `b` join `kategori` `k` on(`b`.`id_kategori` = `k`.`id_kategori`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `idx_kategori_barang` (`id_kategori`),
  ADD KEY `idx_harga_barang` (`harga`);

--
-- Indeks untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_transaksi`,`id_barang`),
  ADD KEY `id_barang` (`id_barang`),
  ADD KEY `idx_detail_transaksi` (`id_transaksi`);

--
-- Indeks untuk tabel `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`id_kasir`),
  ADD UNIQUE KEY `idx_username_login` (`username`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_kasir` (`id_kasir`),
  ADD KEY `id_metode` (`id_metode`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT untuk tabel `kasir`
--
ALTER TABLE `kasir`
  MODIFY `id_kasir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1011;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_kasir`) REFERENCES `kasir` (`id_kasir`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_metode`) REFERENCES `metode_pembayaran` (`id_metode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
