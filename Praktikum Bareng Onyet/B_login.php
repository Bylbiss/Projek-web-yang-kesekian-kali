<?php
session_start();
require 'logic/auth.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABclinic - Masuk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="ABclinic.jpg">
</head>

<body class="login-page">
    <div class="image-side"></div>

    <div class="form-side">
        <!-- Logo container di dalam form-side -->
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

        <button class="toggle-btn">
            <a href="B_registrasi.php" style="text-decoration: none; color: inherit; display: block; width: 100%;">
                Anda pengguna baru di ABclinic? Daftar disini
            </a>
        </button>

        <div class="divider">
            <span>ATAU</span>
        </div>

        <div class="form-group">
            <input type="email" id="login-email" placeholder="john@example.com" autocomplete="email">
        </div>
        <div class="form-group password-toggle">
            <input type="password" id="login-password" placeholder="Kata Sandi" autocomplete="current-password">
            <i class="far fa-eye" id="toggle-login-password"></i>
        </div>
        <button id="login-submit" class="login-button" disabled>Masuk</button>

        <div id="message" class="message"></div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('toggle-login-password').addEventListener('click', function() {
            const input = document.getElementById('login-password');
            const icon = this;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Enable/disable button
        const email = document.getElementById('login-email');
        const password = document.getElementById('login-password');
        const button = document.getElementById('login-submit');

        function updateButtonState() {
            button.disabled = !email.value.trim() || !password.value.trim();
        }

        email.addEventListener('input', updateButtonState);
        password.addEventListener('input', updateButtonState);

        // Initialize button state
        updateButtonState();

        // Handle form submission
        document.getElementById('login-submit').addEventListener('click', async () => {
            const msg = document.getElementById('message');
            msg.className = 'message';
            msg.textContent = '';

            // Tampilkan loading
            const button = document.getElementById('login-submit');
            const originalText = button.textContent;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            button.disabled = true;

            try {
                const res = await fetch('logic/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        aksi: 'login',
                        email: email.value.trim(),
                        password: password.value
                    })
                });

                const data = await res.json();

                msg.classList.add('show');
                if (data.sukses) {
                    msg.classList.add('success');
                    msg.textContent = data.pesan || 'Login berhasil!';
                    // Redirect setelah login berhasil
                    setTimeout(() => {
                        window.location.href = 'B_dashboard.php';
                    }, 1000);
                } else {
                    msg.classList.add('error');
                    msg.textContent = data.pesan || 'Login gagal. Periksa email dan kata sandi Anda.';
                    // Reset button
                    button.textContent = originalText;
                    button.disabled = false;
                }
            } catch (err) {
                console.error('Login error:', err);
                msg.className = 'message error show';
                msg.textContent = 'Terjadi kesalahan jaringan. Coba lagi.';
                // Reset button
                button.textContent = originalText;
                button.disabled = false;
            }
        });

        // Allow form submission with Enter key
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                if (!button.disabled) {
                    document.getElementById('login-submit').click();
                }
            }
        });
    </script>
</body>

</html>