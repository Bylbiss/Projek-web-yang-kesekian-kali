<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perkenalan Kelompok</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="ABclinic.jpg">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #ffe6f2 0%, #ffccdd 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #5a003a;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(255, 105, 180, 0.15);
            position: relative;
            overflow: hidden;
        }

        /* Dekorasi background */
        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 182, 193, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: 0;
            opacity: 0.5;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 3.2rem;
            color: #ff3385;
            text-shadow: 3px 3px 0px rgba(255, 182, 193, 0.4);
            margin-bottom: 10px;
            letter-spacing: 1.5px;
        }

        .header p {
            font-size: 1.2rem;
            color: #a60055;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .team-container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 60px;
            position: relative;
            z-index: 1;
        }

        .member {
            width: 360px;
            /* Dari 320px menjadi 360px (opsional) */
            text-align: center;
            padding: 30px;
            /* Dari 25px menjadi 30px */
            background: linear-gradient(145deg, #fff5f9, #ffe6f0);
            border-radius: 22px;
            /* Dari 20px menjadi 22px */
            box-shadow: 0 15px 30px rgba(255, 105, 180, 0.15);
            /* Shadow lebih kuat */
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .member:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 30px rgba(255, 105, 180, 0.2);
        }

        .member::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #ff66b2, #ff3385);
        }

        /* Foto kotak yang lebih besar */
        .member-img {
            width: 280px;
            /* Dari 220px menjadi 280px - lebih lebar */
            height: 280px;
            /* Dari 220px menjadi 280px - lebih tinggi */
            overflow: hidden;
            margin: 0 auto 25px;
            border: 6px solid #ffccdd;
            /* Border sedikit lebih tebal */
            box-shadow: 0 15px 25px rgba(255, 105, 180, 0.3);
            /* Shadow lebih kuat */
            position: relative;
            border-radius: 15px;
            /* Sudut lebih membulat */
            background: linear-gradient(145deg, #ffd9e8, #ffcce0);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .member-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
            border-radius: 10px;
            /* Dari 8px menjadi 10px */
        }

        .member:hover .member-img img {
            transform: scale(1.08);
            filter: brightness(1.05);
        }

        /* Efek dekorasi di sudut foto */
        .member-img::before {
            content: '';
            position: absolute;
            top: -4px;
            /* Dari -3px menjadi -4px */
            left: -4px;
            /* Dari -3px menjadi -4px */
            width: 25px;
            /* Dari 20px menjadi 25px */
            height: 25px;
            /* Dari 20px menjadi 25px */
            border-top: 4px solid #ff3385;
            /* Dari 3px menjadi 4px */
            border-left: 4px solid #ff3385;
            /* Dari 3px menjadi 4px */
            border-radius: 6px 0 0 0;
            /* Dari 4px menjadi 6px */
            z-index: 2;
        }

        .member-img::after {
            content: '';
            position: absolute;
            bottom: -4px;
            /* Dari -3px menjadi -4px */
            right: -4px;
            /* Dari -3px menjadi -4px */
            width: 25px;
            /* Dari 20px menjadi 25px */
            height: 25px;
            /* Dari 20px menjadi 25px */
            border-bottom: 4px solid #ff3385;
            /* Dari 3px menjadi 4px */
            border-right: 4px solid #ff3385;
            /* Dari 3px menjadi 4px */
            border-radius: 0 0 6px 0;
            /* Dari 4px menjadi 6px */
            z-index: 2;
        }

        .member-info h3 {
            font-size: 1.6rem;
            color: #cc0066;
            margin-bottom: 10px;
            padding: 0 10px;
        }

        .member-info p {
            font-size: 1.1rem;
            color: #99004d;
            background: rgba(255, 204, 221, 0.5);
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .left-member {
            order: 1;
        }

        .right-member {
            order: 3;
        }

        /* Tombol tengah bawah */
        .button-container {
            text-align: center;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }

        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 18px 45px;
            background: linear-gradient(135deg, #ff66b2, #ff3385);
            color: white;
            font-size: 1.3rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            box-shadow: 0 10px 20px rgba(255, 51, 133, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .dashboard-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(255, 51, 133, 0.4);
            background: linear-gradient(135deg, #ff3385, #ff0066);
        }

        .dashboard-btn:active {
            transform: translateY(0);
        }

        .dashboard-btn i {
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .dashboard-btn:hover i {
            transform: translateX(5px);
        }

        /* Efek kilau tombol */
        .dashboard-btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: rotate(30deg);
            transition: transform 0.7s;
        }

        .dashboard-btn:hover::after {
            transform: rotate(30deg) translate(20%, 20%);
        }

        /* Dekorasi floral */
        .floral-decor {
            position: absolute;
            z-index: 0;
            opacity: 0.4;
        }

        .floral-decor-1 {
            top: 50px;
            left: 30px;
            transform: rotate(-15deg);
            font-size: 4rem;
            color: #ff66b2;
        }

        .floral-decor-2 {
            bottom: 80px;
            right: 30px;
            transform: rotate(15deg);
            font-size: 4rem;
            color: #ff66b2;
        }

        /* Efek placeholder jika gambar tidak ditemukan */
        .member-img .placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #ffb6c1, #ff8fab);
            color: white;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .member:hover .member-img img {
            transform: scale(1.12);
            /* Dari 1.08 menjadi 1.12 - lebih besar */
            filter: brightness(1.1);
            /* Dari 1.05 menjadi 1.1 - lebih terang */
        }

        /* Responsif */
        @media (max-width: 992px) {
            .team-container {
                justify-content: center;
            }

            .member {
                width: 300px;
            }

            .member-img {
                width: 200px;
                height: 200px;
            }

            .left-member,
            .right-member {
                order: unset;
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }

            .member {
                width: 100%;
                max-width: 320px;
            }

            .member-img {
                width: 250px;
                /* Dari 220px menjadi 250px untuk mobile */
                height: 250px;
                /* Dari 220px menjadi 250px untuk mobile */
            }

            .floral-decor {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px 15px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .member-img {
                width: 220px;
                /* Dari 180px menjadi 220px untuk mobile kecil */
                height: 220px;
                /* Dari 180px menjadi 220px untuk mobile kecil */
            }

            .dashboard-btn {
                padding: 15px 30px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Dekorasi floral -->
        <div class="floral-decor floral-decor-1">
            <i class="fas fa-heart"></i>
        </div>
        <div class="floral-decor floral-decor-2">
            <i class="fas fa-heart"></i>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>Kelompok 5 - <span style="color:#ff0066;">AB</span> Paw</h1>
            <p>Selamat datang di web kelompok kami! Kami tim penuh semangat membara dan kreativitas yang meluncur tinggi untuk menciptakan karya inovatif.</p>
        </div>

        <!-- Anggota Kelompok -->
        <div class="team-container">
            <!-- Anggota Kiri -->
            <div class="member left-member">
                <div class="member-img">
                    <img src="assets/onyet2.jpg" alt="Amelia Rahma" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjIwIiBoZWlnaHQ9IjIyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjIwIiBoZWlnaHQ9IjIyMCIgZmlsbD0iI2ZmNjZiMiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNmZmZmZmYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjNlbSI+QTwvdGV4dD48L3N2Zz4=';">
                </div>
                <div class="member-info">
                    <h3>Buncis</h3>
                    <p>123240003</p>
                </div>
            </div>

            <!-- Anggota Kanan -->
            <div class="member right-member">
                <div class="member-img">
                    <img src="assets/onyet1.jpg" alt="Budi Santoso" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjIwIiBoZWlnaHQ9IjIyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjIwIiBoZWlnaHQ9IjIyMCIgZmlsbD0iI2ZmNjZiMiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiNmZmZmZmYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjNlbSI+QjwvdGV4dD48L3N2Zz4=';">
                </div>
                <div class="member-info">
                    <h3>Ipeh</h3>
                    <p>123240234</p>
                </div>
            </div>
        </div>

        <!-- Tombol Dashboard -->
        <div class="button-container">
            <a href="B_dashboard.php" class="dashboard-btn">
                <span>Masuk ke Dashboard</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <script>
        // Efek animasi tombol saat hover
        const dashboardBtn = document.querySelector('.dashboard-btn');

        dashboardBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });

        dashboardBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });

        // Efek animasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const members = document.querySelectorAll('.member');
            const header = document.querySelector('.header');

            // Animasi untuk header
            header.style.opacity = '0';
            header.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                header.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                header.style.opacity = '1';
                header.style.transform = 'translateY(0)';
            }, 300);

            // Animasi untuk anggota
            members.forEach((member, index) => {
                member.style.opacity = '0';
                member.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    member.style.transition = `opacity 0.8s ease ${index * 0.2}s, transform 0.8s ease ${index * 0.2}s`;
                    member.style.opacity = '1';
                    member.style.transform = 'translateY(0)';
                }, 500 + (index * 200));
            });

            // Animasi untuk tombol
            const buttonContainer = document.querySelector('.button-container');
            buttonContainer.style.opacity = '0';

            setTimeout(() => {
                buttonContainer.style.transition = 'opacity 0.8s ease 1s';
                buttonContainer.style.opacity = '1';
            }, 1000);

            // Cek apakah gambar berhasil dimuat
            const images = document.querySelectorAll('.member-img img');
            images.forEach(img => {
                img.onerror = function() {
                    // Jika gambar gagal dimuat, tampilkan placeholder dengan inisial
                    const altText = this.alt || '';
                    const initial = altText.charAt(0).toUpperCase() || '?';
                    this.src = `data:image/svg+xml;base64,${btoa(`
    <svg width="220" height="220" xmlns="http://www.w3.org/2000/svg">
        <rect width="220" height="220" fill="#ff66b2"/>
        <text x="50%" y="50%" font-family="Arial" font-size="48" fill="white" text-anchor="middle" dy="0.3em">${initial}</text>
    </svg>
`)}`;
                };
            });
        });
    </script>
</body>

</html>