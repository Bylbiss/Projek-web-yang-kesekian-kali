<?php
require "logic/koneksi.php";
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: B_login.php");
    exit();
}

// Ambil data pemilik dari database
$id_pemilik = $_SESSION['user_id'];
$query_pemilik = "SELECT * FROM pemilik WHERE id_pemilik = ?";
$stmt_pemilik = $koneksi->prepare($query_pemilik);
$stmt_pemilik->bind_param("i", $id_pemilik);
$stmt_pemilik->execute();
$result_pemilik = $stmt_pemilik->get_result();
$pemilik = $result_pemilik->fetch_assoc();

// Ambil data pets dari database
$query_pets = "SELECT * FROM pets WHERE id_pemilik = ?";
$stmt_pets = $koneksi->prepare($query_pets);
$stmt_pets->bind_param("i", $id_pemilik);
$stmt_pets->execute();
$result_pets = $stmt_pets->get_result();
$pets = $result_pets->fetch_all(MYSQLI_ASSOC);

// AMBIL DATA JADWAL YANG SUDAH TERBOOKING
$query_booked_slots = "SELECT tanggal_konsultasi, waktu_konsultasi, id_dokter 
FROM pemesanan_online 
WHERE status_pemesanan != 'cancelled'";
$result_booked = $koneksi->query($query_booked_slots);
$booked_slots = [];

if ($result_booked) {
    while ($row = $result_booked->fetch_assoc()) {
        $booked_slots[] = [
            'tanggal' => $row['tanggal_konsultasi'],
            'waktu' => $row['waktu_konsultasi'],
            'dokter_id' => $row['id_dokter']
        ];
    }
}

// Convert ke JSON untuk digunakan di JavaScript
$booked_slots_json = json_encode($booked_slots);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Online - AB Paw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="pemesanan-online-page">
    <!-- Header -->
    <?php require_once 'components/header.php'; ?>

    <!-- Main Content -->
    <div class="pemesanan-online-container">
        <div class="main-container">
            <!-- Content -->
            <div class="content">
                <h1 class="page-title">Pemesanan Konsultasi Online</h1>

                <!-- Informasi Dokter -->
                <div class="section">
                    <h2 class="section-title">Informasi Dokter</h2>
                    <div class="doctor-info">
                        <div class="doctor-image" id="doctor-image"></div>
                        <div class="doctor-details">
                            <h3 id="doctor-name">-</h3>
                            <p id="doctor-specialty">-</p>
                            <p id="doctor-species">-</p>
                            <p><strong id="doctor-fee">-</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Form Pemesanan -->
                <div class="section">
                    <h2 class="section-title">Informasi Pasien</h2>
                    <form id="booking-form" method="POST" action="logic/proses_pemesanan_on.php">
                        <input type="hidden" name="id_dokter" id="form-id-dokter">
                        <input type="hidden" name="biaya_konsultasi" id="form-biaya-konsultasi">
                        <input type="hidden" name="id_pemilik" value="<?php echo $pemilik['id_pemilik']; ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="owner-name">Nama Pemilik</label>
                                <input type="text" id="owner-name" class="form-input" value="<?php echo htmlspecialchars($pemilik['nama_pemilik'] ?? ''); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="pet-select">Pilih Hewan Peliharaan</label>
                                <select id="pet-select" class="form-select" name="id_pet" required>
                                    <option value="">Pilih hewan peliharaan</option>
                                    <?php foreach ($pets as $pet): ?>
                                        <option value="<?php echo $pet['id_pet']; ?>">
                                            <?php echo htmlspecialchars($pet['nama_pet'] . ' (' . $pet['jenis_hewan'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="consultation-date">Tanggal Konsultasi</label>
                                <input type="date" id="consultation-date" class="form-input" name="tanggal_konsultasi" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Pilih Jam Konsultasi</label>
                                <div class="time-slots" id="time-slots-container">
                                    <div class="time-slot" data-time="09:00">09:00</div>
                                    <div class="time-slot" data-time="10:00">10:00</div>
                                    <div class="time-slot" data-time="11:00">11:00</div>
                                    <div class="time-slot" data-time="13:00">13:00</div>
                                    <div class="time-slot" data-time="14:00">14:00</div>
                                    <div class="time-slot" data-time="15:00">15:00</div>
                                    <div class="time-slot disabled" data-time="16:00">16:00</div>
                                    <div class="time-slot" data-time="17:00">17:00</div>
                                </div>
                                <input type="hidden" id="selected-time" name="waktu_konsultasi" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="symptoms">Keluhan / Gejala</label>
                            <textarea class="form-textarea" id="symptoms" name="keluhan" placeholder="Jelaskan keluhan atau gejala yang dialami hewan peliharaan Anda..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kupon Diskon</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" id="coupon-code" class="form-input" placeholder="Kupon akan otomatis terapkan dari halaman kupon" style="flex: 1;" readonly>
                                <a href="I_kupon.php" class="btn" style="white-space: nowrap;">
                                    <i class="fas fa-gift"></i> Lihat Kupon
                                </a>
                            </div>
                            <div id="coupon-message" style="margin-top: 8px; font-size: 14px;"></div>
                            <input type="hidden" id="applied-coupon" name="applied_coupon" value="">
                            <input type="hidden" id="discount-amount-input" name="discount_amount" value="0">
                        </div>
                    </form>
                </div>

                <!-- Kupon Diskon -->
                <div class="section">
                    <h2 class="section-title">Kupon Diskon</h2>
                    <div class="coupon-section">
                        <div class="coupon-info">
                            <div>
                                <strong id="coupon-status">Tidak ada kupon yang aktif</strong>
                                <div id="coupon-details" style="font-size: 0.9rem; color: var(--gray); margin-top: 5px;"></div>
                            </div>
                            <a href="I_kupon.php" class="btn btn-outline">
                                <i class="fas fa-ticket-alt"></i> Lihat Kupon
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Ringkasan -->
            <div class="sidebar">
                <h2 class="section-title">Ringkasan Pesanan</h2>

                <div class="summary-item">
                    <span>Biaya Konsultasi</span>
                    <span id="consultation-fee">Rp 0</span>
                </div>

                <div class="summary-item" id="discount-item" style="display: none;">
                    <span>Diskon</span>
                    <span class="discount" id="discount-amount">-Rp 0</span>
                </div>

                <div class="summary-total">
                    <span>Total Biaya</span>
                    <span id="total-fee">Rp 0</span>
                </div>

                <button class="btn" style="width: 100%; margin-top: 20px; justify-content: center;" onclick="processBooking()">
                    <i class="fas fa-credit-card"></i> Lanjutkan Pembayaran
                </button>

                <a href="I_temui_online.php" class="btn btn-outline" style="width: 100%; margin-top: 10px; justify-content: center;">
                    <i class="fas fa-arrow-left"></i> Ganti Dokter
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'components/footer.php'; ?>

    <script>
        // Data dokter yang dipilih
        let selectedDoctor = null;
        let selectedCoupon = null;
        let baseFee = 0;
        let discountAmount = 0;
        let selectedTime = '';

        // DATA JADWAL YANG SUDAH TERBOOKING (dari PHP)
        const bookedSlots = <?php echo $booked_slots_json; ?>;

        // Fungsi untuk memformat angka menjadi format Rupiah
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Cek apakah slot waktu sudah dipesan
        function isTimeSlotBooked(date, time, doctorId) {
            return bookedSlots.some(slot =>
                slot.tanggal === date &&
                slot.waktu === time &&
                slot.dokter_id == doctorId
            );
        }

        // Ubah format waktu ke menit
        function convertTimeToMinutes(timeString) {
            const [hours, minutes] = timeString.split(':').map(Number);
            return hours * 60 + minutes;
        }

        // ✅ PERBAIKAN: Fungsi generate time slots 06:00-22:00
        function generateTimeSlots() {
            const timeSlotsContainer = document.getElementById('time-slots-container'); // Ganti ini
            timeSlotsContainer.innerHTML = '';

            for (let hour = 6; hour <= 22; hour++) {
                const timeString = hour.toString().padStart(2, '0') + ':00';
                const timeSlot = document.createElement('div');
                timeSlot.className = 'time-slot';
                timeSlot.setAttribute('data-time', timeString);
                timeSlot.textContent = timeString;
                timeSlotsContainer.appendChild(timeSlot);
            }

            // Setelah generate, inisialisasi event listeners
            initializeTimeSlots();
        }

        // Update ketersediaan time slot berdasarkan tanggal yang dipilih
        function updateTimeSlotAvailability() {
            const selectedDate = document.getElementById('consultation-date').value;
            const doctorId = selectedDoctor ? selectedDoctor.id : null;

            if (!selectedDate || !doctorId) {
                return;
            }

            const today = new Date().toISOString().split('T')[0];
            const now = new Date();
            const currentHour = String(now.getHours()).padStart(2, '0');
            const currentMinute = String(now.getMinutes()).padStart(2, '0');
            const currentTime = `${currentHour}:${currentMinute}`;
            const currentTimeInMinutes = convertTimeToMinutes(currentTime);

            document.querySelectorAll('.time-slot').forEach(slot => {
                const time = slot.getAttribute('data-time');
                const slotTimeInMinutes = convertTimeToMinutes(time);
                const isBooked = isTimeSlotBooked(selectedDate, time, doctorId);
                const isPastTime = selectedDate === today && slotTimeInMinutes < currentTimeInMinutes;

                // ✅ TAMBAH: Validasi jam kerja dokter (06:00-22:00)
                const slotHour = parseInt(time.split(':')[0]);
                const isOutsideDoctorHours = slotHour < 6 || slotHour >= 22;

                // Reset semua state
                slot.classList.remove('disabled', 'past-time', 'selected', 'outside-hours');
                slot.style.cursor = 'pointer';
                slot.title = '';

                // Set state berdasarkan kondisi
                if (isBooked || isPastTime || isOutsideDoctorHours) {
                    if (isPastTime) {
                        slot.classList.add('past-time');
                        slot.title = '⏰ Jam ini sudah lewat';
                    } else if (isBooked) {
                        slot.classList.add('disabled');
                        slot.title = '❌ Jam sudah dipesan';
                    } else if (isOutsideDoctorHours) {
                        slot.classList.add('disabled');
                        slot.title = '⏰ Diluar jam kerja dokter (06:00-22:00)';
                    }
                    slot.style.cursor = 'not-allowed';
                } else {
                    slot.title = '✓ Tersedia';
                }
            });

            // Reset selected time jika tidak tersedia lagi
            if (selectedTime) {
                const selectedSlot = document.querySelector(`.time-slot[data-time="${selectedTime}"]`);
                if (selectedSlot && (selectedSlot.classList.contains('disabled') || selectedSlot.classList.contains('past-time'))) {
                    selectedTime = '';
                    document.getElementById('selected-time').value = '';
                    document.querySelectorAll('.time-slot').forEach(s => {
                        s.classList.remove('selected');
                    });
                }
            }
        }

        // ✅ PERBAIKAN: Inisialisasi time slots yang benar
        function initializeTimeSlots() {
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.addEventListener('click', function() {
                    if (this.classList.contains('disabled') || this.classList.contains('past-time')) {
                        return;
                    }

                    // Remove previous selection
                    document.querySelectorAll('.time-slot').forEach(s => {
                        s.classList.remove('selected');
                    });

                    // Add new selection
                    this.classList.add('selected');
                    selectedTime = this.getAttribute('data-time');
                    document.getElementById('selected-time').value = selectedTime;
                    console.log('✓ Selected time:', selectedTime);
                });
            });
        }

        // Load data dokter yang dipilih
        function loadSelectedDoctor() {
            const storedDoctor = localStorage.getItem('selectedDoctor');

            if (storedDoctor) {
                try {
                    selectedDoctor = JSON.parse(storedDoctor);

                    if (!selectedDoctor.id || !selectedDoctor.consultationFee) {
                        throw new Error('Data dokter tidak valid');
                    }

                    displayDoctorInfo(selectedDoctor);
                    baseFee = selectedDoctor.consultationFee;
                    document.getElementById('form-id-dokter').value = selectedDoctor.id;
                    document.getElementById('form-biaya-konsultasi').value = baseFee;

                    // ✅ PERBAIKAN: Generate time slots setelah dokter dimuat
                    generateTimeSlots();

                    // Update availability
                    updateTimeSlotAvailability();
                    updateSummary();

                } catch (error) {
                    console.error('Error loading doctor data:', error);
                    alert('Data dokter tidak valid. Silakan pilih dokter kembali.');
                    window.location.href = 'I_temui_online.php';
                }
            } else {
                alert('Silakan pilih dokter terlebih dahulu');
                window.location.href = 'I_temui_online.php';
            }
        }

        // Fungsi untuk menampilkan informasi dokter
        function displayDoctorInfo(doctor) {
            const doctorImage = document.getElementById('doctor-image');
            const doctorName = document.getElementById('doctor-name');
            const doctorSpecialty = document.getElementById('doctor-specialty');
            const doctorSpecies = document.getElementById('doctor-species');
            const doctorFee = document.getElementById('doctor-fee');

            if (doctor.image) {
                doctorImage.style.backgroundImage = `url('${doctor.image}')`;
            } else {
                doctorImage.style.backgroundColor = '#e83e8c';
                doctorImage.style.display = 'flex';
                doctorImage.style.alignItems = 'center';
                doctorImage.style.justifyContent = 'center';
                doctorImage.style.color = 'white';
                doctorImage.innerHTML = '<i class="fas fa-user-md" style="font-size: 24px;"></i>';
            }

            doctorName.textContent = 'Dr. ' + doctor.name;
            doctorSpecialty.textContent = doctor.specialty;
            doctorSpecies.textContent = 'Spesies: ' + doctor.animalSpecies;
            doctorFee.textContent = formatRupiah(doctor.consultationFee);
        }

        // Hitung diskon
        function calculateDiscount(consultationFee, coupon) {
            if (!coupon) return 0;
            const discountAmount = consultationFee * (coupon.discount);
            return Math.min(discountAmount, coupon.maxDiscount);
        }

        // Update ringkasan pesanan
        function updateSummary() {
            const consultationFee = selectedDoctor ? selectedDoctor.consultationFee : 0;
            discountAmount = calculateDiscount(consultationFee, selectedCoupon);
            const totalFee = consultationFee - discountAmount;

            document.getElementById('consultation-fee').textContent = formatRupiah(consultationFee);
            document.getElementById('total-fee').textContent = formatRupiah(totalFee);

            const discountItem = document.getElementById('discount-item');
            const discountAmountElement = document.getElementById('discount-amount');

            if (discountAmount > 0) {
                discountItem.style.display = 'flex';
                discountAmountElement.textContent = '- ' + formatRupiah(discountAmount);
                document.getElementById('discount-amount-input').value = discountAmount;
            } else {
                discountItem.style.display = 'none';
                document.getElementById('discount-amount-input').value = 0;
            }
        }

        // Load data kupon
        function loadCouponData() {
            const couponData = localStorage.getItem('selectedCoupon');

            if (couponData) {
                try {
                    selectedCoupon = JSON.parse(couponData);

                    document.getElementById('coupon-status').textContent = 'Kupon Aktif: ' + selectedCoupon.code;
                    document.getElementById('coupon-details').innerHTML =
                        `Diskon ${selectedCoupon.percentage || (selectedCoupon.discount * 100)}% (Maks. ${formatRupiah(selectedCoupon.maxDiscount)})`;

                    document.getElementById('coupon-code').value = selectedCoupon.code;
                    document.getElementById('applied-coupon').value = selectedCoupon.code;

                    document.getElementById('coupon-message').innerHTML =
                        `<span style="color: green;"><i class="fas fa-check-circle"></i> Kupon berhasil diterapkan! Diskon ${selectedCoupon.percentage || (selectedCoupon.discount * 100)}%</span>`;

                    updateSummary();

                } catch (error) {
                    console.error('Error loading coupon data:', error);
                    resetCoupon();
                }
            } else {
                resetCoupon();
            }
        }

        // Reset kupon
        function resetCoupon() {
            selectedCoupon = null;
            document.getElementById('coupon-status').textContent = 'Tidak ada kupon yang aktif';
            document.getElementById('coupon-details').innerHTML = '';
            document.getElementById('coupon-code').value = '';
            document.getElementById('applied-coupon').value = '';
            document.getElementById('coupon-message').innerHTML = '';
            updateSummary();
        }

        // Set tanggal minimal untuk input tanggal
        function setMinDate() {
            const now = new Date();
            const currentHour = now.getHours();

            let minDate = new Date(now);
            if (currentHour >= 22) {
                minDate.setDate(minDate.getDate() + 1);
            }

            const yyyy = minDate.getFullYear();
            const mm = String(minDate.getMonth() + 1).padStart(2, '0');
            const dd = String(minDate.getDate()).padStart(2, '0');

            const minDateString = `${yyyy}-${mm}-${dd}`;

            const dateInput = document.getElementById('consultation-date');
            dateInput.setAttribute('min', minDateString);
            dateInput.value = minDateString;

            setTimeout(() => {
                const event = new Event('change', {
                    bubbles: true
                });
                dateInput.dispatchEvent(event);
                updateTimeSlotAvailability();
            }, 100);
        }

        // Inisialisasi halaman
        function initializePage() {
            console.log('=== INITIALIZING PAGE ===');

            // 1. Load dokter
            console.log('Step 1: Loading doctor data...');
            loadSelectedDoctor();

            // 2. Set tanggal
            console.log('Step 2: Setting min date...');
            setMinDate();

            // 3. Load kupon
            console.log('Step 3: Loading coupon...');
            loadCouponData();

            // 4. Event listener untuk tanggal
            console.log('Step 4: Adding date change listener...');
            document.getElementById('consultation-date').addEventListener('change', function() {
                console.log('Date changed to:', this.value);
                updateTimeSlotAvailability();

                // Reset selected time ketika tanggal berubah
                selectedTime = '';
                document.getElementById('selected-time').value = '';
                document.querySelectorAll('.time-slot').forEach(s => {
                    s.classList.remove('selected');
                });
            });

            // 5. Update summary
            console.log('Step 5: Updating summary...');
            updateSummary();

            // 6. Start auto refresh
            console.log('Step 6: Starting auto-refresh timer...');
            startAutoRefreshTimer();

            console.log('=== PAGE INITIALIZATION COMPLETE ===');
        }

        // Proses pemesanan - VERSI DIPERBAIKI
        async function processBooking() {
            console.log('=== processBooking dipanggil ===');
            try {
                // 1. Ambil semua data dari form
                const petId = document.getElementById('pet-select').value;
                const symptoms = document.getElementById('symptoms').value.trim();
                const consultationDate = document.getElementById('consultation-date').value;
                const doctorId = selectedDoctor ? selectedDoctor.id : null;
                discountAmount = parseFloat(document.getElementById('discount-amount-input').value) || 0;
                const appliedCoupon = document.getElementById('applied-coupon').value || '';

                console.log('Data form:', {
                    petId,
                    symptoms: symptoms.substring(0, 50) + '...',
                    consultationDate,
                    selectedTime,
                    doctorId,
                    baseFee,
                    discountAmount,
                    appliedCoupon
                });

                // 2. Validasi
                if (!petId) {
                    alert('Silakan pilih hewan peliharaan!');
                    return;
                }

                if (!symptoms) {
                    alert('Silakan isi keluhan/gejala!');
                    return;
                }

                if (!selectedTime) {
                    alert('Silakan pilih jam konsultasi terlebih dahulu');
                    return;
                }

                if (!consultationDate) {
                    alert('Silakan pilih tanggal konsultasi');
                    return;
                }

                if (!selectedDoctor || !doctorId) {
                    alert('Data dokter tidak ditemukan. Silakan pilih dokter kembali.');
                    window.location.href = 'I_temui_online.php';
                    return;
                }

                // 3. Cek apakah jam sudah dipesan
                const isBooked = isTimeSlotBooked(consultationDate, selectedTime, doctorId);
                if (isBooked) {
                    alert('Maaf, jam yang Anda pilih sudah dipesan. Silakan pilih jam atau tanggal lain.');
                    updateTimeSlotAvailability();
                    return;
                }

                // 4. Siapkan payload
                const payload = new URLSearchParams({
                    id_dokter: doctorId,
                    id_pet: petId,
                    tanggal_konsultasi: consultationDate,
                    waktu_konsultasi: selectedTime,
                    keluhan: symptoms,
                    biaya_konsultasi: baseFee,
                    discount_amount: discountAmount,
                    applied_coupon: appliedCoupon
                });

                console.log('Payload:', payload.toString());

                // 5. Kirim request ke server
                const res = await fetch('logic/proses_pemesanan_on.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: payload.toString()
                });

                console.log('Response status:', res.status);
                console.log('Response headers:', Object.fromEntries(res.headers.entries()));

                // 6. Tangani response
                const responseText = await res.text();
                console.log('Raw response:', responseText.substring(0, 500));

                try {
                    // Coba parse sebagai JSON
                    const json = JSON.parse(responseText);
                    console.log('Parsed JSON:', json);

                    if (json.success) {
                        window.location.href = 'I_result_on.php';
                    } else {
                        alert('❌ ' + (json.message || 'Gagal membuat booking'));
                    }
                } catch (parseError) {
                    console.error('Failed to parse JSON:', parseError);
                    console.error('Raw response was:', responseText);

                    // Tampilkan error yang lebih informatif
                    if (responseText.includes('<br />') || responseText.includes('<b>')) {
                        alert('❌ Server mengembalikan error PHP. Silakan cek console untuk detail.');
                    } else if (responseText.includes('error') || responseText.includes('Error')) {
                        alert('❌ Server error: ' + responseText.substring(0, 200));
                    } else {
                        alert('❌ Terjadi kesalahan tidak terduga. Silakan cek console.');
                    }
                }

            } catch (err) {
                console.error('processBooking error:', err);
                alert('❌ Network error: ' + err.message);
            }
        }

        // Fungsi untuk auto-refresh time slots setiap menit
        function startAutoRefreshTimer() {
            setInterval(function() {
                const selectedDate = document.getElementById('consultation-date').value;
                if (selectedDate) {
                    console.log('Auto-refreshing time slots...');
                    updateTimeSlotAvailability();
                }
            }, 60000);
        }

        // Jalankan inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            initializePage();
        });
    </script>

</body>

</html>