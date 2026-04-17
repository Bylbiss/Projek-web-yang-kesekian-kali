<?php
session_start();
require 'logic/auth.php';
require_once "logic/filter.php";

if (!sudahLogin()) {
    header('Location: B_login.php');
    exit;
}

$user = getUser();

// Ambil data dokter dari database menggunakan filter
$doctors = filterDoctorsWithQueue(date('Y-m-d'));

// Ambil data hewan peliharaan user
$id_pemilik = $_SESSION['user_id'];
$pets = getUserPets($id_pemilik);

// Ambil data untuk filter
$specialties = getAllSpecialties();
$animal_species_all = getAllAnimalSpecies();

// Array foto dokter
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
    <title>Jadwalkan ke Klinik - AB Paw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="ABclinic.jpg">
</head>

<body class="schedule-clinic-page">
    <!-- Header -->
    <?php require_once 'components/header.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3 class="sidebar-title">Menu</h3>
                <ul class="sidebar-menu">
                    <li><a href="B_jadwal_keKlinik.php" class="active"><i class="fas fa-calendar-check"></i> Jadwalkan ke klinik</a></li>
                    <li><a href="I_temui_online.php"><i class="fas fa-comment-medical"></i> Telekonsultasi online</a></li>
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
                    <h1 class="page-title">Jadwalkan Kunjungan ke Klinik</h1>
                    <p>Pilih dokter dan booking waktu kunjungan untuk hewan peliharaan Anda</p>
                </div>

                <div class="service-highlight">
                    <i class="fas fa-info-circle"></i>
                    <strong>Layanan Pemesanan Offline</strong>
                    <p>Booking waktu konsultasi langsung di klinik tanpa biaya booking. Biaya konsultasi dibayar langsung di klinik.</p>
                </div>

                <div class="filter-section">
                    <select class="filter-select" id="specialization-filter">
                        <option value="all">Semua Bidang Khusus</option>
                        <?php foreach ($specialties as $specialty): ?>
                            <option value="<?php echo htmlspecialchars($specialty); ?>"><?php echo htmlspecialchars($specialty); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="filter-select" id="animal-filter">
                        <option value="all">Semua Hewan</option>
                        <?php foreach ($animal_species_all as $species): ?>
                            <option value="<?php echo htmlspecialchars($species); ?>"><?php echo htmlspecialchars(ucfirst($species)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-results" id="filter-results">
                    Menampilkan semua dokter
                </div>

                <h2 class="section-title">DOKTER TERSEDIA</h2>

                <div class="doctor-list" id="doctor-list">
                    <?php if (empty($doctors)): ?>
                        <div class="no-doctors">
                            <i class="fas fa-user-md"></i>
                            <p>Tidak ada dokter yang tersedia saat ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($doctors as $doctor):
                            $doctor_photo = isset($doctor_photos[$doctor['id_dokter']]) ? $doctor_photos[$doctor['id_dokter']] : $default_photo;
                        ?>
                            <div class="doctor-card"
                                data-specialty="<?php echo htmlspecialchars($doctor['bidang_khusus']); ?>"
                                data-species="<?php echo htmlspecialchars($doctor['spesies_hewan']); ?>">
                                <div class="doctor-header">
                                    <div class="doctor-image" style="background-image: url('<?php echo $doctor_photo; ?>')"></div>
                                    <div class="doctor-info">
                                        <div class="doctor-name"><?php echo htmlspecialchars($doctor['nama_dokter']); ?></div>
                                        <div class="doctor-specialty"><?php echo htmlspecialchars($doctor['bidang_khusus']); ?></div>
                                        <div class="doctor-experience">
                                            <i class="fas fa-briefcase"></i>
                                            <span>5+ tahun pengalaman</span>
                                        </div>
                                        <div class="contact-info">
                                            <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($doctor['no_hp']); ?></span>
                                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($doctor['kota']); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="doctor-card-content">
                                    <div class="doctor-main-info">
                                        <?php if ($doctor['spesies_hewan']): ?>
                                            <div class="doctor-specialization">
                                                <strong>Spesialis:</strong> <?php echo htmlspecialchars($doctor['spesies_hewan']); ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="schedule-info">
                                            <strong><i class="fas fa-clock"></i> Jam Praktek Klinik:</strong>
                                            <div class="schedule-item">
                                                <span class="schedule-day">Senin - Jumat</span>
                                                <span class="schedule-time">08:00 - 16:00 WIB</span>
                                            </div>
                                            <div class="schedule-item">
                                                <span class="schedule-day">Sabtu</span>
                                                <span class="schedule-time">08:00 - 14:00 WIB</span>
                                            </div>
                                            <div class="schedule-item">
                                                <span class="schedule-day">Minggu</span>
                                                <span class="schedule-time">09:00 - 12:00 WIB</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="doctor-additional-info">
                                        <div class="availability-info">
                                            <i class="fas fa-calendar-check"></i>
                                            Tersedia untuk Booking Offline
                                        </div>

                                        <div class="doctor-actions">
                                            <button class="schedule-button" onclick="selectDoctor(<?php echo $doctor['id_dokter']; ?>)">
                                                <i class="fas fa-calendar-plus"></i>
                                                Booking Waktu
                                            </button>
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

    <!-- Footer -->
    <?php require_once 'components/footer.php'; ?>

    <script>
        // Data hewan user
        const userPetsData = <?php
                                $pets_data = [];
                                foreach ($pets as $pet) {
                                    $pets_data[] = [
                                        'id_pet' => $pet['id_pet'],
                                        'nama_pet' => $pet['nama_pet'],
                                        'jenis_hewan' => $pet['jenis_hewan']
                                    ];
                                }
                                echo json_encode($pets_data);
                                ?>;

        // Fungsi untuk memilih dokter dengan validasi hewan
        function selectDoctor(doctorId) {
            console.log('Doctor ID clicked:', doctorId); // Debug

            // Ambil data spesialisasi dokter dari card yang diklik
            const doctorCard = document.querySelector(`.doctor-card[data-species]`);
            let doctorSpecies = '';

            // Cari card yang sesuai dengan doctorId
            document.querySelectorAll('.doctor-card').forEach(card => {
                // Cari card yang berisi button dengan onclick yang sesuai
                const button = card.querySelector(`button[onclick*="${doctorId}"]`);
                if (button) {
                    doctorSpecies = card.getAttribute('data-species');
                    console.log('Found doctor species:', doctorSpecies); // Debug
                }
            });

            // Simpan ID dokter yang dipilih
            localStorage.setItem('selectedDoctorId', doctorId);

            // Cek kompatibilitas hewan user dengan dokter
            checkPetCompatibility(doctorId, doctorSpecies);
        }

        // Fungsi untuk cek kompatibilitas hewan
        function checkPetCompatibility(doctorId, doctorSpecies) {
            console.log('Checking compatibility for doctor:', doctorId, 'with species:', doctorSpecies); // Debug
            console.log('User pets:', userPetsData); // Debug

            if (!doctorSpecies) {
                console.error('Doctor species not found');
                // Jika tidak bisa dapat species, lanjutkan saja (fallback)
                window.location.href = 'B_pemesanan_klinik.php?doctor_id=' + doctorId;
                return;
            }

            // Split spesies dokter menjadi array
            const doctorSpeciesArray = doctorSpecies.toLowerCase().split(',').map(s => s.trim());
            console.log('Doctor species array:', doctorSpeciesArray); // Debug

            // Cek apakah user punya hewan yang sesuai
            let hasCompatiblePet = false;

            userPetsData.forEach(pet => {
                const petSpecies = pet.jenis_hewan.toLowerCase();
                console.log('Checking pet:', pet.nama_pet, 'species:', petSpecies); // Debug

                if (doctorSpeciesArray.some(species =>
                        petSpecies.includes(species) ||
                        species.includes(petSpecies)
                    )) {
                    console.log('Compatible pet found:', pet.nama_pet); // Debug
                    hasCompatiblePet = true;
                }
            });

            console.log('Has compatible pet:', hasCompatiblePet); // Debug

            if (hasCompatiblePet) {
                // Jika ada hewan yang compatible, lanjutkan ke booking
                window.location.href = 'B_pemesanan_klinik.php?doctor_id=' + doctorId;
            } else {
                // Jika tidak ada hewan yang compatible, tampilkan popup
                showIncompatiblePetPopup(doctorSpecies);
            }
        }

        // Fungsi untuk menampilkan popup hewan tidak compatible
        function showIncompatiblePetPopup(doctorSpecies) {
            // Buat popup element
            const popup = document.createElement('div');
            popup.className = 'incompatible-pet-popup';
            popup.style.cssText = `position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
background: rgba(0,0,0,0.5);
display: flex;
justify-content: center;
align-items: center;
z-index: 1000;
`;

            popup.innerHTML = `
<div style="background: white; padding: 30px; border-radius: 15px;
max-width: 450px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); text-align: center;">
<div style="font-size: 3rem; color: #ff6b6b; margin-bottom: 15px;">
<i class="fas fa-exclamation-triangle"></i>
</div>

<h3 style="color: #333; margin-bottom: 15px; font-size: 1.4rem;">
Dokter Tidak Sesuai
</h3>
<p style="color: #666; margin-bottom: 25px; line-height: 1.6;">
Maaf, dokter ini khusus menangani: 
<strong style="color: var(--primary);">${doctorSpecies}</strong>
</p>
<p style="color: #888; margin-bottom: 25px; font-size: 0.9rem;">
Silakan pilih dokter lain yang sesuai dengan hewan peliharaan Anda.
</p>
<button onclick="closePopup()" style="padding: 12px 30px; background: var(--primary);
color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;
font-size: 1rem; transition: all 0.3s ease;">
<i class="fas fa-check"></i> Mengerti
</button>
</div>
`;

            document.body.appendChild(popup);
        }

        // Fungsi untuk menutup popup
        function closePopup() {
            const popup = document.querySelector('.incompatible-pet-popup');
            if (popup) {
                popup.remove();
            }
        }

        // Event listener untuk esc key dan klik outside
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePopup();
            }
        });

        // Tutup popup ketika klik di luar
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('incompatible-pet-popup')) {
                closePopup();
            }
        });

        // Filter dokter berdasarkan spesialisasi dan hewan
        document.addEventListener('DOMContentLoaded', function() {
            const specializationFilter = document.getElementById('specialization-filter');
            const animalFilter = document.getElementById('animal-filter');
            const doctorCards = document.querySelectorAll('.doctor-card');
            const filterResults = document.getElementById('filter-results');

            function applyFilters() {
                const selectedSpecialty = specializationFilter.value;
                const selectedAnimal = animalFilter.value;
                let visibleCount = 0;

                doctorCards.forEach(card => {
                    const cardSpecialty = card.getAttribute('data-specialty');
                    const cardSpecies = card.getAttribute('data-species');

                    let showCard = true;
                    let matchesSpecialty = false;
                    let matchesAnimal = false;

                    // OR Logic: Filter berdasarkan spesialisasi ATAU hewan
                    if (selectedSpecialty !== 'all') {
                        matchesSpecialty = (cardSpecialty === selectedSpecialty);
                    }

                    if (selectedAnimal !== 'all') {
                        if (!cardSpecies) {
                            matchesAnimal = false;
                        } else {
                            const speciesArray = cardSpecies.toLowerCase().split(',');
                            matchesAnimal = speciesArray.some(species =>
                                species.trim().includes(selectedAnimal.toLowerCase()) ||
                                selectedAnimal.toLowerCase().includes(species.trim())
                            );
                        }
                    }

                    // Logika OR: Tampilkan jika sesuai spesialisasi ATAU sesuai hewan
                    if (selectedSpecialty !== 'all' && selectedAnimal !== 'all') {
                        // Kedua filter dipilih: OR logic
                        showCard = matchesSpecialty || matchesAnimal;
                    } else if (selectedSpecialty !== 'all') {
                        // Hanya filter spesialisasi
                        showCard = matchesSpecialty;
                    } else if (selectedAnimal !== 'all') {
                        // Hanya filter hewan
                        showCard = matchesAnimal;
                    }
                    // Jika keduanya 'all', showCard tetap true

                    // Tampilkan atau sembunyikan card
                    card.style.display = showCard ? 'block' : 'none';
                    if (showCard) visibleCount++;
                });

                // Update hasil filter
                if (selectedSpecialty === 'all' && selectedAnimal === 'all') {
                    filterResults.textContent = 'Menampilkan semua dokter';
                } else {
                    let filterText = 'Menampilkan ' + visibleCount + ' dokter';
                    const conditions = [];

                    if (selectedSpecialty !== 'all') {
                        conditions.push('spesialisasi: ' + selectedSpecialty);
                    }
                    if (selectedAnimal !== 'all') {
                        conditions.push('hewan: ' + selectedAnimal);
                    }

                    filterText += ' (sesuai ' + conditions.join(' ATAU ') + ')';
                    filterResults.textContent = filterText;
                }

                // Cek jika tidak ada dokter yang ditampilkan
                const doctorList = document.getElementById('doctor-list');
                const existingNoDoctors = document.querySelector('.no-doctors-filtered');

                if (visibleCount === 0) {
                    if (!existingNoDoctors) {
                        const noDoctorsMessage = document.createElement('div');
                        noDoctorsMessage.className = 'no-doctors no-doctors-filtered';
                        noDoctorsMessage.innerHTML = `
                <i class="fas fa-search"></i>
                <p>Tidak ada dokter yang sesuai dengan filter yang dipilih.</p>
                <button onclick="resetFilters()" style="margin-top: 10px; padding: 8px 16px; background: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Tampilkan Semua Dokter
                </button>
            `;
                        doctorList.appendChild(noDoctorsMessage);
                    }
                } else {
                    if (existingNoDoctors) {
                        existingNoDoctors.remove();
                    }
                }
            }

            // Fungsi reset filter
            window.resetFilters = function() {
                specializationFilter.value = 'all';
                animalFilter.value = 'all';
                applyFilters();
            }

            // Event listeners untuk filter
            specializationFilter.addEventListener('change', applyFilters);
            animalFilter.addEventListener('change', applyFilters);

            // Apply filter pertama kali
            applyFilters();
        });
    </script>
</body>

</html>