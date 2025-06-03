<?php
require_once '../config/db.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Belum login'
    ]); 
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $result = $conn->query("SELECT * FROM books WHERE id = $id");
            if ($result && $result->num_rows > 0) {
                echo json_encode(['success' => true, 'data' => $result->fetch_assoc()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan']);
            }
        } else {
            $result = $conn->query("SELECT * FROM books ORDER BY id DESC");
            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $books]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $judul = $_POST['judul'] ?? '';
        $penulis = $_POST['penulis'] ?? '';
        $stok = (int) ($_POST['stok'] ?? 0);

        if (!$judul || !$penulis || $stok <= 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap atau stok tidak valid']);
            exit;
        }

        $query = "INSERT INTO books (judul, penulis, stok) VALUES ('$judul', '$penulis', $stok)";
        if ($conn->query($query)) {
            echo json_encode([
                'success' => true,
                'message' => 'Buku berhasil ditambahkan',
                'data' => ['id' => $conn->insert_id]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan buku']);
        }
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);
        $id = (int) ($_GET['id'] ?? 0);
        $judul = $_PUT['judul'] ?? '';
        $penulis = $_PUT['penulis'] ?? '';
        $stok = (int) ($_PUT['stok'] ?? 0);

        if (!$id || !$judul || !$penulis || $stok < 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap atau ID/stok tidak valid']);
            exit;
        }

        $update = $conn->query("UPDATE books SET judul = '$judul', penulis = '$penulis', stok = $stok WHERE id = $id");
        echo json_encode(['success' => $update, 'message' => $update ? 'Berhasil' : 'Gagal']);
        break;

    case 'DELETE':
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID buku tidak valid']);
            exit;
        }

        $delete = $conn->query("DELETE FROM books WHERE id = $id");
        echo json_encode(['success' => $delete, 'message' => $delete ? 'Buku berhasil dihapus' : 'Gagal menghapus buku']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
        break;
}
