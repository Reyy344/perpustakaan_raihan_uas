<?php
session_start();
require_once '../config/db.php';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $role = $_POST['role'];

    if ($username && $password && $nama_lengkap && in_array($role, ['anggota', 'staff', 'admin'])) {
        // Hash password
        $hashed_password = md5($password); // Kamu bisa ganti jadi password_hash() untuk keamanan lebih baik

        // Cek apakah username sudah ada
        $cek = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Username sudah digunakan.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO users (username, password, nama_lengkap, role, is_active) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param("ssss", $username, $hashed_password, $nama_lengkap, $role);
            if ($stmt->execute()) {
                $success = "User berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan user.";
            }
        }
    } else {
        $error = "Semua field wajib diisi dengan benar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        form { max-width: 500px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin-top: 10px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 10px 20px; background-color: #28a745; border: none; color: white; font-weight: bold; border-radius: 5px; cursor: pointer; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        a { text-decoration: none; display: inline-block; margin-top: 10px; }
    </style>
</head>
<body>

<form method="post">
    <h2>➕ Tambah User Baru</h2>

    <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password</label>
    <input type="text" id="password" name="password" required>

    <label for="nama_lengkap">Nama Lengkap</label>
    <input type="text" id="nama_lengkap" name="nama_lengkap" required>

    <label for="role">Role</label>
    <select id="role" name="role" required>
        <option value="anggota">Anggota</option>
        <option value="staff">Staff</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit">Tambah User</button>
    <br>
    <a href="../index1.php">⬅ Kembali ke Dashboard</a>
</form>

</body>
</html>
