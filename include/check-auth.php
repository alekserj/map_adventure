<?php
// Убедимся, что нет лишних пробелов/символов перед <?php
header('Content-Type: application/json');

session_start();

// Простой ответ без лишних проверок
die(json_encode([
    'isAuth' => isset($_SESSION['user']),
    'user' => $_SESSION['user'] ?? null
]));
?>