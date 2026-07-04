<footer>
    <div>
        <h3>FOR PET PARENTS</h3>
        <p>Portal Pemilik Hewan</p>
        <div class="app-store">
            <img src="assets/appstore.jpg" alt="App Store">
            <img src="assets/googleplay.jpg" alt="Google Play">
        </div>
    </div>

    <div>
        <h3>BANTUAN</h3>
        <p>Temukan dokter hewan terdekat</p>
        <p><a href="I_FAQ.php">Pertanyaan Umum (FAQ)</a></p>
        <p>support.id@abpaw.com</p>
        <p><i class="fab fa-whatsapp"></i> <a href="https://wa.me/qr/EWNIZHEANZDNK1">Hubungi via WhatsApp</a></p>
    </div>

    <div>
        <h3>SOCIAL</h3>
        <div class="social-icons">
            <a href="https://www.instagram.com/bylbiss_lhq?igsh=MWo5enU5M2NxeG9iMA=="><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook"></i></a>
        </div>
    </div>
</footer>

<script>
    // User Dropdown Functionality - IMPROVED
    const userDropdownContainer = document.getElementById('userDropdownContainer');
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');

    // Ini adalah fungsi untuk dropdown menu profil user 
    // Kita ambil elemen HTML yang dibutuhkan menggunakan ID-nya
    if (userDropdownBtn) {
        // Toggle dropdown - buka/tutup menu
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Mencegah event klik menyebar ke elemen lain
            userDropdownContainer.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdownContainer.contains(e.target)) {
                userDropdownContainer.classList.remove('active');
            }
        });

        // Mencegah dropdown tertutup ketika mengklik di dalam menu dropdown
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Logout confirmation
    function confirmLogout(event) {
        event.preventDefault();
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logic/logout.php';
        }
        return false;
    }

    // Close dropdown when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (userDropdownContainer) {
                userDropdownContainer.classList.remove('active');
            }
        }
    });
</script>