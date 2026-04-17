<?php
session_start();
require 'logic/auth.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABclinic - Buat Akun</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="ABclinic.jpg">
</head>

<body class="register-page">
    <div class="image-side"></div>

    <div class="form-side">
        <div class="logo-container login-logo-container">
            <a href="B_dashboard.php" class="logo-link">
                <div class="logo-content">
                    <div class="icon login-icon">
                        <i class="fas fa-paw"></i>
                        <i class="fas fa-stethoscope" style="margin-left: -10px; color: #e83e8c;"></i>
                    </div>
                    <div class="logo-text login-logo">ABclinic</div>
                    <div class="tagline login-tagline">Untuk Pemilik Hewan Peliharaan</div>
                    <div class="location login-location">INDONESIA</div>
                </div>
            </a>
        </div>

        <div class="step-indicator">
            <span id="step-text">Langkah 1 dari 3</span>
        </div>

        <div class="form-title">Buat akun</div>
        <div class="form-subtitle" id="step1-subtitle">Isi data pribadi Anda untuk memulai.</div>
        <div class="form-subtitle" id="step2-subtitle" style="display:none;">Ini adalah alamat tempat tinggal Anda.</div>
        <div class="form-subtitle" id="step3-subtitle" style="display:none;">Tambahkan hewan peliharaan Anda — bisa lebih dari satu!</div>

        <!-- Step 1 -->
        <div id="step1" class="step-form">
            <div class="form-group">
                <input type="text" id="nama_depan" placeholder="Nama Depan" required>
            </div>
            <div class="form-group">
                <input type="text" id="nama_belakang" placeholder="Nama Belakang" required>
            </div>
            <div class="form-group password-wrapper">
                <input type="password" id="password" placeholder="Buat Kata Sandi" required>
                <span class="toggle-password" id="toggle-pass">👁️</span>
            </div>
            <div class="password-requirements" id="password-req">
                <span class="requirement" id="req-length">• Minimal 6 karakter</span>
                <span class="requirement" id="req-upper">• Huruf besar (A-Z)</span>
                <span class="requirement" id="req-lower">• Huruf kecil (a-z)</span>
                <span class="requirement" id="req-digit">• Angka (0-9)</span>
            </div>
            <div class="form-group password-wrapper">
                <input type="password" id="confirm_password" placeholder="Konfirmasi Kata Sandi" required>
                <span class="toggle-password" id="toggle-confirm">👁️</span>
            </div>
            <div class="input-row">
                <div>
                    <input type="text" id="no_hp" placeholder="Contoh: +628123456789"
                        value="+62" maxlength="15" required
                        title="12 digit angka, contoh: +628123456789">
                    <small id="no_hp_counter" style="font-size: 12px; color: #666; display: block; margin-top: 4px;">
                        * Format: +628123456789
                    </small>
                </div>
                <div><input type="email" id="email" placeholder="Email" required></div>
            </div>
            <button id="next-step1" class="btn-primary">Berikutnya</button>
        </div>

        <!-- Step 2 -->
        <div id="step2" class="step-form" style="display:none;">
            <div class="form-group">
                <input type="text" id="alamat1" placeholder="Alamat Lengkap" required>
            </div>
            <div class="input-row">
                <div><input type="text" id="kota" placeholder="Kota" required></div>
                <div>
                    <input type="text" id="kode_pos" placeholder="Kode Pos"
                        maxlength="5" pattern="\d*" required
                        title="5 digit angka, contoh: 12345">
                    <small id="kode_pos_counter" style="font-size: 12px; color: #666; display: block; margin-top: 4px;">
                        * 5 digit angka (contoh: 12345)
                    </small>
                </div>
            </div>
            <div class="nav-buttons">
                <button id="back-step2" class="btn-secondary">Kembali</button>
                <button id="next-step2" class="btn-primary">Berikutnya</button>
            </div>
        </div>

        <!-- Step 3 -->
        <div id="step3" class="step-form" style="display:none;">
            <div class="pet-warning" id="pet-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Harap tambahkan minimal 1 hewan peliharaan!</strong><br>
                Klik tombol <strong>"+ Tambah Hewan Lain"</strong> di bawah untuk menambahkan hewan peliharaan pertama Anda.
            </div>

            <div class="pet-list" id="pet-list"></div>

            <button class="add-pet-btn" id="add-pet">+ Tambah Hewan</button>

            <div class="nav-buttons">
                <button id="back-step3" class="btn-secondary">Kembali</button>
                <button id="submit-registration" class="btn-primary" disabled>Daftar Sekarang</button>
            </div>
        </div>

        <div id="message" class="message"></div>
        <div class="login-link">Sudah punya akun? <a href="B_login.php">Masuk di sini</a></div>
    </div>

    <script>
        let currentStep = 1;
        let hasValidPet = false;
        let petCount = 1;

        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        const stepText = document.getElementById('step-text');
        const step1Sub = document.getElementById('step1-subtitle');
        const step2Sub = document.getElementById('step2-subtitle');
        const step3Sub = document.getElementById('step3-subtitle');

        // === Fungsi penyimpanan ===
        function saveStep1() {
            // Bersihkan nomor HP sebelum disimpan
            let no_hp = document.getElementById('no_hp').value;

            // Hanya bersihkan jika mengandung karakter non-digit selain +
            if (!/^\+62\d+$/.test(no_hp)) {
                // Jika format salah, coba perbaiki
                no_hp = no_hp.replace(/[^\d+]/g, '');
                if (!no_hp.startsWith('+62')) {
                    no_hp = '+62' + no_hp.replace(/^\+?62?/, '');
                }
            }

            sessionStorage.setItem('step1', JSON.stringify({
                nama_depan: document.getElementById('nama_depan').value,
                nama_belakang: document.getElementById('nama_belakang').value,
                password: document.getElementById('password').value,
                confirm_password: document.getElementById('confirm_password').value,
                no_hp: no_hp, // Simpan yang sudah dibersihkan
                email: document.getElementById('email').value
            }));
        }

        function saveStep2() {
            // Bersihkan kode_pos sebelum disimpan
            let kode_pos = document.getElementById('kode_pos').value.replace(/\D/g, '');
            if (kode_pos.length > 5) kode_pos = kode_pos.substring(0, 5);

            sessionStorage.setItem('step2', JSON.stringify({
                alamat1: document.getElementById('alamat1').value,
                kota: document.getElementById('kota').value,
                kode_pos: kode_pos // Simpan yang sudah dibersihkan
            }));
        }

        function loadSavedData() {
            const step1Data = sessionStorage.getItem('step1');
            if (step1Data) {
                const data = JSON.parse(step1Data);
                document.getElementById('nama_depan').value = data.nama_depan || '';
                document.getElementById('nama_belakang').value = data.nama_belakang || '';
                document.getElementById('password').value = data.password || '';
                document.getElementById('confirm_password').value = data.confirm_password || '';

                // Pastikan nomor HP tetap dengan format +62
                let no_hp = data.no_hp || '+62';
                if (!no_hp.startsWith('+62')) {
                    no_hp = '+62' + no_hp.replace(/^\+?62?/, '');
                }

                document.getElementById('no_hp').value = no_hp;
                document.getElementById('email').value = data.email || '';
                updatePasswordRequirements();
            }

            const step2Data = sessionStorage.getItem('step2');
            if (step2Data) {
                const data = JSON.parse(step2Data);
                document.getElementById('alamat1').value = data.alamat1 || '';
                document.getElementById('kota').value = data.kota || '';
                document.getElementById('kode_pos').value = data.kode_pos || '';

                // Update counter kode pos
                updateKodePosCounter(data.kode_pos ? data.kode_pos.length : 0);
            }
        }

        function showStep(step) {
            step1.style.display = step === 1 ? 'block' : 'none';
            step2.style.display = step === 2 ? 'block' : 'none';
            step3.style.display = step === 3 ? 'block' : 'none';

            step1Sub.style.display = step === 1 ? 'block' : 'none';
            step2Sub.style.display = step === 2 ? 'block' : 'none';
            step3Sub.style.display = step === 3 ? 'block' : 'none';

            stepText.textContent = `Langkah ${step} dari 3`;
            currentStep = step;

            // Saat masuk ke step 3, langsung tampilkan warning
            if (step === 3) {
                const petWarning = document.getElementById('pet-warning');
                if (petWarning) petWarning.style.display = 'block';

                // NONAKTIFKAN tombol daftar
                const submitBtn = document.getElementById('submit-registration');
                if (submitBtn) submitBtn.disabled = true;
            }

            // Periksa validitas hewan saat masuk ke step 3
            if (step === 3) {
                setTimeout(checkPetValidity, 100);
            }
        }

        // === Validasi Password ===
        function updatePasswordRequirements() {
            const pass = document.getElementById('password').value;
            const reqLength = document.getElementById('req-length');
            const reqUpper = document.getElementById('req-upper');
            const reqLower = document.getElementById('req-lower');
            const reqDigit = document.getElementById('req-digit');

            const checks = {
                length: pass.length >= 6,
                upper: /[A-Z]/.test(pass),
                lower: /[a-z]/.test(pass),
                digit: /[0-9]/.test(pass)
            };

            reqLength.className = `requirement ${checks.length ? 'valid' : 'invalid'}`;
            reqUpper.className = `requirement ${checks.upper ? 'valid' : 'invalid'}`;
            reqLower.className = `requirement ${checks.lower ? 'valid' : 'invalid'}`;
            reqDigit.className = `requirement ${checks.digit ? 'valid' : 'invalid'}`;

            return Object.values(checks).every(v => v);
        }

        function isPasswordMatch() {
            const pass = document.getElementById('password').value.trim();
            const confirm = document.getElementById('confirm_password').value.trim();
            return pass === confirm;
        }

        // === Toggle Password ===
        document.getElementById('toggle-pass').addEventListener('click', function() {
            const input = document.getElementById('password');
            this.textContent = input.type === 'password' ? '🙈' : '👁️';
            input.type = input.type === 'password' ? 'text' : 'password';
        });

        document.getElementById('toggle-confirm').addEventListener('click', function() {
            const input = document.getElementById('confirm_password');
            this.textContent = input.type === 'password' ? '🙈' : '👁️';
            input.type = input.type === 'password' ? 'text' : 'password';
        });

        // === Real-time validation ===
        document.getElementById('password').addEventListener('input', updatePasswordRequirements);
        document.getElementById('confirm_password').addEventListener('input', function() {
            const matchMsg = document.createElement('div');
            matchMsg.id = 'match-msg';
            matchMsg.style.fontSize = '12px';
            matchMsg.style.marginTop = '6px';

            const pass = document.getElementById('password').value.trim();
            const confirm = this.value.trim();

            if (confirm && pass !== confirm) {
                matchMsg.textContent = '❌ Kata sandi tidak cocok';
                matchMsg.style.color = '#e74c3c';
            } else if (confirm) {
                matchMsg.textContent = '✅ Cocok';
                matchMsg.style.color = '#27ae60';
            } else {
                const existing = document.getElementById('match-msg');
                if (existing) existing.remove();
                return;
            }

            const existing = document.getElementById('match-msg');
            if (existing) existing.remove();
            this.parentNode.appendChild(matchMsg);
        });

        // === VALIDASI NOMOR HP REAL-TIME ===
        document.getElementById('no_hp').addEventListener('input', function(e) {
            // Simpan posisi kursor
            const cursorPos = this.selectionStart;

            // Hapus semua karakter non-digit dan non-plus
            let value = this.value.replace(/[^\d+]/g, '');

            // Pastikan dimulai dengan +62
            if (!value.startsWith('+62')) {
                value = '+62' + value.replace(/^\+?62?/, '');
            }

            // Batasi maksimal 15 karakter (+62 + 12 digit = 15)
            if (value.length > 15) {
                value = value.substring(0, 15);
            }

            // Update nilai
            this.value = value;

            // Kembalikan posisi kursor
            this.setSelectionRange(cursorPos, cursorPos);

            // Update counter
            const digitsAfter62 = value.length - 3; // -3 untuk "+62"
            const counter = document.getElementById('no_hp_counter');

            if (counter) {
                counter.textContent = `${digitsAfter62} digit setelah +62`;
                counter.style.color = digitsAfter62 >= 9 && digitsAfter62 <= 12 ? '#27ae60' :
                    (digitsAfter62 < 9 ? '#f39c12' : '#e74c3c');
            }
        });

        // === VALIDASI KODE POS REAL-TIME ===
        document.getElementById('kode_pos').addEventListener('input', function(e) {
            // Hapus semua karakter non-digit
            let value = this.value.replace(/\D/g, '');

            // Batasi maksimal 5 karakter
            if (value.length > 5) {
                value = value.substring(0, 5);
            }

            this.value = value;

            // Update counter
            updateKodePosCounter(value.length);
        });

        function updateKodePosCounter(length) {
            const counter = document.getElementById('kode_pos_counter');
            if (counter) {
                counter.textContent = `${length}/5 digit`;
                counter.style.color = length === 5 ? '#27ae60' :
                    (length < 5 ? '#666' : '#e74c3c');
            }
        }

        // === Navigasi ===
        document.getElementById('next-step1').addEventListener('click', () => {
            // === DAPATKAN NILAI DULU ===
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const no_hp = document.getElementById('no_hp').value.trim();

            if (!updatePasswordRequirements()) {
                alert('Kata sandi belum memenuhi persyaratan!');
                return;
            }
            if (pass !== confirm) {
                alert('Konfirmasi kata sandi tidak cocok!');
                return;
            }

            // Hapus semua karakter non-digit
            const cleanNoHP = no_hp.replace(/\D/g, '');

            // Validasi panjang: +62 (3 karakter) + 9-12 digit
            const angkaSetelah62 = cleanNoHP.substring(3); // Hapus "+62"
            if (angkaSetelah62.length < 9 || angkaSetelah62.length > 12) {
                alert('Nomor HP harus 9-12 digit setelah +62\nContoh: +628123456789 (12 digit total)');
                document.getElementById('no_hp').focus();
                return;
            }

            // Validasi hanya angka setelah +62
            if (!/^\d+$/.test(angkaSetelah62)) {
                alert('Nomor HP hanya boleh mengandung angka setelah +62');
                document.getElementById('no_hp').focus();
                return;
            }

            // Update nilai dengan format yang benar
            document.getElementById('no_hp').value = cleanNoHP;

            saveStep1();
            showStep(2);
        });

        document.getElementById('next-step2').addEventListener('click', () => {
            const alamat = document.getElementById('alamat1').value.trim();
            const kota = document.getElementById('kota').value.trim();
            const kode_pos = document.getElementById('kode_pos').value.trim();

            if (!alamat || !kota) {
                alert('Alamat dan kota wajib diisi!');
                return;
            }

            // Validasi kode pos
            if (kode_pos && !/^\d{5}$/.test(kode_pos)) {
                alert('Kode Pos harus tepat 5 digit angka');
                document.getElementById('kode_pos').focus();
                return;
            }

            saveStep2();
            showStep(3);
        });

        document.getElementById('back-step2').addEventListener('click', () => showStep(1));
        document.getElementById('back-step3').addEventListener('click', () => showStep(2));

        // === Pet Form ===
        async function addPetForm(index) {
            try {
                const response = await fetch('B_tambah_hewan.php');
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                let html = await response.text();

                const petList = document.getElementById('pet-list');
                const petItem = document.createElement('div');
                petItem.className = 'pet-item';
                petItem.innerHTML = html;
                petList.appendChild(petItem);

                // Setup gender buttons
                const genderBtns = petItem.querySelectorAll('.gender-btn');
                genderBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        genderBtns.forEach(b => b.classList.remove('selected'));
                        btn.classList.add('selected');
                        checkPetValidity(); // Periksa validitas setelah pilih gender
                    });
                });

                // Set default gender selection
                if (genderBtns.length > 0) {
                    genderBtns[0].click(); // Default: Jantan
                }

                // Tambahkan event listener untuk semua input di form hewan
                const inputs = petItem.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('input', checkPetValidity);
                    input.addEventListener('change', checkPetValidity);
                });

                // Setup remove pet button
                const removeBtn = petItem.querySelector('.remove-pet');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        this.closest('.pet-item').remove();
                        checkPetValidity();
                    });
                }

                // Periksa validitas setelah menambahkan form
                checkPetValidity();

            } catch (e) {
                console.error('Gagal memuat form hewan:', e);
                const msg = document.getElementById('message');
                msg.className = 'message error show';
                msg.textContent = 'Gagal memuat form hewan. Silakan refresh halaman.';
            }
        }

        // Fungsi untuk memeriksa apakah ada hewan yang valid
        function checkPetValidity() {
            const petItems = document.querySelectorAll('.pet-item');
            const petWarning = document.getElementById('pet-warning');
            const submitBtn = document.getElementById('submit-registration');

            // Reset status
            hasValidPet = false;

            // Periksa setiap form hewan
            petItems.forEach(petItem => {
                const namaPet = petItem.querySelector('[name="nama_pet[]"]')?.value.trim() || '';
                const jenisHewan = petItem.querySelector('[name="jenis_hewan[]"]')?.value || '';

                // Validasi: nama hewan tidak kosong DAN jenis hewan dipilih
                if (namaPet !== '' && jenisHewan !== '') {
                    hasValidPet = true;
                }
            });

            // Tampilkan/sembunyikan warning
            if (petWarning) {
                if (petItems.length === 0 || !hasValidPet) {
                    petWarning.style.display = 'block';
                    if (submitBtn) submitBtn.disabled = true;
                } else {
                    petWarning.style.display = 'none';
                    if (submitBtn) submitBtn.disabled = false;
                }
            }

            return hasValidPet;
        }

        document.getElementById('add-pet').addEventListener('click', async () => {
            const addPetBtn = document.getElementById('add-pet');
            const originalText = addPetBtn.textContent;

            // Tampilkan loading
            addPetBtn.textContent = 'Memuat...';
            addPetBtn.disabled = true;

            try {
                await addPetForm(petCount++);
            } finally {
                // Kembalikan ke normal
                addPetBtn.textContent = originalText;
                addPetBtn.disabled = false;
            }
        });

        // === Submit ===
        document.getElementById('submit-registration').addEventListener('click', async () => {
            // === DAPATKAN NILAI ===
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const no_hp = document.getElementById('no_hp').value.trim();
            const kode_pos = document.getElementById('kode_pos').value.trim();

            // === VALIDASI PASSWORD ===
            if (pass !== confirm) {
                alert('Password dan konfirmasi tidak sama!');
                return;
            }

            // === VALIDASI NOMOR HP ===
            const cleanNoHP = no_hp.replace(/[^\d+]/g, '');

            const angkaSetelah62 = cleanNoHP.substring(3);
            if (angkaSetelah62.length < 9 || angkaSetelah62.length > 12) {
                alert('Nomor HP harus 9-12 digit setelah +62\nContoh: +628123456789 (12 digit total)');
                document.getElementById('no_hp').focus();
                return;
            }

            if (!/^\d+$/.test(angkaSetelah62)) {
                alert('Nomor HP hanya boleh mengandung angka setelah +62');
                document.getElementById('no_hp').focus();
                return;
            }

            // === VALIDASI KODE POS ===
            if (kode_pos && !/^\d{5}$/.test(kode_pos)) {
                alert('Kode Pos harus tepat 5 digit angka');
                document.getElementById('kode_pos').focus();
                return;
            }

            // === VALIDASI HEWAN ===
            const allPetItems = document.querySelectorAll('.pet-item');
            let hasValidPet = false;

            allPetItems.forEach((petItem, index) => {
                const namaPet = petItem.querySelector('[name="nama_pet[]"]')?.value.trim() || '';
                const jenisHewan = petItem.querySelector('[name="jenis_hewan[]"]')?.value || '';

                if (namaPet !== '' && jenisHewan !== '') {
                    hasValidPet = true;
                }
            });

            if (!hasValidPet) {
                alert('Anda wajib mengisi minimal 1 hewan peliharaan yang valid!');
                return;
            }

            // Update nilai dengan format yang benar
            document.getElementById('no_hp').value = cleanNoHP;

            // Lanjutkan kirim data
            const formData = new FormData();
            formData.append('aksi', 'register');
            formData.append('nama_pemilik', document.getElementById('nama_depan').value.trim() + ' ' + document.getElementById('nama_belakang').value.trim());
            formData.append('email', document.getElementById('email').value.trim());
            formData.append('no_hp', cleanNoHP); // Gunakan yang sudah dibersihkan
            formData.append('password', pass);
            formData.append('confirm_password', confirm);
            formData.append('alamat', document.getElementById('alamat1').value.trim());
            formData.append('kota', document.getElementById('kota').value.trim());
            formData.append('kode_pos', document.getElementById('kode_pos').value.trim());

            // Kumpulkan data hewan sebagai array
            const petItems = document.querySelectorAll('.pet-item');
            petItems.forEach((petItem, index) => {
                // Ambil semua input values
                const namaPet = petItem.querySelector('[name="nama_pet[]"]')?.value || '';
                const jenisHewan = petItem.querySelector('[name="jenis_hewan[]"]')?.value || '';
                const ras = petItem.querySelector('[name="ras[]"]')?.value || '';
                const tanggalLahir = petItem.querySelector('[name="tanggal_lahir[]"]')?.value || '';
                const usia = petItem.querySelector('[name="usia[]"]')?.value || '';
                const berat = petItem.querySelector('[name="berat[]"]')?.value || '';
                const sterilisasi = petItem.querySelector('[name="sterilisasi[]"]')?.value || 'belum';

                // Jenis kelamin dari tombol yang dipilih
                const genderSelector = petItem.querySelector('.gender-selector');
                let jenisKelamin = 'tidak_diketahui';
                if (genderSelector) {
                    const selectedGender = genderSelector.querySelector('.gender-btn.selected');
                    if (selectedGender) {
                        jenisKelamin = selectedGender.getAttribute('data-value');
                    }
                }

                // Hanya tambahkan jika nama pet tidak kosong
                if (namaPet.trim() !== '') {
                    formData.append('nama_pet[]', namaPet);
                    formData.append('jenis_kelamin[]', jenisKelamin);
                    formData.append('jenis_hewan[]', jenisHewan);
                    formData.append('ras[]', ras);
                    formData.append('tanggal_lahir[]', tanggalLahir);
                    formData.append('usia[]', usia);
                    formData.append('berat[]', berat);
                    formData.append('sterilisasi[]', sterilisasi);
                }
            });

            // Debug FormData sebelum kirim
            console.log("Mengirim data:");
            for (let [key, value] of formData.entries()) {
                console.log(key + ": " + value);
            }

            // Kirim ke server
            try {
                const res = await fetch('logic/auth.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                const messageEl = document.getElementById('message');
                messageEl.className = `message ${data.sukses ? 'success' : 'error'} show`;
                messageEl.textContent = data.pesan;

                if (data.sukses) {
                    sessionStorage.clear();
                    setTimeout(() => location.href = 'B_dashboard.php', 1500);
                }
            } catch (e) {
                console.error("Fetch error:", e);
                document.getElementById('message').className = 'message error show';
                document.getElementById('message').textContent = 'Gagal terhubung ke server.';
            }
        });

        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedData();
            updateKodePosCounter(document.getElementById('kode_pos').value.replace(/\D/g, '').length);
        });
    </script>
</body>

</html>