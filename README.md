# tiktok-auth

## Deskripsi  
`tiktok-auth` adalah skrip PHP sederhana untuk menangani autentikasi & otorisasi pengguna (login / registrasi / OTP verification / session management), cocok untuk aplikasi kecil atau prototipe.  

## Fitur  
- Halaman registrasi pengguna  
- Verifikasi OTP (one-time password)  
- Halaman login / logout  
- Session management untuk pengguna yang sudah login  
- Contoh halaman dashboard setelah login  

## Prasyarat  
- PHP (versi yang kompatibel dengan proyek ini)  
- MySQL / MariaDB  
- XAMPP (di Windows) atau lingkungan server web + database yang setara  

## Instalasi & Setup dengan XAMPP  

1. Pastikan XAMPP sudah terinstall dan layanan **Apache** serta **MySQL** aktif. :contentReference[oaicite:4]{index=4}  
2. Salin folder proyek ke dalam direktori:  
C:\xampp\htdocs\tiktok-auth

php
Salin kode
3. Buka browser → akses `http://localhost/phpmyadmin` → buat database baru (misalnya `tiktok_auth`).  
4. Import file `database.sql` ke database yang baru dibuat.  
5. Buka file `koneksi.php`, lalu sesuaikan konfigurasi database:  

$host = "localhost";
$user = "root";
$pass = "";
$db   = "tiktok_auth";

Salin kode
http://localhost/tiktok-auth/
— untuk memulai dari halaman login / registrasi.

Struktur File / Direktori
pgsql
Salin kode
/               – root project  
 ├─ index.php           – halaman login  
 ├─ register.php        – halaman registrasi  
 ├─ verifikasi_otp.php  – halaman verifikasi OTP  
 ├─ dashboard.php       – contoh halaman setelah login  
 ├─ logout.php          – proses logout  
 ├─ koneksi.php         – konfigurasi koneksi database  
 └─ database.sql        – skrip SQL untuk membuat tabel user / session  
Cara Pakai
Jalankan XAMPP → start Apache & MySQL.

Akses http://localhost/tiktok-auth/ di browser.

Untuk register → buka register.php. Sistem akan menangani input & (asumsi) pengiriman/verifikasi OTP.

Setelah verifikasi → login lewat index.php.

Setelah login → akses dashboard.php.

Untuk logout → akses logout.php.

Catatan & Peringatan
Proyek ini hanya contoh / prototipe — tidak disarankan langsung dipakai di produksi tanpa audit keamanan.

Tidak ada enkripsi kuat untuk password/OTP — sebaiknya implementasikan hash & validasi input.

Pastikan struktur tabel di database sesuai dengan asumsi kode. Jika ada kolom tambahan (misalnya: token, otp, expired_at), tambahkan secara manual lewat phpMyAdmin.

Gunakan SSL / HTTPS & sanitasi input jika proyek dikembangkan lebih lanjut.

Kontribusi
Jika kamu ingin menambah fitur (misalnya: enkripsi password, pengiriman OTP melalui email/SMS, validasi input lebih ketat, integrasi OAuth/API eksternal), silakan fork repositori dan kirim pull request.
