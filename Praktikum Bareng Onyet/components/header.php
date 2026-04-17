<?php
// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/logic/auth.php';

// Fungsi helper untuk membuat URL
function url($path)
{
    return $path; // Path relatif langsung
}
?>
<header>
    <div class="logo-container">
        <a href="<?php echo url('B_dashboard.php'); ?>" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
            <div class="logo-icon"><i class="fas fa-paw"></i></div>
            <div class="logo-text">AB Paw</div>
        </a>
    </div>
    <div class="header-right">
        <!-- Menu Layanan Kami & FAQ -->
        <div class="nav-links">
            <a href="<?php echo url('B_layanan_kami.php'); ?>">Layanan Kami</a>
            <a href="<?php echo url('I_FAQ.php'); ?>">FAQ</a>
        </div>

        <?php if (sudahLogin()): ?>
            <a href="<?php echo url('B_profil.php'); ?>" class="user-profile-link">
                <div style="display: flex; align-items: center; gap: 8px; color: rgb(255, 105, 180); font-weight: 500;">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['nama_pemilik'] ?? 'User'); ?></span>
                </div>
            </a>
        <?php else: ?>
            <a href="<?php echo url('B_login.php'); ?>" class="btn-login">Masuk</a>
        <?php endif; ?>
    </div>
</header>