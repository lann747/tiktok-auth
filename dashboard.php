<?php
session_start();

// Cek apakah user sudah login dengan benar
if($_SESSION['status'] != "login"){
    header("Location: index.php");
    exit;
}

$nama = $_SESSION['user']['nama'];
$email = $_SESSION['user']['email'];
$user_id = $_SESSION['user']['id'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - TikTok Style</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
        background: #000000;
        color: #ffffff;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-image:
            radial-gradient(circle at 20% 80%, rgba(255, 0, 128, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(0, 242, 234, 0.1) 0%, transparent 50%);
    }

    /* Navigation Bar */
    .navbar {
        background: rgba(22, 24, 28, 0.95);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 15px 30px;
        backdrop-filter: blur(10px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .nav-logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tiktok-logo {
        width: 36px;
        height: 36px;
    }

    .tiktok-logo svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0 3px 10px rgba(255, 0, 80, 0.3));
    }

    .nav-logo-text {
        font-size: 20px;
        font-weight: 700;
        background: linear-gradient(90deg, #FF0050, #00F2EA);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FF0050, #00F2EA);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        color: white;
    }

    .logout-button {
        background: rgba(255, 0, 80, 0.2);
        border: 1px solid rgba(255, 0, 80, 0.3);
        color: #FF7BAC;
        padding: 8px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .logout-button:hover {
        background: rgba(255, 0, 80, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 0, 80, 0.2);
    }

    /* Main Content */
    .dashboard-container {
        flex: 1;
        padding: 40px 20px;
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
    }

    .welcome-section {
        background: rgba(22, 24, 28, 0.7);
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        text-align: center;
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .welcome-icon {
        font-size: 60px;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #FF0050, #00F2EA);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .welcome-title {
        font-size: 32px;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #FF0050, #00F2EA);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .welcome-subtitle {
        color: #A8A8A8;
        font-size: 16px;
        margin-bottom: 30px;
    }

    .user-name {
        font-size: 42px;
        font-weight: 800;
        margin: 10px 0;
        color: #FFFFFF;
        text-shadow: 0 0 20px rgba(255, 0, 128, 0.3);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: rgba(22, 24, 28, 0.7);
        border-radius: 16px;
        padding: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255, 0, 80, 0.3);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }

    .stat-icon {
        font-size: 32px;
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 5px;
        background: linear-gradient(90deg, #FF0050, #00F2EA);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: #A8A8A8;
        font-size: 14px;
        font-weight: 600;
    }

    /* Quick Actions */
    .actions-title {
        font-size: 24px;
        margin-bottom: 25px;
        color: #FFFFFF;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .action-card {
        background: rgba(22, 24, 28, 0.7);
        border-radius: 16px;
        padding: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        text-decoration: none;
        color: #FFFFFF;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .action-card:hover {
        transform: translateY(-3px);
        border-color: rgba(0, 242, 234, 0.3);
        box-shadow: 0 10px 25px rgba(0, 242, 234, 0.2);
    }

    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FF0050, #00F2EA);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .action-content h3 {
        font-size: 18px;
        margin-bottom: 5px;
        color: #FFFFFF;
    }

    .action-content p {
        color: #A8A8A8;
        font-size: 14px;
    }

    /* Footer */
    .footer {
        text-align: center;
        padding: 25px;
        background: rgba(22, 24, 28, 0.95);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 40px;
        color: #6C6C6C;
        font-size: 14px;
        backdrop-filter: blur(10px);
    }

    .copyright {
        margin-bottom: 10px;
    }

    .footer-links {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .footer-links a {
        color: #A8A8A8;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: #00F2EA;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar {
            padding: 15px 20px;
        }

        .dashboard-container {
            padding: 30px 15px;
        }

        .welcome-section {
            padding: 30px 20px;
        }

        .welcome-title {
            font-size: 28px;
        }

        .user-name {
            font-size: 36px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .actions-grid {
            grid-template-columns: 1fr;
        }

        .user-info span {
            display: none;
        }
    }

    /* Animations */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .floating {
        animation: float 3s ease-in-out infinite;
    }

    .pulse-effect {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 0, 80, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(255, 0, 80, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 0, 80, 0);
        }
    }

    .glow {
        animation: glow 2s ease-in-out infinite alternate;
    }

    @keyframes glow {
        from {
            text-shadow: 0 0 10px rgba(255, 0, 80, 0.5);
        }

        to {
            text-shadow: 0 0 20px rgba(255, 0, 80, 0.8), 0 0 30px rgba(255, 0, 80, 0.6);
        }
    }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-logo">
            <div class="tiktok-logo">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="tiktok-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#FF0050;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#00F2EA;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <path
                        d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.773 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z"
                        fill="url(#tiktok-gradient)" />
                </svg>
            </div>
            <div class="nav-logo-text">Dashboard</div>
        </div>

        <div class="user-info">
            <span>Halo, <strong><?php echo $nama; ?></strong></span>
            <div class="user-avatar">
                <?php echo strtoupper(substr($nama, 0, 1)); ?>
            </div>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-icon">🎉</div>
            <h1 class="welcome-title">Selamat Datang Kembali!</h1>
            <p class="welcome-subtitle">Anda berhasil login ke sistem kami</p>
            <div class="user-name glow"><?php echo $nama; ?></div>
            <p class="welcome-subtitle"><?php echo $email; ?></p>
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👤</div>
                <div class="stat-value">ID: <?php echo $user_id; ?></div>
                <div class="stat-label">User ID Anda</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value"><?php echo date('d M Y'); ?></div>
                <div class="stat-label">Hari ini</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🕒</div>
                <div class="stat-value"><?php echo date('H:i'); ?></div>
                <div class="stat-label">Waktu Login</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value">Aktif</div>
                <div class="stat-label">Status Akun</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="actions-title">Quick Actions</h2>
        <div class="actions-grid">
            <a href="#" class="action-card">
                <div class="action-icon">👤</div>
                <div class="action-content">
                    <h3>Edit Profil</h3>
                    <p>Ubah informasi profil Anda</p>
                </div>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">🔒</div>
                <div class="action-content">
                    <h3>Ubah Password</h3>
                    <p>Perbarui kata sandi Anda</p>
                </div>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">⚙️</div>
                <div class="action-content">
                    <h3>Pengaturan</h3>
                    <p>Atur preferensi akun</p>
                </div>
            </a>

            <a href="logout.php" class="action-card">
                <div class="action-icon">🚪</div>
                <div class="action-content">
                    <h3>Logout</h3>
                    <p>Keluar dari akun Anda</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> TikTok Style Dashboard. All rights reserved.
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Help Center</a>
        </div>
    </div>
</body>

</html>