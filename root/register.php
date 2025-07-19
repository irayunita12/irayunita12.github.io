<?php
session_start();
require 'db.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    // Ambil data dari form
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $whatsapp = preg_replace('/[^0-9]/', '', $_POST['whatsapp']); // Hanya angka
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $profile_picture = null;

    // Validasi
    if (empty($nama) || empty($email) || empty($whatsapp) || empty($password)) {
        $error = 'Semua field wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek email sudah terdaftar
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Handle file upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/profile_pictures/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                $max_size = 2 * 1024 * 1024; // 2MB
                
                if (in_array(strtolower($file_ext), $allowed_ext)) {
                    if ($_FILES['profile_picture']['size'] <= $max_size) {
                        $file_name = uniqid('profile_') . '.' . $file_ext;
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
                            $profile_picture = $file_path;
                        } else {
                            $error = 'Gagal mengupload foto profil.';
                        }
                    } else {
                        $error = 'Ukuran foto maksimal 2MB.';
                    }
                } else {
                    $error = 'Format file tidak didukung (hanya JPG, JPEG, PNG, GIF).';
                }
            }
            
            if (empty($error)) {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Simpan ke database
                $query = "INSERT INTO users (nama, email, password, whatsapp, role, profile_picture) 
                          VALUES ('$nama', '$email', '$hashed_password', '$whatsapp', 'user', " . 
                          ($profile_picture ? "'$profile_picture'" : "NULL") . ")";
                
                if (mysqli_query($conn, $query)) {
                    $success = 'Pendaftaran berhasil! Silakan login.';
                    // Kosongkan form setelah sukses
                    $_POST = array();
                } else {
                    $error = 'Pendaftaran gagal: ' . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Ira Beauty</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f5f9ff, #eceff9);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    .register-container {
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(107, 142, 255, 0.1);
        width: 100%;
        max-width: 450px;
        padding: 40px;
        text-align: center;
    }

    .logo {
        margin-bottom: 30px;
    }

    .logo img {
        width: 80px;
        height: 80px;
        object-fit: contain;
    }

    .logo h1 {
        color: #333;
        font-size: 28px;
        margin-top: 10px;
    }

    .logo h1 span {
        color: #6b8cff;
    }

    .input-group {
        margin-bottom: 20px;
        text-align: left;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }

    .input-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s;
    }

    .input-group input:focus {
        border-color: #6b8cff;
        box-shadow: 0 0 0 3px rgba(107, 142, 255, 0.2);
        outline: none;
    }

    .register-btn {
        width: 100%;
        padding: 12px;
        background-color: #6b8cff;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
    }

    .register-btn:hover {
        background-color: #5a7ae5;
    }

    .error-message {
        color: #ff4444;
        background-color: #ffebee;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .success-message {
        color: #4CAF50;
        background-color: #e8f5e9;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .login-link {
        margin-top: 20px;
        color: #666;
        font-size: 14px;
    }

    .login-link a {
        color: #6b8cff;
        text-decoration: none;
        font-weight: 500;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    /* Tambahan untuk upload foto profil */
    .profile-picture-container {
        margin-bottom: 20px;
    }

    .profile-picture-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 10px;
        border: 3px solid #6b8cff;
        display: none;
    }

    .profile-picture-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .profile-picture-upload label {
        background-color: #f0f4ff;
        color: #6b8cff;
        padding: 8px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
        margin-top: 5px;
    }

    .profile-picture-upload label:hover {
        background-color: #e0e8ff;
    }

    .profile-picture-upload input[type="file"] {
        display: none;
    }

    .file-info {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="logo">
            <img src="logo.png" alt="Ira Beauty Logo">
            <h1>Ira<span>Beauty</span></h1>
        </div>

        <?php if ($error): ?>
        <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="success-message"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="profile-picture-container">
                <div class="profile-picture-upload">
                    <img id="profile-preview" class="profile-picture-preview" src="#" alt="Preview Foto Profil">
                    <label for="profile_picture">Pilih Foto Profil</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                        onchange="previewImage(this)">
                    <div class="file-info">Format: JPG, PNG (Maks. 2MB)</div>
                </div>
            </div>

            <div class="input-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap"
                    value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required>
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="contoh@email.com"
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
            </div>

            <div class="input-group">
                <label for="whatsapp">Nomor WhatsApp</label>
                <input type="tel" id="whatsapp" name="whatsapp" placeholder="081234567890"
                    value="<?= isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : '' ?>" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Buat password (minimal 8 karakter)"
                    required>
            </div>

            <div class="input-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password"
                    required>
            </div>

            <button type="submit" name="register" class="register-btn">Daftar Sekarang</button>
        </form>

        <p class="login-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>

    <script>
    // Format nomor WhatsApp (hanya angka)
    document.getElementById('whatsapp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Preview gambar yang dipilih
    function previewImage(input) {
        const preview = document.getElementById('profile-preview');
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(file);
        }
    }
    </script>
</body>

</html>