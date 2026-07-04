<?php
session_start();
require_once __DIR__ . '/logic/auth.php';

// Koneksi database HARUS DIPANGGIL DI SINI
require_once __DIR__ . '/logic/koneksi.php';

// Handle logout request
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    // Hapus semua data session
    $_SESSION = array();

    // Hapus cookie session jika ada
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Hancurkan session
    session_destroy();

    // Redirect ke dashboard
    header("Location: B_dashboard.php");
    exit();
}

// ========== HANDLER HAPUS AKUN DENGAN VERIFIKASI PASSWORD ==========
if (isset($_POST['hapus_akun']) && $_POST['hapus_akun'] == 'true' && isset($_POST['password'])) {
    if (!sudahLogin()) {
        header('Location: B_login.php');
        exit();
    }

    $id_pemilik = $_SESSION['user_id'];
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // 1. Ambil data user untuk verifikasi password
    $query_verify = "SELECT * FROM pemilik WHERE id_pemilik = $id_pemilik";
    $result_verify = mysqli_query($koneksi, $query_verify);

    if (!$result_verify) {
        $_SESSION['error_message'] = "Terjadi kesalahan: " . mysqli_error($koneksi);
        header("Location: B_profil.php");
        exit();
    }

    $user_data = mysqli_fetch_assoc($result_verify);

    if (!$user_data) {
        $_SESSION['error_message'] = "User tidak ditemukan";
        header("Location: B_profil.php");
        exit();
    }

    // Verifikasi password
    if (password_verify($password, $user_data['password'])) {
        // Mulai transaction
        mysqli_begin_transaction($koneksi);

        try {
            // 3. Hapus semua hewan peliharaan user terlebih dahulu
            $query_hapus_pets = "DELETE FROM pets WHERE id_pemilik = $id_pemilik";
            if (!mysqli_query($koneksi, $query_hapus_pets)) {
                throw new Exception("Gagal menghapus data hewan: " . mysqli_error($koneksi));
            }

            // 4. Hapus user dari database
            $query_hapus_user = "DELETE FROM pemilik WHERE id_pemilik = $id_pemilik";
            $result = mysqli_query($koneksi, $query_hapus_user);

            if ($result && mysqli_affected_rows($koneksi) > 0) {
                // Commit transaction
                mysqli_commit($koneksi);

                // Hapus session
                $_SESSION = array();
                if (isset($_COOKIE[session_name()])) {
                    setcookie(session_name(), '', time() - 3600, '/');
                }
                session_destroy();

                // Redirect ke halaman login dengan pesan
                $_SESSION['info_message'] = "Akun Anda telah berhasil dihapus. Terima kasih telah menggunakan layanan kami.";
                header("Location: B_login.php");
                exit();
            } else {
                throw new Exception("Gagal menghapus akun user: " . mysqli_error($koneksi));
            }
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $_SESSION['error_message'] = "Terjadi kesalahan: " . $e->getMessage();
            header("Location: B_profil.php");
            exit();
        }
    } else {
        // Password salah
        $_SESSION['error_message'] = "Password salah. Akun tidak dihapus.";
        header("Location: B_profil.php");
        exit();
    }
}

if (!sudahLogin()) {
    header('Location: B_login.php');
    exit;
}

$user = getUser();

// Ambil data hewan dari database
$id_pemilik = $_SESSION['user_id'];
$query_pets = "SELECT * FROM pets WHERE id_pemilik = $id_pemilik";
$result_pets = mysqli_query($koneksi, $query_pets);
$pets = [];
while ($pet = mysqli_fetch_assoc($result_pets)) {
    $pets[] = $pet;
}

// Ambil data hewan untuk edit
$pet_to_edit = null;
if (isset($_GET['edit_pet']) && is_numeric($_GET['edit_pet'])) {
    $pet_id = mysqli_real_escape_string($koneksi, $_GET['edit_pet']);
    $query_edit = "SELECT * FROM pets WHERE id_pet = $pet_id AND id_pemilik = $id_pemilik";
    $result_edit = mysqli_query($koneksi, $query_edit);
    $pet_to_edit = mysqli_fetch_assoc($result_edit);
}

// Proses form update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    if ($_POST['aksi'] === 'update_profile') {
        $nama_pemilik = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
        $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

        // Validasi nomor HP
        $clean_no_hp = preg_replace('/[^\d+]/', '', $no_hp);
        $angka_setelah62 = substr($clean_no_hp, 3);

        if (strlen($angka_setelah62) < 9 || strlen($angka_setelah62) > 12) {
            $_SESSION['error_message'] = "Nomor HP harus 9-12 digit setelah +62";
            header("Location: B_profil.php");
            exit();
        }

        if (!preg_match('/^\d+$/', $angka_setelah62)) {
            $_SESSION['error_message'] = "Nomor HP hanya boleh mengandung angka setelah +62";
            header("Location: B_profil.php");
            exit();
        }

        // CEK APAKAH ADA PERUBAHAN DATA
        if ($nama_pemilik === $user['nama_pemilik'] && $clean_no_hp === $user['no_hp']) {
            // Tidak ada perubahan, tidak perlu update
            $_SESSION['info_message'] = "Tidak ada perubahan data profil";
        } else {
            $query = "UPDATE pemilik SET nama_pemilik = '$nama_pemilik', no_hp = '$clean_no_hp' WHERE id_pemilik = $id_pemilik";
            if (mysqli_query($koneksi, $query)) {
                $_SESSION['success_message'] = "Profil berhasil diperbarui";
                $user = getUser(); // Refresh data user
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui profil: " . mysqli_error($koneksi);
            }
        }
    }

    // Proses update alamat
    if ($_POST['aksi'] === 'update_alamat') {
        $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
        $kota = mysqli_real_escape_string($koneksi, $_POST['kota']);
        $kode_pos = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);

        // Validasi kode pos
        if ($kode_pos && !preg_match('/^\d{5}$/', $kode_pos)) {
            $_SESSION['error_message'] = "Kode Pos harus 5 digit angka";
            header("Location: B_profil.php");
            exit();
        }

        // Gunakan nilai kosong jika tidak ada di database
        $current_alamat = $user['alamat'] ?? '';
        $current_kota = $user['kota'] ?? '';
        $current_kode_pos = $user['kode_pos'] ?? '';

        // CEK APAKAH ADA PERUBAHAN DATA
        if ($alamat === $current_alamat && $kota === $current_kota && $kode_pos === $current_kode_pos) {
            // Tidak ada perubahan, tidak perlu update
            $_SESSION['info_message'] = "Tidak ada perubahan data alamat";
        } else {
            $query = "UPDATE pemilik SET alamat = '$alamat', kota = '$kota', kode_pos = '$kode_pos' WHERE id_pemilik = $id_pemilik";
            if (mysqli_query($koneksi, $query)) {
                $_SESSION['success_message'] = "Alamat berhasil diperbarui";
                $user = getUser(); // Refresh data user
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui alamat: " . mysqli_error($koneksi);
            }
        }
    }

    // Proses tambah hewan
    if ($_POST['aksi'] === 'tambah_hewan') {
        $nama_pet = mysqli_real_escape_string($koneksi, $_POST['nama_pet']);
        $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
        $jenis_hewan = mysqli_real_escape_string($koneksi, $_POST['jenis_hewan']);
        $ras = mysqli_real_escape_string($koneksi, $_POST['ras'] ?? '');
        $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir'] ?? '');
        $usia = mysqli_real_escape_string($koneksi, $_POST['usia'] ?? '');
        $berat = mysqli_real_escape_string($koneksi, $_POST['berat'] ?? '');
        $sterilisasi = mysqli_real_escape_string($koneksi, $_POST['sterilisasi']);

        $query = "INSERT INTO pets (id_pemilik, nama_pet, jenis_kelamin, jenis_hewan, ras, tanggal_lahir, usia, berat, sterilisasi) 
VALUES ($id_pemilik, '$nama_pet', '$jenis_kelamin', '$jenis_hewan', '$ras', '$tanggal_lahir', '$usia', '$berat', '$sterilisasi')";

        if (mysqli_query($koneksi, $query)) {
            $_SESSION['success_message'] = "Hewan berhasil ditambahkan";
            header("Location: B_profil.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan hewan: " . mysqli_error($koneksi);
        }
    }

    // Proses edit hewan
    if ($_POST['aksi'] === 'edit_hewan') {
        $id_pet = mysqli_real_escape_string($koneksi, $_POST['id_pet']);
        $nama_pet = mysqli_real_escape_string($koneksi, $_POST['nama_pet']);
        $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
        $jenis_hewan = mysqli_real_escape_string($koneksi, $_POST['jenis_hewan']);
        $ras = mysqli_real_escape_string($koneksi, $_POST['ras'] ?? '');
        $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir'] ?? '');
        $usia = mysqli_real_escape_string($koneksi, $_POST['usia'] ?? '');
        $berat = mysqli_real_escape_string($koneksi, $_POST['berat'] ?? '');
        $sterilisasi = mysqli_real_escape_string($koneksi, $_POST['sterilisasi']);

        $query = "UPDATE pets SET 
nama_pet = '$nama_pet', 
jenis_kelamin = '$jenis_kelamin', 
jenis_hewan = '$jenis_hewan', 
ras = '$ras', 
tanggal_lahir = '$tanggal_lahir', 
usia = '$usia', 
berat = '$berat', 
sterilisasi = '$sterilisasi' 
WHERE id_pet = $id_pet AND id_pemilik = $id_pemilik";

        if (mysqli_query($koneksi, $query)) {
            $_SESSION['success_message'] = "Hewan berhasil diperbarui";
            header("Location: B_profil.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui hewan: " . mysqli_error($koneksi);
        }
    }

    // Proses hapus hewan
    if ($_POST['aksi'] === 'hapus_hewan') {
        $id_pet = mysqli_real_escape_string($koneksi, $_POST['id_pet']);

        $query = "DELETE FROM pets WHERE id_pet = $id_pet AND id_pemilik = $id_pemilik";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['success_message'] = "Hewan berhasil dihapus";
            header("Location: B_profil.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Gagal menghapus hewan: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - AB Paw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="ABclinic.jpg">
</head>

<body class="profil-page">
    <div class="container">
        <!-- Pindahkan container ke sini -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <a href="B_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>

            <div style="display: flex; gap: 10px;">
                <!-- TOMBOL HAPUS AKUN -->
                <button id="btnHapusAkun" class="btn-danger"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 8px; border: none;">
                    <i class="fas fa-user-times"></i> Hapus Akun
                </button>

                <!-- TOMBOL LOGOUT -->
                <a href="?logout=true"
                    onclick="return confirm('Apakah Anda yakin ingin logout?')"
                    class="btn-danger"
                    style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 12px 24px; border-radius: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
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

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>


        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-info">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <h1><?php echo htmlspecialchars($user['nama_pemilik']); ?></h1>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="openTab('profile')">
                <i class="fas fa-user"></i> Profil Saya
            </button>
            <button class="tab" onclick="openTab('pets')">
                <i class="fas fa-paw"></i> Hewan Peliharaan
            </button>
        </div>

        <!-- Tab Content - Profile -->
        <div id="profile" class="tab-content active">
            <!-- Form update profil -->
            <div class="form-section">
                <h2><i class="fas fa-user-circle"></i> Informasi Pribadi</h2>
                <form method="POST" action="">
                    <input type="hidden" name="aksi" value="update_profile">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_pemilik" value="<?php echo htmlspecialchars($user['nama_pemilik']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background: #f8f9fa;">
                    </div>
                    <div class="form-group">
                        <label>Nomor HP</label>
                        <input type="text"
                            name="no_hp"
                            id="no_hp"
                            value="<?php echo htmlspecialchars($user['no_hp'] ?? ''); ?>"
                            placeholder="Contoh: +628123456789"
                            maxlength="15"
                            required
                            oninput="formatNomorHP(this)">
                        <small id="no_hp_counter" style="font-size: 12px; color: #666; display: block; margin-top: 4px;">
                            * Format: +628123456789
                        </small>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Form alamat -->
            <div class="form-section">
                <h2><i class="fas fa-map-marker-alt"></i> Alamat</h2>
                <form method="POST" action="">
                    <input type="hidden" name="aksi" value="update_alamat">
                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" rows="3"><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Kota</label>
                            <input type="text" name="kota" value="<?php echo htmlspecialchars($user['kota'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Kode Pos</label>
                            <input type="text"
                                name="kode_pos"
                                id="kode_pos"
                                value="<?php echo htmlspecialchars($user['kode_pos'] ?? ''); ?>"
                                maxlength="5"
                                placeholder="Contoh: 12345"
                                oninput="formatKodePos(this)">
                            <small id="kode_pos_counter" style="font-size: 12px; color: #666; display: block; margin-top: 4px;">
                                * 5 digit angka
                            </small>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan Alamat
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab Content - Pets -->
        <div id="pets" class="tab-content">
            <div class="section-header">
                <h2><i class="fas fa-paw"></i> Hewan Peliharaan Saya</h2>
                <button class="btn-primary" onclick="togglePetForm()">
                    <i class="fas fa-plus"></i> Tambah Hewan
                </button>
            </div>

            <!-- Form Tambah Hewan -->
            <div id="addPetForm" class="add-pet-form" style="display: none;">
                <h3 style="margin-bottom: 20px; color: #e83e8c; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-plus-circle"></i> Tambah Hewan Baru
                </h3>
                <form id="newPetForm" method="POST" action="">
                    <input type="hidden" name="aksi" value="tambah_hewan">
                    <div class="form-group">
                        <input type="text" name="nama_pet" placeholder="Nama Hewan Peliharaan" required>
                    </div>
                    <div class="gender-selector">
                        <div class="gender-btn" data-value="jantan">Jantan</div>
                        <div class="gender-btn" data-value="betina">Betina</div>
                        <div class="gender-btn" data-value="tidak_diketahui">Tidak Diketahui</div>
                    </div>
                    <input type="hidden" name="jenis_kelamin" id="jenis_kelamin" required>
                    <div class="form-group">
                        <select name="jenis_hewan" required>
                            <option value="" disabled selected>Jenis Hewan</option>
                            <option value="sapi">Sapi</option>
                            <option value="kambing">Kambing</option>
                            <option value="kerbau">Kerbau</option>
                            <option value="ayam">Ayam</option>
                            <option value="kucing">Kucing</option>
                            <option value="kelinci">Kelinci</option>
                            <option value="anjing">Anjing</option>
                            <option value="hamster">Hamster</option>
                            <option value="burung">Burung</option>
                            <option value="ikan">Ikan</option>
                            <option value="musang">Musang</option>
                            <option value="kura-kura">Kura-kura</option>
                            <option value="landak">Landak</option>
                            <option value="babi">Babi</option>
                            <option value="kuda">Kuda</option>
                            <option value="domba">Domba</option>
                            <option value="lain-lain">Lain-lain</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="ras" placeholder="Ras (Opsional)">
                    </div>
                    <div class="input-row">
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir">
                        </div>
                        <div class="form-group">
                            <label>Usia (tahun)</label>
                            <input type="number" name="usia" placeholder="Usia" min="0" max="50">
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="number" name="berat" placeholder="Berat (kg)" step="0.1" min="0" max="1000">
                    </div>
                    <div class="form-group">
                        <select name="sterilisasi" required>
                            <option value="" disabled selected>Status Sterilisasi</option>
                            <option value="belum">Belum Sterilisasi</option>
                            <option value="sudah">Sudah Sterilisasi</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Hewan
                        </button>
                        <button type="button" class="btn-secondary" onclick="togglePetForm()">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Daftar Hewan -->
            <div class="pets-grid" id="petsList">
                <?php if (empty($pets)): ?>
                    <div class="empty-state">
                        <i class="fas fa-paw"></i>
                        <h3>Belum ada hewan peliharaan</h3>
                        <p>Tambahkan hewan peliharaan pertama Anda</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pets as $pet): ?>
                        <div class="pet-card">
                            <div class="pet-header">
                                <div class="pet-name"><?php echo htmlspecialchars($pet['nama_pet']); ?></div>
                                <div class="pet-type"><?php echo ucfirst($pet['jenis_hewan']); ?></div>
                            </div>
                            <div class="pet-details">
                                <p><i class="fas fa-venus-mars"></i> <strong>Jenis Kelamin:</strong> <?php echo ucfirst($pet['jenis_kelamin']); ?></p>
                                <p><i class="fas fa-dna"></i> <strong>Ras:</strong> <?php echo $pet['ras'] ? htmlspecialchars($pet['ras']) : '-'; ?></p>
                                <p><i class="fas fa-birthday-cake"></i> <strong>Usia:</strong> <?php echo $pet['usia'] ? $pet['usia'] . ' tahun' : '-'; ?></p>
                                <p><i class="fas fa-weight"></i> <strong>Berat:</strong> <?php echo $pet['berat'] ? $pet['berat'] . ' kg' : '-'; ?></p>
                                <p><i class="fas fa-stethoscope"></i> <strong>Sterilisasi:</strong> <?php echo ucfirst($pet['sterilisasi']); ?></p>
                                <?php if ($pet['tanggal_lahir'] && $pet['tanggal_lahir'] != '0000-00-00'): ?>
                                    <p><i class="fas fa-calendar-alt"></i> <strong>Tanggal Lahir:</strong> <?php echo date('d/m/Y', strtotime($pet['tanggal_lahir'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="pet-actions">
                                <button class="btn-primary" onclick="openEditModal(<?php echo $pet['id_pet']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-danger" onclick="confirmDelete(<?php echo $pet['id_pet']; ?>, '<?php echo htmlspecialchars($pet['nama_pet']); ?>')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Edit Hewan -->
    <?php if (isset($_GET['edit_pet']) && $pet_to_edit): ?>
        <div id="editPetModal" class="modal" style="display: block;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-edit"></i> Edit Hewan Peliharaan</h3>
                    <button class="close-modal" onclick="closeEditModal()">&times;</button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="aksi" value="edit_hewan">
                    <input type="hidden" name="id_pet" id="edit_id_pet" value="<?php echo $pet_to_edit['id_pet'] ?? ''; ?>">

                    <div class="form-group">
                        <label>Nama Hewan</label>
                        <input type="text" name="nama_pet" id="edit_nama_pet" value="<?php echo htmlspecialchars($pet_to_edit['nama_pet'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <div class="gender-selector" id="edit_gender_selector">
                            <div class="gender-btn <?php echo ($pet_to_edit['jenis_kelamin'] ?? '') == 'jantan' ? 'selected' : ''; ?>" data-value="jantan">Jantan</div>
                            <div class="gender-btn <?php echo ($pet_to_edit['jenis_kelamin'] ?? '') == 'betina' ? 'selected' : ''; ?>" data-value="betina">Betina</div>
                            <div class="gender-btn <?php echo ($pet_to_edit['jenis_kelamin'] ?? '') == 'tidak_diketahui' ? 'selected' : ''; ?>" data-value="tidak_diketahui">Tidak Diketahui</div>
                        </div>
                        <input type="hidden" name="jenis_kelamin" id="edit_jenis_kelamin" value="<?php echo $pet_to_edit['jenis_kelamin'] ?? 'jantan'; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Jenis Hewan</label>
                        <select name="jenis_hewan" id="edit_jenis_hewan" required>
                            <option value="" disabled>Pilih Jenis Hewan</option>
                            <option value="sapi" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'sapi' ? 'selected' : ''; ?>>Sapi</option>
                            <option value="kambing" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'kambing' ? 'selected' : ''; ?>>Kambing</option>
                            <option value="kerbau" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'kerbau' ? 'selected' : ''; ?>>Kerbau</option>
                            <option value="ayam" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'ayam' ? 'selected' : ''; ?>>Ayam</option>
                            <option value="kucing" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'kucing' ? 'selected' : ''; ?>>Kucing</option>
                            <option value="kelinci" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'kelinci' ? 'selected' : ''; ?>>Kelinci</option>
                            <option value="anjing" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'anjing' ? 'selected' : ''; ?>>Anjing</option>
                            <option value="hamster" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'hamster' ? 'selected' : ''; ?>>Hamster</option>
                            <option value="burung" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'burung' ? 'selected' : ''; ?>>Burung</option>
                            <option value="ikan" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'ikan' ? 'selected' : ''; ?>>Ikan</option>
                            <option value="musang" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'musang' ? 'selected' : ''; ?>>Musang</option>
                            <option value="kura-kura" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'kura-kura' ? 'selected' : ''; ?>>Kura-kura</option>
                            <option value="landak" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'landak' ? 'selected' : ''; ?>>Landak</option>
                            <option value="babi" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'babi' ? 'selected' : ''; ?>>Babi</option>
                            <option value="kuda" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'kuda' ? 'selected' : ''; ?>>Kuda</option>
                            <option value="domba" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'domba' ? 'selected' : ''; ?>>Domba</option>
                            <option value="lain-lain" <?php echo ($pet_to_edit['jenis_hewan'] ?? '') == 'lain-lain' ? 'selected' : ''; ?>>Lain-lain</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ras (Opsional)</label>
                        <input type="text" name="ras" id="edit_ras" value="<?php echo htmlspecialchars($pet_to_edit['ras'] ?? ''); ?>" placeholder="Ras hewan">
                    </div>

                    <div class="input-row">
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" value="<?php echo $pet_to_edit['tanggal_lahir'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Usia (tahun)</label>
                            <input type="number" name="usia" id="edit_usia" value="<?php echo $pet_to_edit['usia'] ?? ''; ?>" min="0" max="50" placeholder="Usia">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Berat (kg)</label>
                        <input type="number" name="berat" id="edit_berat" value="<?php echo $pet_to_edit['berat'] ?? ''; ?>" step="0.1" min="0" max="1000" placeholder="Berat">
                    </div>

                    <div class="form-group">
                        <label>Status Sterilisasi</label>
                        <select name="sterilisasi" id="edit_sterilisasi" required>
                            <option value="" disabled>Pilih Status</option>
                            <option value="belum" <?php echo ($pet_to_edit['sterilisasi'] ?? '') == 'belum' ? 'selected' : ''; ?>>Belum Sterilisasi</option>
                            <option value="sudah" <?php echo ($pet_to_edit['sterilisasi'] ?? '') == 'sudah' ? 'selected' : ''; ?>>Sudah Sterilisasi</option>
                        </select>
                    </div>

                    <div class="modal-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <button type="button" class="btn-secondary" onclick="closeEditModal()">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // ====== FUNGSI UTAMA ======
        function openTab(tabName) {
            // Sembunyikan semua tab
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Hapus active class dari semua tab
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Tampilkan tab yang dipilih
            document.getElementById(tabName).classList.add('active');

            // Tambahkan active class ke tab yang diklik
            event.currentTarget.classList.add('active');
        }

        function togglePetForm() {
            const form = document.getElementById('addPetForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            if (form.style.display === 'block') {
                document.getElementById('newPetForm').reset();
                // Set default gender selection
                document.querySelector('#newPetForm .gender-btn').classList.add('selected');
                document.getElementById('jenis_kelamin').value = 'jantan';
            }
        }

        // Setup gender buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Form tambah hewan
            document.querySelectorAll('#newPetForm .gender-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const genderSelector = this.closest('.gender-selector');
                    genderSelector.querySelectorAll('.gender-btn').forEach(btn => {
                        btn.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    document.getElementById('jenis_kelamin').value = this.getAttribute('data-value');
                });
            });

            // Form edit hewan
            document.querySelectorAll('#edit_gender_selector .gender-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const genderSelector = this.closest('.gender-selector');
                    genderSelector.querySelectorAll('.gender-btn').forEach(btn => {
                        btn.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    document.getElementById('edit_jenis_kelamin').value = this.getAttribute('data-value');
                });
            });
        });

        // Modal functions
        function openEditModal(petId) {
            // Redirect ke halaman dengan parameter edit
            window.location.href = 'B_profil.php?edit_pet=' + petId;
        }

        function closeEditModal() {
            // Redirect kembali tanpa parameter edit
            window.location.href = 'B_profil.php';
        }

        // Buka modal edit jika ada parameter di URL
        <?php if (isset($_GET['edit_pet']) && $pet_to_edit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('editPetModal').style.display = 'block';
            });
        <?php endif; ?>

        // Close modal ketika klik di luar
        window.onclick = function(event) {
            const modal = document.getElementById('editPetModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }

        // Confirm delete dengan popup custom

        function confirmDelete(petId, petName) {
            if (confirm(`Apakah Anda yakin ingin menghapus hewan "${petName}"?`)) {
                // Buat form untuk submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                const inputAksi = document.createElement('input');
                inputAksi.type = 'hidden';
                inputAksi.name = 'aksi';
                inputAksi.value = 'hapus_hewan';

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id_pet';
                inputId.value = petId;

                form.appendChild(inputAksi);
                form.appendChild(inputId);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Tutup modal jika klik di luar
        window.onclick = function(event) {
            const modal = document.getElementById('editPetModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }

        // Validasi form hewan
        document.getElementById('newPetForm')?.addEventListener('submit', function(e) {
            const genderSelected = document.getElementById('jenis_kelamin').value;
            if (!genderSelected) {
                e.preventDefault();
                alert('Pilih jenis kelamin hewan terlebih dahulu');
            }
        });

        // ====== FUNGSI HAPUS AKUN ======
        function tampilkanHapusAkunModal() {
            document.getElementById('hapusAkunModal').style.display = 'block';
            document.getElementById('passwordKonfirmasi').value = '';
            // Fokus ke input password
            setTimeout(() => {
                document.getElementById('passwordKonfirmasi').focus();
            }, 100);
        }

        function tutupHapusAkunModal() {
            document.getElementById('hapusAkunModal').style.display = 'none';
            document.getElementById('passwordKonfirmasi').value = '';
        }

        function konfirmasiHapusAkun() {
            const password = document.getElementById('passwordKonfirmasi').value.trim();

            if (!password) {
                alert('Masukkan password untuk konfirmasi');
                document.getElementById('passwordKonfirmasi').focus();
                return;
            }

            // Konfirmasi akhir
            if (confirm('APAKAH ANDA BENAR-BENAR YAKIN?\n\n' +
                    'Tindakan ini PERMANEN dan TIDAK DAPAT DIBATALKAN.\n' +
                    'Semua data Anda akan dihapus selamanya.')) {
                // Kirim form hapus akun
                document.getElementById('formHapusAkun').submit();
            }
        }

        // Event listener untuk tombol hapus akun
        document.getElementById('btnHapusAkun').addEventListener('click', tampilkanHapusAkunModal);

        // Tutup modal jika klik di luar
        window.onclick = function(event) {
            const modal = document.getElementById('hapusAkunModal');
            const editModal = document.getElementById('editPetModal');

            if (event.target === modal) {
                tutupHapusAkunModal();
            }

            if (event.target === editModal) {
                closeEditModal();
            }
        }

        // Tutup modal dengan ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const hapusModal = document.getElementById('hapusAkunModal');
                const editModal = document.getElementById('editPetModal');

                if (hapusModal && hapusModal.style.display === 'block') {
                    tutupHapusAkunModal();
                }

                if (editModal && editModal.style.display === 'block') {
                    closeEditModal();
                }
            }
        });

        // ====== VALIDASI INPUT ======

        // Format Nomor HP
        function formatNomorHP(input) {
            // Simpan posisi kursor
            const cursorPos = input.selectionStart;

            // Hapus semua karakter non-digit dan non-plus
            let value = input.value.replace(/[^\d+]/g, '');

            // Pastikan dimulai dengan +62
            if (!value.startsWith('+62')) {
                value = '+62' + value.replace(/^\+?62?/, '');
            }

            // Batasi maksimal 15 karakter (+62 + 12 digit = 15)
            if (value.length > 15) {
                value = value.substring(0, 15);
            }

            // Update nilai
            input.value = value;

            // Kembalikan posisi kursor
            input.setSelectionRange(cursorPos, cursorPos);

            // Update counter
            const digitsAfter62 = value.length - 3; // -3 untuk "+62"
            const counter = document.getElementById('no_hp_counter');

            if (counter) {
                counter.textContent = `${digitsAfter62} digit setelah +62`;

                // Warna berdasarkan validitas
                if (digitsAfter62 >= 9 && digitsAfter62 <= 12) {
                    input.style.borderColor = '#27ae60';
                    counter.style.color = '#27ae60';
                } else if (digitsAfter62 < 9) {
                    input.style.borderColor = '#f39c12';
                    counter.style.color = '#f39c12';
                } else {
                    input.style.borderColor = '#e74c3c';
                    counter.style.color = '#e74c3c';
                }
            }
        }

        // Format Kode Pos
        function formatKodePos(input) {
            // Hapus semua karakter non-digit
            let value = input.value.replace(/\D/g, '');

            // Batasi maksimal 5 karakter
            if (value.length > 5) {
                value = value.substring(0, 5);
            }

            input.value = value;

            // Update counter
            const counter = document.getElementById('kode_pos_counter');
            if (counter) {
                counter.textContent = `${value.length}/5 digit`;
                counter.style.color = value.length === 5 ? '#27ae60' :
                    (value.length < 5 ? '#666' : '#e74c3c');
            }
        }

        // Validasi sebelum submit
        function validasiProfil() {
            const noHpInput = document.querySelector('input[name="no_hp"]');
            const kodePosInput = document.querySelector('input[name="kode_pos"]');

            let valid = true;
            let errorMessage = '';

            // Validasi Nomor HP
            if (noHpInput) {
                const cleanNoHP = noHpInput.value.replace(/[^\d+]/g, '');
                const angkaSetelah62 = cleanNoHP.substring(3);

                if (angkaSetelah62.length < 9 || angkaSetelah62.length > 12) {
                    valid = false;
                    errorMessage = 'Nomor HP harus 9-12 digit setelah +62\nContoh: +628123456789 (12 digit total)';
                    noHpInput.focus();
                } else if (!/^\d+$/.test(angkaSetelah62)) {
                    valid = false;
                    errorMessage = 'Nomor HP hanya boleh mengandung angka setelah +62';
                    noHpInput.focus();
                }
            }

            // Validasi Kode Pos
            if (kodePosInput && kodePosInput.value) {
                const kodePos = kodePosInput.value;

                // Cek apakah mengandung non-digit
                if (/\D/.test(kodePos)) {
                    valid = false;
                    errorMessage = 'Kode Pos hanya boleh mengandung angka 0-9';
                    kodePosInput.focus();
                }
                // Cek panjang 5 digit
                else if (kodePos.length !== 5) {
                    valid = false;
                    errorMessage = 'Kode Pos harus tepat 5 digit';
                    kodePosInput.focus();
                }
            }

            if (!valid && errorMessage) {
                alert(errorMessage);
                return false;
            }

            return true;
        }

        // Event Listener untuk Form Submit
        document.addEventListener('DOMContentLoaded', function() {
            // Form profil
            const formProfil = document.querySelector('form[action*="update_profile"]');
            if (formProfil) {
                formProfil.addEventListener('submit', function(e) {
                    if (!validasiProfil()) {
                        e.preventDefault();
                    }
                });
            }

            // Form alamat
            const formAlamat = document.querySelector('form[action*="update_alamat"]');
            if (formAlamat) {
                formAlamat.addEventListener('submit', function(e) {
                    if (!validasiProfil()) {
                        e.preventDefault();
                    }
                });
            }

            // Inisialisasi counter saat halaman dimuat
            const noHpInput = document.querySelector('input[name="no_hp"]');
            if (noHpInput) {
                // Format ulang nomor HP saat halaman dimuat
                setTimeout(() => {
                    formatNomorHP(noHpInput);
                }, 100);
            }

            const kodePosInput = document.querySelector('input[name="kode_pos"]');
            if (kodePosInput) {
                // Format ulang kode pos saat halaman dimuat
                setTimeout(() => {
                    formatKodePos(kodePosInput);
                }, 100);
            }
        });

        // Fungsi untuk validasi input hanya angka (onkeypress)
        function hanyaAngka(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        // Atau gunakan event listener langsung di input
        document.addEventListener('DOMContentLoaded', function() {
            const kodePosInput = document.getElementById('kode_pos');
            if (kodePosInput) {
                kodePosInput.addEventListener('keypress', function(e) {
                    // Hanya izinkan angka
                    const charCode = (e.which) ? e.which : e.keyCode;
                    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>

    <!-- Modal Konfirmasi Hapus Akun -->
    <div id="hapusAkunModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Hapus Akun Permanen</h3>
                <button class="close-modal" onclick="tutupHapusAkunModal()">&times;</button>
            </div>
            <div style="padding: 20px 0;">
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <p style="color: #856404; margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>PERINGATAN: Tindakan ini tidak dapat dibatalkan!</strong>
                    </p>
                </div>

                <p style="color: #666; line-height: 1.6; margin-bottom: 15px;">
                    Apakah Anda yakin ingin <strong>menghapus akun Anda secara permanen?</strong>
                </p>

                <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin: 20px 0;">
                    <p style="color: #dc3545; font-weight: 600; margin-bottom: 10px;">Data yang akan dihapus:</p>
                    <ul style="color: #666; padding-left: 20px; margin: 0;">
                        <li>Profil pengguna Anda</li>
                        <li>Semua data hewan peliharaan</li>
                        <li>Riwayat konsultasi</li>
                        <li>Semua data terkait akun Anda</li>
                    </ul>
                </div>

                <p style="color: #666; line-height: 1.6; margin-bottom: 20px;">
                    Setelah dihapus, Anda tidak dapat mengakses akun ini lagi.
                    Anda perlu membuat akun baru jika ingin menggunakan layanan kami kembali.
                </p>

                <!-- Form Hapus Akun -->
                <form id="formHapusAkun" method="POST" action="">
                    <input type="hidden" name="hapus_akun" value="true">

                    <div class="form-group">
                        <label style="color: #333; font-weight: 600;">
                            <i class="fas fa-key"></i> Konfirmasi dengan Password
                        </label>
                        <input type="password" id="passwordKonfirmasi" name="password"
                            placeholder="Masukkan password Anda untuk konfirmasi"
                            style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;"
                            required>
                        <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                            Masukkan password Anda untuk mengonfirmasi penghapusan akun
                        </small>
                    </div>
                </form>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="tutupHapusAkunModal()" style="flex: 1;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn-danger" onclick="konfirmasiHapusAkun()" style="flex: 1;">
                    <i class="fas fa-user-times"></i> Ya, Hapus Akun
                </button>
            </div>
        </div>
    </div>
</body>

</html>