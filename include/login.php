<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Очистка предыдущих данных
$_SESSION['validation'] = [];
$_SESSION['error'] = '';
$_SESSION['old'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['account-login'] ?? '');
    $password = trim($_POST['account-password'] ?? '');
    $errors = [];

    // 1. Валидация полей
    if (empty($email)) {
        $errors['email'] = 'Введите email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Неверный формат email';
    }

    if (empty($password)) {
        $errors['password'] = 'Введите пароль';
    }

    // Если есть ошибки валидации - сохраняем и выходим
    if (!empty($errors)) {
        $_SESSION['validation'] = $errors;
        $_SESSION['old']['email'] = $email;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // 2. Проверка пользователя в БД (только если валидация успешна)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error'] = 'Пользователь с таким email не найден';
        $_SESSION['old']['email'] = $email;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // 3. Проверка пароля
    if (!password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Неверный пароль';
        $_SESSION['old']['email'] = $email;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Успешная авторизация
    $_SESSION['user'] = [
        'id' => $user['id'],
        'nickname' => $user['nickname'],
        'email' => $user['email']
    ];
    
    header('Location: /');
    exit;
}
?>