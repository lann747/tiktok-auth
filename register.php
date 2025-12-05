<?php
include "koneksi.php";

$msg = "";
$success_msg = "";

if (isset($_GET['register']) && $_GET['register'] == 'success') {
    $success_msg = "Registrasi berhasil! Silakan login.";
}

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($nama == "" || $email == "" || $_POST['password'] == "") {
        $msg = "Semua field harus diisi!";
    } else {
        $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($cek) > 0) {
            $msg = "Email sudah digunakan!";
        } else {
            $query = mysqli_query($koneksi, "
                INSERT INTO users(nama, email, password)
                VALUES('$nama', '$email', '$password')
            ");

            if ($query) {
                header("Location: index.php?register=success");
                exit;
            } else {
                $msg = "Gagal registrasi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - TikTok Style</title>
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
        
        .register-container {
            width: 100%;
            max-width: 460px;
            padding: 40px 30px;
            background: rgba(22, 24, 28, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 25px;
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
        
        .success-message {
            background: rgba(0, 242, 234, 0.15);
            border: 1px solid rgba(0, 242, 234, 0.3);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 24px;
            text-align: center;
            font-size: 14px;
            color: #7BFFEA;
            animation: pulseSuccess 2s infinite;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes pulseSuccess {
            0% { box-shadow: 0 0 0 0 rgba(0, 242, 234, 0.3); }
            70% { box-shadow: 0 0 0 10px rgba(0, 242, 234, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 242, 234, 0); }
        }
        
        .form-group {
            margin-bottom: 20px;
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
        
        .password-requirements {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 12px 15px;
            margin-top: 8px;
            font-size: 12px;
            color: #A8A8A8;
        }
        
        .password-requirements ul {
            margin-left: 15px;
        }
        
        .password-requirements li {
            margin-bottom: 4px;
        }
        
        .register-button {
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
        
        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 0, 80, 0.4);
        }
        
        .register-button:active {
            transform: translateY(0);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #6C6C6C;
            font-size: 14px;
        }
        
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .divider span {
            padding: 0 15px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
        }
        
        .login-link a {
            color: #00F2EA;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .login-link a:hover {
            color: #FF0050;
            text-decoration: underline;
        }
        
        .login-link a::before {
            content: '←';
            font-size: 18px;
            transition: transform 0.3s ease;
        }
        
        .login-link a:hover::before {
            transform: translateX(-4px);
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #6C6C6C;
        }
        
        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
                border-radius: 16px;
                max-width: 400px;
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
            0% { box-shadow: 0 0 0 0 rgba(255, 0, 80, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 0, 80, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 0, 80, 0); }
        }
        
        .logo-glitch {
            animation: glitch 3s infinite;
        }
        
        @keyframes glitch {
            0% { filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3)); }
            95% { filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3)); }
            96% { filter: drop-shadow(5px 0 15px rgba(0, 242, 234, 0.5)); }
            97% { filter: drop-shadow(-5px 0 15px rgba(255, 0, 80, 0.5)); }
            98% { filter: drop-shadow(0 -5px 15px rgba(0, 242, 234, 0.5)); }
            99% { filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3)); }
            100% { filter: drop-shadow(0 5px 15px rgba(255, 0, 80, 0.3)); }
        }
        
        .progress-bar {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #FF0050, #00F2EA);
            width: 0%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <div class="tiktok-logo logo-glitch">
                <!-- SVG Logo TikTok -->
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="tiktok-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#FF0050;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#00F2EA;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.773 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z" fill="url(#tiktok-gradient)"/>
                </svg>
            </div>
            <div class="logo-text">Daftar</div>
        </div>
        
        <p class="tagline">Buat akun baru untuk mulai menggunakan aplikasi</p>
        
        <?php if ($msg): ?>
            <div class="message"><?php echo $msg; ?></div>
        <?php endif; ?>
        
        <?php if ($success_msg): ?>
            <div class="success-message"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <form method="POST" id="registerForm">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="input-field" placeholder="Masukkan nama lengkap" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="input-field" placeholder="nama@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="input-field" placeholder="Buat password yang kuat" required onkeyup="checkPasswordStrength(this.value)">
                <div class="progress-bar">
                    <div class="progress-fill" id="passwordStrength"></div>
                </div>
                <div class="password-requirements">
                    <ul>
                        <li>Minimal 8 karakter</li>
                        <li>Mengandung huruf besar dan kecil</li>
                        <li>Mengandung angka</li>
                    </ul>
                </div>
            </div>
            
            <button type="submit" name="register" class="register-button pulse">BUAT AKUN</button>
        </form>
        
        <div class="divider"><span>ATAU</span></div>
        
        <div class="login-link">
            <a href="index.php">Sudah punya akun? Masuk sekarang</a>
        </div>
    </div>
    
    <div class="footer">
        &copy; <?php echo date('Y'); ?> - Inspired by TikTok UI
    </div>
    
    <script>
        function checkPasswordStrength(password) {
            let strength = 0;
            const progressBar = document.getElementById('passwordStrength');
            
            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[a-z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            
            progressBar.style.width = strength + '%';
            
            // Change color based on strength
            if (strength < 50) {
                progressBar.style.background = '#FF0050';
            } else if (strength < 75) {
                progressBar.style.background = '#FF9966';
            } else {
                progressBar.style.background = 'linear-gradient(90deg, #FF0050, #00F2EA)';
            }
        }
        
        // Check for email parameter in URL for success message
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                const successDiv = document.createElement('div');
                successDiv.className = 'success-message';
                successDiv.textContent = 'Registrasi berhasil! Silakan login.';
                document.querySelector('.register-container').insertBefore(successDiv, document.querySelector('form'));
            }
        }
    </script>
</body>
</html>