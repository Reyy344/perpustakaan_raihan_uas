<?php
require_once '../config/db.php';
header('Content-Type: application/json');

session_start();
// Cek apakah admin login (untuk web, bisa sesuaikan role di Android juga)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $result = $conn->query("SELECT id, username, nama_lengkap, role, is_active, created_at FROM users WHERE id = $id");
            if ($result && $result->num_rows > 0) {
                echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Akun tidak ditemukan']);
            }
        } else {
            $result = $conn->query("SELECT id, username, nama_lengkap, role, is_active, created_at FROM users ORDER BY created_at DESC");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $users]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $nama_lengkap = $_POST['nama_lengkap'] ?? '';
        $role = $_POST['role'] ?? 'anggota';
        $is_active = (int) ($_POST['is_active'] ?? 0);

        if (!$username || !$password || !$nama_lengkap) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            exit;
        }

        // Cek duplikasi username
        $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($check && $check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
            exit;
        }

        $insert = $conn->query("INSERT INTO users (username, password, nama_lengkap, role, is_active, created_at)
                                VALUES ('$username', '$password', '$nama_lengkap', '$role', $is_active, NOW())");

        if ($insert) {
            echo json_encode(['success' => true, 'message' => 'Akun berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan akun']);
        }
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);
        $id = (int) ($_GET['id'] ?? 0);
        $nama_lengkap = $_PUT['nama_lengkap'] ?? '';
        $role = $_PUT['role'] ?? '';
        $is_active = (int) ($_PUT['is_active'] ?? 0);

        if (!$id || !$nama_lengkap || !$role) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        $update = $conn->query("UPDATE users SET nama_lengkap = '$nama_lengkap', role = '$role', is_active = $is_active WHERE id = $id");

        echo json_encode(['success' => $update, 'message' => $update ? 'Berhasil' : 'Gagal']);
        break;

    case 'DELETE':
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

        $delete = $conn->query("DELETE FROM users WHERE id = $id");
        echo json_encode(['success' => $delete, 'message' => $delete ? 'Berhasil dihapus' : 'Gagal menghapus']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
        break;
}
