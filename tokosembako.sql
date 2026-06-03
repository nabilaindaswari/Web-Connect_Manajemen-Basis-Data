-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 04:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_Checkout_Kasir` (IN `p_token_transaksi` VARCHAR(100), IN `p_id_kasir` INT, IN `p_id_metode` INT, IN `p_total_bayar` INT, IN `p_json_keranjang` JSON, OUT `kembalian` INT, OUT `idtransaksix` INT)   BEGIN
    DECLARE v_id_transaksi INT;
    DECLARE i INT DEFAULT 0;
    DECLARE v_jml_item INT;
    DECLARE v_id_barang INT;
    DECLARE v_jumlah INT;
    DECLARE v_subtotal INT;
    DECLARE v_total_harga INT DEFAULT 0;
    DECLARE v_hargabarang INT;
    DECLARE v_stok_sekarang INT;

    DECLARE EXIT HANDLER FOR 1062 
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Transaksi Gagal: Terdeteksi pengiriman data ganda (Duplikat).';
    END;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SET v_jml_item = JSON_LENGTH(p_json_keranjang);

    -- [TAMBAHAN BARU: TOLAK JIKA KERANJANG KOSONG]
    IF v_jml_item = 0 OR v_jml_item IS NULL THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Transaksi Batal: Keranjang belanja kosong!';
    END IF;

    -- Insert ke tabel transaksi pindah ke SINI (hanya dieksekusi jika keranjang ada isinya)
    INSERT INTO transaksi (token_transaksi, tanggal_transaksi, total_harga, total_bayar, statuss, id_kasir, id_metode)
    VALUES (p_token_transaksi, NOW(), 0, p_total_bayar, 1, p_id_kasir, p_id_metode);    
    
    SET v_id_transaksi = LAST_INSERT_ID();

    WHILE i < v_jml_item DO
        SET v_id_barang = JSON_EXTRACT(p_json_keranjang, CONCAT('$[', i, '].id_barang'));
        SET v_jumlah = JSON_EXTRACT(p_json_keranjang, CONCAT('$[', i, '].jumlah'));
        
        SELECT harga, stok INTO v_hargabarang, v_stok_sekarang 
        FROM barang 
        WHERE id_barang = v_id_barang 
        FOR UPDATE;
        
        IF v_stok_sekarang < v_jumlah THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Transaksi Batal: Stok barang tidak mencukupi';
        END IF;

        SET v_subtotal = f_hitung_subtotal(v_hargabarang, v_jumlah);
        SET v_total_harga = v_total_harga + v_subtotal;

        UPDATE barang
        SET stok = stok - v_jumlah
        WHERE id_barang = v_id_barang;

        INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah_barang, subtotal)
        VALUES (v_id_transaksi, v_id_barang, v_jumlah, v_subtotal);        
        
        SET i = i + 1;
    END WHILE;

    IF p_total_bayar < v_total_harga THEN
        ROLLBACK;
        SET kembalian = 0;
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
-- Functions
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
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` int(11) UNSIGNED NOT NULL,
  `stok` int(11) UNSIGNED NOT NULL,
  `pict` varchar(255) DEFAULT NULL,
  `id_kategori` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `harga`, `stok`, `pict`, `id_kategori`) VALUES
(1, 'Beras BMW 5kg', 75000, 0, 'beras_bmw_5kg.jpg', 1),
(2, 'Beras Sania 5kg', 74000, 12, 'beras_sania_5kg.jpg', 1),
(3, 'Beras Maknyuss 5kg', 76000, 9, 'beras_maknyuss_5kg.jpg', 1),
(4, 'Beras Merah 1kg', 20000, 5, 'beras_merah_1kg.jpg', 1),
(5, 'Kacang Hijau 500g', 12000, 21, 'kacang_hijau_500g.jpg', 1),
(6, 'Kacang Tanah 500g', 14000, 16, 'kacang_tanah_500g.jpg', 1),
(7, 'Beras Ketan Putih 500g', 11000, 14, 'ketan_putih_500g.jpg', 1),
(8, 'Kedelai 500g', 13000, 20, 'kedelai_500g.jpg', 1),
(9, 'Bimoli Pouch 1L', 20000, 40, 'bimoli_1l.jpg', 2),
(10, 'Bimoli Pouch 2L', 38000, 34, 'bimoli_2l.jpg', 2),
(11, 'Sunco Pouch 1L', 21000, 29, 'sunco_1l.jpg', 2),
(12, 'Sunco Pouch 2L', 39000, 25, 'sunco_2l.jpg', 2),
(13, 'Filma Pouch 2L', 37000, 20, 'filma_2l.jpg', 2),
(14, 'Tropical Pouch 2L', 38500, 25, 'tropical_2l.jpg', 2),
(15, 'Blue Band Serbaguna 200g', 10500, 40, 'blueband_200g.jpg', 2),
(16, 'Forvita Margarin 200g', 8500, 22, 'forvita_200g.jpg', 2),
(17, 'Minyak Kelapa Barco 1L', 32000, 7, 'barco_1l.jpg', 2),
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
(30, 'Royco Ayam 100g', 5000, 92, 'royco_ayam_100g.jpg', 4),
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
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah_barang` int(10) UNSIGNED NOT NULL,
  `subtotal` int(10) UNSIGNED NOT NULL,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_transaksi`, `id_barang`, `jumlah_barang`, `subtotal`, `tanggal_transaksi`) VALUES
(1001, 101, 1, 15000, '2026-05-28 08:46:26'),
(1001, 103, 1, 5000, '2026-05-28 08:46:26'),
(1002, 102, 1, 12000, '2026-05-28 08:46:26'),
(1002, 103, 1, 5000, '2026-05-28 08:46:26'),
(1003, 101, 2, 30000, '2026-05-28 08:46:26'),
(1004, 103, 1, 5000, '2026-05-28 08:46:26'),
(1004, 105, 1, 10000, '2026-05-28 08:46:26'),
(1005, 101, 2, 30000, '2026-05-28 08:46:26'),
(1005, 103, 3, 15000, '2026-05-28 08:46:26'),
(1010, 101, 4, 60000, '2026-05-28 08:46:26'),
(1010, 102, 2, 24000, '2026-05-28 08:46:26'),
(1010, 103, 1, 5000, '2026-05-28 08:46:26'),
(1010, 104, 2, 16000, '2026-05-28 08:46:26'),
(1034, 1, 1, 75000, '2026-05-29 07:57:06'),
(1035, 1, 1, 75000, '2026-05-29 08:03:45'),
(1036, 1, 2, 150000, '2026-05-29 08:18:44'),
(1037, 1, 2, 150000, '2026-05-29 08:20:02'),
(1038, 1, 2, 150000, '2026-05-29 08:20:50'),
(1039, 1, 2, 150000, '2026-05-29 08:21:32'),
(1040, 1, 2, 150000, '2026-05-29 08:22:14'),
(1041, 1, 2, 150000, '2026-05-29 08:22:50'),
(1042, 1, 2, 150000, '2026-05-29 08:24:07'),
(1043, 1, 2, 150000, '2026-05-29 08:25:38'),
(1044, 1, 2, 150000, '2026-05-29 08:25:51'),
(1045, 1, 2, 150000, '2026-05-29 08:26:06'),
(1046, 1, 2, 150000, '2026-05-29 08:27:06'),
(1047, 1, 2, 150000, '2026-05-29 08:31:24'),
(1048, 1, 2, 150000, '2026-05-29 08:31:46'),
(1049, 1, 2, 150000, '2026-05-29 08:31:48'),
(1050, 16, 1, 8500, '2026-05-29 08:51:07'),
(1050, 17, 1, 32000, '2026-05-29 08:51:07'),
(1050, 30, 1, 5000, '2026-05-29 08:51:07'),
(1051, 16, 1, 8500, '2026-05-29 08:53:43'),
(1051, 17, 1, 32000, '2026-05-29 08:53:43'),
(1051, 30, 1, 5000, '2026-05-29 08:53:43'),
(1052, 16, 1, 8500, '2026-05-29 08:54:05'),
(1052, 17, 1, 32000, '2026-05-29 08:54:05'),
(1052, 30, 1, 5000, '2026-05-29 08:54:05'),
(1053, 16, 1, 8500, '2026-05-29 08:54:22'),
(1053, 17, 1, 32000, '2026-05-29 08:54:22'),
(1053, 30, 1, 5000, '2026-05-29 08:54:22'),
(1054, 16, 1, 8500, '2026-05-29 08:54:30'),
(1054, 17, 1, 32000, '2026-05-29 08:54:30'),
(1054, 30, 1, 5000, '2026-05-29 08:54:30'),
(1055, 16, 1, 8500, '2026-05-29 09:00:03'),
(1055, 17, 1, 32000, '2026-05-29 09:00:03'),
(1055, 30, 1, 5000, '2026-05-29 09:00:03'),
(1056, 16, 1, 8500, '2026-05-29 09:43:30'),
(1056, 17, 1, 32000, '2026-05-29 09:43:30'),
(1056, 30, 1, 5000, '2026-05-29 09:43:30'),
(1057, 16, 1, 8500, '2026-05-29 09:43:35'),
(1057, 17, 1, 32000, '2026-05-29 09:43:35'),
(1057, 30, 1, 5000, '2026-05-29 09:43:35'),
(1058, 1, 1, 75000, '2026-05-31 01:20:42'),
(1059, 1, 2, 150000, '2026-05-31 01:38:18'),
(1059, 2, 1, 74000, '2026-05-31 01:38:18'),
(1059, 3, 1, 76000, '2026-05-31 01:38:18'),
(1059, 4, 1, 20000, '2026-05-31 01:38:18'),
(1060, 1, 3, 225000, '2026-05-31 01:39:24'),
(1060, 2, 2, 148000, '2026-05-31 01:39:24'),
(1060, 3, 1, 76000, '2026-05-31 01:39:24'),
(1060, 4, 1, 20000, '2026-05-31 01:39:24'),
(1061, 1, 3, 225000, '2026-05-31 01:49:05'),
(1061, 2, 2, 148000, '2026-05-31 01:49:05'),
(1061, 3, 1, 76000, '2026-05-31 01:49:05'),
(1061, 4, 1, 20000, '2026-05-31 01:49:05'),
(1062, 1, 3, 225000, '2026-05-31 02:00:11'),
(1062, 2, 2, 148000, '2026-05-31 02:00:11'),
(1062, 3, 1, 76000, '2026-05-31 02:00:11'),
(1062, 4, 1, 20000, '2026-05-31 02:00:11'),
(1065, 1, 3, 225000, '2026-05-31 03:40:07'),
(1067, 1, 1, 75000, '2026-05-31 03:44:37'),
(1067, 2, 1, 74000, '2026-05-31 03:44:37'),
(1067, 3, 1, 76000, '2026-05-31 03:44:37'),
(1067, 4, 1, 20000, '2026-05-31 03:44:37'),
(1067, 6, 1, 14000, '2026-05-31 03:44:37'),
(1067, 7, 1, 11000, '2026-05-31 03:44:37'),
(1070, 1, 1, 75000, '2026-05-31 03:45:22'),
(1071, 3, 1, 76000, '2026-05-31 04:08:57'),
(1072, 5, 1, 12000, '2026-05-31 04:48:31'),
(1075, 5, 1, 12000, '2026-05-31 05:24:20'),
(1076, 4, 1, 20000, '2026-05-31 05:25:55'),
(1077, 11, 1, 21000, '2026-05-31 05:28:11'),
(1078, 6, 1, 14000, '2026-05-31 06:09:53'),
(1079, 5, 1, 12000, '2026-05-31 06:10:39'),
(1080, 6, 1, 14000, '2026-05-31 06:13:03'),
(1081, 6, 1, 14000, '2026-05-31 07:12:01'),
(1082, 6, 1, 14000, '2026-05-31 07:12:13'),
(1083, 6, 1, 14000, '2026-05-31 07:13:15'),
(1084, 6, 1, 14000, '2026-05-31 07:15:37'),
(1085, 6, 1, 14000, '2026-05-31 07:19:00'),
(1086, 4, 1, 20000, '2026-05-31 07:19:59'),
(1087, 4, 1, 20000, '2026-05-31 07:20:03'),
(1088, 4, 1, 20000, '2026-05-31 07:20:06'),
(1089, 4, 1, 20000, '2026-05-31 07:20:32'),
(1091, 5, 1, 12000, '2026-05-31 07:27:47'),
(1092, 10, 1, 38000, '2026-05-31 07:29:04'),
(1093, 1, 1, 75000, '2026-06-03 14:01:07'),
(1094, 1, 1, 75000, '2026-06-03 14:05:56'),
(1095, 6, 1, 14000, '2026-06-03 14:11:46');

-- --------------------------------------------------------

--
-- Table structure for table `kasir`
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
-- Dumping data for table `kasir`
--

INSERT INTO `kasir` (`id_kasir`, `nama_lengkap`, `username`, `password_hash`, `access_level`, `created_at`) VALUES
(1, 'Rizky Hadi', 'rizkyhadi', '$2y$10$YP9XEHqB7DGvaDg0Xs50yOvRURl5d0TYe/b0ncqHqdk1V3MlrR6Zi', 20, '2026-05-28 11:17:59'),
(2, 'Azkafalah Cendikia Suryatmaja', 'azkafalahcendikia', '$2y$10$aTpli389aff/ew2JkXNtleuhOy27kt7VekTb3runuiAOk8puZc.6G', 0, '2026-05-28 11:17:59'),
(3, 'Rizky Hadi', 'rizky', '$2y$10$YP9XEHqB7DGvaDg0Xs50yOvRURl5d0TYe/b0ncqHqdk1V3MlrR6Zi', 10, '2026-05-28 11:17:59'),
(4, 'Nabila Indaswari', 'nabila_indaswari', '$2y$10$hmzjid4b3Ne8MT/yT0s6w.E3dGPwdF4v.6pZ3efE3yA/PyD6gMl7C', 20, '2026-06-03 12:04:44');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Beras & Biji-bijian'),
(4, 'Bumbu Dapur'),
(3, 'Gula & Garam'),
(12, 'Lain-lain'),
(8, 'Makanan Ringan'),
(6, 'Mie Instan & Pasta'),
(7, 'Minuman & Bubuk'),
(2, 'Minyak & Margarin'),
(10, 'Pembersih Rumah'),
(9, 'Perawatan Diri'),
(11, 'Telur & Bahan Segar'),
(5, 'Tepung & Bahan Kue');

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode` int(11) NOT NULL,
  `nama_metode` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode`, `nama_metode`) VALUES
(1, 'Cash'),
(2, 'Debit Card'),
(3, 'QRIS');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `total_harga` int(10) UNSIGNED NOT NULL,
  `total_bayar` int(10) UNSIGNED NOT NULL,
  `statuss` tinyint(1) NOT NULL,
  `id_kasir` int(11) NOT NULL,
  `id_metode` int(11) NOT NULL,
  `token_transaksi` varchar(100) NOT NULL
) ;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tanggal_transaksi`, `total_harga`, `total_bayar`, `statuss`, `id_kasir`, `id_metode`, `token_transaksi`) VALUES
(1065, '2026-05-31 10:40:07', 225000, 250000, 1, 3, 1, 'd4be479ef46e26ff93e11e9b96664779'),
(1066, '2026-05-31 10:40:37', 0, 250000, 1, 3, 1, '6c9387953f6a23740ebb0e450c31d783'),
(1067, '2026-05-31 10:44:37', 270000, 300000, 1, 3, 1, '48e0df67243eba2ee291ce91d71155ca'),
(1070, '2026-05-31 10:45:22', 75000, 100000, 1, 3, 1, '86066362aba2ef96b77168c5fac59ac1'),
(1071, '2026-05-31 11:08:57', 76000, 80000, 1, 3, 1, '2352b2f43c3b472d8d9e0733a4a3d342'),
(1072, '2026-05-31 11:48:31', 12000, 15000, 1, 1, 1, 'da088dd698407924983e2a2b08303fa5'),
(1075, '2026-05-31 12:24:20', 12000, 15000, 1, 1, 1, '2c242a287c2caec442615773ba8b06e8'),
(1076, '2026-05-31 12:25:55', 20000, 20000, 1, 1, 1, 'c875553102ad166578a62e310f60388b'),
(1077, '2026-05-31 12:28:11', 21000, 21000, 1, 1, 1, '97b300f1e76561dc8aa1cfef0bcff4ba'),
(1078, '2026-05-31 13:09:53', 14000, 15000, 1, 1, 1, 'ea9661ab6326994ef243d35026c93575'),
(1079, '2026-05-31 13:10:39', 12000, 12000, 1, 1, 1, '36afe543caa46d60f03b4fabf9393186'),
(1080, '2026-05-31 13:13:03', 14000, 15000, 1, 1, 1, '3fa2c81abd0a2fbf6f97392d9b582170'),
(1081, '2026-05-31 14:12:01', 14000, 15000, 1, 1, 1, '6d3667d36a90fcb5535717c219d34b89'),
(1082, '2026-05-31 14:12:13', 14000, 15000, 1, 1, 1, '335d364afb5fa4f67eb9911f383125b3'),
(1083, '2026-05-31 14:13:15', 14000, 14000, 1, 1, 1, '2cbc52d90b0a9e6f2e98204ca0aafc4c'),
(1084, '2026-05-31 14:15:37', 14000, 14000, 1, 1, 1, '75cca012d36d0f86aae74ffd60158205'),
(1085, '2026-05-31 14:19:00', 14000, 14000, 1, 1, 1, 'aae7f7a695b2033a26bdfe12ba285d0c'),
(1086, '2026-05-31 14:19:59', 20000, 20000, 1, 1, 1, '8d1179a6c032b07bedafab11fa7b02ce'),
(1087, '2026-05-31 14:20:03', 20000, 20000, 1, 1, 1, '3ea3354635d27b1755cf338f339364e6'),
(1088, '2026-05-31 14:20:06', 20000, 20000, 1, 1, 1, '520994695310507484c5f558ee4cadf6'),
(1089, '2026-05-31 14:20:32', 20000, 20000, 1, 1, 1, '7938b42d65a706b3b7d09f99ee6f5e9c'),
(1091, '2026-05-31 14:27:47', 12000, 12000, 1, 1, 1, 'c7fe2c7909122a591100ce52cd61aeef'),
(1092, '2026-05-31 14:29:04', 38000, 50000, 1, 1, 1, 'ef00aa5ddf64c8bc8464b845863ea47c'),
(1093, '2026-06-03 21:01:07', 75000, 100000, 1, 3, 1, 'b936380b91f67b46416401a20d3b0025'),
(1094, '2026-06-03 21:05:56', 75000, 100000, 1, 3, 1, '13f377edd7fa0360e3b62a87194b9fed'),
(1095, '2026-06-03 21:11:46', 14000, 20000, 1, 4, 1, '8509ef0725c886213acd88ca053e2e3d');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_barang_kategori`
-- (See below for the actual view)
--
CREATE TABLE `view_barang_kategori` (
`nama_barang` varchar(100)
,`harga` int(11) unsigned
,`nama_kategori` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_transaksi_lengkap`
-- (See below for the actual view)
--
CREATE TABLE `view_transaksi_lengkap` (
);

-- --------------------------------------------------------

--
-- Structure for view `view_barang_kategori`
--
DROP TABLE IF EXISTS `view_barang_kategori`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_barang_kategori`  AS SELECT `b`.`nama_barang` AS `nama_barang`, `b`.`harga` AS `harga`, `k`.`nama_kategori` AS `nama_kategori` FROM (`barang` `b` join `kategori` `k` on(`b`.`id_kategori` = `k`.`id_kategori`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_transaksi_lengkap`
--
DROP TABLE IF EXISTS `view_transaksi_lengkap`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_transaksi_lengkap`  AS SELECT `t`.`id_transaksi` AS `id_transaksi`, `t`.`tanggal_transaksi` AS `tanggal_transaksi`, `t`.`total_harga` AS `total_harga`, `t`.`total_bayar` AS `total_bayar`, `k`.`nama_kasir` AS `nama_kasir`, `m`.`nama_metode` AS `nama_metode` FROM ((`transaksi` `t` join `kasir` `k` on(`t`.`id_kasir` = `k`.`id_kasir`)) join `metode_pembayaran` `m` on(`t`.`id_metode` = `m`.`id_metode`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_transaksi`,`id_barang`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`id_kasir`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `unique_nama_kategori` (`nama_kategori`);

--
-- Indexes for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `token_transaksi` (`token_transaksi`),
  ADD KEY `id_kasir` (`id_kasir`),
  ADD KEY `id_metode` (`id_metode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `kasir`
--
ALTER TABLE `kasir`
  MODIFY `id_kasir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_kasir`) REFERENCES `kasir` (`id_kasir`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_metode`) REFERENCES `metode_pembayaran` (`id_metode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
