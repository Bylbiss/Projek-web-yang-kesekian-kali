<?php
// ✅ AKTIFKAN OUTPUT BUFFERING DARI AWAL
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0); // TIDAK tampilkan error di browser
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Log error ke file

// ✅ SET HEADER JSON SEBELUM APAPUN
header('Content-Type: application/json; charset=utf-8');

// ✅ MATIKAN SEMUA OUTPUT YANG TIDAK DIINGINKAN
@ini_set('display_errors', '0');
@error_reporting(0);

session_start();
require_once 'koneksi.php';

// Function untuk return error JSON
function returnError($message)
{
    // ✅ HAPUS SEMUA OUTPUT BUFFER SEBELUM KIRIM JSON
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit();
}

// Function untuk return success JSON
function returnSuccess($data)
{
    // ✅ HAPUS SEMUA OUTPUT BUFFER SEBELUM KIRIM JSON
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code(200);
    echo json_encode(array_merge(['success' => true], $data), JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    // Cek method POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        returnError('Method harus POST');
    }

    // Cek user login
    if (!isset($_SESSION['user_id'])) {
        returnError('User tidak login. Silakan login kembali.');
    }

    // Ambil input POST dengan validasi
    $id_dokter = isset($_POST['id_dokter']) ? intval($_POST['id_dokter']) : 0;
    $id_pet = isset($_POST['id_pet']) ? intval($_POST['id_pet']) : 0;
    $tanggal_konsultasi = isset($_POST['tanggal_konsultasi']) ? trim($_POST['tanggal_konsultasi']) : '';
    $waktu_konsultasi = isset($_POST['waktu_konsultasi']) ? trim($_POST['waktu_konsultasi']) : '';
    $keluhan = isset($_POST['keluhan']) ? trim($_POST['keluhan']) : '';
    $biaya_konsultasi = isset($_POST['biaya_konsultasi']) ? floatval($_POST['biaya_konsultasi']) : 0;
    $discount_amount = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0;
    $applied_coupon = isset($_POST['applied_coupon']) ? trim($_POST['applied_coupon']) : '';

    $id_pemilik = $_SESSION['user_id'];

    // Validasi input
    $errors = [];
    if (!$id_dokter) $errors[] = 'ID Dokter tidak valid';
    if (!$id_pet) $errors[] = 'Silakan pilih hewan peliharaan';
    if (empty($tanggal_konsultasi)) $errors[] = 'Silakan pilih tanggal konsultasi';
    if (empty($waktu_konsultasi)) $errors[] = 'Silakan pilih jam konsultasi';
    if (empty($keluhan)) $errors[] = 'Silakan isi keluhan/gejala';

    if (!empty($errors)) {
        returnError(implode(', ', $errors));
    }

    // Validasi format tanggal
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_konsultasi)) {
        returnError('Format tanggal tidak valid. Gunakan format YYYY-MM-DD');
    }

    // Validasi format waktu
    if (!preg_match('/^\d{2}:\d{2}$/', $waktu_konsultasi)) {
        returnError('Format waktu tidak valid. Gunakan format HH:MM');
    }

    // Cek koneksi database
    if (!$koneksi) {
        returnError('Koneksi database gagal');
    }

    // Cek double booking
    $query_check = "SELECT id_pemesanan FROM pemesanan_online 
        WHERE id_dokter = ? 
        AND tanggal_konsultasi = ? 
        AND waktu_konsultasi = ? 
        AND status_pemesanan NOT IN ('cancelled', 'rejected')
        LIMIT 1";

    $stmt_check = $koneksi->prepare($query_check);
    if (!$stmt_check) {
        returnError('Database prepare error: ' . $koneksi->error);
    }

    $stmt_check->bind_param("iss", $id_dokter, $tanggal_konsultasi, $waktu_konsultasi);
    if (!$stmt_check->execute()) {
        returnError('Database execute error: ' . $stmt_check->error);
    }

    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        returnError('Jam ' . $waktu_konsultasi . ' pada tanggal ' . $tanggal_konsultasi . ' sudah dipesan');
    }
    $stmt_check->close();

    // Generate kode pemesanan
    $kode_pemesanan = 'ONLINE-' . date('YmdHis') . '-' . rand(1000, 9999);

    // Hitung total biaya
    $total_biaya = $biaya_konsultasi - $discount_amount;
    if ($total_biaya < 0) $total_biaya = 0;

    $status = 'pending';

    // ✅ QUERY INSERT YANG BENAR
    $query_insert = "INSERT INTO pemesanan_online 
        (kode_pemesanan, id_pemilik, id_dokter, id_pet, 
         tanggal_konsultasi, waktu_konsultasi, keluhan, 
         biaya_konsultasi, kupon_digunakan, jumlah_diskon, 
         total_biaya, status_pemesanan, waktu_pemesanan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt_insert = $koneksi->prepare($query_insert);
    if (!$stmt_insert) {
        returnError('Database prepare insert error: ' . $koneksi->error);
    }

    // ✅ BIND PARAM YANG BENAR (12 parameter)
    if (!$stmt_insert->bind_param(
        "siiisssddsds",  // 12 parameter types
        $kode_pemesanan,     // s
        $id_pemilik,         // i
        $id_dokter,          // i
        $id_pet,             // i
        $tanggal_konsultasi, // s
        $waktu_konsultasi,   // s
        $keluhan,            // s
        $biaya_konsultasi,   // d
        $applied_coupon,     // s
        $discount_amount,    // d
        $total_biaya,        // d
        $status              // s
    )) {
        returnError('Bind param error: ' . $stmt_insert->error);
    }

    if (!$stmt_insert->execute()) {
        returnError('Execute insert error: ' . $stmt_insert->error);
    }

    $id_pemesanan = $koneksi->insert_id;
    $stmt_insert->close();

    // Set session untuk result
    $_SESSION['booking_result'] = [
        'id_pemesanan' => $id_pemesanan,
        'kode_pemesanan' => $kode_pemesanan,
        'tanggal_konsultasi' => $tanggal_konsultasi,
        'waktu_konsultasi' => $waktu_konsultasi,
        'total_biaya' => $total_biaya
    ];

    returnSuccess([
        'id_pemesanan' => $id_pemesanan,
        'kode_pemesanan' => $kode_pemesanan,
        'tanggal_konsultasi' => $tanggal_konsultasi,
        'waktu_konsultasi' => $waktu_konsultasi,
        'total_biaya' => $total_biaya,
        'message' => 'Booking berhasil dibuat'
    ]);
} catch (Exception $e) {
    returnError('Exception: ' . $e->getMessage());
} finally {
    // ✅ BERSIHKAN OUTPUT BUFFER
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (isset($koneksi)) {
        $koneksi->close();
    }
}

// ✅ AKHIRI DENGAN EXIT
exit();
