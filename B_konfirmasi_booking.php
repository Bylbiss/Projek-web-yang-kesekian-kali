<?php
session_start();
require_once __DIR__ . '/logic/auth.php';

if (!sudahLogin()) {
    header('Location: B_login.php');
    exit;
}

$user = getUser();

// Ambil data booking terbaru dari database
global $koneksi;
$id_pemilik = $_SESSION['user_id'];

// Query untuk mendapatkan booking terbaru
$query_booking = "SELECT po.*, d.nama_dokter, d.bidang_khusus, d.kota, p.nama_pet, p.jenis_hewan 
FROM pemesanan_offline po 
JOIN dokter d ON po.id_dokter = d.id_dokter 
JOIN pets p ON po.id_pet = p.id_pet 
WHERE po.id_pemilik = $id_pemilik 
ORDER BY po.id_antrean DESC 
LIMIT 1";

$result_booking = mysqli_query($koneksi, $query_booking);
$booking = mysqli_fetch_assoc($result_booking);

// Jika tidak ada booking, redirect ke halaman jadwal
if (!$booking) {
    header('Location: B_jadwal_keKlinik.php');
    exit;
}

// Format tanggal dan waktu
$tanggal_janji = date('d F Y', strtotime($booking['tanggal_antrean']));
$waktu_janji = date('H:i', strtotime($booking['waktu_antrean']));
$nomor_antrian = $booking['nomor_antrean']; // Field yang benar adalah nomor_antrean

// Foto dokter
$doctor_photos = [
    1 => "https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80",
    2 => "https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80",
    3 => "https://images.unsplash.com/photo-1594824947933-d0501ba2fe65?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80",
    4 => "https://images.unsplash.com/photo-1622253692010-333f2da60319?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80",
    5 => "https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80",
    6 => "https://images.unsplash.com/photo-1551601651-2a8555f1a136?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80",
    7 => "https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80",
    8 => "https://images.unsplash.com/photo-1584432810601-6c7f27d2362b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80",
    9 => "https://images.unsplash.com/photo-1622253692010-333f2da60319?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80"
];

$default_photo = "https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80";
$doctor_photo = isset($doctor_photos[$booking['id_dokter']]) ? $doctor_photos[$booking['id_dokter']] : $default_photo;

// Tentukan status booking berdasarkan status_antrean
$status_booking = '';
$status_color = '';
switch ($booking['status_antrean']) {
    case 'menunggu':
        $status_booking = 'Menunggu';
        $status_color = '#ff9800';
        break;
    case 'diproses':
        $status_booking = 'Sedang Diproses';
        $status_color = '#2196f3';
        break;
    case 'selesai':
        $status_booking = 'Selesai';
        $status_color = '#4CAF50';
        break;
    case 'batal':
        $status_booking = 'Dibatalkan';
        $status_color = '#f44336';
        break;
    default:
        $status_booking = 'Menunggu';
        $status_color = '#ff9800';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Booking - AB Paw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="ABclinic.jpg">
</head>

<body class="booking-confirmation-page">
    <!-- Header -->
    <?php require_once 'components/header.php'; ?>

    <!-- Confirmation Container -->
    <div class="confirmation-container">
        <h1 class="page-title">Konfirmasi Booking</h1>
        <p class="page-subtitle">Booking kunjungan klinik Anda telah berhasil diproses</p>

        <div class="confirmation-card">
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2 class="success-title">Booking Berhasil!</h2>
                <p class="success-message">Terima kasih telah melakukan booking. Berikut detail kunjungan Anda:</p>
            </div>

            <!-- Nomor Antrian -->
            <div class="queue-number">
                <div class="queue-label">Nomor Antrian Anda</div>
                <div class="queue-value"><?php echo $nomor_antrian; ?></div>
                <div class="queue-label">Harap datang 15 menit sebelum jadwal</div>
            </div>

            <!-- Informasi Dokter -->
            <div class="doctor-info">
                <div class="doctor-image" style="background-image: url('<?php echo $doctor_photo; ?>')"></div>
                <div class="doctor-details">
                    <h4><?php echo htmlspecialchars($booking['nama_dokter']); ?></h4>
                    <div class="doctor-specialty"><?php echo htmlspecialchars($booking['bidang_khusus']); ?></div>
                    <div class="doctor-location"><?php echo htmlspecialchars($booking['kota']); ?></div>
                </div>
            </div>

            <!-- Detail Booking -->
            <div class="booking-details">
                <div class="detail-section">
                    <h3><i class="fas fa-calendar-alt"></i> Detail Jadwal</h3>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal</span>
                        <span class="detail-value"><?php echo $tanggal_janji; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Waktu</span>
                        <span class="detail-value"><?php echo $waktu_janji; ?> WIB</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lokasi</span>
                        <span class="detail-value">Klinik AB Paw</span>
                    </div>
                </div>

                <div class="detail-section">
                    <h3><i class="fas fa-paw"></i> Informasi Hewan</h3>
                    <div class="detail-item">
                        <span class="detail-label">Nama Hewan</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['nama_pet']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Jenis Hewan</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['jenis_hewan']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            <span class="status-badge" style="background-color: <?php echo $status_color; ?>; color: white;">
                                <?php echo $status_booking; ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Keluhan -->
            <?php if (!empty($booking['keluhan'])): ?>
                <div class="detail-section">
                    <h3><i class="fas fa-comment-medical"></i> Keluhan</h3>
                    <p style="color: var(--dark); line-height: 1.6;"><?php echo htmlspecialchars($booking['keluhan']); ?></p>
                </div>
            <?php endif; ?>

            <!-- Estimasi Waktu -->
            <?php if (!empty($booking['estimasi_waktu'])): ?>
                <div class="detail-section">
                    <h3><i class="fas fa-clock"></i> Estimasi Waktu</h3>
                    <p style="color: var(--dark); line-height: 1.6;">
                        Estimasi waktu tunggu: <?php echo date('H:i', strtotime($booking['estimasi_waktu'])); ?> WIB
                    </p>
                </div>
            <?php endif; ?>

            <!-- Instruksi -->
            <div class="instructions">
                <h3><i class="fas fa-info-circle"></i> Instruksi Penting</h3>
                <ul class="instruction-list">
                    <li>Datang 15 menit sebelum jadwal konsultasi</li>
                    <li>Bawa hewan peliharaan dalam kondisi sehat dan bersih</li>
                    <li>Bawa dokumen kesehatan hewan (jika ada)</li>
                    <li>Tunjukkan nomor antrian ini di resepsionis</li>
                    <li>Pembayaran dilakukan langsung di klinik setelah konsultasi</li>
                    <?php if ($booking['status_antrean'] == 'menunggu'): ?>
                        <li>Status antrian dapat berubah sewaktu-waktu</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="B_jadwal_keKlinik.php" class="btn btn-large btn-outline">
                    <i class="fas fa-calendar-plus"></i> Booking Lagi
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'components/footer.php'; ?>

    <script>
        // Animasi tambahan untuk efek visual
        document.addEventListener('DOMContentLoaded', function() {
            // Efek confetti sederhana
            const confetti = () => {
                const confettiCount = 30;
                const confettiContainer = document.querySelector('.confirmation-card');

                for (let i = 0; i < confettiCount; i++) {
                    const confetti = document.createElement('div');
                    confetti.innerHTML = '🎉';
                    confetti.style.position = 'absolute';
                    confetti.style.fontSize = Math.random() * 20 + 10 + 'px';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.top = '-50px';
                    confetti.style.opacity = '0';
                    confetti.style.animation = `fall ${Math.random() * 3 + 2}s linear forwards`;
                    confetti.style.animationDelay = Math.random() * 2 + 's';
                    confettiContainer.appendChild(confetti);
                }
            };

            // Tambahkan style untuk animasi confetti
            const style = document.createElement('style');
            style.textContent = `
@keyframes fall {
0% {
transform: translateY(0) rotate(0deg);
opacity: 1;
}
100% {
transform: translateY(500px) rotate(360deg);
opacity: 0;
}
}
`;
            document.head.appendChild(style);

            // Jalankan confetti setelah halaman dimuat
            setTimeout(confetti, 500);
        });

        // Fungsi untuk share booking
        function shareBooking() {
            if (navigator.share) {
                navigator.share({
                    title: 'Booking Klinik AB Paw',
                    text: `Saya telah booking konsultasi di AB Paw untuk tanggal <?php echo $tanggal_janji; ?> pukul <?php echo $waktu_janji; ?> WIB`,
                    url: window.location.href
                });
            } else {
                alert('Fitur share tidak didukung di browser ini. Anda bisa screenshot halaman ini.');
            }
        }
    </script>
</body>

</html>