<?php
session_start();
require "logic/koneksi.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: B_login.php");
    exit();
}

// Cek apakah ada ID pemesanan
$id_pemesanan = null;

// Cek dari GET parameter
if (isset($_GET['id'])) {
    $id_pemesanan = intval($_GET['id']);
}
// Cek dari POST
else if (isset($_POST['id_pemesanan'])) {
    $id_pemesanan = intval($_POST['id_pemesanan']);
}
// Cek dari session
else if (isset($_SESSION['booking_result'])) {
    $id_pemesanan = $_SESSION['booking_result']['id_pemesanan'];
}
// Jika tidak ada, cari pemesanan terakhir user
else {
    $user_id = $_SESSION['user_id'];
    $query_last = "SELECT id_pemesanan FROM pemesanan_online 
WHERE id_pemilik = ? 
ORDER BY created_at DESC LIMIT 1";
    $stmt = $koneksi->prepare($query_last);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $id_pemesanan = $row['id_pemesanan'];
    } else {
        // Jika tidak ada pemesanan, redirect ke halaman pemesanan
        header("Location: I_pemesanan_on.php");
        exit();
    }
}

// Ambil detail data dari database
$query = "SELECT po.*, d.nama_dokter, d.no_hp as dokter_hp, d.bidang_khusus, 
d.spesies_hewan, p.nama_pet, p.jenis_hewan, 
pem.nama_pemilik, pem.no_hp as pemilik_hp
FROM pemesanan_online po
JOIN dokter d ON po.id_dokter = d.id_dokter
JOIN pets p ON po.id_pet = p.id_pet
JOIN pemilik pem ON po.id_pemilik = pem.id_pemilik
WHERE po.id_pemesanan = ?";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$result_db = $stmt->get_result();
$bookingDetails = $result_db->fetch_assoc();

if (!$bookingDetails) {
    header("Location: I_pemesanan_on.php");
    exit();
}

// Format tanggal dan waktu
$tanggal_konsultasi = date('d F Y', strtotime($bookingDetails['tanggal_konsultasi']));
$waktu_konsultasi = $bookingDetails['waktu_konsultasi'];
$waktu_pemesanan = date('d F Y H:i', strtotime($bookingDetails['waktu_pemesanan']));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pemesanan - AB Paw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="confirmation-page"></body>
<!-- Header -->
<?php require_once 'components/header.php'; ?>
<div class="confirmation-container">
    <div class="confirmation-content">
        <!-- Doctor Information -->
        <div class="doctor-card">
            <div class="doctor-header">
                <div class="doctor-avatar">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="doctor-info">
                    <h3>Dr. <?php echo htmlspecialchars($bookingDetails['nama_dokter']); ?></h3>
                    <p class="doctor-specialty"><?php echo htmlspecialchars($bookingDetails['bidang_khusus']); ?></p>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="review-count">4.7 (128 review)</span>
                    <span class="status-badge status-pending">Menunggu Konfirmasi</span>
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <h3>Ringkasan Pemesanan</h3>

            <div class="summary-item">
                <span class="summary-label">Kode Booking</span>
                <span class="summary-value"><?php echo htmlspecialchars($bookingDetails['kode_pemesanan']); ?></span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Waktu Pemesanan</span>
                <span class="summary-value"><?php echo $waktu_pemesanan; ?> WIB</span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Nama Pemilik</span>
                <span class="summary-value"><?php echo htmlspecialchars($bookingDetails['nama_pemilik']); ?></span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Hewan Peliharaan</span>
                <span class="summary-value">
                    <?php echo htmlspecialchars($bookingDetails['nama_pet']); ?>
                    (<?php echo htmlspecialchars($bookingDetails['jenis_hewan']); ?>)
                </span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Tanggal & Waktu Konsultasi</span>
                <span class="summary-value">
                    <?php echo $tanggal_konsultasi; ?> pukul <?php echo $waktu_konsultasi; ?> WIB
                </span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Keluhan</span>
                <span class="summary-value"><?php echo htmlspecialchars($bookingDetails['keluhan']); ?></span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Kupon Diskon</span>
                <span class="summary-value">
                    <?php echo $bookingDetails['kupon_digunakan'] ? htmlspecialchars($bookingDetails['kupon_digunakan']) : 'Tidak ada'; ?>
                </span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Biaya Konsultasi</span>
                <span class="summary-value">Rp <?php echo number_format($bookingDetails['biaya_konsultasi'], 0, ',', '.'); ?></span>
            </div>

            <?php if ($bookingDetails['jumlah_diskon'] > 0): ?>
                <div class="summary-item discount-amount">
                    <span class="summary-label">Diskon</span>
                    <span class="summary-value">-Rp <?php echo number_format($bookingDetails['jumlah_diskon'], 0, ',', '.'); ?></span>
                </div>
            <?php endif; ?>

            <div class="summary-total">
                <span>Total Biaya</span>
                <span>Rp <?php echo number_format($bookingDetails['total_biaya'], 0, ',', '.'); ?></span>
            </div>
        </div>

        <!-- WhatsApp Contact -->
        <div class="whatsapp-section">
            <h3><i class="fab fa-whatsapp"></i> Hubungi Dokter via WhatsApp</h3>
            <p>Klik tombol di bawah untuk mengonfirmasi jadwal dengan dokter</p>
            <?php
            $phone = preg_replace('/[^0-9]/', '', $bookingDetails['dokter_hp']);
            $message = "Halo Dr. " . urlencode($bookingDetails['nama_dokter']) .
                ", saya " . urlencode($bookingDetails['nama_pemilik']) .
                " telah melakukan pemesanan konsultasi online dengan kode " .
                urlencode($bookingDetails['kode_pemesanan']) .
                " untuk tanggal " . urlencode($tanggal_konsultasi) .
                " pukul " . urlencode($waktu_konsultasi) . " WIB";
            ?>
            <a href="https://wa.me/<?php echo $phone; ?>?text=<?php echo $message; ?>"
                class="whatsapp-btn" target="_blank">
                <i class="fab fa-whatsapp"></i> Hubungi Dr. <?php echo htmlspecialchars($bookingDetails['nama_dokter']); ?>
            </a>
        </div>

        <!-- Preparation Section -->
        <div class="preparation-section">
            <h3><i class="fas fa-clipboard-check"></i> Yang Perlu Dipersiapkan</h3>
            <ul class="preparation-list">
                <li>Foto atau video hewan peliharaan</li>
                <li>Riwayat kesehatan hewan peliharaan</li>
                <li>Daftar obat yang sedang dikonsumsi (jika ada)</li>
                <li>Koneksi internet yang stabil</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Konfirmasi
            </button>
            <a href="I_riwayat.php" class="btn btn-secondary">
                <i class="fas fa-history"></i> Lihat Riwayat
            </a>
            <a href="B_dashboard.php" class="btn btn-outline">
                <i class="fas fa-home"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk mengonfirmasi WhatsApp
    function confirmWhatsApp() {
        if (confirm('Apakah Anda yakin ingin menghubungi dokter via WhatsApp?')) {
            return true;
        }
        return false;
    }

    // Fungsi untuk print
    function printConfirmation() {
        window.print();
    }

    // Set WhatsApp link untuk confirmation
    document.querySelector('.whatsapp-btn').addEventListener('click', function(e) {
        if (!confirmWhatsApp()) {
            e.preventDefault();
        }
    });

    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        const successMsg = document.querySelector('.success-title');
        if (successMsg) {
            successMsg.style.opacity = '0.9';
        }
    }, 5000);

    // Save booking ID to localStorage for backup
    localStorage.setItem('last_booking_id', '<?php echo $id_pemesanan; ?>');
    localStorage.setItem('booking_code', '<?php echo $bookingDetails['kode_pemesanan']; ?>');
</script>

<!-- Footer -->
<?php require_once 'components/footer.php'; ?>
</body>

</html>