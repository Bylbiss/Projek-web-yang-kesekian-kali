<?php
session_start();
require "logic/auth.php";
require "logic/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AB Paw - Layanan Kami</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="ABclinic.jpg">
</head>

<body class="services-page">
    <!-- Header -->
    <?php require_once 'components/header.php'; ?>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">Layanan Unggulan</div>
                <h1 class="hero-title">Layanan Komprehensif untuk Hewan Kesayangan Anda</h1>
                <p class="hero-description">Dari konsultasi online hingga perawatan di klinik, kami menyediakan semua kebutuhan kesehatan hewan peliharaan Anda dalam satu platform.</p>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="services-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Layanan Utama Kami</h2>
                <p class="section-subtitle">Pilih layanan yang sesuai dengan kebutuhan hewan peliharaan Anda</p>
            </div>

            <div class="services-grid">
                <!-- Service 1: Konsultasi Online -->
                <div class="service-card">
                    <div class="service-image">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="service-content">
                        <h3 class="service-title">Konsultasi Online</h3>
                        <p class="service-description">Konsultasikan masalah kesehatan hewan Anda dengan dokter hewan profesional melalui video call, kapan saja dan di mana saja.</p>
                        <ul class="service-features">
                            <li>Dokter hewan berpengalaman</li>
                            <li>Konsultasi 24/7</li>
                            <li>Resep obat digital</li>
                            <li>Rekam medis online</li>
                        </ul>
                        <div class="service-price">Mulai dari Rp 50.000</div>
                        <a href="<?php echo isset($_SESSION['nama_pemilik']) ? 'I_temui_online.php' : 'B_login.php'; ?>" class="service-cta">Konsultasi Sekarang</a>
                    </div>
                </div>

                <!-- Service 2: Pesan Antrean ke Klinik Hewan -->
                <div class="service-card">
                    <div class="service-image">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="service-content">
                        <h3 class="service-title">Pesan Antrean ke Klinik Hewan</h3>
                        <p class="service-description">Pesan janji temu ke klinik hewan mitra kami dengan mudah melalui platform online. Hindari antrean panjang dan dapatkan waktu perawatan yang tepat.</p>
                        <ul class="service-features">
                            <li>Pilih klinik terdekat</li>
                            <li>Pilih waktu yang tersedia</li>
                            <li>Notifikasi pengingat otomatis</li>
                            <li>Antrean digital tanpa ribet</li>
                        </ul>
                        <div class="service-price">Gratis Pemesanan</div>
                        <a href="<?php echo isset($_SESSION['nama_pemilik']) ? 'B_jadwal_keKlinik.php' : 'B_login.php'; ?>" class="service-cta">Pesan Antrean Sekarang</a>
                    </div>
                </div>

                <!-- Service 3: Klinik Mitra -->
                <div class="service-card">
                    <div class="service-image">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <div class="service-content">
                        <h3 class="service-title">Klinik Mitra</h3>
                        <p class="service-description">Akses ke jaringan klinik hewan terpercaya dengan fasilitas lengkap dan peralatan medis modern.</p>
                        <ul class="service-features">
                            <li>Jaringan nasional</li>
                            <li>Fasilitas lengkap</li>
                            <li>Dokter spesialis</li>
                            <li>Diskon khusus member</li>
                        </ul>
                        <div class="service-price">Diskon hingga 30%</div>
                        <a href="<?php echo isset($_SESSION['nama_pemilik']) ? 'I_kupon.php' : 'B_login.php'; ?>" class="service-cta">Lihat Kupon</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Categories -->
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Kategori Layanan</h2>
                <p class="section-subtitle">Temukan layanan yang tepat untuk kebutuhan spesifik hewan Anda</p>
            </div>

            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <h3 class="category-title">Vaksinasi</h3>
                    <p class="category-description">Program vaksinasi lengkap untuk mencegah penyakit berbahaya pada hewan peliharaan Anda.</p>
                </div>

                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-clinic-medical"></i>
                    </div>
                    <h3 class="category-title">Klinik & Antrean</h3>
                    <p class="category-description">Pesan janji temu ke klinik hewan mitra dengan sistem antrean digital.</p>
                </div>

                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h3 class="category-title">Farmasi</h3>
                    <p class="category-description">Obat-obatan dan suplemen berkualitas untuk kesehatan optimal.</p>
                </div>

                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-cut"></i>
                    </div>
                    <h3 class="category-title">Grooming</h3>
                    <p class="category-description">Perawatan kecantikan dan kebersihan untuk hewan peliharaan Anda.</p>
                </div>

                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-ambulance"></i>
                    </div>
                    <h3 class="category-title">Darurat 24/7</h3>
                    <p class="category-description">Layanan darurat kapan saja untuk keadaan gawat darurat.</p>
                </div>

                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="category-title">Kesehatan Reproduksi</h3>
                    <p class="category-description">Konsultasi dan perawatan kesehatan reproduksi hewan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Pertanyaan Umum</h2>
                <p class="section-subtitle">Temukan jawaban untuk pertanyaan yang sering diajukan</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item">
                    <h3 class="faq-question">Bagaimana cara membuat janji konsultasi?</h3>
                    <p class="faq-answer">Anda bisa membuat janji melalui aplikasi AB Paw atau website kami. Pilih layanan, dokter, dan waktu yang tersedia.</p>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Apakah ada biaya untuk pemesanan antrean ke klinik?</h3>
                    <p class="faq-answer">Pemesanan antrean ke klinik hewan mitra kami sepenuhnya gratis. Anda hanya membayar biaya perawatan sesuai yang ditetapkan oleh klinik.</p>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Bagaimana jika hewan saya darurat di malam hari?</h3>
                    <p class="faq-answer">Kami menyediakan layanan darurat 24/7. Hubungi hotline kami atau gunakan aplikasi untuk konsultasi darurat.</p>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Apakah resep obat bisa dibeli di apotek biasa?</h3>
                    <p class="faq-answer">Resep dari dokter kami bisa ditebus di apotek hewan mitra kami atau dikirim langsung ke alamat Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php require_once 'components/footer.php'; ?>

    <script>
        // User Dropdown Functionality
        const userDropdownContainer = document.getElementById('userDropdownContainer');
        const userDropdownBtn = document.getElementById('userDropdownBtn');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (userDropdownBtn) {
            userDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdownContainer.classList.toggle('active');
            });

            document.addEventListener('click', function(e) {
                if (!userDropdownContainer.contains(e.target)) {
                    userDropdownContainer.classList.remove('active');
                }
            });

            dropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Mobile menu toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }

        // Logout confirmation
        function confirmLogout(event) {
            event.preventDefault();
            if (confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = 'logic/logout.php';
            }
            return false;
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add animation to service cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Apply animation to all cards
        document.querySelectorAll('.service-card, .category-card, .faq-item').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>

</html>