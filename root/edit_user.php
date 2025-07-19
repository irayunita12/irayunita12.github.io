<?php
require 'auth_check.php';
require 'db.php';

// Only allow admin users to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit;
}

$user_id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch user data
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header("Location: admin_users.php");
    exit;
}

if (isset($_POST['update_user'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $whatsapp = preg_replace('/[^0-9]/', '', $_POST['whatsapp']);
    $role = $_POST['role'];
    $profile_picture = $user['profile_picture']; // Keep existing picture by default

    // Handle file upload if new picture is provided
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
                // Delete old picture if exists
                if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
                    unlink($user['profile_picture']);
                }
                
                $file_name = uniqid('profile_') . '.' . $file_ext;
                $file_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
                    $profile_picture = $file_path;
                } else {
                    $error = 'Failed to upload profile picture.';
                }
            } else {
                $error = 'Profile picture must be less than 2MB.';
            }
        } else {
            $error = 'Only JPG, JPEG, PNG, GIF files are allowed.';
        }
    }

    if (empty($error)) {
        // Validation
        if (empty($nama) || empty($email) || empty($whatsapp)) {
            $error = 'All fields are required!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format!';
        } else {
            // Check if email already exists for another user
            $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND id != $user_id");
            if (mysqli_num_rows($check_email) > 0) {
                $error = 'Email already registered to another user!';
            } else {
                // Update user data
                $query = "UPDATE users SET 
                         nama = '$nama',
                         email = '$email',
                         whatsapp = '$whatsapp',
                         role = '$role',
                         profile_picture = " . ($profile_picture ? "'$profile_picture'" : "NULL") . "
                         WHERE id = $user_id";
                
                if (mysqli_query($conn, $query)) {
                    $success = 'User updated successfully!';
                    // Refresh user data
                    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
                    $user = mysqli_fetch_assoc($result);
                } else {
                    $error = 'Error updating user: ' . mysqli_error($conn);
                }
            }
        }
    }
}

// Handle password change if submitted
if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password)) {
        $error = 'Password cannot be empty!';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Password changed successfully!';
        } else {
            $error = 'Error changing password: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Ira Beauty</title>
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

    .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    h1 {
        color: #6b8cff;
        margin-bottom: 20px;
        text-align: center;
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

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
    }

    .form-group input:focus,
    .form-group select:focus {
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

    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #6b8cff;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    /* Profile Picture Styles */
    .profile-picture-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }

    .profile-picture {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #6b8cff;
        margin-bottom: 10px;
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

    .initials {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #6b8cff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit User: <?= htmlspecialchars($user['nama']) ?></h1>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2>User Information</h2>

            <div class="profile-picture-container">
                <?php if (!empty($user['profile_picture'])): ?>
                <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture"
                    class="profile-picture">
                <?php else: ?>
                <div class="initials"><?= strtoupper(substr($user['nama'], 0, 1)) ?></div>
                <?php endif; ?>

                <div class="profile-picture-upload">
                    <label for="profile_picture">Change Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                        onchange="previewImage(this)">
                    <div class="file-info">JPG, PNG, GIF (Max 2MB)</div>
                </div>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama">Full Name</label>
                    <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="whatsapp">WhatsApp Number</label>
                    <input type="tel" id="whatsapp" name="whatsapp" value="<?= htmlspecialchars($user['whatsapp']) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <button type="submit" name="update_user" class="btn">Update User</button>
            </form>
        </div>

        <div class="form-section">
            <h2>Change Password</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="change_password" class="btn">Change Password</button>
            </form>
        </div>

        <a href="admin_users.php" class="back-link">← Back to Users List</a>
    </div>

    <script>
    // Format WhatsApp number (digits only)
    document.getElementById('whatsapp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Preview selected image
    function previewImage(input) {
        const preview = document.querySelector('.profile-picture');
        const initials = document.querySelector('.initials');
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                if (preview) {
                    preview.src = e.target.result;
                } else if (initials) {
                    initials.style.display = 'none';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'profile-picture';
                    img.alt = 'Preview';
                    initials.parentNode.insertBefore(img, initials.nextSibling);
                }
            }

            reader.readAsDataURL(file);
        }
    }
    </script>
</body>

</html>