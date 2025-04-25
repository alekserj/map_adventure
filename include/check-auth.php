<?php
header('Content-Type: application/json');

session_start();

die(json_encode([
    'isAuth' => isset($_SESSION['user']),
    'user' => $_SESSION['user'] ?? null
]));
?>