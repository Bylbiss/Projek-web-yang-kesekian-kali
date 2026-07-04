<?php
session_start();
require_once 'logic/auth.php';

// Tentukan base URL untuk semua link
$base_url = ''; // Kosong karena file berada di root
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AB Paw - Klinik Hewan Terpercaya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="assets/ABclinic.jpg">
</head>

<body class="dashboard-page">
    <?php require_once 'components/header.php'; ?>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <div class="hero-logo">AB Paw</div>
            <p class="hero-desc">
                Klinik hewan terpercaya untuk hewan peliharaan kesayanganmu. Kami menyediakan layanan kesehatan hewan berkualitas dengan dokter berpengalaman dan peralatan modern.
            </p>

            <!-- Conditional routing berdasarkan login status -->
            <div class="fish-eye-menu">
                <div class="fish-item" onclick="location.href='<?php echo sudahLogin() ? 'I_temui_online.php' : 'B_login.php'; ?>'">
                    <div class="fish-icon"><i class="fas fa-video"></i></div>
                    <div class="fish-title">Temui Online</div>
                    <div class="fish-desc">Konsultasi online langsung dengan dokter hewan profesional dari rumah.</div>
                </div>

                <div class="fish-item" onclick="location.href='<?php echo sudahLogin() ? 'B_jadwal_keKlinik.php' : 'B_login.php'; ?>'">
                    <div class="fish-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="fish-title">Jadwalkan ke Klinik</div>
                    <div class="fish-desc">Pesan jadwal kunjungan fisik ke klinik kami sesuai waktu yang tersedia.</div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'components/footer.php'; ?>
</body>

</html>