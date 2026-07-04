<?php
session_start();
require_once __DIR__ . '/logic/auth.php';

if (!sudahLogin()) {
    header('Location: B_login.php');
    exit;
}

$user = getUser();

// Ambil ID dokter dari parameter URL
$selected_doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;

// Jika tidak ada ID dokter, redirect kembali
if ($selected_doctor_id === 0) {
    header('Location: B_jadwal_keKlinik.php');
    exit;
}

// Ambil data dokter yang dipilih
global $koneksi;
$query_doctor = "SELECT * FROM dokter WHERE id_dokter = $selected_doctor_id";
$result_doctor = mysqli_query($koneksi, $query_doctor);
$doctor = mysqli_fetch_assoc($result_doctor);

if (!$doctor) {
    header('Location: B_jadwal_keKlinik.php');
    exit;
}

// Ambil data hewan peliharaan user yang SESUAI dengan dokter
$id_pemilik = $_SESSION['user_id'];

// ✅ PERBAIKAN: Hanya ambil hewan yang sesuai dengan spesialisasi dokter
if (!empty($doctor['spesies_hewan'])) {
    // Bersihkan dan pecah spesies dokter
    $doctor_species_array = array_map(
        'trim',
        explode(',', strtolower($doctor['spesies_hewan']))
    );

    // Buat kondisi WHERE untuk query
    $conditions = [];
    foreach ($doctor_species_array as $species) {
        $escaped_species = mysqli_real_escape_string($koneksi, $species);
        $conditions[] = "LOWER(jenis_hewan) LIKE '%$escaped_species%'";
    }

    $where_condition = implode(' OR ', $conditions);
    $query_pets = "SELECT * FROM pets WHERE id_pemilik = $id_pemilik AND ($where_condition)";
} else {
    // Jika dokter tidak punya spesialisasi, ambil semua hewan
    $query_pets = "SELECT * FROM pets WHERE id_pemilik = $id_pemilik";
}

$result_pets = mysqli_query($koneksi, $query_pets);
$pets = [];
$pets_count = 0;
while ($pet = mysqli_fetch_assoc($result_pets)) {
    $pets[] = $pet;
    $pets_count++;
}

// ✅ PERBAIKAN: Cek apakah user punya hewan yang sesuai
$hasMatchingPet = ($pets_count > 0);

// Jika tidak ada hewan yang sesuai
if (!$hasMatchingPet) {
    // Ambil semua hewan user untuk ditampilkan pesan error yang spesifik
    $query_all_pets = "SELECT jenis_hewan FROM pets WHERE id_pemilik = $id_pemilik";
    $result_all_pets = mysqli_query($koneksi, $query_all_pets);
    $user_pets_species = [];

    while ($pet = mysqli_fetch_assoc($result_all_pets)) {
        $user_pets_species[] = strtolower($pet['jenis_hewan']);
    }

    $user_pets_list = implode(', ', array_unique($user_pets_species));

    $_SESSION['error'] = "Anda tidak memiliki hewan yang sesuai dengan spesialisasi dokter ini.<br>"
        . "Dokter ini menangani: <strong>" . $doctor['spesies_hewan'] . "</strong><br>"
        . "Hewan Anda: <strong>" . ($user_pets_list ?: 'Belum ada hewan') . "</strong><br>"
        . "Silakan tambahkan hewan yang sesuai atau pilih dokter lain.";

    header('Location: B_jadwal_keKlinik.php');
    exit;
}

// Jika tidak ada hewan sama sekali
if (empty($pets)) {
    $_SESSION['error'] = "Anda belum memiliki hewan peliharaan. Silakan tambahkan hewan terlebih dahulu.";
    header('Location: B_tambah_hewan2.php');
    exit;
}

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
$doctor_photo = isset($doctor_photos[$selected_doctor_id]) ? $doctor_photos[$selected_doctor_id] : $default_photo;

// Generate jam available (sesuai jam praktek dokter)
$available_hours = [
    '08:00:00',
    '08:30:00',
    '09:00:00',
    '09:30:00',
    '10:00:00',
    '10:30:00',
    '11:00:00',
    '11:30:00',
    '12:00:00',
    '12:30:00',
    '13:00:00',
    '13:30:00',
    '14:00:00',
    '14:30:00',
    '15:00:00',
    '15:30:00',
    '16:00:00'
];

// Ambil jam yang sudah dipesan untuk 7 hari ke depan
$booked_slots = [];
$today = date('Y-m-d');
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));

    $query_booked = "SELECT waktu_antrean 
FROM pemesanan_offline 
WHERE id_dokter = $selected_doctor_id 
AND tanggal_antrean = '$date' 
AND status_antrean IN ('menunggu', 'diproses')";

    $result_booked = mysqli_query($koneksi, $query_booked);
    while ($row = mysqli_fetch_assoc($result_booked)) {
        $booked_slots[$date][] = $row['waktu_antrean'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Waktu - AB Paw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="ABclinic.jpg">
</head>

<body class="pemesanan-klinik-page">
    <!-- Header -->
    <?php require_once 'components/header.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container">
            <!-- Doctor Info Sidebar -->
            <div class="doctor-info-sidebar">
                <div class="doctor-header">
                    <div class="doctor-image" style="background-image: url('<?php echo $doctor_photo; ?>')"></div>
                    <div class="doctor-details">
                        <h3><?php echo htmlspecialchars($doctor['nama_dokter']); ?></h3>
                        <div class="doctor-specialty"><?php echo htmlspecialchars($doctor['bidang_khusus']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <strong><i class="fas fa-stethoscope"></i> Spesialisasi</strong>
                    <p><?php echo htmlspecialchars($doctor['spesies_hewan']); ?></p>
                </div>

                <div class="info-item">
                    <strong><i class="fas fa-map-marker-alt"></i> Lokasi</strong>
                    <p><?php echo htmlspecialchars($doctor['kota']); ?></p>
                </div>

                <div class="info-item">
                    <strong><i class="fas fa-clock"></i> Jam Praktek</strong>
                    <p>Senin - Jumat: 08:00 - 16:00 WIB<br>
                        Sabtu: 08:00 - 14:00 WIB<br>
                        Minggu: 09:00 - 12:00 WIB</p>
                </div>

                <div class="info-item">
                    <strong><i class="fas fa-info-circle"></i> Catatan</strong>
                    <p>Datang 15 menit sebelum jadwal konsultasi. Bawa hewan peliharaan dalam kondisi sehat.</p>
                </div>
            </div>

            <!-- Booking Content -->
            <div class="booking-content">
                <div class="page-header">
                    <h1 class="page-title">Booking Waktu Konsultasi</h1>
                    <p>Lengkapi informasi booking untuk kunjungan ke klinik</p>
                </div>

                <!-- Info Booking -->
                <div class="booking-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Informasi Penting:</strong>
                    <ul style="margin: 5px 0 0 20px; font-size: 14px;">
                        <li>Jam yang sudah dipesan oleh pengguna lain tidak dapat dipilih</li>
                        <li>Jam yang sudah lewat (berdasarkan waktu sekarang) tidak dapat dipilih</li>
                        <li>Hari Minggu: 09:00 - 12:00 WIB</li>
                        <li>Hari Sabtu: 08:00 - 14:00 WIB</li>
                        <li>Senin-Jumat: 08:00 - 16:00 WIB</li>
                    </ul>
                </div>

                <form id="bookingForm" action="logic/proses_pemesanan_off.php" method="POST">
                    <input type="hidden" name="id_dokter" value="<?php echo $selected_doctor_id; ?>">

                    <!-- Informasi User -->
                    <div class="form-section">
                        <h3 class="section-title">Informasi Pemilik</h3>
                        <div class="user-info-grid">
                            <div class="info-card">
                                <h4><i class="fas fa-user"></i> Nama Lengkap</h4>
                                <p><?php echo htmlspecialchars($user['nama_pemilik']); ?></p>
                            </div>
                            <div class="info-card">
                                <h4><i class="fas fa-phone"></i> Nomor Telepon</h4>
                                <p><?php echo htmlspecialchars($user['no_hp']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Pilih Hewan -->
                    <div class="form-section">
                        <h3 class="section-title">Pilih Hewan Peliharaan <span class="required">*</span></h3>

                        <?php if (count($pets) > 0): ?>
                            <div class="pet-selection">
                                <p style="margin-bottom: 15px; color: var(--gray); font-size: 14px;">
                                    <i class="fas fa-info-circle"></i>
                                    Dokter ini khusus menangani: <strong><?php echo htmlspecialchars($doctor['spesies_hewan']); ?></strong>
                                </p>

                                <div class="pet-cards" id="pet-cards">
                                    <?php foreach ($pets as $index => $pet): ?>
                                        <div class="pet-card" data-pet-id="<?php echo $pet['id_pet']; ?>">
                                            <div class="pet-card-header">
                                                <h5><?php echo htmlspecialchars($pet['nama_pet']); ?></h5>
                                                <span class="pet-type-badge"><?php echo htmlspecialchars($pet['jenis_hewan']); ?></span>
                                            </div>
                                            <div class="pet-card-body">
                                                <p><i class="fas fa-venus-mars"></i>
                                                    <strong>Jenis Kelamin:</strong>
                                                    <?php
                                                    $gender_map = [
                                                        'jantan' => 'Jantan',
                                                        'betina' => 'Betina',
                                                        'tidak_diketahui' => 'Tidak diketahui'
                                                    ];
                                                    echo htmlspecialchars($gender_map[$pet['jenis_kelamin']] ?? '-');
                                                    ?>
                                                </p>
                                                <p><i class="fas fa-paw"></i>
                                                    <strong>Ras:</strong>
                                                    <?php echo htmlspecialchars($pet['ras'] ?: '-'); ?>
                                                </p>
                                                <p><i class="fas fa-birthday-cake"></i>
                                                    <strong>Usia:</strong>
                                                    <?php echo $pet['usia'] ? $pet['usia'] . ' tahun' : '-'; ?>
                                                </p>
                                            </div>
                                            <div class="pet-card-footer">
                                                <span class="compatible-badge">
                                                    <i class="fas fa-check-circle"></i> Sesuai dengan dokter
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="validation-message" id="pet-validation">Pilih hewan peliharaan terlebih dahulu</div>
                                <input type="hidden" name="id_pet" id="selected-pet" required>
                            </div>
                        <?php else: ?>
                            <div class="no-pets-message" style="text-align: center; padding: 30px; background: #fff3cd; border-radius: 8px;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #856404; margin-bottom: 15px;"></i>
                                <h4>Tidak ada hewan yang sesuai</h4>
                                <p style="margin-bottom: 20px;">
                                    Anda tidak memiliki hewan yang cocok dengan spesialisasi dokter ini.<br>
                                    Dokter menangani: <strong><?php echo htmlspecialchars($doctor['spesies_hewan']); ?></strong>
                                </p>
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <a href="B_tambah_hewan2.php" class="btn" style="background: var(--primary);">
                                        <i class="fas fa-plus"></i> Tambah Hewan Baru
                                    </a>
                                    <a href="B_jadwal_keKlinik.php" class="btn btn-outline">
                                        <i class="fas fa-arrow-left"></i> Pilih Dokter Lain
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pilih Tanggal -->
                    <div class="form-section">
                        <h3 class="section-title">Pilih Tanggal <span class="required">*</span></h3>
                        <div class="date-selection">
                            <div class="date-grid" id="date-grid">
                                <!-- Dates will be populated by JavaScript -->
                            </div>
                            <div class="validation-message" id="date-validation">Pilih tanggal terlebih dahulu</div>
                            <input type="hidden" name="tanggal_janji" id="selected-date" required>
                        </div>
                    </div>

                    <!-- Pilih Jam -->
                    <div class="form-section">
                        <h3 class="section-title">Pilih Jam <span class="required">*</span></h3>
                        <div class="time-selection">
                            <div class="time-grid" id="time-grid">
                                <!-- Time options will be populated by JavaScript -->
                            </div>
                            <div class="validation-message" id="time-validation">Pilih jam terlebih dahulu</div>
                            <input type="hidden" name="waktu_janji" id="selected-time" required>
                        </div>
                    </div>


                    <!-- Keluhan -->
                    <div class="form-section">
                        <h3 class="section-title">Keluhan (Opsional)</h3>
                        <textarea name="keluhan" placeholder="Deskripsikan keluhan hewan peliharaan Anda..."></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="B_jadwal_keKlinik.php" class="btn btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-submit" id="submit-btn" disabled>
                            <i class="fas fa-calendar-check"></i> Booking Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data jam yang sudah dipesan dari PHP ke JavaScript
            const bookedSlots = <?php echo json_encode($booked_slots); ?>;
            const availableHours = <?php echo json_encode($available_hours); ?>;

            // ELEMENTS YANG DIBUTUHKAN
            const petCards = document.querySelectorAll('.pet-card');
            const dateGrid = document.getElementById('date-grid');
            const timeGrid = document.getElementById('time-grid');
            const selectedPet = document.getElementById('selected-pet');
            const selectedDate = document.getElementById('selected-date');
            const selectedTime = document.getElementById('selected-time');
            const submitBtn = document.getElementById('submit-btn');
            const validationMessages = {
                pet: document.getElementById('pet-validation'),
                date: document.getElementById('date-validation'),
                time: document.getElementById('time-validation')
            };

            // Get current date and time in Indonesia timezone (WIB = UTC+7)
            function getCurrentDateTime() {
                const now = new Date();
                // Set to Indonesia timezone (UTC+7)
                const indonesiaTime = new Date(now.getTime() + (7 * 60 * 60 * 1000));
                return indonesiaTime;
            }

            // Format date to YYYY-MM-DD
            function formatDate(date) {
                const year = date.getUTCFullYear();
                const month = String(date.getUTCMonth() + 1).padStart(2, '0');
                const day = String(date.getUTCDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Format time to HH:MM:SS
            function formatTime(date) {
                const hours = String(date.getUTCHours()).padStart(2, '0');
                const minutes = String(date.getUTCMinutes()).padStart(2, '0');
                const seconds = String(date.getUTCSeconds()).padStart(2, '0');
                return `${hours}:${minutes}:${seconds}`;
            }

            // Format time to HH:MM for display
            function formatTimeForDisplay(timeString) {
                return timeString.substring(0, 5);
            }

            // Generate dates for next 7 days
            function generateDates() {
                const currentDateTime = getCurrentDateTime();
                dateGrid.innerHTML = '';

                for (let i = 0; i < 7; i++) {
                    const date = new Date(currentDateTime);
                    date.setUTCDate(date.getUTCDate() + i);

                    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                    const dayName = dayNames[date.getUTCDay()];
                    const dayDate = date.getUTCDate();
                    const monthName = monthNames[date.getUTCMonth()];
                    const year = date.getUTCFullYear();

                    const dateString = formatDate(date);
                    const displayDate = `${dayDate} ${monthName} ${year}`;

                    // Cek apakah tanggal ini adalah hari ini
                    const isToday = i === 0;

                    // Cek apakah tanggal sudah lewat (tidak termasuk hari ini)
                    const currentDateString = formatDate(currentDateTime);
                    const isPastDate = dateString < currentDateString && !isToday;

                    const dateOption = document.createElement('div');
                    dateOption.className = `date-option ${isPastDate ? 'disabled' : ''}`;
                    dateOption.setAttribute('data-date', dateString);

                    if (isPastDate) {
                        dateOption.innerHTML = `
<div class="date-day">${dayName}</div>
<div class="date-date">${displayDate}</div>
<div style="font-size:10px;color:#dc3545;margin-top:5px;">Sudah Lewat</div>
`;
                        dateOption.style.cursor = 'not-allowed';
                        dateOption.title = 'Tanggal ini sudah lewat';
                    } else if (isToday) {
                        dateOption.innerHTML = `
<div class="date-day">${dayName}</div>
<div class="date-date">${displayDate}</div>
<div style="font-size:10px;color:var(--primary);margin-top:5px;">Hari Ini</div>
`;
                    } else {
                        dateOption.innerHTML = `
<div class="date-day">${dayName}</div>
<div class="date-date">${displayDate}</div>
`;
                    }

                    if (!isPastDate) {
                        dateOption.addEventListener('click', function() {
                            document.querySelectorAll('.date-option').forEach(opt => opt.classList.remove('selected'));
                            this.classList.add('selected');
                            selectedDate.value = this.getAttribute('data-date');

                            // Update available times based on selected date
                            updateTimeOptions(selectedDate.value);

                            validationMessages.date.style.display = 'none';
                            checkFormValidity();
                        });
                        dateOption.style.cursor = 'pointer';
                    }

                    dateGrid.appendChild(dateOption);
                }
            }

            // Update time options based on selected date
            function updateTimeOptions(selectedDate) {
                timeGrid.innerHTML = '';
                selectedTime.value = '';

                const bookedTimesForDate = bookedSlots[selectedDate] || [];
                const currentDateTime = getCurrentDateTime();
                const currentDateString = formatDate(currentDateTime);
                const selectedDateTime = new Date(selectedDate + 'T00:00:00Z');

                const isToday = selectedDate === currentDateString;
                const isPastDate = selectedDate < currentDateString;

                // Jika tanggal sudah lewat (bukan hari ini), semua jam disabled
                if (isPastDate && !isToday) {
                    timeGrid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 20px; color: #dc3545;">Tanggal ini sudah lewat</div>';
                    return;
                }

                // Filter jam berdasarkan hari
                const dayOfWeek = selectedDateTime.getUTCDay(); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

                // Batasan jam berdasarkan hari
                let maxHour = 16; // Default Senin-Jumat
                let minHour = 8;

                if (dayOfWeek === 0) { // Minggu
                    maxHour = 12;
                    minHour = 9;
                } else if (dayOfWeek === 6) { // Sabtu
                    maxHour = 14;
                }

                // Get current time
                const currentTime = formatTime(currentDateTime);
                const currentHour = parseInt(currentTime.substring(0, 2));
                const currentMinute = parseInt(currentTime.substring(3, 5));

                availableHours.forEach(hour => {
                    const timeOption = document.createElement('div');
                    const displayHour = formatTimeForDisplay(hour);
                    const hourValue = parseInt(hour.substring(0, 2));
                    const minuteValue = parseInt(hour.substring(3, 5));

                    // Cek apakah jam valid untuk hari tersebut
                    const isValidHour = hourValue >= minHour && hourValue <= maxHour;

                    // Cek apakah jam ini sudah dipesan
                    const isBooked = bookedTimesForDate.includes(hour);

                    // Cek apakah jam sudah lewat (hanya untuk hari ini)
                    let isPastTime = false;
                    if (isToday && isValidHour) {
                        // Jika jam kurang dari jam sekarang, atau sama dengan jam sekarang tapi menit sudah lewat
                        if (hourValue < currentHour || (hourValue === currentHour && minuteValue <= currentMinute)) {
                            isPastTime = true;
                        }
                    }

                    const isDisabled = !isValidHour || isBooked || isPastTime;

                    timeOption.className = `time-option`;

                    // Tambah class berdasarkan status
                    if (isDisabled) {
                        if (isBooked) {
                            timeOption.classList.add('booked');
                        } else if (isPastTime) {
                            timeOption.classList.add('past');
                        } else if (!isValidHour) {
                            timeOption.classList.add('disabled');
                        }
                    }

                    timeOption.setAttribute('data-time', hour);
                    timeOption.setAttribute('data-original-time', displayHour);

                    // Text untuk display
                    let displayText = displayHour;
                    let statusText = '';

                    if (isBooked) {
                        statusText = 'Sudah dipesan';
                        displayText += ' 🔒';
                        timeOption.title = 'Jam ini sudah dipesan oleh pengguna lain';
                    } else if (isPastTime) {
                        statusText = 'Sudah lewat';
                        displayText += ' ⏰';
                        timeOption.title = 'Jam ini sudah lewat';
                    } else if (!isValidHour) {
                        statusText = 'Tidak tersedia';
                        displayText += ' ❌';
                        if (dayOfWeek === 0) {
                            timeOption.title = 'Hari Minggu hanya tersedia hingga jam 12:00';
                        } else if (dayOfWeek === 6) {
                            timeOption.title = 'Hari Sabtu hanya tersedia hingga jam 14:00';
                        } else {
                            timeOption.title = 'Jam praktek hanya hingga 16:00';
                        }
                    } else {
                        timeOption.title = 'Tersedia - Klik untuk memilih';
                    }

                    // Tambahkan status text jika ada
                    if (statusText) {
                        displayText += `\n(${statusText})`;
                    }

                    timeOption.textContent = displayHour;

                    // Tambahkan status badge di bawah jam jika perlu
                    if (statusText) {
                        const statusBadge = document.createElement('div');
                        statusBadge.style.fontSize = '10px';
                        statusBadge.style.color = isBooked ? '#dc3545' : (isPastTime ? '#ff9800' : '#6c757d');
                        statusBadge.textContent = statusText;
                        timeOption.appendChild(statusBadge);
                    }

                    if (!isDisabled) {
                        timeOption.addEventListener('click', function() {
                            document.querySelectorAll('.time-option').forEach(opt => {
                                if (!opt.classList.contains('booked') && !opt.classList.contains('disabled') && !opt.classList.contains('past')) {
                                    opt.classList.remove('selected');
                                }
                            });
                            this.classList.add('selected');
                            selectedTime.value = this.getAttribute('data-time');
                            validationMessages.time.style.display = 'none';
                            checkFormValidity();
                        });
                        timeOption.style.cursor = 'pointer';
                        timeOption.style.opacity = '1';
                    } else {
                        timeOption.style.cursor = 'not-allowed';
                        timeOption.style.opacity = '0.6';
                    }

                    timeGrid.appendChild(timeOption);
                });
            }

            // Pet selection
            // Modifikasi bagian JavaScript untuk validasi
            petCards.forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.pet-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedPet.value = this.getAttribute('data-pet-id');
                    validationMessages.pet.style.display = 'none';
                    checkFormValidity();

                    // Tambahkan feedback visual
                    const petName = this.querySelector('h5').textContent;
                    console.log(`Hewan dipilih: ${petName}`);
                });
            });

            // Tambahkan fungsi untuk mengecek apakah hewan valid untuk dokter
            function isPetCompatible(petSpecies, doctorSpecies) {
                if (!doctorSpecies) return true;

                const doctorSpeciesArray = doctorSpecies.toLowerCase().split(',').map(s => s.trim());
                const petSpeciesLower = petSpecies.toLowerCase();

                return doctorSpeciesArray.some(species =>
                    petSpeciesLower.includes(species) || species.includes(petSpeciesLower)
                );
            }

            // Saat form submit, tambahkan validasi tambahan
            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                let isValid = true;
                const errorMessages = [];

                if (!selectedPet.value) {
                    validationMessages.pet.style.display = 'block';
                    errorMessages.push('Silakan pilih hewan peliharaan');
                    isValid = false;
                }

                if (!selectedDate.value) {
                    validationMessages.date.style.display = 'block';
                    errorMessages.push('Silakan pilih tanggal');
                    isValid = false;
                }

                if (!selectedTime.value) {
                    validationMessages.time.style.display = 'block';
                    errorMessages.push('Silakan pilih waktu');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Silakan lengkapi semua data yang diperlukan:\n' + errorMessages.join('\n'));
                } else {
                    // Tampilkan konfirmasi
                    const petName = document.querySelector('.pet-card.selected h5').textContent;
                    const selectedDateText = document.querySelector('.date-option.selected .date-date').textContent;
                    const selectedTimeText = document.querySelector('.time-option.selected').textContent.split('\n')[0];

                    const confirmation = confirm(
                        'Konfirmasi Booking:\n\n' +
                        '🐾 Hewan: ' + petName + '\n' +
                        '📅 Tanggal: ' + selectedDateText + '\n' +
                        '⏰ Waktu: ' + selectedTimeText + '\n' +
                        '\nApakah data sudah benar?'
                    );

                    if (confirmation) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                        submitBtn.disabled = true;
                    } else {
                        e.preventDefault();
                    }
                }
            });

            // Check form validity
            function checkFormValidity() {
                const isPetSelected = selectedPet.value !== '';
                const isDateSelected = selectedDate.value !== '';
                const isTimeSelected = selectedTime.value !== '';

                submitBtn.disabled = !(isPetSelected && isDateSelected && isTimeSelected);

                if (!submitBtn.disabled) {
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                } else {
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                }
            }

            // Form submission dengan validasi tambahan
            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                let isValid = true;

                if (!selectedPet.value) {
                    validationMessages.pet.style.display = 'block';
                    isValid = false;
                }
                if (!selectedDate.value) {
                    validationMessages.date.style.display = 'block';
                    isValid = false;
                }
                if (!selectedTime.value) {
                    validationMessages.time.style.display = 'block';
                    isValid = false;
                }

                // Validasi real-time untuk jam yang sudah lewat
                if (selectedDate.value && selectedTime.value) {
                    const currentDateTime = getCurrentDateTime();
                    const currentDateString = formatDate(currentDateTime);

                    // Cek jika tanggal sudah lewat
                    if (selectedDate.value < currentDateString) {
                        alert('Tanggal yang dipilih sudah lewat. Silakan pilih tanggal lain.');
                        isValid = false;
                    }

                    // Cek jika jam sudah lewat untuk hari ini
                    if (selectedDate.value === currentDateString) {
                        const currentTime = formatTime(currentDateTime);
                        if (selectedTime.value <= currentTime) {
                            alert('Waktu yang dipilih sudah lewat. Silakan pilih waktu lain.');
                            isValid = false;
                        }
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                } else {
                    // Tampilkan loading atau konfirmasi
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    submitBtn.disabled = true;
                }
            });

            // Initialize dates
            generateDates();

            // Auto-select today's date
            setTimeout(() => {
                const todayDate = document.querySelector('.date-option:not(.disabled)');
                if (todayDate) {
                    todayDate.click();
                }
            }, 100);

            // Auto-refresh time slots setiap 1 menit untuk update real-time
            setInterval(function() {
                if (selectedDate.value) {
                    updateTimeOptions(selectedDate.value);

                    // Juga update validity check
                    checkFormValidity();
                }
            }, 60000); // Refresh setiap 1 menit

            // Juga update dates setiap 5 menit untuk pastikan tanggal yang sudah lewat di-disable
            setInterval(function() {
                generateDates();
            }, 300000); // Refresh setiap 5 menit
        });
    </script>

    <!-- Footer -->
    <?php require_once 'components/footer.php'; ?>
</body>

</html>