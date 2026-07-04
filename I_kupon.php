<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupon Diskon - AB Paw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="kupon-page">
    <!-- Header -->
    <?php require_once 'components/header.php'; ?>

    <!-- Coupon Container -->
    <div class="coupon-container">
        <h1 class="page-title">🎫 Kupon Diskon Spesial</h1>
        <p class="page-subtitle">Pilih kupon yang ingin digunakan untuk konsultasi online</p>

        <div class="coupon-scroll-container">
            <button class="scroll-btn prev" onclick="scrollCoupons(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>

            <div class="coupon-scroll-wrapper" id="couponScroll">
                <!-- Kupon 1 -->
                <div class="coupon-card">
                    <div class="badge">ULANGTAHUN AB PAW</div>
                    <div class="coupon-header">
                        <div class="coupon-info">
                            <div class="coupon-percentage">15%</div>
                            <div class="coupon-description">Diskon Semua Konsultasi</div>
                        </div>
                        <div class="coupon-code">DISKON15</div>
                    </div>
                    <div class="coupon-details">
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Berlaku untuk semua dokter</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Minimal pembelian Rp 100.000</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Maksimal diskon Rp 50.000</span>
                        </div>
                    </div>
                    <div class="coupon-footer">
                        <div class="coupon-expiry">⏳ Berlaku hingga 30 November 2025</div>
                        <button class="use-coupon-btn" onclick="selectCoupon('DISKON15')">
                            <i class="fas fa-ticket-alt"></i> Gunakan Kupon
                        </button>
                    </div>
                </div>

                <!-- Kupon 2 -->
                <div class="coupon-card">
                    <div class="badge">SPESIAL</div>
                    <div class="coupon-header">
                        <div class="coupon-info">
                            <div class="coupon-percentage">25%</div>
                            <div class="coupon-description">Konsultasi Pertama</div>
                        </div>
                        <div class="coupon-code">DISKON25</div>
                    </div>
                    <div class="coupon-details">
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Khusus konsultasi pertama</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Minimal pembelian Rp 150.000</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Maksimal diskon Rp 75.000</span>
                        </div>
                    </div>
                    <div class="coupon-footer">
                        <button class="use-coupon-btn" onclick="selectCoupon('DISKON25')">
                            <i class="fas fa-ticket-alt"></i> Gunakan Kupon
                        </button>
                    </div>
                </div>

                <!-- Kupon 3 -->
                <div class="coupon-card">
                    <div class="badge">EKSKLUSIF</div>
                    <div class="coupon-header">
                        <div class="coupon-info">
                            <div class="coupon-percentage">30%</div>
                            <div class="coupon-description">Member Spesial</div>
                        </div>
                        <div class="coupon-code">DISKON30</div>
                    </div>
                    <div class="coupon-details">
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Untuk member AB Paw</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Minimal pembelian Rp 200.000</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Maksimal diskon Rp 100.000</span>
                        </div>
                    </div>
                    <div class="coupon-footer">
                        <div class="coupon-expiry">⏳ Berlaku hingga 7 Desember 2025</div>
                        <button class="use-coupon-btn" onclick="selectCoupon('DISKON30')">
                            <i class="fas fa-ticket-alt"></i> Gunakan Kupon
                        </button>
                    </div>
                </div>
            </div>

            <button class="scroll-btn next" onclick="scrollCoupons(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'components/footer.php'; ?>

    <script>
        // Data kupon yang detail
        const couponData = {
            'DISKON15': {
                discount: 0.15,
                type: 'percentage',
                description: 'Diskon 15% untuk semua konsultasi',
                minPurchase: 100000,
                maxDiscount: 50000,
                percentage: 15
            },
            'DISKON25': {
                discount: 0.25,
                type: 'percentage',
                description: 'Diskon 25% konsultasi pertama',
                minPurchase: 150000,
                maxDiscount: 75000,
                percentage: 25
            },
            'DISKON30': {
                discount: 0.30,
                type: 'percentage',
                description: 'Diskon 30% spesial member',
                minPurchase: 200000,
                maxDiscount: 100000,
                percentage: 30
            }
        };

        // Fungsi untuk memilih kupon
        function selectCoupon(couponCode) {
            if (couponData[couponCode]) {
                // Simpan data kupon yang dipilih ke localStorage
                const selectedCoupon = {
                    code: couponCode,
                    ...couponData[couponCode]
                };

                localStorage.setItem('selectedCoupon', JSON.stringify(selectedCoupon));

                // Tampilkan konfirmasi
                alert(`🎉 Kupon ${couponCode} berhasil dipilih!\n\nKupon akan otomatis diterapkan saat kembali ke halaman pemesanan.`);

                // Redirect kembali ke halaman pemesanan
                window.location.href = 'I_pemesanan_on.php';
            } else {
                alert('Kupon tidak valid!');
            }
        }

        // Fungsi untuk scroll horizontal
        function scrollCoupons(direction) {
            const scrollContainer = document.getElementById('couponScroll');
            const scrollAmount = 340; // Lebar card + gap

            if (direction === 1) {
                scrollContainer.scrollLeft += scrollAmount;
            } else {
                scrollContainer.scrollLeft -= scrollAmount;
            }
        }

        // Efek hover untuk coupon cards
        document.querySelectorAll('.coupon-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Touch swipe untuk mobile
        let startX;
        const scrollWrapper = document.getElementById('couponScroll');

        scrollWrapper.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        scrollWrapper.addEventListener('touchmove', (e) => {
            if (!startX) return;

            const currentX = e.touches[0].clientX;
            const diff = startX - currentX;

            if (Math.abs(diff) > 50) {
                scrollWrapper.scrollLeft += diff * 2;
                startX = currentX;
            }
        });

        // Cek dan restore backup data dokter jika ada
        window.addEventListener('load', function() {
            const backupDoctor = sessionStorage.getItem('backupDoctor');
            if (backupDoctor && !localStorage.getItem('selectedDoctor')) {
                localStorage.setItem('selectedDoctor', backupDoctor);
                sessionStorage.removeItem('backupDoctor');
            }
        });
    </script>
</body>

</html>