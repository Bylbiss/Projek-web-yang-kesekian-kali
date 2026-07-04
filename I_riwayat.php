<?php
session_start();
require_once __DIR__ . '/logic/auth.php';
require 'logic/filter.php';

if (!sudahLogin()) {
    header('Location: B_login.php');
    exit;
}

$user = getUser();

// Koneksi database
require_once __DIR__ . '/logic/koneksi.php';

// Proses cancel booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $booking_id = mysqli_real_escape_string($koneksi, $_POST['booking_id']);
    $booking_type = mysqli_real_escape_string($koneksi, $_POST['booking_type']);

    if ($booking_type === 'offline') {
        // Update status pemesanan offline
        $query = "UPDATE pemesanan_offline SET status_antrean = 'batal' WHERE id_antrean = $booking_id AND id_pemilik = {$_SESSION['user_id']}";
    } else {
        // Update status pemesanan online
        $query = "UPDATE pemesanan_online SET status_pemesanan = 'cancelled' WHERE id_pemesanan = $booking_id AND id_pemilik = {$_SESSION['user_id']}";
    }

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success_message'] = "Booking berhasil dibatalkan";
    } else {
        $_SESSION['error_message'] = "Gagal membatalkan booking: " . mysqli_error($koneksi);
    }

    header("Location: I_riwayat.php");
    exit;
}

// Ambil data riwayat booking dari database menggunakan filter
$id_pemilik = $_SESSION['user_id'];
$bookings = filterHistoryBookings($id_pemilik);

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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - AB Paw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="history-page">
    <!-- Header -->
    <?php require_once 'components/header.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3 class="sidebar-title">Menu</h3>
                <ul class="sidebar-menu">
                    <li><a href="B_jadwal_keKlinik.php"><i class="fas fa-calendar-check"></i> Jadwalkan ke klinik</a></li>
                    <li><a href="I_temui_online.php"><i class="fas fa-comment-medical"></i> Telekonsultasi online</a></li>
                </ul>

                <div class="sidebar-divider"></div>

                <h3 class="sidebar-title">Janji Saya</h3>
                <ul class="sidebar-menu">
                    <li><a href="B_akandatang.php"><i class="fas fa-clock"></i> Akan Datang</a></li>
                    <li><a href="I_riwayat.php" class="active"><i class="fas fa-history"></i> Riwayat</a></li>
                </ul>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Riwayat Konsultasi</h1>
                    <p>Lihat semua riwayat kunjungan dan konsultasi hewan peliharaan Anda</p>
                </div>

                <!-- Tampilkan pesan sukses/error -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <?php
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="filter-section">
                    <select class="filter-select" id="type-filter">
                        <option value="all">Semua Tipe</option>
                        <option value="offline">Kunjungan Klinik</option>
                        <option value="online">Konsultasi Online</option>
                    </select>
                    <select class="filter-select" id="status-filter">
                        <option value="all">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="diproses">Diproses</option>
                        <option value="confirmed">Dikonfirmasi</option>
                        <option value="selesai">Selesai</option>
                        <option value="completed">Completed</option>
                        <option value="batal">Batal</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="history-list" id="history-list">
                    <?php if (empty($bookings)): ?>
                        <div class="no-bookings">
                            <i class="fas fa-history"></i>
                            <h3>Belum Ada Riwayat Konsultasi</h3>
                            <p>Anda belum memiliki riwayat kunjungan klinik atau konsultasi online.</p>
                            <a href="B_jadwal_keKlinik.php" class="action-button" style="margin-top: 15px;">
                                <i class="fas fa-calendar-plus"></i> Buat Janji Sekarang
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking):
                            $doctor_photo = isset($doctor_photos[$booking['id_dokter']]) ? $doctor_photos[$booking['id_dokter']] : $default_photo;
                            $tanggal = date('d F Y', strtotime($booking['tanggal']));
                            $waktu = date('H:i', strtotime($booking['waktu']));

                            // Tentukan status dan class
                            $status = $booking['status'];
                            $status_class = 'status-' . $status;
                            $status_text = ucfirst($status);

                            // Tentukan tipe booking
                            $tipe_text = $booking['tipe'] == 'offline' ? 'Kunjungan Klinik' : 'Konsultasi Online';
                            $tipe_icon = $booking['tipe'] == 'offline' ? 'fa-hospital' : 'fa-video';

                            // Format nomor antrian (jika ada)
                            $nomor_antrian_display = '';
                            if ($booking['tipe'] == 'offline' && !empty($booking['nomor_antrean'])) {
                                $nomor_antrian_display = str_pad($booking['nomor_antrean'], 3, '0', STR_PAD_LEFT);
                            }
                        ?>
                            <div class="history-card" data-type="<?php echo $booking['tipe']; ?>" data-status="<?php echo $status; ?>">
                                <div class="history-header">
                                    <div class="doctor-image" style="background-image: url('<?php echo $doctor_photo; ?>')"></div>
                                    <div class="doctor-info">
                                        <div class="doctor-name"><?php echo htmlspecialchars($booking['nama_dokter']); ?></div>
                                        <div class="doctor-specialty"><?php echo htmlspecialchars($booking['bidang_khusus']); ?></div>
                                        <div class="doctor-experience">
                                            <i class="fas fa-briefcase"></i>
                                            <span>5+ tahun pengalaman</span>
                                        </div>
                                        <div class="contact-info">
                                            <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking['dokter_hp']); ?></span>
                                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['kota']); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="history-card-content">
                                    <div class="history-main-info">
                                        <div class="booking-type">
                                            <i class="fas <?php echo $tipe_icon; ?>"></i> <?php echo $tipe_text; ?>
                                        </div>

                                        <?php if ($booking['tipe'] == 'offline' && !empty($nomor_antrian_display)): ?>
                                            <div class="queue-number">
                                                <div class="queue-label">Nomor Antrian</div>
                                                <div class="queue-value"><?php echo $nomor_antrian_display; ?></div>
                                                <div class="queue-label">Tunjukkan ke resepsionis</div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="booking-info">
                                            <strong><i class="fas fa-calendar-alt"></i> Detail Janji:</strong>
                                            <div class="detail-item">
                                                <span class="detail-label">Tanggal</span>
                                                <span class="detail-value"><?php echo $tanggal; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Waktu</span>
                                                <span class="detail-value"><?php echo $waktu; ?> WIB</span>
                                            </div>
                                            <?php if ($booking['kode_pemesanan']): ?>
                                                <div class="detail-item">
                                                    <span class="detail-label">Kode Booking</span>
                                                    <span class="detail-value"><?php echo $booking['kode_pemesanan']; ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($booking['keluhan']): ?>
                                            <div class="booking-info">
                                                <strong><i class="fas fa-clipboard"></i> Keluhan:</strong>
                                                <p><?php echo htmlspecialchars($booking['keluhan']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="history-additional-info">
                                        <div class="status-info">
                                            <i class="fas fa-info-circle"></i>
                                            Status: <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </div>

                                        <div class="booking-info">
                                            <strong><i class="fas fa-paw"></i> Hewan Peliharaan:</strong>
                                            <p><?php echo htmlspecialchars($booking['nama_pet']); ?> (<?php echo htmlspecialchars($booking['jenis_hewan']); ?>)</p>
                                        </div>

                                        <?php if ($booking['tipe'] == 'online' && $booking['total_biaya']): ?>
                                            <div class="booking-info">
                                                <strong><i class="fas fa-money-bill-wave"></i> Biaya:</strong>
                                                <div class="detail-item">
                                                    <span class="detail-label">Konsultasi</span>
                                                    <span class="detail-value">Rp <?php echo number_format($booking['biaya_konsultasi'], 0, ',', '.'); ?></span>
                                                </div>
                                                <?php if ($booking['jumlah_diskon'] > 0): ?>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Diskon</span>
                                                        <span class="detail-value" style="color: var(--accent);">-Rp <?php echo number_format($booking['jumlah_diskon'], 0, ',', '.'); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="detail-item" style="border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px;">
                                                    <span class="detail-label"><strong>Total</strong></span>
                                                    <span class="detail-value"><strong>Rp <?php echo number_format($booking['total_biaya'], 0, ',', '.'); ?></strong></span>
                                                </div>
                                            </div>
                                        <?php elseif ($booking['tipe'] == 'offline'): ?>
                                            <div class="booking-info">
                                                <strong><i class="fas fa-money-bill-wave"></i> Biaya:</strong>
                                                <p>Pembayaran dilakukan langsung di klinik setelah konsultasi</p>
                                            </div>
                                        <?php endif; ?>

                                        <div class="history-actions">
                                            <?php if ($status == 'menunggu' || $status == 'pending' || $status == 'diproses' || $status == 'confirmed'): ?>
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                    <input type="hidden" name="booking_type" value="<?php echo $booking['tipe']; ?>">
                                                    <button type="submit" class="action-button danger-button" onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                                                        <i class="fas fa-times"></i> Batalkan
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <!-- Tombol Lihat Detail untuk SEMUA tipe booking -->
                                            <button class="action-button secondary-button" onclick="showDetailModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)">
                                                <i class="fas fa-eye"></i> Lihat Detail
                                            </button>

                                            <?php if ($status == 'selesai' || $status == 'completed'): ?>
                                                <a href="B_jadwal_keKlinik.php?doctor_id=<?php echo $booking['id_dokter']; ?>" class="action-button">
                                                    <i class="fas fa-redo"></i> Booking Lagi
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail (untuk SEMUA tipe booking) -->
    <div class="modal" id="detailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-file-medical"></i> Detail Konsultasi</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Detail akan diisi oleh JavaScript -->
            </div>
            <div class="modal-footer">
                <button class="action-button" onclick="printDetail()">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <button class="action-button secondary-button" onclick="closeModal()">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'components/footer.php'; ?>

    <script>
        // Filter riwayat
        document.addEventListener('DOMContentLoaded', function() {
            const typeFilter = document.getElementById('type-filter');
            const statusFilter = document.getElementById('status-filter');
            const historyCards = document.querySelectorAll('.history-card');

            function applyFilters() {
                const selectedType = typeFilter.value;
                const selectedStatus = statusFilter.value;
                let visibleCount = 0;

                historyCards.forEach(card => {
                    const cardType = card.getAttribute('data-type');
                    const cardStatus = card.getAttribute('data-status');

                    let typeMatch = false;
                    let statusMatch = false;

                    // OR Logic: Filter berdasarkan tipe ATAU status
                    if (selectedType !== 'all') {
                        typeMatch = (cardType === selectedType);
                    }

                    if (selectedStatus !== 'all') {
                        // Mapping status untuk offline dan online
                        const statusMapping = {
                            'menunggu': ['menunggu', 'pending'],
                            'diproses': ['diproses', 'confirmed'],
                            'selesai': ['selesai', 'completed'],
                            'batal': ['batal', 'cancelled']
                        };

                        const validStatuses = statusMapping[selectedStatus] || [selectedStatus];
                        statusMatch = validStatuses.includes(cardStatus);
                    }

                    // Logika OR: Tampilkan jika sesuai tipe ATAU sesuai status
                    let showCard = false;
                    if (selectedType !== 'all' && selectedStatus !== 'all') {
                        // Kedua filter dipilih: OR logic
                        showCard = typeMatch || statusMatch;
                    } else if (selectedType !== 'all') {
                        // Hanya filter tipe
                        showCard = typeMatch;
                    } else if (selectedStatus !== 'all') {
                        // Hanya filter status
                        showCard = statusMatch;
                    } else {
                        // Tidak ada filter
                        showCard = true;
                    }

                    // Tampilkan atau sembunyikan card
                    card.style.display = showCard ? 'block' : 'none';
                    if (showCard) visibleCount++;
                });

                // Cek jika tidak ada booking yang ditampilkan
                const historyList = document.getElementById('history-list');
                const existingNoResults = historyList.querySelector('.no-bookings-filtered');

                if (visibleCount === 0 && historyCards.length > 0) {
                    if (!existingNoResults) {
                        const noResults = document.createElement('div');
                        noResults.className = 'no-bookings no-bookings-filtered';
                        noResults.innerHTML = `
                    <i class="fas fa-search"></i>
                    <h3>Tidak Ada Hasil</h3>
                    <p>Tidak ada riwayat yang sesuai dengan filter yang dipilih.</p>
                    <button onclick="resetFilters()" class="action-button" style="margin-top: 15px;">
                        <i class="fas fa-redo"></i> Reset Filter
                    </button>
                `;
                        historyList.appendChild(noResults);
                    }
                } else {
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }
                }
            }

            // Fungsi reset filter
            window.resetFilters = function() {
                typeFilter.value = 'all';
                statusFilter.value = 'all';
                applyFilters();
            }

            // Event listeners untuk filter
            if (typeFilter) typeFilter.addEventListener('change', applyFilters);
            if (statusFilter) statusFilter.addEventListener('change', applyFilters);

            // Apply filter pertama kali
            applyFilters();
        });

        // Fungsi untuk menampilkan modal detail
        function showDetailModal(booking) {
            const modal = document.getElementById('detailModal');
            const modalBody = document.getElementById('modalBody');

            // Format tanggal
            const tanggal = new Date(booking.tanggal).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            const waktu = booking.waktu.substring(0, 5);

            // Buat konten modal
            let modalContent = `
        <div class="modal-detail-item">
            <span class="modal-detail-label">Tipe Konsultasi</span>
            <span class="modal-detail-value">${booking.tipe === 'offline' ? 'Kunjungan Klinik' : 'Konsultasi Online'}</span>
        </div>
    `;

            // Untuk offline: tampilkan nomor antrian
            if (booking.tipe === 'offline' && booking.nomor_antrean) {
                const nomorAntrian = String(booking.nomor_antrean).padStart(3, '0');
                modalContent += `
            <div class="modal-detail-item">
                <span class="modal-detail-label">Nomor Antrian</span>
                <span class="modal-detail-value" style="font-size: 1.2rem; font-weight: 700; color: var(--primary);">${nomorAntrian}</span>
            </div>
        `;
            }

            // Untuk online: tampilkan kode booking
            if (booking.tipe === 'online' && booking.kode_pemesanan) {
                modalContent += `
            <div class="modal-detail-item">
                <span class="modal-detail-label">Kode Booking</span>
                <span class="modal-detail-value">${booking.kode_pemesanan}</span>
            </div>
        `;
            }

            // Informasi umum
            modalContent += `
        <div class="modal-detail-item">
            <span class="modal-detail-label">Nama Pemilik</span>
            <span class="modal-detail-value">${booking.nama_pemilik}</span>
        </div>
        <div class="modal-detail-item">
            <span class="modal-detail-label">Hewan Peliharaan</span>
            <span class="modal-detail-value">${booking.nama_pet} (${booking.jenis_hewan})</span>
        </div>
        <div class="modal-detail-item">
            <span class="modal-detail-label">Dokter</span>
            <span class="modal-detail-value">Dr. ${booking.nama_dokter} - ${booking.bidang_khusus}</span>
        </div>
        <div class="modal-detail-item">
            <span class="modal-detail-label">Tanggal & Waktu</span>
            <span class="modal-detail-value">${tanggal} pukul ${waktu} WIB</span>
        </div>
        <div class="modal-detail-item">
            <span class="modal-detail-label">Lokasi</span>
            <span class="modal-detail-value">${booking.kota}</span>
        </div>
        <div class="modal-detail-item">
            <span class="modal-detail-label">Status</span>
            <span class="modal-detail-value">${booking.status}</span>
        </div>
    `;

            if (booking.keluhan) {
                modalContent += `
            <div class="modal-detail-item">
                <span class="modal-detail-label">Keluhan</span>
                <span class="modal-detail-value">${booking.keluhan}</span>
            </div>
        `;
            }

            // Untuk online: informasi biaya
            if (booking.tipe === 'online' && booking.biaya_konsultasi) {
                if (booking.kupon_digunakan) {
                    modalContent += `
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Kupon Digunakan</span>
                    <span class="modal-detail-value">${booking.kupon_digunakan}</span>
                </div>
            `;
                }

                modalContent += `
            <div class="modal-detail-item">
                <span class="modal-detail-label">Biaya Konsultasi</span>
                <span class="modal-detail-value">Rp ${formatNumber(booking.biaya_konsultasi)}</span>
            </div>
        `;

                if (booking.jumlah_diskon > 0) {
                    modalContent += `
                <div class="modal-detail-item">
                    <span class="modal-detail-label">Diskon</span>
                    <span class="modal-detail-value" style="color: var(--accent);">-Rp ${formatNumber(booking.jumlah_diskon)}</span>
                </div>
            `;
                }

                modalContent += `
            <div class="modal-detail-item" style="border-top: 2px solid var(--primary); padding-top: 15px; margin-top: 15px;">
                <span class="modal-detail-label" style="font-weight: 700;">Total Biaya</span>
                <span class="modal-detail-value" style="font-weight: 700; color: var(--primary);">Rp ${formatNumber(booking.total_biaya)}</span>
            </div>
        `;
            }

            // Untuk offline: informasi pembayaran
            if (booking.tipe === 'offline') {
                modalContent += `
            <div class="modal-detail-item">
                <span class="modal-detail-label">Pembayaran</span>
                <span class="modal-detail-value">Dibayar langsung di klinik setelah konsultasi</span>
            </div>
            <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ff9800;">
                <strong><i class="fas fa-info-circle"></i> Instruksi Kunjungan:</strong>
                <p>1. Datang 15 menit sebelum jadwal</p>
                <p>2. Tunjukkan nomor antrian ke resepsionis</p>
                <p>3. Bawa hewan peliharaan dalam kondisi sehat</p>
                <p>4. Pembayaran dilakukan setelah konsultasi</p>
            </div>
        `;
            }

            // Tambahkan info kontak dokter jika ada
            if (booking.dokter_hp) {
                modalContent += `
            <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 8px;">
                <strong><i class="fas fa-phone"></i> Kontak Dokter:</strong>
                <p>Dr. ${booking.nama_dokter}</p>
                <p>No. HP: ${booking.dokter_hp}</p>
                <a href="https://wa.me/${booking.dokter_hp.replace(/[^0-9]/g, '')}" class="action-button" style="margin-top: 10px;" target="_blank">
                    <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
                </a>
            </div>
        `;
            }

            modalBody.innerHTML = modalContent;
            modal.style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            const modal = document.getElementById('detailModal');
            modal.style.display = 'none';
        }

        // Fungsi untuk print detail
        function printDetail() {
            const modalContent = document.querySelector('.modal-content').cloneNode(true);
            const printWindow = window.open('', '_blank');

            // Hapus tombol footer dari print
            const footer = modalContent.querySelector('.modal-footer');
            if (footer) footer.remove();

            printWindow.document.write(`
        <html>
        <head>
            <title>Detail Konsultasi - AB Paw</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .modal-detail-item { display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
                .modal-detail-label { color: #666; font-weight: 500; }
                .modal-detail-value { font-weight: 600; }
                .print-title { text-align: center; color: #e83e8c; margin-bottom: 20px; }
                .info-box { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #e83e8c; }
            </style>
        </head>
        <body>
            <h1 class="print-title">Detail Konsultasi - AB Paw</h1>
            ${modalContent.innerHTML}
        </body>
        </html>
    `);
            printWindow.document.close();
            printWindow.print();
        }

        // Fungsi format number
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Tutup modal ketika klik di luar konten
        document.getElementById('detailModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Fungsi untuk menampilkan modal detail untuk SEMUA tipe booking
        function showDetailModal(booking) {
            const modal = document.getElementById('detailModal');
            const modalBody = document.getElementById('modalBody');

            // Format tanggal
            const tanggal = new Date(booking.tanggal).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            const waktu = booking.waktu.substring(0, 5);

            // Buat konten modal berdasarkan tipe booking
            let modalContent = `
<div class="modal-detail-item">
<span class="modal-detail-label">Tipe Konsultasi</span>
<span class="modal-detail-value">${booking.tipe === 'offline' ? 'Kunjungan Klinik' : 'Konsultasi Online'}</span>
</div>
`;

            // Untuk offline: tampilkan nomor antrian
            if (booking.tipe === 'offline' && booking.nomor_antrean) {
                const nomorAntrian = String(booking.nomor_antrean).padStart(3, '0');
                modalContent += `
<div class="modal-detail-item">
<span class="modal-detail-label">Nomor Antrian</span>
<span class="modal-detail-value" style="font-size: 1.2rem; font-weight: 700; color: var(--primary);">${nomorAntrian}</span>
</div>
`;
            }

            // Untuk online: tampilkan kode booking
            if (booking.tipe === 'online' && booking.kode_pemesanan) {
                modalContent += `
<div class="modal-detail-item">
<span class="modal-detail-label">Kode Booking</span>
<span class="modal-detail-value">${booking.kode_pemesanan}</span>
</div>
`;
            }

            // Informasi umum untuk semua tipe
            modalContent += `
<div class="modal-detail-item">
<span class="modal-detail-label">Nama Pemilik</span>
<span class="modal-detail-value">${booking.nama_pemilik}</span>
</div>
<div class="modal-detail-item">
<span class="modal-detail-label">Hewan Peliharaan</span>
<span class="modal-detail-value">${booking.nama_pet} (${booking.jenis_hewan})</span>
</div>
<div class="modal-detail-item">
<span class="modal-detail-label">Dokter</span>
<span class="modal-detail-value">Dr. ${booking.nama_dokter} - ${booking.bidang_khusus}</span>
</div>
<div class="modal-detail-item">
<span class="modal-detail-label">Tanggal & Waktu</span>
<span class="modal-detail-value">${tanggal} pukul ${waktu} WIB</span>
</div>
<div class="modal-detail-item">
<span class="modal-detail-label">Lokasi</span>
<span class="modal-detail-value">${booking.kota}</span>
</div>
<div class="modal-detail-item">
<span class="modal-detail-label">Status</span>
<span class="modal-detail-value">${booking.status}</span>
</div>
`;

            if (booking.keluhan) {
                modalContent += `
<div class="modal-detail-item">
<span class="modal-detail-label">Keluhan</span>
<span class="modal-detail-value">${booking.keluhan}</span>
</div>
`;
            }

            // Untuk online: tampilkan informasi biaya
            if (booking.tipe === 'online' && booking.biaya_konsultasi) {
                if (booking.kupon_digunakan) {
                    modalContent += `
<div class="modal-detail-item">
<span class="modal-detail-label">Kupon Digunakan</span>
<span class="modal-detail-value">${booking.kupon_digunakan}</span>
</div>
`;
                }

                modalContent += `
<div class="modal-detail-item">
<span class="modal-detail-label">Biaya Konsultasi</span>
<span class="modal-detail-value">Rp ${formatNumber(booking.biaya_konsultasi)}</span>
</div>
`;

                if (booking.jumlah_diskon > 0) {
                    modalContent += `
<div class="modal-detail-item">
<span class="modal-detail-label">Diskon</span>
<span class="modal-detail-value" style="color: var(--accent);">-Rp ${formatNumber(booking.jumlah_diskon)}</span>
</div>
`;
                }

                modalContent += `
<div class="modal-detail-item" style="border-top: 2px solid var(--primary); padding-top: 15px; margin-top: 15px;">
<span class="modal-detail-label" style="font-weight: 700;">Total Biaya</span>
<span class="modal-detail-value" style="font-weight: 700; color: var(--primary);">Rp ${formatNumber(booking.total_biaya)}</span>
</div>
`;
            }

            // Untuk offline: informasi pembayaran
            if (booking.tipe === 'offline') {
                modalContent += `
<div class="modal-detail-item">
<span class="modal-detail-label">Pembayaran</span>
<span class="modal-detail-value">Dibayar langsung di klinik setelah konsultasi</span>
</div>
<div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ff9800;">
<strong><i class="fas fa-info-circle"></i> Instruksi Kunjungan:</strong>
<p>1. Datang 15 menit sebelum jadwal</p>
<p>2. Tunjukkan nomor antrian ke resepsionis</p>
<p>3. Bawa hewan peliharaan dalam kondisi sehat</p>
<p>4. Pembayaran dilakukan setelah konsultasi</p>
</div>
`;
            }

            // Tambahkan info kontak dokter jika ada
            if (booking.dokter_hp) {
                modalContent += `
<div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 8px;">
<strong><i class="fas fa-phone"></i> Kontak Dokter:</strong>
<p>Dr. ${booking.nama_dokter}</p>
<p>No. HP: ${booking.dokter_hp}</p>
<a href="https://wa.me/${booking.dokter_hp.replace(/[^0-9]/g, '')}" class="action-button" style="margin-top: 10px;" target="_blank">
<i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
</a>
</div>
`;
            }

            modalBody.innerHTML = modalContent;
            modal.style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            const modal = document.getElementById('detailModal');
            modal.style.display = 'none';
        }

        // Fungsi untuk print detail
        function printDetail() {
            const modalContent = document.querySelector('.modal-content').cloneNode(true);
            const printWindow = window.open('', '_blank');

            // Hapus tombol footer dari print
            const footer = modalContent.querySelector('.modal-footer');
            if (footer) footer.remove();

            printWindow.document.write(`
<html>
<head>
<title>Detail Konsultasi - AB Paw</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; }
.modal-detail-item { display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
.modal-detail-label { color: #666; font-weight: 500; }
.modal-detail-value { font-weight: 600; }
.print-title { text-align: center; color: #e83e8c; margin-bottom: 20px; }
.info-box { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #e83e8c; }
</style>
</head>
<body>
<h1 class="print-title">Detail Konsultasi - AB Paw</h1>
${modalContent.innerHTML}
</body>
</html>
`);
            printWindow.document.close();
            printWindow.print();
        }

        // Fungsi format number
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Tutup modal ketika klik di luar konten
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>

</html>