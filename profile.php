<?php
require 'auth_check.php'; // Memastikan user sudah login
require 'db.php';

$error = '';
$success = '';
$userData = [];

// Ambil data user dari database
$userId = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$userId'");
$userData = mysqli_fetch_assoc($query);

if (isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $whatsapp = preg_replace('/[^0-9]/', '', $_POST['whatsapp']);
    
    // Validasi
    if (empty($nama) || empty($email) || empty($whatsapp)) {
        $error = 'Semua field wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Update data user
        $updateQuery = "UPDATE users SET 
                        nama = '$nama',
                        email = '$email',
                        whatsapp = '$whatsapp'
                        WHERE id = '$userId'";
        
        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['nama'] = $nama; // Update session
            $_SESSION['email'] = $email;
            $success = 'Profil berhasil diperbarui!';
            $userData = array_merge($userData, $_POST); // Update tampilan form
        } else {
            $error = 'Gagal memperbarui profil: ' . mysqli_error($conn);
        }
    }
}

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi password
    if (!password_verify($current_password, $userData['password'])) {
        $error = 'Password saat ini salah!';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password baru minimal 8 karakter!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed_password' WHERE id = '$userId'");
        $success = 'Password berhasil diubah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Ira Beauty</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #f5f9ff;
        color: #333;
    }

    .profile-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .profile-header h1 {
        color: #6b8cff;
        margin-bottom: 10px;
    }

    .profile-header p {
        color: #666;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 15px;
        display: block;
        border: 3px solid #6b8cff;
    }

    .form-section {
        margin-bottom: 30px;
        padding: 20px;
        border-radius: 10px;
        background: #f8faff;
    }

    .form-section h2 {
        color: #6b8cff;
        margin-bottom: 20px;
        font-size: 1.3rem;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
    }

    .form-group input:focus {
        border-color: #6b8cff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(107, 140, 255, 0.2);
    }

    .btn {
        padding: 12px 25px;
        background: #6b8cff;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn:hover {
        background: #5a7ae5;
        transform: translateY(-2px);
    }

    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-error {
        background: #ffebee;
        color: #ff4444;
    }

    .alert-success {
        background: #e8f5e9;
        color: #4CAF50;
    }

    .tab-container {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .tab {
        padding: 10px 20px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
    }

    .tab.active {
        border-bottom-color: #6b8cff;
        color: #6b8cff;
        font-weight: 500;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
    </style>
</head>

<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="uploads/avatars/<?= $userData['avatar'] ?? 'default.png' ?>" class="profile-avatar">
            <h1><?= htmlspecialchars($userData['nama']) ?></h1>
            <p>Member sejak: <?= date('d M Y', strtotime($userData['created_at'])) ?></p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="tab-container">
            <div class="tab active" onclick="openTab('profile')">Profil Saya</div>
            <div class="tab" onclick="openTab('password')">Ubah Password</div>
        </div>

        <!-- Tab 1: Edit Profil -->
        <div id="profile" class="tab-content active">
            <div class="form-section">
                <h2>Informasi Pribadi</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($userData['nama']) ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp">Nomor WhatsApp</label>
                        <input type="tel" id="whatsapp" name="whatsapp"
                            value="<?= htmlspecialchars($userData['whatsapp']) ?>" required>
                    </div>

                    <button type="submit" name="update_profile" class="btn">Simpan Perubahan</button>
                </form>
            </div>
        </div>

        <!-- Tab 2: Ubah Password -->
        <div id="password" class="tab-content">
            <div class="form-section">
                <h2>Ubah Password</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="change_password" class="btn">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openTab(tabName) {
        // Sembunyikan semua tab content
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Tampilkan tab yang dipilih
        document.getElementById(tabName).classList.add('active');

        // Update tab navigasi
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.currentTarget.classList.add('active');
    }

    // Format nomor WhatsApp
    document.getElementById('whatsapp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    </script>
</body>

</html>