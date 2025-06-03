<?php
require_once '../config/db.php';
header('Content-Type: application/json');

session_start();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Ambil semua peminjaman (admin), atau milik sendiri (anggota)
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Belum login']);
            exit;
        }

        $sql = "SELECT br.*, b.judul, u.nama_lengkap 
                FROM borrow_requests br
                JOIN books b ON br.book_id = b.id
                JOIN users u ON br.user_id = u.id";

        if ($user['role'] === 'anggota') {
            $user_id = (int) $user['id'];
            $sql .= " WHERE br.user_id = $user_id";
        } elseif (isset($_GET['id'])) {
            $loan_id = (int) $_GET['id'];
            $sql .= " WHERE br.id = $loan_id";
        }

        $sql .= " ORDER BY br.request_at DESC";

        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'POST':
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Belum login']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $book_id = (int) ($_POST['book_id'] ?? 0);
        $user_id = (int) $user['id'];

        if (!$book_id) {
            echo json_encode(['success' => false, 'message' => 'ID buku tidak valid']);
            exit;
        }

        // Cek stok buku
        $cek = $conn->query("SELECT stok FROM books WHERE id = $book_id");
        if (!$cek || $cek->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan']);
            exit;
        }

        $stok = (int) $cek->fetch_assoc()['stok'];
        if ($stok <= 0) {
            echo json_encode(['success' => false, 'message' => 'Stok buku habis']);
            exit;
        }

	$updateStock = $conn->query("UPDATE books SET stok = stok - 1 WHERE id = $book_id");
	if (!$updateStock) {
            echo json_encode(['success' => false, 'message' => 'Stok buku mengalami error']);
	    exit;
        }

        // Insert borrow request
        $insert = $conn->query("INSERT INTO borrow_requests (user_id, book_id, request_at, picked_up, returned) 
                                VALUES ($user_id, $book_id, NOW(), 0, 0)");

        if ($insert) {
            echo json_encode(['success' => true, 'message' => 'Permintaan peminjaman berhasil dikirim']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses permintaan']);
        }
        break;

    case 'PUT':
        $user = $_SESSION['user'] ?? null;
        if (!$user || $user['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Hanya admin yang dapat mengubah status']);
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        parse_str(file_get_contents("php://input"), $_PUT);
        $picked_up = isset($_PUT['picked_up']) ? 1 : 0;
        $returned = isset($_PUT['returned']) ? 1 : 0;

        $return_at = $returned ? "NOW()" : "NULL";

        $sql = "UPDATE borrow_requests 
                SET picked_up = $picked_up,
                    returned = $returned,
                    return_at = $return_at
                WHERE id = $id";

        $update = $conn->query($sql);

        if ($update) {
            echo json_encode(['success' => true, 'message' => 'Status diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status']);
        }
        break;

    case 'DELETE':
        $user = $_SESSION['user'] ?? null;
        if (!$user || $user['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

	$updateStock = $conn->query("UPDATE books SET stok = stok + 1 WHERE id = (SELECT book_id FROM borrow_requests WHERE id = $id)");
	if (!$updateStock) {
            echo json_encode(['success' => false, 'message' => 'Stok buku mengalami error']);
	    exit;
        }

        $delete = $conn->query("DELETE FROM borrow_requests WHERE id = $id");
        echo json_encode([
            'success' => $delete,
            'message' => $delete ? 'Permintaan berhasil dihapus' : 'Gagal menghapus'
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
}
