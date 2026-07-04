<?php
session_start();
require 'auth.php';
require 'koneksi.php';

if (!sudahLogin()) {
    header('Location: ../B_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../B_jadwal_keKlinik.php');
    exit;
}

// Ambil data dari form
$id_dokter = intval($_POST['id_dokter']);
$id_pet = intval($_POST['id_pet']);
$tanggal_janji = $_POST['tanggal_janji'];
$waktu_janji = $_POST['waktu_janji'];
$keluhan = mysqli_real_escape_string($koneksi, $_POST['keluhan'] ?? '');
$id_pemilik = $_SESSION['user_id'];

// Validasi data
if (empty($id_dokter) || empty($id_pet) || empty($tanggal_janji) || empty($waktu_janji)) {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header('Location: ../B_pemesanan_klinik.php?doctor_id=' . $id_dokter);
    exit;
}

// Validasi: Cek apakah waktu yang dipilih sudah lewat
$current_datetime = date('Y-m-d H:i:s');
$selected_datetime = $tanggal_janji . ' ' . $waktu_janji;

if (strtotime($selected_datetime) <= strtotime($current_datetime)) {
    $_SESSION['error'] = "Waktu yang dipilih sudah lewat. Silakan pilih waktu yang akan datang.";
    header('Location: ../B_pemesanan_klinik.php?doctor_id=' . $id_dokter);
    exit;
}

// Validasi: Tidak bisa booking untuk hari Minggu jam 12:00 ke atas
$day_of_week = date('N', strtotime($tanggal_janji)); // 7 = Minggu
if ($day_of_week == 7) {
    // Jika hari Minggu, batasi hanya sampai jam 12:00
    $selected_time = strtotime($waktu_janji);
    if ($selected_time > strtotime('12:00:00')) {
        $_SESSION['error'] = "Pada hari Minggu, booking hanya tersedia hingga jam 12:00 WIB.";
        header('Location: ../B_pemesanan_klinik.php?doctor_id=' . $id_dokter);
        exit;
    }
}

// Validasi: Tidak bisa booking untuk hari Sabtu jam 14:00 ke atas
if ($day_of_week == 6) { // 6 = Sabtu
    $selected_time = strtotime($waktu_janji);
    if ($selected_time > strtotime('14:00:00')) {
        $_SESSION['error'] = "Pada hari Sabtu, booking hanya tersedia hingga jam 14:00 WIB.";
        header('Location: ../B_pemesanan_klinik.php?doctor_id=' . $id_dokter);
        exit;
    }
}

// Validasi: Untuk hari Senin-Jumat, batasi hingga jam 16:00
if ($day_of_week >= 1 && $day_of_week <= 5) {
    $selected_time = strtotime($waktu_janji);
    if ($selected_time > strtotime('16:00:00')) {
        $_SESSION['error'] = "Pada hari kerja, booking hanya tersedia hingga jam 16:00 WIB.";
        header('Location: ../B_pemesanan_klinik.php?doctor_id=' . $id_dokter);
        exit;
    }
}

// Cek apakah hewan milik user
$check_pet = mysqli_query($koneksi, "SELECT * FROM pets WHERE id_pet = $id_pet AND id_pemilik = $id_pemilik");
if (mysqli_num_rows($check_pet) === 0) {
    $_SESSION['error'] = "Hewan tidak valid!";
    header('Location: ../B_jadwal_keKlinik.php');
    exit;
}

// Cek apakah slot waktu sudah dipesan
$query_check = "SELECT COUNT(*) as total 
FROM pemesanan_offline 
WHERE id_dokter = $id_dokter 
AND tanggal_antrean = '$tanggal_janji' 
AND waktu_antrean = '$waktu_janji'
AND status_antrean IN ('menunggu', 'diproses')";

$result_check = mysqli_query($koneksi, $query_check);
$data_check = mysqli_fetch_assoc($result_check);

if ($data_check['total'] > 0) {
    $_SESSION['error'] = "Maaf, jam $waktu_janji pada tanggal $tanggal_janji sudah dipesan oleh pengguna lain. Silakan pilih jam lain.";
    header('Location: ../B_pemesanan_klinik.php?doctor_id=' . $id_dokter);
    exit;
}

// Generate nomor antrean
$query_last_queue = "SELECT MAX(nomor_antrean) as last_queue 
FROM pemesanan_offline 
WHERE id_dokter = $id_dokter 
AND tanggal_antrean = '$tanggal_janji'";

$result_last_queue = mysqli_query($koneksi, $query_last_queue);
$data_last_queue = mysqli_fetch_assoc($result_last_queue);

$next_queue = 1;
if ($data_last_queue['last_queue']) {
    $next_queue = intval($data_last_queue['last_queue']) + 1;
}

// Format nomor antrean
$nomor_antrean = str_pad($next_queue, 3, '0', STR_PAD_LEFT);

// Insert pemesanan menggunakan prepared statement
$query_insert = "INSERT INTO pemesanan_offline 
(id_dokter, id_pemilik, id_pet, nomor_antrean, tanggal_antrean, waktu_antrean, keluhan, status_antrean) 
VALUES 
(?, ?, ?, ?, ?, ?, ?, 'menunggu')";

$stmt = mysqli_prepare($koneksi, $query_insert);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'iiissss', $id_dokter, $id_pemilik, $id_pet, $nomor_antrean, $tanggal_janji, $waktu_janji, $keluhan);
    if (mysqli_stmt_execute($stmt)) {
        $booking_id = mysqli_insert_id($koneksi);
        $_SESSION['success'] = "Booking berhasil! Nomor antrean Anda: $nomor_antrean";
        header('Location: ../B_konfirmasi_booking.php?booking_id=' . $booking_id);
        mysqli_stmt_close($stmt);
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat booking: " . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        header('Location: ../B_pemesanan_klinik.php?doctor_id=' . $id_dokter);
        exit;
    }
} else {
    $_SESSION['error'] = "Terjadi kesalahan dalam persiapan query: " . mysqli_error($koneksi);
    header('Location: ../B_pemesanan_klinik.php?doctor_id=' . $id_dokter);
    exit;
}