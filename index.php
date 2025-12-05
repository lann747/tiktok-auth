<?php
// Error reporting untuk debugging - HAPUS INI DI PRODUCTION
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Cek apakah koneksi.php ada
if (!file_exists('koneksi.php')) {
    die("<div style='background: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 5px;'>
        <h3>Error: File koneksi.php tidak ditemukan</h3>
        <p>Buat file koneksi.php dengan konfigurasi database yang benar.</p>
    </div>");
}

include 'koneksi.php';

$msg = "";
$phpmailer_available = false;

// Cek apakah PHPMailer tersedia
$vendor_path = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendor_path)) {
    require_once $vendor_path;
    $phpmailer_available = true;
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // Validasi input
    if (empty($email) || empty($password)) {
        $msg = "Email dan password harus diisi!";
    } else {
        $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
        
        if (!$query) {
            $msg = "Error query: " . mysqli_error($koneksi);
        } elseif (mysqli_num_rows($query) == 1) {
            $data = mysqli_fetch_assoc($query);
            
            if (password_verify($password, $data['password'])) {
                // Generate OTP
                $otp = rand(100000, 999999);
                $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                $user_id = $data['id'];
                
                // Update OTP ke database
                $update_query = mysqli_query($koneksi, "UPDATE users SET otp_code='$otp', otp_expiry='$expiry' WHERE id='$user_id'");
                
                if (!$update_query) {
                    $msg = "Error update OTP: " . mysqli_error($koneksi);
                } else {
                    // Simpan ke session
                    $_SESSION['temp_user_id'] = $user_id;
                    $_SESSION['temp_user_email'] = $data['email'];
                    $_SESSION['temp_otp'] = $otp; // Untuk debugging
                    
                    // Coba kirim email jika PHPMailer tersedia
                    if ($phpmailer_available) {
                        try {
                            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                            
                            // Server settings untuk InfinityFree
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com'; // InfinityFree biasanya membolehkan SMTP external
                            $mail->SMTPAuth = true;
                            $mail->Username = 'your_email@gmail.com'; // GANTI dengan email Gmail Anda
                            $mail->Password = 'your_app_password'; // GANTI dengan App Password Gmail
                            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 587;
                            
                            // Atau gunakan SMTP InfinityFree (jika ada)
                            // $mail->Host = 'mail.yourdomain.infinityfreeapp.com';
                            // $mail->Username = 'noreply@yourdomain.infinityfreeapp.com';
                            // $mail->Password = 'your_email_password';
                            // $mail->Port = 465;
                            // $mail->SMTPSecure = 'ssl';
                            
                            $mail->setFrom('noreply@tiktokstyle.infinityfreeapp.com', 'TikTok Style');
                            $mail->addAddress($data['email']);
                            $mail->isHTML(true);
                            $mail->Subject = 'Kode OTP Login - TikTok Style';
                            $mail->Body = "
                                <h2>Kode OTP Login TikTok Style</h2>
                                <p>Kode OTP Anda: <strong>$otp</strong></p>
                                <p>Kode berlaku 5 menit.</p>
                            ";
                            $mail->AltBody = "Kode OTP Anda: $otp\nKode berlaku 5 menit.";
                            
                            $mail->send();
                            $_SESSION['email_sent'] = true;
                            
                        } catch (Exception $e) {
                            $_SESSION['email_sent'] = false;
                            $_SESSION['email_error'] = $mail->ErrorInfo;
                            // Tetap lanjutkan, OTP akan ditampilkan di halaman verifikasi
                        }
                    } else {
                        $_SESSION['email_sent'] = false;
                        // PHPMailer tidak tersedia, OTP akan ditampilkan
                    }
                    
                    // Redirect ke halaman verifikasi OTP
                    header("Location: verifikasi_otp.php");
                    exit;
                }
            } else {
                $msg = "Password salah!";
            }
        } else {
            $msg = "Email tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login - TikTok Style</title>
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

    .login-container {
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
        margin-bottom: 30px;
        position: relative;
    }

    .tiktok-logo {
        width: 80px;
        height: 80px;
        margin: 0 auto 15px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tiktok-logo svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3));
    }

    .logo-text {
        font-size: 32px;
        font-weight: 800;
        background: linear-gradient(90deg, #FF0050, #00F2EA);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -1px;
        position: relative;
        display: inline-block;
    }

    .logo-text::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 3px;
        bottom: -6px;
        left: 0;
        background: linear-gradient(90deg, #FF0050, #00F2EA);
        border-radius: 2px;
        transform: scaleX(0.6);
    }

    .tagline {
        text-align: center;
        color: #A8A8A8;
        font-size: 14px;
        margin-bottom: 30px;
        letter-spacing: 0.5px;
    }

    .message {
        background: rgba(255, 0, 80, 0.15);
        border: 1px solid rgba(255, 0, 80, 0.3);
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 24px;
        text-align: center;
        font-size: 14px;
        color: #FF7BAC;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .success-message {
        background: rgba(0, 242, 234, 0.15);
        border: 1px solid rgba(0, 242, 234, 0.3);
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 24px;
        text-align: center;
        font-size: 14px;
        color: #7BFFEA;
    }

    .form-group {
        margin-bottom: 24px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #E1E3E6;
    }

    .input-field {
        width: 100%;
        padding: 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .input-field:focus {
        outline: none;
        border-color: #FF0050;
        box-shadow: 0 0 0 3px rgba(255, 0, 80, 0.2);
    }

    .input-field:hover {
        border-color: rgba(255, 255, 255, 0.3);
    }

    .login-button {
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
        margin-top: 10px;
        letter-spacing: 0.5px;
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(255, 0, 80, 0.4);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .divider {
        display: flex;
        align-items: center;
        margin: 30px 0;
        color: #6C6C6C;
        font-size: 14px;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
    }

    .divider span {
        padding: 0 15px;
    }

    .register-link {
        text-align: center;
        margin-top: 25px;
    }

    .register-link a {
        color: #00F2EA;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .register-link a:hover {
        color: #FF0050;
        text-decoration: underline;
    }

    .register-link a::after {
        content: '→';
        font-size: 18px;
        transition: transform 0.3s ease;
    }

    .register-link a:hover::after {
        transform: translateX(4px);
    }

    .footer {
        text-align: center;
        margin-top: 40px;
        font-size: 12px;
        color: #6C6C6C;
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 30px 20px;
            border-radius: 16px;
        }

        .logo-text {
            font-size: 28px;
        }

        .tiktok-logo {
            width: 70px;
            height: 70px;
        }
    }

    .pulse {
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

    .logo-glitch {
        animation: glitch 3s infinite;
    }

    @keyframes glitch {
        0% {
            filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3));
        }

        95% {
            filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3));
        }

        96% {
            filter: drop-shadow(5px 0 15px rgba(0, 242, 234, 0.5));
        }

        97% {
            filter: drop-shadow(-5px 0 15px rgba(255, 0, 80, 0.5));
        }

        98% {
            filter: drop-shadow(0 -5px 15px rgba(0, 242, 234, 0.5));
        }

        99% {
            filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3));
        }

        100% {
            filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3));
        }
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <div class="tiktok-logo logo-glitch">
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
            <div class="logo-text">Login</div>
        </div>

        <p class="tagline">Masuk untuk melanjutkan ke dashboard</p>

        <?php if ($msg): ?>
        <div class="message"><?php echo $msg; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['register']) && $_GET['register'] == 'success'): ?>
        <div class="success-message">
            ✅ Registrasi berhasil! Silakan login dengan email dan password Anda.
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="input-field" placeholder="nama@email.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="input-field" placeholder="Masukkan password"
                    required>
            </div>

            <button type="submit" name="login" class="login-button pulse">MASUK</button>
        </form>

        <div class="divider"><span>ATAU</span></div>

        <div class="register-link">
            <a href="register.php">Belum punya akun? Daftar sekarang</a>
        </div>
    </div>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> - Inspired by TikTok UI
    </div>
</body>

</html>