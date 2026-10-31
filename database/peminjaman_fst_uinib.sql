-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 27, 2026 at 02:39 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peminjaman_fst_uinib`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` bigint UNSIGNED NOT NULL,
  `kategori_id` bigint UNSIGNED NOT NULL,
  `kode_fasilitas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_fasilitas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('tersedia','dipinjam','perbaikan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `kondisi_awal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `kapasitas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '1 Unit',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `kategori_id`, `kode_fasilitas`, `nama_fasilitas`, `status`, `kondisi_awal`, `kapasitas`, `jumlah`, `deskripsi`, `gambar`, `created_at`, `updated_at`) VALUES
(1, 1, 'RK-101', 'Ruang Kuliah 101', 'tersedia', 'baik', NULL, '1 Unit', 'Kapasitas 40 Orang, Full AC, Whiteboard', NULL, '2026-09-20 05:00:03', '2026-09-20 05:00:03'),
(2, 2, 'LAB-KOM', 'Lab Komputer Dasar', 'tersedia', 'baik', NULL, '1 Unit', '40 Unit PC Inti, AC Sentral', NULL, '2026-09-20 05:00:03', '2026-09-20 05:00:03'),
(3, 5, 'LCD-01', 'Proyektor Epson EB-X400 (LCD-01)', 'tersedia', 'baik', NULL, '1 Unit', 'Lengkap dengan tas, kabel HDMI 1.5m, dan remote control.', NULL, '2026-09-20 05:00:03', '2026-10-18 08:57:47'),
(4, 5, 'LCD-02', 'Proyektor Epson EB-X500 (LCD-02)', 'tersedia', 'rusak_ringan', NULL, '1 Unit', 'Lengkap dengan tas dan kabel HDMI. Remote kontrol tidak tersedia.', NULL, '2026-09-20 05:00:03', '2026-10-18 08:57:47'),
(5, 6, 'SPK-01', 'Speaker Portable Soundcraft 12 Inch (SPK-01)', 'tersedia', 'baik', NULL, '1 Unit', 'Speaker aktif beroda dengan Bluetooth, 2 Mic Wireless, dan kabel charger.', NULL, '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(6, 6, 'MIC-01', 'Wireless Microphone Shure Dual Channel (MIC-01)', 'tersedia', 'baik', NULL, '1 Unit', 'Receiver UHF dual channel + 2 handheld wireless mic + adaptor.', NULL, '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(7, 8, 'PTR-01', 'Wireless Presenter Logitech R400 (PTR-01)', 'tersedia', 'baik', NULL, '1 Unit', 'Pointer laser merah dengan dongle USB receiver, baterai AAA.', NULL, '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(8, 7, 'KBL-01', 'Kabel Rol Terminal Uticon 20M (KBL-01)', 'tersedia', 'baik', NULL, '1 Unit', 'Kabel rol listrik 20 meter, 4 stop kontak dengan lampu indikator.', NULL, '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(9, 7, 'CNV-01', 'Adapter Konverter HDMI to VGA + Audio (CNV-01)', 'tersedia', 'baik', NULL, '1 Unit', 'Konverter port display untuk laptop ke proyektor model lama.', NULL, '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(10, 9, 'AULA-01', 'Aula FST', 'tersedia', 'baik', '200 orang', '1 Unit', 'Infocus, Sound System, AC', 'images/aula-fst.jpg', '2026-10-23 17:18:00', '2026-10-24 15:15:39'),
(11, 10, 'RR-01', 'Ruang Rapat 1', 'tersedia', 'baik', '20 Orang', '1 Unit', 'Ruangan Ruang Rapat 1 FST UIN Imam Bonjol Padang', NULL, '2026-10-23 17:18:00', '2026-10-23 17:18:00'),
(12, 1, 'RK-01', 'Ruang Kelas 1', 'tersedia', 'baik', '40 orang', '1 Unit', 'Infocus, AC, Papan Tulis', 'images/ruang-kelas.jpg', '2026-10-23 17:18:00', '2026-10-24 15:15:39'),
(13, 10, 'RR-02', 'Ruang Rapat 2', 'dipinjam', 'baik', '15 Orang', '1 Unit', 'Ruangan Ruang Rapat 2 FST UIN Imam Bonjol Padang', NULL, '2026-10-23 17:18:00', '2026-10-23 17:18:00'),
(14, 1, 'RK-02', 'Ruang Kelas 2', 'tersedia', 'baik', '40 Orang', '1 Unit', 'Ruangan Ruang Kelas 2 FST UIN Imam Bonjol Padang', NULL, '2026-10-23 17:18:00', '2026-10-23 17:18:00'),
(15, 11, 'RD-01', 'Ruang Diskusi', 'tersedia', 'baik', '10 Orang', '1 Unit', 'Ruangan Ruang Diskusi FST UIN Imam Bonjol Padang', NULL, '2026-10-23 17:18:00', '2026-10-23 17:18:00'),
(16, 2, 'LAB-01', 'Lab Komputer', 'tersedia', 'baik', '40 orang', '1 Unit', 'Infocus, AC', 'images/lab-komputer.jpg', '2026-10-23 17:18:00', '2026-10-24 15:15:39'),
(17, 12, 'INF-01', 'Infocus', 'tersedia', 'Baik', NULL, '5 Unit', 'Alat Infocus untuk kegiatan akademik FST.', 'images/infocus.jpg', '2026-10-23 17:38:00', '2026-10-25 01:55:29'),
(18, 12, 'LPT-01', 'Laptop', 'tersedia', 'Baik', NULL, '3 Unit', 'Alat Laptop untuk kegiatan akademik FST.', NULL, '2026-10-23 17:38:00', '2026-10-23 17:38:00'),
(19, 13, 'MIC-02', 'Mikrofon', 'tersedia', 'Baik', NULL, '2 Unit', 'Alat Mikrofon untuk kegiatan akademik FST.', 'images/mikrofon.jpg', '2026-10-23 17:38:00', '2026-10-25 01:55:29'),
(20, 13, 'SND-01', 'Sound System', 'tersedia', 'Baik', NULL, '2 Set', 'Alat Sound System untuk kegiatan akademik FST.', 'images/sound-system.jpg', '2026-10-23 17:38:00', '2026-10-25 01:55:29'),
(21, 14, 'PRJ-01', 'Proyektor', 'tersedia', 'Baik', NULL, '4 Unit', 'Alat Proyektor untuk kegiatan akademik FST.', NULL, '2026-10-23 17:38:00', '2026-10-23 17:38:00'),
(22, 14, 'HDM-01', 'Hdmi', 'tersedia', 'Baik', NULL, '10 Unit', 'Alat Hdmi untuk kegiatan akademik FST.', NULL, '2026-10-23 17:38:00', '2026-10-23 17:38:00'),
(23, 14, 'CAM-01', 'Kamera', 'perbaikan', 'Baik', NULL, '2 Unit', 'Alat Kamera untuk kegiatan akademik FST.', NULL, '2026-10-23 17:38:00', '2026-10-23 17:38:00'),
(24, 4, 'R-RAPAT-FST', 'Ruang Rapat FST', 'tersedia', 'baik', '30', '1', 'Ruang rapat utama pimpinan dan dosen FST', NULL, '2026-10-23 18:16:19', '2026-10-23 18:16:19'),
(25, 16, 'R-RAPAT', 'Ruang Rapat', 'tersedia', 'baik', '30', '1', NULL, NULL, '2026-10-24 03:53:03', '2026-10-24 03:53:03'),
(26, 16, 'AULA-FST', 'Aula', 'tersedia', 'baik', '200', '1', NULL, NULL, '2026-10-24 03:53:03', '2026-10-24 03:53:03');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_fasilitas`
--

CREATE TABLE `kategori_fasilitas` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('ruangan','peralatan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_fasilitas`
--

INSERT INTO `kategori_fasilitas` (`id`, `nama_kategori`, `jenis`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Kelas', 'ruangan', 'Ruangan untuk aktivitas perkuliahan.', '2026-09-20 05:00:03', '2026-09-28 15:02:22'),
(2, 'Labor', 'ruangan', 'Laboratorium terpadu untuk praktikum.', '2026-09-20 05:00:03', '2026-09-28 15:02:22'),
(4, 'Ruang Rapat', 'ruangan', 'Ruangan khusus untuk rapat atau meeting', '2026-09-28 14:46:12', '2026-09-28 14:46:12'),
(5, 'Infocus & Proyektor', 'peralatan', 'Proyektor LCD, Layar Portabel, dan aksesoris proyektor.', '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(6, 'Audio & Speaker', 'peralatan', 'Speaker portable, wireless mic, dan sound system kegiatan.', '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(7, 'Kabel & Konverter', 'peralatan', 'Kabel HDMI, VGA converter, kabel rol listrik terminal.', '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(8, 'Pointer & Aksesoris', 'peralatan', 'Wireless presenter pointer laser untuk sidang & presentasi.', '2026-10-18 08:57:47', '2026-10-18 08:57:47'),
(9, 'Aula', 'ruangan', 'Ruangan aula serbaguna', '2026-10-23 17:18:00', '2026-10-23 17:18:00'),
(10, 'Rapat', 'ruangan', 'Ruang rapat fakultas', '2026-10-23 17:18:00', '2026-10-23 17:18:00'),
(11, 'Diskusi', 'ruangan', 'Ruang diskusi dan kolaborasi', '2026-10-23 17:18:00', '2026-10-23 17:18:00'),
(12, 'Perangkat Presentasi', 'peralatan', 'Perangkat elektronik visual dan komputasi', '2026-10-23 17:38:00', '2026-10-25 01:55:29'),
(13, 'Audio', 'peralatan', 'Perangkat sistem tata suara dan mikrofon', '2026-10-23 17:38:00', '2026-10-23 17:38:00'),
(14, 'Pendukung', 'peralatan', 'Aksesoris kabel, konverter, dan pendukung', '2026-10-23 17:38:00', '2026-10-23 17:38:00'),
(15, 'Alat Multimedia', 'peralatan', NULL, '2026-10-23 18:16:19', '2026-10-23 18:16:19'),
(16, 'Ruangan', 'ruangan', NULL, '2026-10-24 03:53:03', '2026-10-24 03:53:03');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint UNSIGNED NOT NULL,
  `pengguna_id` bigint UNSIGNED DEFAULT NULL,
  `tindakan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_kegiatan` text COLLATE utf8mb4_unicode_ci,
  `alamat_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `pengguna_id`, `tindakan`, `detail_kegiatan`, `alamat_ip`, `created_at`) VALUES
(1, NULL, 'registrasi', 'Pengguna baru mendaftar: 2317020090@gmail.com', '127.0.0.1', '2026-09-20 12:21:08'),
(2, NULL, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-20 12:23:34'),
(3, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-20 12:23:51'),
(4, 1, 'hapus_admin', 'Menghapus admin: budi@fst-uinib.ac.id', '127.0.0.1', '2026-09-20 12:48:29'),
(5, 1, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-20 13:35:21'),
(6, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-20 13:36:27'),
(7, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-21 05:49:25'),
(8, 1, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-21 05:54:50'),
(9, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-21 06:02:03'),
(10, 1, 'hapus_admin', 'Menghapus admin: admin@fst-uinib.ac.id', '127.0.0.1', '2026-09-21 06:02:37'),
(11, 1, 'tambah_admin', 'Menambahkan admin baru: adminfst@uinib.ac.id', '127.0.0.1', '2026-09-21 06:08:59'),
(12, 1, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-21 06:09:05'),
(13, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-21 06:09:14'),
(14, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-21 07:40:35'),
(15, NULL, 'registrasi', 'Pengguna baru mendaftar: 2317020090@uinib.ac.id', '127.0.0.1', '2026-09-21 07:42:23'),
(16, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-23 14:25:12'),
(17, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-26 15:15:51'),
(18, 5, 'ajukan_peminjaman', 'Admin menginput peminjaman untuk: Proyektor Epson LCD-01', '127.0.0.1', '2026-09-26 15:17:25'),
(19, 5, 'proses_pengembalian', 'Memproses pengembalian peminjaman #1 - Kondisi: baik', '127.0.0.1', '2026-09-26 15:17:37'),
(20, 5, 'ajukan_peminjaman', 'Admin menginput peminjaman untuk: Proyektor Epson LCD-01', '127.0.0.1', '2026-09-26 15:28:47'),
(21, 5, 'proses_pengembalian', 'Memproses pengembalian peminjaman #2 - Kondisi: baik', '127.0.0.1', '2026-09-26 15:28:57'),
(22, 5, 'ajukan_peminjaman', 'Admin menginput peminjaman untuk: Proyektor Epson LCD-01', '127.0.0.1', '2026-09-26 16:04:51'),
(23, 5, 'proses_pengembalian', 'Memproses pengembalian peminjaman #3 - Kondisi: baik', '127.0.0.1', '2026-09-26 16:04:58'),
(24, 5, 'ajukan_peminjaman', 'Admin menginput peminjaman untuk: Proyektor Epson LCD-01', '127.0.0.1', '2026-09-26 17:39:10'),
(25, 5, 'proses_pengembalian', 'Memproses pengembalian peminjaman #4 - Kondisi: baik', '127.0.0.1', '2026-09-26 17:39:16'),
(26, 5, 'ajukan_peminjaman', 'Admin menginput peminjaman untuk: Proyektor Epson LCD-01', '127.0.0.1', '2026-09-26 17:53:45'),
(27, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-26 17:54:09'),
(28, NULL, 'registrasi', 'Pengguna baru mendaftar: 2317020090@gmail.com', '127.0.0.1', '2026-09-27 05:30:29'),
(29, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 13:54:40'),
(30, 1, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 13:55:38'),
(31, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 13:55:46'),
(32, 5, 'ajukan_peminjaman', 'Admin menginput peminjaman untuk: Proyektor Epson LCD-01', '127.0.0.1', '2026-09-28 13:56:45'),
(33, 5, 'proses_pengembalian', 'Memproses pengembalian peminjaman #6 - Kondisi: baik', '127.0.0.1', '2026-09-28 13:57:03'),
(34, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 13:59:01'),
(35, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 14:04:41'),
(36, 1, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 14:29:57'),
(37, 7, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 14:30:20'),
(38, 7, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 15:01:58'),
(39, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 15:02:07'),
(40, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 15:14:26'),
(41, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 15:14:47'),
(42, 1, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 15:17:37'),
(43, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 15:23:40'),
(44, 1, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 16:09:35'),
(45, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 16:09:48'),
(46, 5, 'ubah_fasilitas', 'Mengubah fasilitas: Proyektor Epson LCD-02', '127.0.0.1', '2026-09-28 16:10:29'),
(47, 5, 'ubah_fasilitas', 'Mengubah fasilitas: Proyektor Epson LCD-02', '127.0.0.1', '2026-09-28 16:10:37'),
(48, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 16:12:43'),
(49, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-09-28 16:31:54'),
(50, 5, 'ajukan_peminjaman', 'Admin menginput jadwal ruangan untuk: Ruang Kuliah 101', '127.0.0.1', '2026-09-28 16:38:11'),
(51, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-09-28 16:38:18'),
(52, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-02 18:11:26'),
(53, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-05 03:26:22'),
(54, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-23 16:31:10'),
(55, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-23 16:54:05'),
(56, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-23 16:57:07'),
(57, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-23 17:26:16'),
(58, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-23 18:01:41'),
(59, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-23 18:24:00'),
(60, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-23 18:25:51'),
(61, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-23 18:26:58'),
(62, 5, 'setujui_peminjaman', 'Menyetujui peminjaman #10 oleh Dr. Ahmad', '127.0.0.1', '2026-10-23 18:27:00'),
(63, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 01:17:46'),
(64, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 01:20:23'),
(65, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 01:34:46'),
(66, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 01:35:14'),
(67, 5, 'proses_pengembalian', 'Memproses pengembalian peminjaman #7 - Kondisi: baik', '127.0.0.1', '2026-10-24 01:35:15'),
(68, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 02:40:49'),
(69, 5, 'proses_pengembalian', 'Memproses pengembalian peminjaman #9 - Kondisi: baik', '127.0.0.1', '2026-10-24 02:43:41'),
(70, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 03:16:20'),
(71, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 03:27:35'),
(72, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 04:14:39'),
(73, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 04:15:51'),
(74, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 04:31:09'),
(75, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 05:02:11'),
(76, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-10-24 05:08:08'),
(77, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 05:09:07'),
(78, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 05:23:10'),
(79, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 05:50:07'),
(80, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 06:07:59'),
(81, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 06:20:30'),
(82, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 08:35:08'),
(83, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-10-24 08:35:21'),
(84, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 08:35:38'),
(85, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 08:45:27'),
(86, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 08:46:55'),
(87, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 14:45:16'),
(88, NULL, 'registrasi', 'Pengguna baru mendaftar: antahlah123@gmail.com', '127.0.0.1', '2026-10-24 14:56:38'),
(89, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 15:35:32'),
(90, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 16:11:37'),
(91, 20, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 19:11:37'),
(92, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 19:42:52'),
(93, 20, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-24 21:34:17'),
(94, 20, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 01:58:01'),
(95, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 04:05:37'),
(96, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 04:07:56'),
(97, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 04:10:43'),
(98, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 04:11:31'),
(99, 20, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 12:05:41'),
(100, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 12:20:40'),
(101, 8, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 14:20:50'),
(102, 20, 'ajukan_peminjaman', 'Mengajukan peminjaman fasilitas: Aula', '127.0.0.1', '2026-10-25 16:45:48'),
(103, 20, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-10-25 16:46:36'),
(104, 5, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 16:46:50'),
(105, 5, 'setujui_peminjaman', 'Menyetujui peminjaman #19 oleh Paldo Muliadi', '127.0.0.1', '2026-10-25 16:47:06'),
(106, 5, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-10-25 16:47:17'),
(107, 20, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-25 16:47:32'),
(108, 20, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-26 03:54:59'),
(109, 20, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-27 13:41:33'),
(110, 20, 'ajukan_peminjaman', 'Mengajukan peminjaman fasilitas: Ruang Rapat', '127.0.0.1', '2026-10-27 13:55:32'),
(111, 20, 'logout', 'Pengguna keluar dari sistem', '127.0.0.1', '2026-10-27 14:01:46'),
(112, 1, 'login', 'Pengguna berhasil masuk ke sistem', '127.0.0.1', '2026-10-27 14:03:30');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '0001_01_01_000003_create_kategori_fasilitas_table', 1),
(5, '0001_01_01_000004_create_fasilitas_table', 1),
(6, '0001_01_01_000005_create_peminjaman_table', 1),
(7, '0001_01_01_000006_create_pengembalian_table', 1),
(8, '0001_01_01_000007_create_ulasan_layanan_table', 1),
(9, '0001_01_01_000008_create_log_aktivitas_table', 1),
(10, '2026_09_20_125212_add_jenis_pengguna_to_pengguna_table', 2),
(11, '2026_09_20_131111_add_status_akun_to_pengguna_table', 3),
(12, '2026_09_28_221034_create_pengaturans_table', 4),
(13, '2026_10_24_001332_add_kapasitas_to_fasilitas_table', 5),
(14, '2026_10_24_003209_add_jumlah_to_fasilitas_table', 6),
(15, '2026_10_25_223433_add_jumlah_peserta_to_peminjaman_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('2317020090@gmail.com', '732397', '2026-09-28 14:00:56'),
('antahlah123@gmail.com', '200641', '2026-10-27 14:01:15'),
('superadmin@fst-uinib.ac.id', '042131', '2026-09-28 15:57:54');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` bigint UNSIGNED NOT NULL,
  `pengguna_id` bigint UNSIGNED NOT NULL,
  `fasilitas_id` bigint UNSIGNED NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `tujuan_peminjaman` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_peserta` int DEFAULT NULL,
  `status_pengajuan` enum('menunggu','disetujui','ditolak','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `alasan_penolakan` text COLLATE utf8mb4_unicode_ci,
  `berkas_persyaratan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path file PDF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `pengguna_id`, `fasilitas_id`, `tanggal_mulai`, `tanggal_selesai`, `tujuan_peminjaman`, `jumlah_peserta`, `status_pengajuan`, `alasan_penolakan`, `berkas_persyaratan`, `created_at`, `updated_at`) VALUES
(9, 8, 17, '2026-09-20 08:00:00', '2026-09-20 12:00:00', 'Seminar', NULL, 'disetujui', NULL, NULL, '2026-10-23 18:16:19', '2026-10-24 03:00:21'),
(10, 9, 24, '2026-09-19 08:00:00', '2026-09-19 10:00:00', 'Rapat Fakultas', NULL, 'selesai', NULL, NULL, '2026-10-23 18:16:19', '2026-10-24 01:26:58'),
(11, 10, 20, '2026-09-18 13:00:00', '2026-09-18 15:00:00', 'Kegiatan HMPS', NULL, 'disetujui', NULL, NULL, '2026-10-23 18:16:19', '2026-10-24 01:26:58'),
(12, 8, 17, '2026-09-20 08:00:00', '2026-09-20 12:00:00', 'Seminar Sistem Informasi', NULL, 'disetujui', NULL, NULL, '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(13, 9, 25, '2026-09-19 13:00:00', '2026-09-19 15:00:00', 'Rapat Fakultas', NULL, 'selesai', NULL, NULL, '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(14, 11, 20, '2026-09-18 08:00:00', '2026-09-18 10:00:00', 'Kegiatan HMPS', NULL, 'ditolak', 'Jadwal bentrok dengan acara Dies Natalis Fakultas', NULL, '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(15, 12, 18, '2026-09-17 09:00:00', '2026-09-17 12:00:00', 'Praktikum', NULL, 'disetujui', NULL, NULL, '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(16, 13, 26, '2026-09-16 14:00:00', '2026-09-16 17:00:00', 'Pelatihan', NULL, 'selesai', NULL, NULL, '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(17, 14, 17, '2026-09-15 08:00:00', '2026-09-15 10:00:00', 'Presentasi', NULL, 'disetujui', NULL, NULL, '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(18, 15, 19, '2026-09-14 10:00:00', '2026-09-14 12:00:00', 'Kuliah Umum', NULL, 'selesai', NULL, NULL, '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(19, 20, 26, '2026-12-23 08:00:00', '2026-12-23 12:00:00', 'kknn', NULL, 'disetujui', NULL, NULL, '2026-10-25 16:45:48', '2026-10-25 16:47:06'),
(20, 20, 25, '2026-10-28 08:00:00', '2026-10-28 12:00:00', 'bwsvd', NULL, 'menunggu', NULL, NULL, '2026-10-27 13:55:32', '2026-10-27 13:55:32');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturans`
--

CREATE TABLE `pengaturans` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturans`
--

INSERT INTO `pengaturans` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'logo', 'pengaturan/SYaCBdgFFKuJOLe2HUwxBBKxiR6oFAzbgmdKkbbW.png', '2026-09-28 15:17:01', '2026-09-28 15:17:01'),
(2, 'nama_website', 'SIMORA FST', '2026-09-28 15:24:35', '2026-09-28 15:24:35'),
(3, 'warna_nama_website', '#ffffff', '2026-09-28 15:24:35', '2026-10-05 03:28:57');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id` bigint UNSIGNED NOT NULL,
  `peminjaman_id` bigint UNSIGNED NOT NULL,
  `admin_id` bigint UNSIGNED NOT NULL,
  `tanggal_pengembalian` datetime NOT NULL,
  `kondisi_akhir` enum('baik','rusak','hilang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id`, `peminjaman_id`, `admin_id`, `tanggal_pengembalian`, `kondisi_akhir`, `catatan_admin`, `created_at`, `updated_at`) VALUES
(6, 10, 5, '2026-09-19 10:15:00', 'baik', 'Fasilitas dikembalikan dalam kondisi bersih dan lengkap tepat waktu.', '2026-10-24 01:26:58', '2026-10-24 01:26:58'),
(9, 13, 5, '2026-09-19 15:15:00', 'baik', 'Fasilitas dikembalikan lengkap dan dalam kondisi baik.', '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(10, 16, 5, '2026-09-16 17:15:00', 'baik', 'Fasilitas dikembalikan lengkap dan dalam kondisi baik.', '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(11, 18, 5, '2026-09-14 12:15:00', 'baik', 'Fasilitas dikembalikan lengkap dan dalam kondisi baik.', '2026-10-24 03:53:05', '2026-10-24 03:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_identitas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'NIM atau NIP',
  `nomor_telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kata_sandi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peran` enum('super_admin','admin','pengguna') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pengguna',
  `jenis_pengguna` enum('mahasiswa','dosen') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_akun` enum('aktif','menunggu','nonaktif') COLLATE utf8mb4_unicode_ci DEFAULT 'aktif',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama_lengkap`, `email`, `nomor_identitas`, `nomor_telepon`, `kata_sandi`, `peran`, `jenis_pengguna`, `status_akun`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin FST', 'superadmin@fst-uinib.ac.id', '198001012005011001', '08111111111', '$2y$12$/OOeoO29Kwq8NQp0.JTIRuPOCBa5TEuVw/6hYrOfHJ/PAjuQU0uXO', 'super_admin', NULL, 'aktif', NULL, '2026-09-20 05:00:02', '2026-09-20 05:00:02'),
(5, 'nando', 'adminfst@uinib.ac.id', '20382737828784', '082386799607', '$2y$12$dubAKaDSkNHoIN4ETPdOfu/L2Df5tFuPA/P8JLqhSXbK3eiqi2eDS', 'admin', NULL, 'aktif', NULL, '2026-09-20 23:08:59', '2026-09-20 23:08:59'),
(7, 'Paldo Muliadi', '2317020090@gmail.com', '2317020090', '082386799607', '$2y$12$8QCN023KrekkmkOH73/PGODyVvovxbny/sXnqZo8CByUD.PuUhUAO', 'pengguna', 'mahasiswa', 'aktif', NULL, '2026-09-27 05:30:29', '2026-09-27 05:30:29'),
(8, 'Budi', 'budi@fst.uinib.ac.id', '23170200013', '081234567891', '$2y$12$hJq5n0RGmOTO65BnjPuwDO.bH72o7efRXbdxVJBj71p2xwlfepRGi', 'pengguna', 'mahasiswa', 'aktif', NULL, '2026-10-23 18:15:22', '2026-10-23 18:15:22'),
(9, 'Dr. Ahmad', 'ahmad@fst.uinib.ac.id', '1987654321', '081234567892', '$2y$12$GKiY9lom4C3.0MWV8YaGNua4wMJth7kGthvkSYUTzeQ/Dv6gq.8tu', 'pengguna', 'dosen', 'aktif', NULL, '2026-10-23 18:15:23', '2026-10-23 18:15:23'),
(10, 'Marisa', 'marisa@fst.uinib.ac.id', '23170200010', '081234567893', '$2y$12$wCbebveYJjffeRfwuiay3.iBUiyCQnmNzvZvwhPXgG6b5uMMEcqIC', 'pengguna', 'mahasiswa', 'aktif', NULL, '2026-10-23 18:15:23', '2026-10-23 18:15:23'),
(11, 'Siti', 'siti@fst.uinib.ac.id', '23170200014', '081234567890', '$2y$12$i.9QcJ4L5DBG9/Ciq8lwtuRcJEHzhrHjphvGSFrcYnxuxy4wo3uW2', 'pengguna', 'mahasiswa', 'aktif', NULL, '2026-10-24 03:53:04', '2026-10-24 03:53:04'),
(12, 'Andi', 'andi@fst.uinib.ac.id', '23170200015', '081234567890', '$2y$12$0a.SfxL1p7jmoYV5bcW5/ecNPSIBqTvL0ZRxEsl.I4NoULNKF17ye', 'pengguna', 'mahasiswa', 'aktif', NULL, '2026-10-24 03:53:04', '2026-10-24 03:53:04'),
(13, 'Rina', 'rina@fst.uinib.ac.id', '23170200016', '081234567890', '$2y$12$tIdun74vEkApM9mJKCKgQuON4Nrs1HPSGa2Ao3EhujJ9rG2mYCh/K', 'pengguna', 'mahasiswa', 'aktif', NULL, '2026-10-24 03:53:04', '2026-10-24 03:53:04'),
(14, 'Rani', 'rani@fst.uinib.ac.id', '23170200017', '081234567890', '$2y$12$2a6ZLX16MPbC7muD8/6KfOwYqam5D/VMP4Yuyn4aUnwb9Gan7S9aS', 'pengguna', 'mahasiswa', 'aktif', NULL, '2026-10-24 03:53:04', '2026-10-24 03:53:04'),
(15, 'Dewi', 'dewi@fst.uinib.ac.id', '1988123456', '081234567890', '$2y$12$w7Ogcb5A0cSoZFX218X.FOjceVVV767s.7l3ujRmqSQMhIEYheLMK', 'pengguna', 'dosen', 'aktif', NULL, '2026-10-24 03:53:05', '2026-10-24 03:53:05'),
(16, 'Ahmad Fauzan', 'ahmad@gmail.com', '198501012010011001', '081234567891', '$2y$12$JpuaEYJrXT.sXwcfdRwNIORNiOXn4/Azcovyp8gJBKsscZ.F6wq4C', 'admin', NULL, 'aktif', NULL, '2026-10-24 05:42:45', '2026-10-24 05:42:45'),
(17, 'Siti Rahma', 'siti@gmail.com', '198802022012012002', '081234567892', '$2y$12$n5pq3EqFaT6j7iBVBdERIuW5GfeQVBWB7O5U6VkqMypYUTaH9M8GO', 'admin', NULL, 'aktif', NULL, '2026-10-24 05:42:45', '2026-10-24 05:42:45'),
(18, 'Marisa Tri Yolanda', 'marisa@gmail.com', '199203032015012003', '081234567893', '$2y$12$X8R8g3n2Wtr48URxQidnkeYE1lH0JNjbVqp1vbn2qzcfUjuTCk48O', 'admin', NULL, 'nonaktif', NULL, '2026-10-24 05:44:25', '2026-10-24 05:44:25'),
(19, 'Rina Putri', 'rina@gmail.com', '199504042018012004', '081234567894', '$2y$12$6Bs3vd7pBpMeYcaldtklmewPojVzHSrfshqnwLcsdH4alKy0Tdw4G', 'admin', NULL, 'aktif', NULL, '2026-10-24 05:44:25', '2026-10-24 05:44:25'),
(20, 'Paldo Muliadi', 'antahlah123@gmail.com', '4635262637', NULL, '$2y$12$ESn46OJsGqh2Uurnewy.yumDMtwSaEKYUT2LzaCqRddknduggB5Fe', 'pengguna', 'mahasiswa', 'aktif', NULL, '2026-10-24 14:56:38', '2026-10-24 14:56:38');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('zMy48jCqlgYINn8zqHbZYG0qbFz0WYrFfFuNX78k', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', 'eyJfdG9rZW4iOiJsdkJ0TGFnSkdVbjg4enNDT2kweGN4N0dRQm9ZZEhXOGc1SnhhbE1sIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3N1cGVyLWFkbWluXC91bGFzYW4iLCJyb3V0ZSI6InN1cGVyX2FkbWluLnVsYXNhbi5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1793110501);

-- --------------------------------------------------------

--
-- Table structure for table `ulasan_layanan`
--

CREATE TABLE `ulasan_layanan` (
  `id` bigint UNSIGNED NOT NULL,
  `peminjaman_id` bigint UNSIGNED NOT NULL,
  `pengguna_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL COMMENT 'Skala 1-5',
  `komentar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fasilitas_kode_fasilitas_unique` (`kode_fasilitas`),
  ADD KEY `fasilitas_kategori_id_foreign` (`kategori_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_fasilitas`
--
ALTER TABLE `kategori_fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_aktivitas_pengguna_id_foreign` (`pengguna_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjaman_pengguna_id_foreign` (`pengguna_id`),
  ADD KEY `peminjaman_fasilitas_id_foreign` (`fasilitas_id`);

--
-- Indexes for table `pengaturans`
--
ALTER TABLE `pengaturans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengaturans_key_unique` (`key`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengembalian_peminjaman_id_foreign` (`peminjaman_id`),
  ADD KEY `pengembalian_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengguna_email_unique` (`email`),
  ADD UNIQUE KEY `pengguna_nomor_identitas_unique` (`nomor_identitas`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `ulasan_layanan`
--
ALTER TABLE `ulasan_layanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ulasan_layanan_peminjaman_id_foreign` (`peminjaman_id`),
  ADD KEY `ulasan_layanan_pengguna_id_foreign` (`pengguna_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_fasilitas`
--
ALTER TABLE `kategori_fasilitas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pengaturans`
--
ALTER TABLE `pengaturans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `ulasan_layanan`
--
ALTER TABLE `ulasan_layanan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD CONSTRAINT `fasilitas_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_fasilitas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_fasilitas_id_foreign` FOREIGN KEY (`fasilitas_id`) REFERENCES `fasilitas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengembalian_peminjaman_id_foreign` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ulasan_layanan`
--
ALTER TABLE `ulasan_layanan`
  ADD CONSTRAINT `ulasan_layanan_peminjaman_id_foreign` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulasan_layanan_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
