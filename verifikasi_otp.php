<?php
session_start();
include 'koneksi.php';

// Cek apakah ada session sementara
if (!isset($_SESSION['temp_user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if (isset($_POST['verifikasi'])) {
    $otp_input = mysqli_real_escape_string($koneksi, $_POST['otp_code']);
    $user_id = $_SESSION['temp_user_id'];
    $current_time = date('Y-m-d H:i:s');

    // Cek OTP dan Expiry di Database
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id' AND otp_code='$otp_input' AND otp_expiry > '$current_time'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Hapus OTP agar tidak bisa dipakai ulang
        mysqli_query($koneksi, "UPDATE users SET otp_code=NULL, otp_expiry=NULL WHERE id='$user_id'");
        
        // Buat Session Login
        $_SESSION['status'] = "login";
        $_SESSION['user'] = [
            'id' => $data['id'],
            'nama' => $data['nama'],
            'email' => $data['email']
        ];
        
        // Hapus session temporary
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_user_email']);
        if (isset($_SESSION['temp_otp'])) unset($_SESSION['temp_otp']);
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Kode OTP salah atau sudah kadaluarsa!";
    }
}

// Untuk debugging - tampilkan OTP jika ada di session
$debug_otp = isset($_SESSION['temp_otp']) ? $_SESSION['temp_otp'] : '';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Verifikasi OTP - TikTok Style</title>
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
        align-items: center;
        justify-content: center;
        padding: 20px;
        background-image:
            radial-gradient(circle at 20% 80%, rgba(255, 0, 128, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(0, 242, 234, 0.1) 0%, transparent 50%);
    }

    .otp-container {
        width: 100%;
        max-width: 420px;
        padding: 40px 30px;
        background: rgba(22, 24, 28, 0.95);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .logo {
        text-align: center;
        margin-bottom: 25px;
    }

    .tiktok-logo {
        width: 70px;
        height: 70px;
        margin: 0 auto 15px;
    }

    .tiktok-logo svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3));
    }

    .logo-text {
        font-size: 28px;
        font-weight: 700;
        background: linear-gradient(90deg, #FF0050, #00F2EA);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .tagline {
        text-align: center;
        color: #A8A8A8;
        font-size: 14px;
        margin-bottom: 25px;
    }

    .email-info {
        background: rgba(0, 242, 234, 0.1);
        border: 1px solid rgba(0, 242, 234, 0.2);
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 20px;
        text-align: center;
        font-size: 13px;
        color: #7BFFEA;
    }

    .error-message {
        background: rgba(255, 0, 80, 0.15);
        border: 1px solid rgba(255, 0, 80, 0.3);
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 20px;
        text-align: center;
        font-size: 14px;
        color: #FF7BAC;
    }

    .debug-info {
        background: rgba(255, 204, 0, 0.15);
        border: 1px solid rgba(255, 204, 0, 0.3);
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 20px;
        text-align: center;
        font-size: 13px;
        color: #FFCC00;
    }

    .debug-info strong {
        display: block;
        margin-bottom: 5px;
    }

    .otp-input-group {
        margin-bottom: 25px;
    }

    .otp-label {
        display: block;
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #E1E3E6;
    }

    .otp-input {
        width: 100%;
        padding: 18px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        font-size: 20px;
        text-align: center;
        letter-spacing: 5px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .otp-input:focus {
        outline: none;
        border-color: #FF0050;
        box-shadow: 0 0 0 3px rgba(255, 0, 80, 0.2);
    }

    .verifikasi-button {
        width: 100%;
        padding: 18px;
        background: linear-gradient(90deg, #FF0050, #00F2EA);
        border: none;
        border-radius: 12px;
        color: white;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        letter-spacing: 0.5px;
    }

    .verifikasi-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(255, 0, 80, 0.4);
    }

    .timer {
        text-align: center;
        margin-top: 15px;
        font-size: 14px;
        color: #A8A8A8;
    }

    .resend-link {
        text-align: center;
        margin-top: 20px;
    }

    .resend-link a {
        color: #00F2EA;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }

    .resend-link a:hover {
        text-decoration: underline;
    }

    .back-link {
        text-align: center;
        margin-top: 25px;
    }

    .back-link a {
        color: #A8A8A8;
        text-decoration: none;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .back-link a:hover {
        color: #FFFFFF;
    }

    @media (max-width: 480px) {
        .otp-container {
            padding: 30px 20px;
        }

        .logo-text {
            font-size: 24px;
        }

        .tiktok-logo {
            width: 60px;
            height: 60px;
        }
    }
    </style>
    <script>
    // Timer countdown
    let timeLeft = 300; // 5 menit dalam detik

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timer').textContent =
            `Kode berlaku: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft > 0) {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        } else {
            document.getElementById('timer').textContent = "Kode OTP telah kadaluarsa";
            document.getElementById('timer').style.color = "#FF0050";
        }
    }

    // Auto focus dan move antara input OTP
    function moveToNext(current, nextFieldID) {
        if (current.value.length >= current.maxLength) {
            document.getElementById(nextFieldID).focus();
        }
    }

    window.onload = function() {
        updateTimer(); // Mulai timer
        document.getElementById('otp1').focus(); // Auto focus ke input pertama
    }
    </script>
</head>

<body>
    <div class="otp-container">
        <div class="logo">
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
            <div class="logo-text">Verifikasi OTP</div>
        </div>

        <p class="tagline">Masukkan kode 6 digit yang dikirim ke email Anda</p>

        <?php if (isset($_SESSION['temp_user_email'])): ?>
        <div class="email-info">
            📧 Kode OTP telah dikirim ke:<br>
            <strong><?php echo $_SESSION['temp_user_email']; ?></strong>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($debug_otp)): ?>
        <div class="debug-info">
            <strong>DEVELOPMENT MODE</strong>
            Kode OTP: <strong><?php echo $debug_otp; ?></strong><br>
            <small>Hanya untuk testing. Hapus di production!</small>
        </div>
        <?php endif; ?>

        <form method="POST" id="otpForm">
            <div class="otp-input-group">
                <label class="otp-label">Kode OTP (6 digit)</label>
                <input type="text" name="otp_code" class="otp-input" placeholder="123456" maxlength="6" required
                    pattern="[0-9]{6}" title="Masukkan 6 digit angka">
            </div>

            <button type="submit" name="verifikasi" class="verifikasi-button">
                VERIFIKASI
            </button>
        </form>

        <div class="timer" id="timer">
            Kode berlaku: 05:00
        </div>

        <div class="resend-link">
            <a href="#" onclick="alert('Fitur kirim ulang OTP belum diimplementasikan'); return false;">
                Kirim ulang kode OTP
            </a>
        </div>

        <div class="back-link">
            <a href="logout.php">⬅ Kembali ke halaman login</a>
        </div>
    </div>
</body>

</html>