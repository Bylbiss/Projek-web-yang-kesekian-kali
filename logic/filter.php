<?php
/**
 * File filter.php - Logika filter terpusat untuk aplikasi AB Paw
 */

require_once __DIR__ . '/koneksi.php';

/**
 * Fungsi untuk filter booking akan datang
 */
function filterUpcomingBookings($id_pemilik, $current_datetime, $filters = []) {
    global $koneksi;
    
    $where_conditions = ["po.id_pemilik = $id_pemilik"];
    
    // Filter status untuk offline
    $offline_status = "('menunggu', 'diproses')";
    if (isset($filters['status']) && $filters['status'] !== 'all') {
        $status = mysqli_real_escape_string($koneksi, $filters['status']);
        if (in_array($status, ['menunggu', 'diproses'])) {
            $offline_status = "('$status')";
        }
    }
    
    // Filter status untuk online
    $online_status = "('pending', 'confirmed')";
    if (isset($filters['status']) && $filters['status'] !== 'all') {
        $status = mysqli_real_escape_string($koneksi, $filters['status']);
        if (in_array($status, ['pending', 'confirmed'])) {
            $online_status = "('$status')";
        }
    }
    
    // Filter berdasarkan tipe (OR logic)
    $tipe_condition = "";
    if (isset($filters['tipe']) && $filters['tipe'] !== 'all') {
        $tipe = mysqli_real_escape_string($koneksi, $filters['tipe']);
        $tipe_condition = "AND (";
        if ($tipe === 'offline') {
            $tipe_condition .= "'offline' as tipe";
        } elseif ($tipe === 'online') {
            $tipe_condition .= "'online' as tipe";
        }
        $tipe_condition .= ")";
    }
    
    $query = "SELECT 
        'offline' as tipe,
        po.id_antrean as id,
        po.tanggal_antrean as tanggal,
        po.waktu_antrean as waktu,
        po.nomor_antrean,
        po.keluhan,
        po.status_antrean as status,
        po.created_at,
        d.nama_dokter,
        d.bidang_khusus,
        d.kota,
        p.nama_pet,
        p.jenis_hewan,
        NULL as biaya_konsultasi,
        NULL as kupon_digunakan,
        NULL as jumlah_diskon,
        NULL as total_biaya,
        d.id_dokter,
        d.no_hp as dokter_hp,
        pem.nama_pemilik,
        NULL as kode_pemesanan
    FROM pemesanan_offline po 
    JOIN dokter d ON po.id_dokter = d.id_dokter 
    JOIN pets p ON po.id_pet = p.id_pet 
    JOIN pemilik pem ON po.id_pemilik = pem.id_pemilik
    WHERE po.id_pemilik = $id_pemilik 
    AND po.status_antrean IN $offline_status
    AND CONCAT(po.tanggal_antrean, ' ', po.waktu_antrean) > '$current_datetime'
    
    UNION ALL
    
    SELECT 
        'online' as tipe,
        po.id_pemesanan as id,
        po.tanggal_konsultasi as tanggal,
        po.waktu_konsultasi as waktu,
        NULL as nomor_antrean,
        po.keluhan,
        po.status_pemesanan as status,
        po.created_at,
        d.nama_dokter,
        d.bidang_khusus,
        d.kota,
        p.nama_pet,
        p.jenis_hewan,
        po.biaya_konsultasi,
        po.kupon_digunakan,
        po.jumlah_diskon,
        po.total_biaya,
        d.id_dokter,
        d.no_hp as dokter_hp,
        pem.nama_pemilik,
        po.kode_pemesanan
    FROM pemesanan_online po 
    JOIN dokter d ON po.id_dokter = d.id_dokter 
    JOIN pets p ON po.id_pet = p.id_pet 
    JOIN pemilik pem ON po.id_pemilik = pem.id_pemilik
    WHERE po.id_pemilik = $id_pemilik 
    AND po.status_pemesanan IN $online_status
    AND CONCAT(po.tanggal_konsultasi, ' ', po.waktu_konsultasi) > '$current_datetime'
    
    ORDER BY tanggal ASC, waktu ASC";
    
    $result = mysqli_query($koneksi, $query);
    $bookings = [];
    while ($booking = mysqli_fetch_assoc($result)) {
        $bookings[] = $booking;
    }
    
    return $bookings;
}

/**
 * Fungsi untuk filter riwayat booking
 */
function filterHistoryBookings($id_pemilik, $filters = []) {
    global $koneksi;
    
    // Filter berdasarkan tipe (OR logic)
    $tipe_condition = "";
    if (isset($filters['tipe']) && $filters['tipe'] !== 'all') {
        $tipe = mysqli_real_escape_string($koneksi, $filters['tipe']);
        if ($tipe === 'offline') {
            $tipe_condition = "('offline' as tipe";
        } elseif ($tipe === 'online') {
            $tipe_condition = "('online' as tipe";
        }
    } else {
        $tipe_condition = "('offline' as tipe, 'online' as tipe)";
    }
    
    // Filter status (OR logic)
    $status_condition_offline = "";
    $status_condition_online = "";
    if (isset($filters['status']) && $filters['status'] !== 'all') {
        $status = mysqli_real_escape_string($koneksi, $filters['status']);
        
        // Mapping status untuk offline dan online
        $status_mapping = [
            'menunggu' => ['menunggu', 'pending'],
            'diproses' => ['diproses', 'confirmed'],
            'selesai' => ['selesai', 'completed'],
            'batal' => ['batal', 'cancelled']
        ];
        
        if (isset($status_mapping[$status])) {
            $status_list = $status_mapping[$status];
            $status_str = "'" . implode("','", $status_list) . "'";
            $status_condition_offline = "AND po.status_antrean IN ($status_str)";
            $status_condition_online = "AND po.status_pemesanan IN ($status_str)";
        }
    }
    
    $query = "SELECT 
        'offline' as tipe,
        po.id_antrean as id,
        po.tanggal_antrean as tanggal,
        po.waktu_antrean as waktu,
        po.nomor_antrean,
        po.keluhan,
        po.status_antrean as status,
        po.created_at,
        d.nama_dokter,
        d.bidang_khusus,
        d.kota,
        p.nama_pet,
        p.jenis_hewan,
        NULL as biaya_konsultasi,
        NULL as kupon_digunakan,
        NULL as jumlah_diskon,
        NULL as total_biaya,
        d.id_dokter,
        d.no_hp as dokter_hp,
        pem.nama_pemilik,
        NULL as kode_pemesanan
    FROM pemesanan_offline po 
    JOIN dokter d ON po.id_dokter = d.id_dokter 
    JOIN pets p ON po.id_pet = p.id_pet 
    JOIN pemilik pem ON po.id_pemilik = pem.id_pemilik
    WHERE po.id_pemilik = $id_pemilik 
    $status_condition_offline
    
    UNION ALL
    
    SELECT 
        'online' as tipe,
        po.id_pemesanan as id,
        po.tanggal_konsultasi as tanggal,
        po.waktu_konsultasi as waktu,
        NULL as nomor_antrean,
        po.keluhan,
        po.status_pemesanan as status,
        po.created_at,
        d.nama_dokter,
        d.bidang_khusus,
        d.kota,
        p.nama_pet,
        p.jenis_hewan,
        po.biaya_konsultasi,
        po.kupon_digunakan,
        po.jumlah_diskon,
        po.total_biaya,
        d.id_dokter,
        d.no_hp as dokter_hp,
        pem.nama_pemilik,
        po.kode_pemesanan
    FROM pemesanan_online po 
    JOIN dokter d ON po.id_dokter = d.id_dokter 
    JOIN pets p ON po.id_pet = p.id_pet 
    JOIN pemilik pem ON po.id_pemilik = pem.id_pemilik
    WHERE po.id_pemilik = $id_pemilik 
    $status_condition_online
    
    ORDER BY created_at DESC";
    
    $result = mysqli_query($koneksi, $query);
    $bookings = [];
    while ($booking = mysqli_fetch_assoc($result)) {
        $bookings[] = $booking;
    }
    
    return $bookings;
}

/**
 * Fungsi untuk filter dokter dengan antrean (untuk B_jadwal_keKlinik.php)
 * Menggunakan OR logic untuk spesialisasi dan hewan
 */
function filterDoctorsWithQueue($date, $filters = []) {
    global $koneksi;
    
    $where_conditions = [];
    
    // Filter OR logic untuk spesialisasi ATAU hewan
    if (!empty($filters['specialization']) && $filters['specialization'] !== 'all' &&
        !empty($filters['animal']) && $filters['animal'] !== 'all') {
        
        $specialization = mysqli_real_escape_string($koneksi, $filters['specialization']);
        $animal = mysqli_real_escape_string($koneksi, $filters['animal']);
        
        // OR Condition: spesialisasi SESUAI ATAU hewan SESUAI
        $where_conditions[] = "(d.bidang_khusus = '$specialization' OR 
                               d.spesies_hewan LIKE '%$animal%')";
        
    } elseif (!empty($filters['specialization']) && $filters['specialization'] !== 'all') {
        $specialization = mysqli_real_escape_string($koneksi, $filters['specialization']);
        $where_conditions[] = "d.bidang_khusus = '$specialization'";
        
    } elseif (!empty($filters['animal']) && $filters['animal'] !== 'all') {
        $animal = mysqli_real_escape_string($koneksi, $filters['animal']);
        $where_conditions[] = "d.spesies_hewan LIKE '%$animal%'";
    }
    
    $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" OR ", $where_conditions);
    
    $query = "SELECT d.*, 
                     (SELECT COUNT(*) 
                      FROM pemesanan_offline 
                      WHERE id_dokter = d.id_dokter 
                        AND tanggal_antrean = '$date' 
                        AND status_antrean IN ('menunggu', 'diproses')) as total_antrean
              FROM dokter d
              $where_clause
              ORDER BY d.nama_dokter ASC";
    
    $result = mysqli_query($koneksi, $query);
    $doctors = [];
    while ($doctor = mysqli_fetch_assoc($result)) {
        $doctors[] = $doctor;
    }
    
    return $doctors;
}

/**
 * Fungsi untuk filter dokter (untuk I_temui_online.php)
 * Menggunakan OR logic untuk spesialisasi dan hewan
 */
function filterDoctors($filters = []) {
    global $koneksi;
    
    $where_conditions = [];
    
    // Filter OR logic untuk spesialisasi ATAU hewan
    if (!empty($filters['specialization']) && $filters['specialization'] !== 'all' &&
        !empty($filters['animal']) && $filters['animal'] !== 'all') {
        
        $specialization = mysqli_real_escape_string($koneksi, $filters['specialization']);
        $animal = mysqli_real_escape_string($koneksi, $filters['animal']);
        
        // OR Condition: spesialisasi SESUAI ATAU hewan SESUAI
        $where_conditions[] = "(d.bidang_khusus = '$specialization' OR 
                               d.spesies_hewan LIKE '%$animal%')";
        
    } elseif (!empty($filters['specialization']) && $filters['specialization'] !== 'all') {
        $specialization = mysqli_real_escape_string($koneksi, $filters['specialization']);
        $where_conditions[] = "d.bidang_khusus = '$specialization'";
        
    } elseif (!empty($filters['animal']) && $filters['animal'] !== 'all') {
        $animal = mysqli_real_escape_string($koneksi, $filters['animal']);
        $where_conditions[] = "d.spesies_hewan LIKE '%$animal%'";
    }
    
    $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" OR ", $where_conditions);
    
    $query = "SELECT * FROM dokter d $where_clause ORDER BY d.nama_dokter ASC";
    $result = mysqli_query($koneksi, $query);
    $doctors = [];
    while ($doctor = mysqli_fetch_assoc($result)) {
        $doctors[] = $doctor;
    }
    
    return $doctors;
}

/**
 * Fungsi untuk mendapatkan semua spesialisasi unik
 */
function getAllSpecialties() {
    global $koneksi;
    $query = "SELECT DISTINCT bidang_khusus FROM dokter WHERE bidang_khusus IS NOT NULL AND bidang_khusus != '' ORDER BY bidang_khusus";
    $result = mysqli_query($koneksi, $query);
    $specialties = [];
    while ($specialty = mysqli_fetch_assoc($result)) {
        $specialties[] = $specialty['bidang_khusus'];
    }
    return $specialties;
}

/**
 * Fungsi untuk mendapatkan semua spesies hewan unik (OR logic)
 */
function getAllAnimalSpecies() {
    global $koneksi;
    $query = "SELECT DISTINCT spesies_hewan FROM dokter WHERE spesies_hewan IS NOT NULL AND spesies_hewan != ''";
    $result = mysqli_query($koneksi, $query);
    $animal_species_all = [];

    while ($species_row = mysqli_fetch_assoc($result)) {
        if (!empty($species_row['spesies_hewan'])) {
            $species_array = explode(',', $species_row['spesies_hewan']);
            foreach ($species_array as $species) {
                $trimmed_species = trim($species);
                if (!empty($trimmed_species) && !in_array($trimmed_species, $animal_species_all)) {
                    $animal_species_all[] = $trimmed_species;
                }
            }
        }
    }

    sort($animal_species_all);
    return $animal_species_all;
}

/**
 * Fungsi untuk mendapatkan hewan peliharaan user
 */
function getUserPets($user_id) {
    global $koneksi;
    $query = "SELECT * FROM pets WHERE id_pemilik = $user_id";
    $result = mysqli_query($koneksi, $query);
    $pets = [];
    while ($pet = mysqli_fetch_assoc($result)) {
        $pets[] = $pet;
    }
    return $pets;
}

/**
 * Fungsi untuk mendapatkan jenis hewan user (untuk warning)
 */
function getUserAnimalTypes($user_id) {
    global $koneksi;
    $query = "SELECT jenis_hewan FROM pets WHERE id_pemilik = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $user_animal_types = [];
    while ($pet = $result->fetch_assoc()) {
        $user_animal_types[] = strtolower($pet['jenis_hewan']);
    }
    
    return $user_animal_types;
}

function getDoctors() {
    global $koneksi;
    $query = "SELECT * FROM dokter";
    $result = mysqli_query($koneksi, $query);
    $doctors = [];
    while ($doctor = mysqli_fetch_assoc($result)) {
        $doctors[] = $doctor;
    }
    return $doctors;
}

function getDoctorsWithQueue($date) {
    global $koneksi;
    $query = "SELECT d.*, 
                     (SELECT COUNT(*) 
                      FROM pemesanan_offline 
                      WHERE id_dokter = d.id_dokter 
                        AND tanggal_antrean = '$date' 
                        AND status_antrean IN ('menunggu', 'diproses')) as total_antrean
              FROM dokter d";
    $result = mysqli_query($koneksi, $query);
    $doctors = [];
    while ($doctor = mysqli_fetch_assoc($result)) {
        $doctors[] = $doctor;
    }
    return $doctors;
}


function getUserAnimals($user_id) {
    global $koneksi;
    $query = "SELECT jenis_hewan FROM pets WHERE id_pemilik = $user_id";
    $result = mysqli_query($koneksi, $query);
    $animals = [];
    while ($animal = mysqli_fetch_assoc($result)) {
        $animals[] = strtolower($animal['jenis_hewan']);
    }
    return $animals;
}

function getSpecialties() {
    global $koneksi;
    $query = "SELECT DISTINCT bidang_khusus FROM dokter WHERE bidang_khusus IS NOT NULL AND bidang_khusus != ''";
    $result = mysqli_query($koneksi, $query);
    $specialties = [];
    while ($specialty = mysqli_fetch_assoc($result)) {
        $specialties[] = $specialty['bidang_khusus'];
    }
    return $specialties;
}

function filterHistoryBookingsWithParams($id_pemilik, $filters = []) {
    global $koneksi;
    
    $conditions_offline = ["po.id_pemilik = $id_pemilik"];
    $conditions_online = ["po.id_pemilik = $id_pemilik"];
    
    // Filter berdasarkan tipe (OR logic)
    if (isset($filters['tipe']) && $filters['tipe'] !== 'all') {
        $tipe = mysqli_real_escape_string($koneksi, $filters['tipe']);
        // Jika filter tipe dipilih, kita hanya akan mengambil satu tipe saja
        if ($tipe === 'offline') {
            // Hanya ambil offline
            $query = "SELECT 
                'offline' as tipe,
                po.id_antrean as id,
                po.tanggal_antrean as tanggal,
                po.waktu_antrean as waktu,
                po.nomor_antrean,
                po.keluhan,
                po.status_antrean as status,
                po.created_at,
                d.nama_dokter,
                d.bidang_khusus,
                d.kota,
                p.nama_pet,
                p.jenis_hewan,
                NULL as biaya_konsultasi,
                NULL as kupon_digunakan,
                NULL as jumlah_diskon,
                NULL as total_biaya,
                d.id_dokter,
                d.no_hp as dokter_hp,
                pem.nama_pemilik,
                NULL as kode_pemesanan
            FROM pemesanan_offline po 
            JOIN dokter d ON po.id_dokter = d.id_dokter 
            JOIN pets p ON po.id_pet = p.id_pet 
            JOIN pemilik pem ON po.id_pemilik = pem.id_pemilik
            WHERE po.id_pemilik = $id_pemilik";
            
            // Filter status untuk offline
            if (isset($filters['status']) && $filters['status'] !== 'all') {
                $status = mysqli_real_escape_string($koneksi, $filters['status']);
                $status_mapping = [
                    'menunggu' => 'menunggu',
                    'diproses' => 'diproses',
                    'selesai' => 'selesai',
                    'batal' => 'batal',
                    'pending' => 'menunggu', // pending dianggap menunggu untuk offline
                    'confirmed' => 'diproses', // confirmed dianggap diproses untuk offline
                    'completed' => 'selesai', // completed dianggap selesai untuk offline
                    'cancelled' => 'batal' // cancelled dianggap batal untuk offline
                ];
                
                if (isset($status_mapping[$status])) {
                    $status_value = $status_mapping[$status];
                    $query .= " AND po.status_antrean = '$status_value'";
                }
            }
            
            $query .= " ORDER BY po.created_at DESC";
            
            $result = mysqli_query($koneksi, $query);
            $bookings = [];
            while ($booking = mysqli_fetch_assoc($result)) {
                $bookings[] = $booking;
            }
            
            return $bookings;
        } elseif ($tipe === 'online') {
            // Hanya ambil online
            $query = "SELECT 
                'online' as tipe,
                po.id_pemesanan as id,
                po.tanggal_konsultasi as tanggal,
                po.waktu_konsultasi as waktu,
                NULL as nomor_antrean,
                po.keluhan,
                po.status_pemesanan as status,
                po.created_at,
                d.nama_dokter,
                d.bidang_khusus,
                d.kota,
                p.nama_pet,
                p.jenis_hewan,
                po.biaya_konsultasi,
                po.kupon_digunakan,
                po.jumlah_diskon,
                po.total_biaya,
                d.id_dokter,
                d.no_hp as dokter_hp,
                pem.nama_pemilik,
                po.kode_pemesanan
            FROM pemesanan_online po 
            JOIN dokter d ON po.id_dokter = d.id_dokter 
            JOIN pets p ON po.id_pet = p.id_pet 
            JOIN pemilik pem ON po.id_pemilik = pem.id_pemilik
            WHERE po.id_pemilik = $id_pemilik";
            
            // Filter status untuk online
            if (isset($filters['status']) && $filters['status'] !== 'all') {
                $status = mysqli_real_escape_string($koneksi, $filters['status']);
                $status_mapping = [
                    'menunggu' => 'pending',
                    'diproses' => 'confirmed',
                    'selesai' => 'completed',
                    'batal' => 'cancelled',
                    'pending' => 'pending',
                    'confirmed' => 'confirmed',
                    'completed' => 'completed',
                    'cancelled' => 'cancelled'
                ];
                
                if (isset($status_mapping[$status])) {
                    $status_value = $status_mapping[$status];
                    $query .= " AND po.status_pemesanan = '$status_value'";
                }
            }
            
            $query .= " ORDER BY po.created_at DESC";
            
            $result = mysqli_query($koneksi, $query);
            $bookings = [];
            while ($booking = mysqli_fetch_assoc($result)) {
                $bookings[] = $booking;
            }
            
            return $bookings;
        }
    }
    
    // Jika tidak ada filter tipe atau tipe = 'all', ambil semua
    return filterHistoryBookings($id_pemilik, $filters);
}
?>