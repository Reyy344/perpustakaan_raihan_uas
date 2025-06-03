<?php
require_once '../config/db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Belum login'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true,
        'message' => 'User aktif',
        'data' => $_SESSION['user']
    ]);
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // $data = json_decode(file_get_contents("php://input"), true);
    $nama_lengkap = $_PUT['nama_lengkap'] ?? '';

    if (!$nama_lengkap) {
        echo json_encode([
            'success' => false,
            'message' => 'Nama lengkap tidak boleh kosong'
        ]);
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    $update = $conn->query("UPDATE users SET nama_lengkap = '$nama_lengkap' WHERE id = $user_id");

    if ($update) {
        $_SESSION['user']['nama_lengkap'] = $nama_lengkap;
        echo json_encode([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $_SESSION['user']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal memperbarui profil'
        ]);
    }
}
