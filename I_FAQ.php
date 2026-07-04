<?php
session_start();
require "logic/auth.php";
require "logic/koneksi.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Klinik Hewan</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="ABclinic.jpg">
</head>

<body class="faq-page">
    <?php require_once 'components/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Ada Pertanyaan? </h1>
            <p>Temukan jawaban untuk pertanyaan umum seputar layanan kesehatan hewan peliharaan kami</p>
        </div>
    </section>


    <!-- FAQ Section -->
    <section class="container">
        <h2 class="section-title">FAQ</h2>
        <p class="section-subtitle">pertanyaan yang paling sering diajukan oleh pemilik hewan peliharaan</p>
       
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari pertanyaan atau kata kunci...">
            <i class="fas fa-search"></i>
        </div>
       
        <div class="search-results" id="searchResults">
            Ketik kata kunci untuk mencari pertanyaan
        </div>
       
        <div class="faq-container" id="faqContainer">
            <div class="faq-item active">
                <div class="faq-question">
                    <span>Bagaimana cara membuat janji konsultasi dengan dokter hewan?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Anda dapat membuat janji konsultasi dengan beberapa cara:</p>
                    <ul style="margin-left: 20px; margin-top: 10px;">
                        <li>Melalui website kami dengan mengklik tombol "Konsultasi Sekarang"</li>
                        <li>Menghubungi kami via telepon di 021-1234-5678</li>
                        <li>Mengunjungi klinik kami langsung untuk membuat janji</li>
                        <li>Menggunakan aplikasi mobile AB Paw yang dapat diunduh di App Store dan Google Play</li>
                    </ul>
                </div>
            </div>
           
            <div class="faq-item">
                <div class="faq-question">
                    <span>Apakah konsultasi online gratis?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Konsultasi pertama untuk evaluasi awal biasanya gratis. Namun, untuk konsultasi lanjutan atau kondisi tertentu yang memerlukan diagnosis mendalam, akan dikenakan biaya sesuai dengan layanan yang diberikan. Detail biaya akan diinformasikan secara transparan sebelum konsultasi dimulai.</p>
                </div>
            </div>
           
            <div class="faq-item">
                <div class="faq-question">
                    <span>Platform apa yang digunakan untuk konsultasi online?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Kami menggunakan platform video conference yang aman dan mudah digunakan seperti Zoom, Google Meet, atau platform khusus kami yang terintegrasi dengan sistem rekam medis. Link akses akan dikirimkan melalui email atau WhatsApp setelah Anda membuat janji konsultasi.</p>
                </div>
            </div>
           
            <div class="faq-item">
                <div class="faq-question">
                    <span>Bagaimana jika saya perlu membatalkan janji konsultasi?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Anda dapat membatalkan janji konsultasi minimal 4 jam sebelum jadwal melalui akun Anda di website atau dengan menghubungi customer service kami di 021-1234-5678. Pembatalan yang dilakukan kurang dari 4 jam sebelum jadwal mungkin akan dikenakan biaya pembatalan sebesar 50% dari biaya konsultasi.</p>
                </div>
            </div>
           
            <div class="faq-item">
                <div class="faq-question">
                    <span>Apakah dokter hewan yang tersedia bersertifikat?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Ya, semua dokter hewan di platform kami adalah profesional bersertifikat dan memiliki pengalaman praktik yang memadai. Mereka telah lulus uji kompetensi dan memiliki STR (Surat Tanda Registrasi) yang masih berlaku. Informasi lengkap tentang kredensial dokter dapat dilihat di profil masing-masing dokter di website kami.</p>
                </div>
            </div>
           
            <div class="faq-item">
                <div class="faq-question">
                    <span>Bagaimana cara mendapatkan resep obat untuk hewan peliharaan?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Setelah konsultasi, jika dokter menentukan bahwa hewan peliharaan Anda memerlukan obat, resep akan diberikan secara elektronik. Anda dapat menebus resep tersebut di apotek hewan terdekat atau memesan melalui layanan delivery kami yang bekerjasama dengan apotek hewan terpercaya.</p>
                </div>
            </div>
           
            <div class="faq-item">
                <div class="faq-question">
                    <span>Apakah layanan ini tersedia 24 jam?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Kami menyediakan layanan konsultasi darurat 24 jam untuk kondisi gawat darurat. Untuk konsultasi rutin, layanan tersedia dari pukul 08.00 hingga 22.00 WIB. Tim dokter jaga darurat selalu siap membantu hewan peliharaan Anda kapan pun diperlukan.</p>
                </div>
            </div>
           
            <div class="faq-item">
                <div class="faq-question">
                    <span>Bagaimana jika hewan peliharaan saya perlu perawatan intensif?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Jika hewan peliharaan Anda memerlukan perawatan intensif, dokter akan merekomendasikan untuk membawanya ke klinik atau rumah sakit hewan terdekat. Kami dapat membantu merujuk ke fasilitas kesehatan hewan yang tepat dan berkoordinasi dengan dokter di sana untuk kelanjutan perawatan.</p>
                </div>
            </div>
        </div>


        <div class="no-results" id="noResults">
            <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
            <h3>Tidak ada hasil ditemukan</h3>
            <p>Coba gunakan kata kunci yang berbeda atau lihat semua pertanyaan di atas</p>
        </div>
    </section>


    <!-- Contact Section -->
    <section class="container">
        <div class="contact-section">
            <h2>Masih Punya Pertanyaan?</h2>
            <p>Jika Anda tidak menemukan jawaban yang Anda cari, jangan ragu untuk menghubungi tim layanan pelanggan kami.</p>
            <div class="contact-buttons">
                <a href="#" class="contact-btn">
                    <i class="fas fa-phone-alt"></i>
                    Hubungi Kami
                </a>
                <a href="#" class="contact-btn">
                    <i class="fas fa-envelope"></i>
                    Kirim Email
                </a>
            </div>
        </div>
    </section>

    <?php require_once 'components/footer.php'; ?>

    <script>
        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentNode;
               
                // Close all other items
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
               
                // Toggle current item
                item.classList.toggle('active');
            });
        });


        // Enhanced Search functionality
        const searchInput = document.getElementById('searchInput');
        const faqItems = document.querySelectorAll('.faq-item');
        const searchResults = document.getElementById('searchResults');
        const noResults = document.getElementById('noResults');
        const faqContainer = document.getElementById('faqContainer');


        // Store original content for highlighting
        const originalContents = [];
        faqItems.forEach((item, index) => {
            originalContents[index] = {
                question: item.querySelector('.faq-question span').innerHTML,
                answer: item.querySelector('.faq-answer').innerHTML
            };
        });


        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let foundCount = 0;
           
            if (searchTerm === '') {
                // Reset to original state
                faqItems.forEach((item, index) => {
                    item.style.display = 'block';
                    item.querySelector('.faq-question span').innerHTML = originalContents[index].question;
                    item.querySelector('.faq-answer').innerHTML = originalContents[index].answer;
                });
                searchResults.textContent = 'Ketik kata kunci untuk mencari pertanyaan';
                noResults.style.display = 'none';
                faqContainer.style.display = 'block';
                return;
            }
           
            faqItems.forEach((item, index) => {
                const question = originalContents[index].question.toLowerCase();
                const answer = originalContents[index].answer.toLowerCase();
               
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    foundCount++;
                   
                    // Highlight matching text
                    const highlightedQuestion = highlightText(originalContents[index].question, searchTerm);
                    const highlightedAnswer = highlightText(originalContents[index].answer, searchTerm);
                   
                    item.querySelector('.faq-question span').innerHTML = highlightedQuestion;
                    item.querySelector('.faq-answer').innerHTML = highlightedAnswer;
                   
                    // Auto-expand items with matches
                    item.classList.add('active');
                } else {
                    item.style.display = 'none';
                }
            });
           
            // Update search results message
            if (foundCount > 0) {
                searchResults.textContent = `Ditemukan ${foundCount} pertanyaan yang sesuai dengan "${searchTerm}"`;
                noResults.style.display = 'none';
                faqContainer.style.display = 'block';
            } else {
                searchResults.textContent = '';
                noResults.style.display = 'block';
                faqContainer.style.display = 'none';
            }
        });


        // Function to highlight matching text
        function highlightText(text, searchTerm) {
            if (!searchTerm) return text;
           
            const regex = new RegExp(`(${escapeRegex(searchTerm)})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }


        // Function to escape special regex characters
        function escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }


        // Clear search when clicking on search icon
        document.querySelector('.search-box i').addEventListener('click', function() {
            searchInput.value = '';
            searchInput.focus();
            searchInput.dispatchEvent(new Event('input'));
        });


        // Add keyboard shortcut (Ctrl+K or Cmd+K) to focus search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    </script>
</body>
</html>

