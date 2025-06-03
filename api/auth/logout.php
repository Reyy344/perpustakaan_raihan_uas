<?php
require_once '../config/db.php';

session_start();
session_unset();
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Logout berhasil'
]);
