<?php
session_start();
require_once "logic/filter.php";
require "logic/koneksi.php";

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: B_login.php");
    exit();
}

// Ambil data hewan peliharaan milik user
$user_id = $_SESSION['user_id'];
$user_animal_types = getUserAnimalTypes($user_id);

// Ambil data dokter dari database menggunakan filter
$doctors_data = filterDoctors();
$doctors = [];

foreach ($doctors_data as $dokter) {
    $doctors[] = [
        'id' => $dokter['id_dokter'],
        'name' => $dokter['nama_dokter'],
        'specialty' => $dokter['bidang_khusus'],
        'consultationFee' => $dokter['biaya_konsultasi'],
        'animalSpecies' => $dokter['spesies_hewan'],
        'experience' => rand(3, 8),
        'image' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
    ];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temui Online - Klinik Hewan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="temui-online-page">
    <!-- Header -->
    <?php require_once 'components/header.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3 class="sidebar-title">Menu</h3>
                <ul class="sidebar-menu">
                    <li><a href="B_jadwal_keKlinik.php"><i class="fas fa-calendar-check"></i> Jadwalkan ke klinik offline</a></li>
                    <li><a href="I_temui_online.php" class="active"><i class="fas fa-comment-medical"></i> Telekonsultasi online</a></li>
                </ul>

                <div class="sidebar-divider"></div>

                <h3 class="sidebar-title">Janji Saya</h3>
                <ul class="sidebar-menu">
                    <li><a href="B_akandatang.php"><i class="fas fa-clock"></i> Akan Datang</a></li>
                    <li><a href="I_riwayat.php"><i class="fas fa-history"></i> Riwayat</a></li>
                </ul>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Hubungi Dokter Hewan untuk Hewan Anda</h1>
                    <p style="color: var(--gray);">Halo <strong><?php echo htmlspecialchars($_SESSION['nama_pemilik'] ?? 'User'); ?></strong>, selamat datang di layanan telekonsultasi!</p>
                </div>

                <div class="animal-mismatch-warning" id="animal-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="warning-text"></span>
                </div>

                <div class="filter-section">
                    <select class="filter-select" id="specialization-filter">
                        <option value="all">Semua Spesialisasi</option>
                    </select>
                    <select class="filter-select" id="animal-filter">
                        <option value="all">Semua Hewan</option>
                    </select>
                </div>

                <h2 class="section-title">TELEKONSULTASI</h2>

                <div class="doctor-list" id="doctor-list">
                    <!-- Data dokter akan diisi secara dinamis -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'components/footer.php'; ?>

    <script>
        // ✅ DATA DOKTER DARI PHP (hapus data hardcoded)
        const doctors = <?php echo json_encode($doctors); ?>;

        // ✅ DATA HEWAN USER DARI PHP
        const userAnimalTypes = <?php echo json_encode($user_animal_types); ?>;

        console.log("User's animal types:", userAnimalTypes);

        // ✅ Fungsi untuk memformat angka menjadi format Rupiah
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // ✅ Fungsi untuk mengecek apakah dokter cocok dengan hewan user
        function isDoctorCompatible(doctor) {
            if (userAnimalTypes.length === 0) {
                return false;
            }

            if (!doctor.animalSpecies) {
                return false;
            }

            const doctorSpecies = doctor.animalSpecies.toLowerCase().split(',').map(s => s.trim());

            // Cek apakah ada hewan user yang cocok dengan spesialisasi dokter
            for (let userAnimal of userAnimalTypes) {
                for (let doctorAnimal of doctorSpecies) {
                    if (doctorAnimal.includes(userAnimal) || userAnimal.includes(doctorAnimal)) {
                        return true;
                    }
                }
            }

            return false;
        }

        // ✅ Fungsi untuk update warning message
        function updateWarningMessage(compatibleCount, totalCount) {
            const warningDiv = document.getElementById('animal-warning');
            const warningText = document.getElementById('warning-text');

            if (userAnimalTypes.length === 0) {
                warningText.textContent = "Anda belum memiliki data hewan peliharaan. Silakan tambahkan hewan peliharaan terlebih dahulu.";
                warningDiv.style.display = 'block';
            } else if (compatibleCount === 0 && totalCount > 0) {
                warningText.textContent = "Tidak ada dokter yang sesuai dengan jenis hewan peliharaan Anda. Anda mungkin perlu menambahkan hewan lain atau mencari dokter umum.";
                warningDiv.style.display = 'block';
            } else if (compatibleCount < totalCount) {
                warningText.textContent = `${compatibleCount} dari ${totalCount} dokter sesuai dengan hewan peliharaan Anda. Dokter yang tidak sesuai ditandai dengan warna abu-abu.`;
                warningDiv.style.display = 'block';
            } else {
                warningDiv.style.display = 'none';
            }
        }

        // ✅ Fungsi untuk menampilkan daftar dokter
        function displayDoctors(doctorsToDisplay) {
            const doctorList = document.getElementById('doctor-list');
            doctorList.innerHTML = '';

            if (doctorsToDisplay.length === 0) {
                doctorList.innerHTML = '<p>Tidak ada dokter yang ditemukan.</p>';
                return;
            }

            let compatibleDoctorsCount = 0;

            doctorsToDisplay.forEach(doctor => {
                const isCompatible = isDoctorCompatible(doctor);
                if (isCompatible) compatibleDoctorsCount++;

                const doctorCard = document.createElement('div');
                doctorCard.className = `doctor-card ${isCompatible ? '' : 'disabled'}`;

                doctorCard.setAttribute('data-compatible', isCompatible);
                doctorCard.setAttribute('data-animals', doctor.animalSpecies);

                doctorCard.innerHTML = `
<div class="doctor-header">
<div class="doctor-image" style="background-image: url('${doctor.image}');"></div>
<div class="doctor-info">
<div class="doctor-name">Dr. ${doctor.name}</div>
<div class="doctor-specialty">${doctor.specialty}</div>
<div class="doctor-experience">Pengalaman: ${doctor.experience} tahun</div>
${!isCompatible ? '<div class="incompatible-label" style="color: red; font-size: 12px; margin-top: 5px;"><i class="fas fa-exclamation-circle"></i> Tidak sesuai dengan hewan Anda</div>' : ''}
</div>
</div>

<div class="doctor-card-content">
<div class="doctor-main-info">
<div class="doctor-details">
<div class="doctor-detail-item">
<div class="consultation-fee">
<div class="fee-label">Biaya Konsultasi</div>
<div class="fee-amount">${formatRupiah(doctor.consultationFee)}</div>
</div>
</div>
</div>
</div>

<div class="doctor-additional-info">
<div class="doctor-specialization">Spesies Hewan: ${doctor.animalSpecies}</div>
<div class="doctor-actions">
${isCompatible ? 
`<button class="consult-button" data-id="${doctor.id}" data-name="${doctor.name}" data-specialty="${doctor.specialty}" data-fee="${doctor.consultationFee}" data-species="${doctor.animalSpecies}">Pilih Dokter</button>` :
`<button class="consult-button disabled-btn" disabled style="opacity: 0.5; cursor: not-allowed;">Pilih Dokter</button>`
}
</div>
</div>
</div>
`;

                doctorList.appendChild(doctorCard);
            });

            // Update warning message
            updateWarningMessage(compatibleDoctorsCount, doctorsToDisplay.length);

            // ✅ Event listener untuk tombol konsultasi
            document.querySelectorAll('.consult-button:not(.disabled-btn)').forEach(button => {
                button.addEventListener('click', function() {
                    const doctorId = this.getAttribute('data-id');
                    const doctor = doctors.find(d => d.id == doctorId);

                    if (doctor) {
                        localStorage.setItem('selectedDoctor', JSON.stringify(doctor));
                        window.location.href = 'I_pemesanan_on.php';
                    } else {
                        alert('Data dokter tidak ditemukan!');
                    }
                });
            });
        }

        // ✅ FUNGSI: Mendapatkan semua spesies hewan unik
        function getAllAnimalSpecies() {
            const allSpecies = [];

            doctors.forEach(doctor => {
                if (doctor.animalSpecies) {
                    const speciesArray = doctor.animalSpecies.split(',').map(species => species.trim());
                    allSpecies.push(...speciesArray);
                }
            });

            return [...new Set(allSpecies)].sort();
        }

        // ✅ Fungsi untuk mengisi filter spesialisasi dan hewan
        function populateFilters() {
            const specializationFilter = document.getElementById('specialization-filter');
            const animalFilter = document.getElementById('animal-filter');

            // Ambil semua spesialisasi unik
            const specializations = [...new Set(doctors.map(doctor => doctor.specialty))];
            specializations.forEach(specialty => {
                const option = document.createElement('option');
                option.value = specialty;
                option.textContent = specialty;
                specializationFilter.appendChild(option);
            });

            // Ambil semua spesies hewan unik
            const animalSpecies = getAllAnimalSpecies();
            animalSpecies.forEach(species => {
                const option = document.createElement('option');
                option.value = species;
                option.textContent = species.charAt(0).toUpperCase() + species.slice(1);
                animalFilter.appendChild(option);
            });
        }

        // ✅ Inisialisasi halaman
        function initializePage() {
            // Tampilkan semua dokter saat pertama kali memuat
            displayDoctors(doctors);

            // Isi filter spesialisasi dan hewan
            populateFilters();

            // Event listener untuk filter spesialisasi
            document.getElementById('specialization-filter').addEventListener('change', function() {
                const filteredDoctors = applyAllFilters();
                displayDoctors(filteredDoctors);
            });

            // Event listener untuk filter hewan
            document.getElementById('animal-filter').addEventListener('change', function() {
                const filteredDoctors = applyAllFilters();
                displayDoctors(filteredDoctors);
            });
        }

        // ✅ Jalankan inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', initializePage);
    </script>
</body>

</html>