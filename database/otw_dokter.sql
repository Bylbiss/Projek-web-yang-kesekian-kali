-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2026 at 04:50 AM
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
-- Database: `otw_dokter`
--

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `bidang_khusus` varchar(100) DEFAULT NULL,
  `biaya_konsultasi` decimal(10,2) DEFAULT NULL,
  `spesies_hewan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `nama_dokter`, `alamat`, `kota`, `no_hp`, `bidang_khusus`, `biaya_konsultasi`, `spesies_hewan`, `created_at`, `updated_at`) VALUES
(1, 'Aneska Zoya Raveena', 'Jl. Kaliurang No. 45', 'Yogyakarta', '081234567891', 'Bedah Hewan Kecil', 175000.00, 'kucing, anjing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(2, 'Arisha Yonna Tanu', 'Jl. Gejayan No. 12', 'Yogyakarta', '082345678912', 'Kesehatan Unggas', 130000.00, 'ayam, burung, itik', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(3, 'Alesha', 'Jl. Colombo No. 78', 'Yogyakarta', '083456789123', 'Reproduksi Hewan Ternak', 220000.00, 'sapi, kambing, domba', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(4, 'Aresha Ravan Arabella', 'Jl. Magelang No. 56', 'Yogyakarta', '084567891234', 'Dermatologi Hewan', 185000.00, 'anjing, kucing, hamster', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(5, 'Abila Rezfan Azkadina', 'Jl. Affandi No. 34', 'Yogyakarta', '085678912345', 'Nutrisi Hewan Eksotis', 170000.00, 'musang, landak, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(6, 'Aurora Ridha Zetana', 'Jl. Bantul No. 67', 'Yogyakarta', '086789123456', 'Kedokteran Hewan Umum', 140000.00, 'kucing, anjing, kelinci, hamster', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(7, 'Aariz Abrar Maalik', 'Jl. Seturan No. 89', 'Yogyakarta', '087891234567', 'Penyakit Tropis Hewan', 195000.00, 'sapi, kuda, babi', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(8, 'Abid Azhar Mubarak', 'Jl. Babarsari No. 23', 'Yogyakarta', '088912345678', 'Kesehatan Hewan Aquatik', 155000.00, 'ikan, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(9, 'Adnan Aiman Hafidz', 'Jl. Janti No. 45', 'Yogyakarta', '089123456789', 'Ortopedi Hewan', 225000.00, 'anjing, kucing, kuda', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(10, 'Aksa Althaf Rafiqi', 'Jl. Ringroad Utara No. 12', 'Yogyakarta', '081234567890', 'Kesehatan Reptil', 160000.00, 'ular, kadal, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(11, 'Alif Arsyad Ghazi', 'Jl. Wates No. 34', 'Yogyakarta', '082345678901', 'Anestesiologi Hewan', 190000.00, 'kucing, anjing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(12, 'Athalla Alfarizi Akbar', 'Jl. Godean No. 56', 'Yogyakarta', '083456789012', 'Kardiologi Hewan', 210000.00, 'anjing, kucing, kuda', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(13, 'Azriel Aqil Ibrahim', 'Jl. Parangtritis No. 78', 'Yogyakarta', '084567890123', 'Onkologi Hewan', 230000.00, 'kucing, anjing', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(14, 'Bagas Dewantara Sakti', 'Jl. Imogiri No. 90', 'Yogyakarta', '085678901234', 'Neurologi Hewan', 220000.00, 'anjing, kucing', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(15, 'Bagaskara Aditya Nugraha', 'Jl. Wonosari No. 11', 'Yogyakarta', '086789012345', 'Gigi dan Mulut Hewan', 165000.00, 'anjing, kucing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(16, 'Baldwin Aditya Wardhana', 'Jl. Prambanan No. 22', 'Yogyakarta', '087890123456', 'Bedah Hewan Kecil', 170000.00, 'kucing, anjing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(17, 'Bara Aditya Nugraha', 'Jl. Raya Yogya-Solo No. 33', 'Yogyakarta', '088901234567', 'Kesehatan Unggas', 125000.00, 'ayam, burung, itik', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(18, 'Barata Wisnu Tama', 'Jl. Raya Yogya-Magelang No. 44', 'Yogyakarta', '089012345678', 'Reproduksi Hewan Ternak', 215000.00, 'sapi, kambing, domba', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(19, 'Baruna Jagat Raya', 'Jl. Monjali No. 55', 'Yogyakarta', '081123456789', 'Dermatologi Hewan', 180000.00, 'anjing, kucing, hamster', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(20, 'Bhisma Yudha Pratama', 'Jl. Sudirman No. 66', 'Yogyakarta', '082234567890', 'Nutrisi Hewan Eksotis', 165000.00, 'musang, landak, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(21, 'Bryan Kenzo Pratama', 'Jl. Malioboro No. 77', 'Yogyakarta', '083345678901', 'Kedokteran Hewan Umum', 135000.00, 'kucing, anjing, kelinci, hamster', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(22, 'Oded Zhafran Athalla', 'Jl. Sosrowijayan No. 88', 'Yogyakarta', '084456789012', 'Penyakit Tropis Hewan', 200000.00, 'sapi, kuda, babi', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(23, 'Calvin Alfarizi Pratama', 'Jl. Pasar Kembang No. 99', 'Yogyakarta', '085567890123', 'Kesehatan Hewan Aquatik', 150000.00, 'ikan, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(24, 'Daniswara Bagus Kaizen', 'Jl. Taman Siswa No. 10', 'Yogyakarta', '086678901234', 'Ortopedi Hewan', 220000.00, 'anjing, kucing, kuda', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(25, 'Ethan Zayn Adrian', 'Jl. Cik Di Tiro No. 20', 'Yogyakarta', '087789012345', 'Kesehatan Reptil', 155000.00, 'ular, kadal, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(26, 'Fahreza Zayn Hakim', 'Jl. Prof. Dr. Sardjito No. 30', 'Yogyakarta', '088890123456', 'Anestesiologi Hewan', 185000.00, 'kucing, anjing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(27, 'Ghifari Dzaki Faza', 'Jl. KHA Dahlan No. 40', 'Yogyakarta', '089901234567', 'Kardiologi Hewan', 205000.00, 'anjing, kucing, kuda', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(28, 'Harith Rayyan Zahran', 'Jl. Urip Sumoharjo No. 50', 'Yogyakarta', '081012345678', 'Onkologi Hewan', 225000.00, 'kucing, anjing', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(29, 'Irfan Rizqi Abdullah', 'Jl. Suryotomo No. 60', 'Yogyakarta', '082123456789', 'Neurologi Hewan', 215000.00, 'anjing, kucing', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(30, 'Jonathan Noah Ezra', 'Jl. Mayor Suryotomo No. 70', 'Yogyakarta', '083234567890', 'Gigi dan Mulut Hewan', 160000.00, 'anjing, kucing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(31, 'Kharis Kaelan Aksa', 'Jl. Brigjen Katamso No. 80', 'Yogyakarta', '084345678901', 'Bedah Hewan Kecil', 165000.00, 'kucing, anjing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(32, 'Luthfi Rafi Alfarizi', 'Jl. Kusumanegara No. 90', 'Yogyakarta', '085456789012', 'Kesehatan Unggas', 120000.00, 'ayam, burung, itik', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(33, 'Milan Alvaro Putra', 'Jl. Adisucipto No. 100', 'Yogyakarta', '086567890123', 'Reproduksi Hewan Ternak', 210000.00, 'sapi, kambing, domba', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(34, 'Nathaniel Kaelan Satria', 'Jl. Laksda Adisucipto No. 110', 'Yogyakarta', '087678901234', 'Dermatologi Hewan', 175000.00, 'anjing, kucing, hamster', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(35, 'Prasetya Dwipa Aji', 'Jl. Raya Janti No. 120', 'Yogyakarta', '088789012345', 'Nutrisi Hewan Eksotis', 160000.00, 'musang, landak, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(36, 'Qamil Haikal Athala', 'Jl. Ringroad Selatan No. 130', 'Yogyakarta', '089890123456', 'Kedokteran Hewan Umum', 130000.00, 'kucing, anjing, kelinci, hamster', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(37, 'Rayhan Kaelan Narendra', 'Jl. Bantu No. 140', 'Yogyakarta', '081901234567', 'Penyakit Tropis Hewan', 195000.00, 'sapi, kuda, babi', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(38, 'Syafiq Rizky Ananda', 'Jl. Gamping No. 150', 'Yogyakarta', '082012345678', 'Kesehatan Hewan Aquatik', 145000.00, 'ikan, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(39, 'Tegar Bagaskara Wijaya', 'Jl. Sagan No. 160', 'Yogyakarta', '083123456789', 'Ortopedi Hewan', 215000.00, 'anjing, kucing, kuda', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(40, 'Uzair Faza Azzam', 'Jl. C. Simanjuntak No. 170', 'Yogyakarta', '084234567890', 'Kesehatan Reptil', 150000.00, 'ular, kadal, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(41, 'Vincent Ethan Alvaro', 'Jl. Gedong Kuning No. 180', 'Yogyakarta', '085345678901', 'Anestesiologi Hewan', 180000.00, 'kucing, anjing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(42, 'Warren Kenzo Pratama', 'Jl. Ngadisuryan No. 190', 'Yogyakarta', '086456789012', 'Kardiologi Hewan', 200000.00, 'anjing, kucing, kuda', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(43, 'Yudhistira Abhimanyu Arjuna', 'Jl. Prawirotaman No. 200', 'Yogyakarta', '087567890123', 'Onkologi Hewan', 220000.00, 'kucing, anjing', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(44, 'Zhian Farras Arshakalif', 'Jl. Tirtodipuran No. 210', 'Yogyakarta', '088678901234', 'Neurologi Hewan', 210000.00, 'anjing, kucing', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(45, 'Bylbiss El Haqqie', 'Jl. Veteran No. 12', 'Yogyakarta', '088803178628', 'Bedah Hewan Kecil', 150000.00, 'kucing, anjing, kelinci', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(46, 'Alifah Chairul Munawar', 'Jl. Diponegoro No. 45', 'Yogyakarta', '081393706713', 'Kesehatan Unggas', 120000.00, 'ayam, burung, itik', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(47, 'Risya Rizqiyah Haryati', 'Jl. Ahmad Yani No. 78', 'Yogyakarta', '088803178628', 'Reproduksi Hewan Ternak', 200000.00, 'sapi, kambing, domba', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(48, 'Agung Dwi Ratna', 'Jl. Gatot Subroto No. 33', 'Yogyakarta', '081393706713', 'Dermatologi Hewan', 175000.00, 'anjing, kucing, hamster', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(49, 'Hamim Thohari', 'Jl. Sudirman No. 90', 'Yogyakarta', '088803178628', 'Nutrisi Hewan Eksotis', 160000.00, 'musang, landak, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(50, 'Siti Azizah', 'Jl. Pahlawan No. 21', 'Yogyakarta', '081393706713', 'Kedokteran Hewan Umum', 130000.00, 'kucing, anjing, kelinci, hamster', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(51, 'Azza Wulandari', 'Jl. Merdeka No. 55', 'Yogyakarta', '088803178628', 'Penyakit Tropis Hewan', 190000.00, 'sapi, kuda, babi', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(52, 'Khusnul Khuluq', 'Jl. Kartini No. 17', 'Yogyakarta', '081393706713', 'Kesehatan Hewan Aquatik', 140000.00, 'ikan, kura-kura', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(53, 'Abid Mustaauliya', 'Jl. Raya Bogor No. 66', 'Yogyakarta', '088803178628', 'Ortopedi Hewan', 210000.00, 'anjing, kucing, kuda', '2026-07-04 02:20:01', '2026-07-04 02:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan_offline`
--

CREATE TABLE `pemesanan_offline` (
  `id_antrean` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `id_pemilik` int(11) NOT NULL,
  `id_pet` int(11) NOT NULL,
  `nomor_antrean` varchar(20) NOT NULL,
  `tanggal_antrean` date NOT NULL,
  `waktu_antrean` time NOT NULL,
  `keluhan` text DEFAULT NULL,
  `status_antrean` enum('menunggu','diproses','selesai','batal') DEFAULT 'menunggu',
  `estimasi_waktu` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemesanan_offline`
--

INSERT INTO `pemesanan_offline` (`id_antrean`, `id_dokter`, `id_pemilik`, `id_pet`, `nomor_antrean`, `tanggal_antrean`, `waktu_antrean`, `keluhan`, `status_antrean`, `estimasi_waktu`, `created_at`, `updated_at`) VALUES
(26, 11, 1, 11, '001', '2025-12-17', '08:00:00', '', 'menunggu', NULL, '2026-07-04 02:20:01', '2026-07-04 02:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan_online`
--

CREATE TABLE `pemesanan_online` (
  `id_pemesanan` int(11) NOT NULL,
  `kode_pemesanan` varchar(20) NOT NULL,
  `id_pemilik` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `id_pet` int(11) NOT NULL,
  `tanggal_konsultasi` date NOT NULL,
  `waktu_konsultasi` varchar(5) NOT NULL,
  `keluhan` text NOT NULL,
  `biaya_konsultasi` decimal(10,2) NOT NULL,
  `kupon_digunakan` varchar(50) DEFAULT NULL,
  `jumlah_diskon` decimal(10,2) DEFAULT 0.00,
  `total_biaya` decimal(10,2) NOT NULL,
  `status_pemesanan` enum('pending','confirmed','paid','completed','cancelled','rejected') DEFAULT 'pending',
  `link_konsultasi` varchar(255) DEFAULT NULL,
  `waktu_pemesanan` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemesanan_online`
--

INSERT INTO `pemesanan_online` (`id_pemesanan`, `kode_pemesanan`, `id_pemilik`, `id_dokter`, `id_pet`, `tanggal_konsultasi`, `waktu_konsultasi`, `keluhan`, `biaya_konsultasi`, `kupon_digunakan`, `jumlah_diskon`, `total_biaya`, `status_pemesanan`, `link_konsultasi`, `waktu_pemesanan`, `created_at`, `updated_at`) VALUES
(25, 'ONLINE-2025121611175', 1, 11, 11, '2025-12-17', '08:00', 'mules', 210000.00, '0', 31500.00, 178500.00, 'pending', NULL, '2025-12-16 17:17:54', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(26, 'ONLINE-2026041712043', 10, 11, 15, '2026-04-17', '20:00', 'mencret', 210000.00, '0', 31500.00, 178500.00, 'pending', NULL, '2026-04-17 17:04:30', '2026-07-04 02:20:01', '2026-07-04 02:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `pemilik`
--

CREATE TABLE `pemilik` (
  `id_pemilik` int(11) NOT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `kode_pos` varchar(5) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemilik`
--

INSERT INTO `pemilik` (`id_pemilik`, `nama_pemilik`, `password`, `no_hp`, `email`, `alamat`, `kota`, `kode_pos`, `created_at`, `updated_at`) VALUES
(1, 'bilbis elhaqqi', '$2y$10$HNRktEVDudp5APIMCPVTh.Exzlptx4TEj1h30OQBrdyTFQJVWGMYa', '+6288805598510', 'bylbisselhaqqie966@gmail.com', 'Bantul', 'Yogyakarta', '', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(6, 'Ipeh aja', '$2y$10$Idru/b6j2NCLLQSQ.WeVCOivl2Ha4sIMZ4XjWPy7BvNTOJ4Tsl3mS', '6288805598511', 'ipepe@gmail.com', 'jalan jalan dulu', 'Yogyakarta', '', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(7, 'bilbis elhaqqi', '$2y$10$Lf4j.QH2eH0Ege8Zj0Fldub76Yki/MAv39/qxO6pEhZpyuhD.3PdS', '6288805598510', 'holokatci@gmail.com', 'Jl. A. W. Syahrani', 'Yogyakarta', '', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(8, 'ucup baba', '$2y$10$kpp/iYdtSossaPsRVsh6quav/RnGUXHLBBa0z/8qWn.caV3VJUvtu', '621234567892', 'hi@gmail.com', 'jalan jogja', 'yk', '', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(9, 'Alipeh Chaii', '$2y$10$qNvC0SXxPBONByAwKLKpUOrbmOI3ONEOcNTJxX2n/lmxA9qHk3leK', '620987766878', 'bylbisselhaqqie@gmail.com', 'Jl. A. W. Syahrani', 'Yogyakarta', '', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(10, 'bilbis elhaqqi', '$2y$10$UlxqmGSI0a9tH26SLeMw0uaYA0voG2KLv0rpTyy00pE22qTBriH6q', '6288805598510', 'bylbisselhaqqie0966@gmail.com', 'Jl. A. W. Syahrani', 'Yogyakarta', '', '2026-07-04 02:20:01', '2026-07-04 02:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id_pet` int(11) NOT NULL,
  `id_pemilik` int(11) NOT NULL,
  `nama_pet` varchar(50) NOT NULL,
  `jenis_kelamin` enum('jantan','betina','tidak_diketahui') DEFAULT 'tidak_diketahui',
  `jenis_hewan` enum('sapi','kambing','kerbau','ayam','kucing','kelinci','anjing','hamster','burung','ikan','musang','kura-kura','landak','babi','kuda','domba','lain-lain') NOT NULL,
  `ras` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `usia` int(11) DEFAULT NULL,
  `berat` decimal(5,2) DEFAULT NULL,
  `sterilisasi` enum('sudah','belum') DEFAULT 'belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id_pet`, `id_pemilik`, `nama_pet`, `jenis_kelamin`, `jenis_hewan`, `ras`, `tanggal_lahir`, `usia`, `berat`, `sterilisasi`, `created_at`, `updated_at`) VALUES
(8, 6, 'meng', 'jantan', 'kucing', '', NULL, NULL, NULL, 'belum', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(10, 7, 'onyen', 'jantan', 'kelinci', '', NULL, NULL, NULL, 'belum', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(11, 1, 'Opet', 'betina', 'kucing', '', NULL, NULL, 0.00, 'belum', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(12, 8, 'mujaer', 'jantan', 'anjing', 'apaaja', NULL, NULL, NULL, 'belum', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(13, 1, 'ipeeyy', 'betina', 'musang', '', NULL, NULL, 0.00, 'belum', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(14, 9, 'Emeng', 'jantan', 'ikan', '', NULL, NULL, 1.00, 'belum', '2026-07-04 02:20:01', '2026-07-04 02:20:01'),
(15, 10, 'Janda', 'betina', 'kucing', 'kampung', NULL, NULL, NULL, 'belum', '2026-07-04 02:20:01', '2026-07-04 02:20:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`);

--
-- Indexes for table `pemesanan_offline`
--
ALTER TABLE `pemesanan_offline`
  ADD PRIMARY KEY (`id_antrean`),
  ADD UNIQUE KEY `unique_antrean` (`id_dokter`,`tanggal_antrean`,`nomor_antrean`),
  ADD KEY `id_dokter` (`id_dokter`),
  ADD KEY `id_pemilik` (`id_pemilik`),
  ADD KEY `id_pet` (`id_pet`);

--
-- Indexes for table `pemesanan_online`
--
ALTER TABLE `pemesanan_online`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD UNIQUE KEY `kode_pemesanan` (`kode_pemesanan`),
  ADD KEY `id_pet` (`id_pet`),
  ADD KEY `id_pemilik` (`id_pemilik`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD PRIMARY KEY (`id_pemilik`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id_pet`),
  ADD KEY `id_pemilik` (`id_pemilik`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `pemesanan_offline`
--
ALTER TABLE `pemesanan_offline`
  MODIFY `id_antrean` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `pemesanan_online`
--
ALTER TABLE `pemesanan_online`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `pemilik`
--
ALTER TABLE `pemilik`
  MODIFY `id_pemilik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id_pet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pemesanan_offline`
--
ALTER TABLE `pemesanan_offline`
  ADD CONSTRAINT `pemesanan_offline_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_offline_ibfk_2` FOREIGN KEY (`id_pemilik`) REFERENCES `pemilik` (`id_pemilik`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_offline_ibfk_3` FOREIGN KEY (`id_pet`) REFERENCES `pets` (`id_pet`) ON DELETE CASCADE;

--
-- Constraints for table `pemesanan_online`
--
ALTER TABLE `pemesanan_online`
  ADD CONSTRAINT `pemesanan_online_ibfk_1` FOREIGN KEY (`id_pemilik`) REFERENCES `pemilik` (`id_pemilik`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_online_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_online_ibfk_3` FOREIGN KEY (`id_pet`) REFERENCES `pets` (`id_pet`) ON DELETE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`id_pemilik`) REFERENCES `pemilik` (`id_pemilik`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
