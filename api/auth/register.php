<?php
require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$nama_lengkap = $_POST['nama_lengkap'] ?? '';

if (!$username || !$password || !$nama_lengkap) {
    echo json_encode([
        'success' => false,
        'message' => 'Semua field wajib diisi'
    ]);
    exit;
}

// Cek apakah username sudah digunakan
$check = $conn->query("SELECT id FROM users WHERE username = '$username'");
if ($check && $check->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Username sudah digunakan'
    ]);
    exit;
}

// Insert user baru dengan role 'anggota' dan belum aktif
$query = "INSERT INTO users (username, password, nama_lengkap, role, is_active, created_at)
          VALUES ('$username', '$password', '$nama_lengkap', 'anggota', 0, NOW())";
$insert = $conn->query($query);

if ($insert) {
    echo json_encode([
        'success' => true,
        'message' => 'Registrasi berhasil. Tunggu aktivasi akun oleh admin.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Registrasi gagal: ' . $conn->error
    ]);
}
