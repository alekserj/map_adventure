<?php
session_start();

$response = [
    'isAuth' => isset($_SESSION['user']),
    'email' => $_SESSION['user']['email'] ?? null
];

header('Content-Type: application/json');
echo json_encode($response);
?>