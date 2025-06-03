<?php
require_once '../config/db.php';

// $data = json_decode(file_get_contents("php://input"), true);
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    echo json_encode([
        'success' => false,
        'message' => 'Username dan password wajib diisi'
    ]);
    exit;
}

$query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = $conn->query($query);

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if ($user['password'] === $password) {
        if ($user['is_active'] != 1) {
            echo json_encode([
                'success' => false,
                'message' => 'Akun belum aktif'
            ]);
        } else {
	    session_start();
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'username' => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'],
                'role' => $user['role']
            ];
            session_commit();

            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil',
                'user' => $_SESSION['user'],
                'session_id' => session_id()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Password salah'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Username tidak ditemukan'
    ]);
}