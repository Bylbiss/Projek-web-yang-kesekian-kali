<?php
require "koneksi.php";


function esc($str)
{
    global $koneksi;
    if ($str === null || $str === '') return '';
    return mysqli_real_escape_string($koneksi, $str);
}

// Fungsi untuk cek apakah user sudah login
function sudahLogin()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Fungsi login
function login($email, $password)
{
    global $koneksi;

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ["sukses" => false, "pesan" => "Format email tidak valid!"];
    }

    $email = esc($email);

    // Query dengan error handling
    $query = "SELECT id_pemilik, nama_pemilik, email, password FROM pemilik WHERE email = '$email'";
    $hasil = mysqli_query($koneksi, $query);

    if (!$hasil) {
        error_log("Database error: " . mysqli_error($koneksi));
        return ["sukses" => false, "pesan" => "Terjadi kesalahan sistem!"];
    }

    if (mysqli_num_rows($hasil) == 0) {
        return ["sukses" => false, "pesan" => "Email belum terdaftar!"];
    }

    $user = mysqli_fetch_assoc($hasil);

    // Debug password verification
    error_log("Password input: " . $password);
    error_log("Password hash: " . $user['password']);
    error_log("Password verify: " . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE'));

    if (password_verify($password, $user['password'])) {
        // SET SESSION VARIABLES
        $_SESSION['user_id'] = (int)$user['id_pemilik'];
        $_SESSION['nama_pemilik'] = $user['nama_pemilik'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // Debug session
        error_log("=== LOGIN SUCCESS ===");
        error_log("User ID: " . $_SESSION['user_id']);
        error_log("Nama: " . $_SESSION['nama_pemilik']);
        error_log("Email: " . $_SESSION['email']);
        error_log("=====================");

        return ["sukses" => true, "pesan" => "Login berhasil!"];
    } else {
        return ["sukses" => false, "pesan" => "Password salah!"];
    }
}

// Fungsi registrasi dengan dukungan multi-pet
function register($nama_pemilik, $email, $no_hp, $password, $alamat = '', $kota = '', $kode_pos = '', $pets = [])
{
    global $koneksi;

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ["sukses" => false, "pesan" => "Format email tidak valid!"];
    }

    // Sanitasi data pemilik
    $email = esc($email);
    $nama_pemilik = esc($nama_pemilik);
    $no_hp = esc($no_hp);
    $alamat = esc($alamat);
    $kota = esc($kota);
    $kode_pos = esc($kode_pos);

    // Cek email sudah dipakai
    $cek_email = mysqli_query($koneksi, "SELECT * FROM pemilik WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        return ["sukses" => false, "pesan" => "Email sudah terdaftar!"];
    }

    // Enkripsi password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Simpan ke tabel pemilik
    $query = "INSERT INTO pemilik (nama_pemilik, password, no_hp, email, alamat, kota, kode_pos)
VALUES ('$nama_pemilik', '$password_hash', '$no_hp', '$email', '$alamat', '$kota', '$kode_pos')";

    if (!mysqli_query($koneksi, $query)) {
        error_log("Error saving owner: " . mysqli_error($koneksi));
        return ["sukses" => false, "pesan" => "Gagal menyimpan data pemilik."];
    }

    // Ambil ID pemilik yang baru saja dibuat
    $id_pemilik = mysqli_insert_id($koneksi);

    // Simpan data hewan peliharaan
    foreach ($pets as $pet) {
        // Sanitasi tiap field hewan
        $nama_pet = esc($pet['nama_pet'] ?? '');
        $jenis_kelamin = in_array($pet['jenis_kelamin'], ['jantan', 'betina', 'tidak_diketahui']) ? $pet['jenis_kelamin'] : 'tidak_diketahui';
        $jenis_hewan = in_array($pet['jenis_hewan'], ['sapi', 'kambing', 'kerbau', 'ayam', 'kucing', 'kelinci', 'anjing', 'hamster', 'burung', 'ikan', 'musang', 'kura-kura', 'landak', 'babi', 'kuda', 'domba', 'lain-lain']) ? esc($pet['jenis_hewan']) : 'lain-lain';
        $ras = esc($pet['ras'] ?? '');
        $tanggal_lahir = !empty($pet['tanggal_lahir']) ? esc($pet['tanggal_lahir']) : null;
        $usia = isset($pet['usia']) && $pet['usia'] !== '' ? (int)$pet['usia'] : null;
        $berat = isset($pet['berat']) && $pet['berat'] !== '' ? (float)$pet['berat'] : null;
        $sterilisasi = in_array($pet['sterilisasi'], ['sudah', 'belum']) ? $pet['sterilisasi'] : 'belum';

        // Handle tanggal_lahir: jika null atau kosong, jadi NULL
        if ($tanggal_lahir === null || empty($tanggal_lahir)) {
            $tgl_sql = 'NULL';
        } else {
            $tgl_sql = "'$tanggal_lahir'";
        }

        // Handle usia dan berat
        $usia_sql = $usia === null ? 'NULL' : $usia;
        $berat_sql = $berat === null ? 'NULL' : $berat;

        // Query insert hewan
        $query_pet = "
INSERT INTO pets (id_pemilik, nama_pet, jenis_kelamin, jenis_hewan, ras, tanggal_lahir, usia, berat, sterilisasi)
VALUES (
$id_pemilik,
'$nama_pet',
'$jenis_kelamin',
'$jenis_hewan',
'$ras',
$tgl_sql,
$usia_sql,
$berat_sql,
'$sterilisasi'
)
";

        if (!mysqli_query($koneksi, $query_pet)) {
            error_log("Error saving pet: " . mysqli_error($koneksi));
        }
    }

    return ["sukses" => true, "pesan" => "Registrasi berhasil! Silakan login."];
}

// Fungsi logout
function logout()
{
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
    return ["sukses" => true, "pesan" => "Anda berhasil logout."];
}

// Fungsi untuk mendapatkan data user
function getUser()
{
    if (!sudahLogin()) {
        return null;
    }

    global $koneksi;
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM pemilik WHERE id_pemilik = '$user_id'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }

    return null;
}

// Proses form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])) {

    // Untuk request POST, kita perlu session_start() jika belum ada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($_POST['aksi'] == 'register') {
        // Debug
        error_log("=== REGISTRATION DEBUG ===");
        error_log("Password: " . ($_POST['password'] ?? ''));
        error_log("Confirm Password: " . ($_POST['confirm_password'] ?? 'NOT SET'));

        $nama    = trim($_POST['nama_pemilik'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $no_hp   = trim($_POST['no_hp'] ?? '');
        $pass    = $_POST['password'] ?? '';
        $ulang   = $_POST['confirm_password'] ?? '';
        $alamat  = trim($_POST['alamat'] ?? '');
        $kota    = trim($_POST['kota'] ?? '');
        $kodepos = trim($_POST['kode_pos'] ?? '');

        // Debug
        error_log("Password: '$pass'");
        error_log("Confirm: '$ulang'");
        error_log("Match: " . ($pass === $ulang ? 'YES' : 'NO'));

        if (empty($nama) || empty($email) || empty($no_hp) || empty($pass)) {
            echo json_encode(["sukses" => false, "pesan" => "Semua field wajib diisi!"]);
            exit;
        }

        if ($pass !== $ulang) {
            echo json_encode(["sukses" => false, "pesan" => "Password dan konfirmasi tidak sama!"]);
            exit;
        }

        // Ambil data hewan dari form fields
        $pets = [];

        // Cek jika ada data hewan yang dikirim
        if (isset($_POST['nama_pet']) && is_array($_POST['nama_pet'])) {
            $petCount = count($_POST['nama_pet']);
            for ($i = 0; $i < $petCount; $i++) {
                // Skip jika nama pet kosong (form hewan kosong)
                if (empty(trim($_POST['nama_pet'][$i] ?? ''))) {
                    continue;
                }

                $pets[] = [
                    'nama_pet' => trim($_POST['nama_pet'][$i] ?? ''),
                    'jenis_kelamin' => $_POST['jenis_kelamin'][$i] ?? 'tidak_diketahui',
                    'jenis_hewan' => $_POST['jenis_hewan'][$i] ?? 'lain-lain',
                    'ras' => trim($_POST['ras'][$i] ?? ''),
                    'tanggal_lahir' => trim($_POST['tanggal_lahir'][$i] ?? ''),
                    'usia' => isset($_POST['usia'][$i]) && $_POST['usia'][$i] !== '' ? (int)$_POST['usia'][$i] : null,
                    'berat' => isset($_POST['berat'][$i]) && $_POST['berat'][$i] !== '' ? (float)$_POST['berat'][$i] : null,
                    'sterilisasi' => $_POST['sterilisasi'][$i] ?? 'belum'
                ];
            }
        }

        error_log("Pets data: " . print_r($pets, true));
        $res = register($nama, $email, $no_hp, $pass, $alamat, $kota, $kodepos, $pets);
        echo json_encode($res);
        exit;
    }

    if ($_POST['aksi'] == 'login') {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if (empty($email) || empty($pass)) {
            echo json_encode(["sukses" => false, "pesan" => "Email dan password wajib diisi!"]);
            exit;
        }

        $res = login($email, $pass);
        echo json_encode($res);
        exit;
    }

    if ($_POST['aksi'] == 'logout') {
        $res = logout();
        echo json_encode($res);
        exit;
    }
}
