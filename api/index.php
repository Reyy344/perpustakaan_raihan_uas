<?php
session_start();
header("Content-Type: application/json");
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

echo $request;

switch (true) {
    case preg_match('/\/auth\/login/', $request):
        require 'auth/login.php';
        break;
    case preg_match('/\/auth\/register/', $request):
        require 'auth/register.php';
        break;
    case preg_match('/\/auth\/logout/', $request):
        require 'auth/logout.php';
        break;
    case preg_match('/\/auth\/me/', $request):
        require 'auth/me.php';
        break;
    case preg_match('/\/books$/', $request):
        if ($method === 'GET')
            require 'books/index.php';
        if ($method === 'POST')
            require 'books/create.php';
        break;
    case preg_match('/\/books\/(\d+)/', $request, $matches):
        $_GET['id'] = $matches[1];
        if ($method === 'GET')
            require 'books/show.php';
        if ($method === 'PUT')
            require 'books/update.php';
        if ($method === 'DELETE')
            require 'books/delete.php';
        break;
    case preg_match('/\/accounts$/', $request):
        if ($method === 'GET')
            require 'accounts/index.php';
        if ($method === 'POST')
            require 'accounts/create.php';
        break;
    case preg_match('/\/accounts\/(\d+)/', $request, $matches):
        $_GET['id'] = $matches[1];
        if ($method === 'GET')
            require 'accounts/show.php';
        if ($method === 'PUT')
            require 'accounts/update.php';
        if ($method === 'DELETE')
            require 'accounts/delete.php';
        break;
    case preg_match('/\/loans$/', $request):
        if ($method === 'GET')
            require 'loans/index.php';
        if ($method === 'POST')
            require 'loans/create.php';
        break;
    case preg_match('/\/loans\/(\d+)/', $request, $matches):
        $_GET['id'] = $matches[1];
        if ($method === 'GET')
            require 'loans/show.php';
        if ($method === 'PUT')
            require 'loans/update.php';
        if ($method === 'DELETE')
            require 'loans/delete.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}