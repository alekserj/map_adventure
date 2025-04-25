<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nickname' => trim($_POST['registration-login'] ?? ''),
        'email' => trim($_POST['registration-email'] ?? ''),
        'password' => $_POST['registration-password'] ?? '',
        'password_confirm' => $_POST['registration-password-confirm'] ?? ''
    ];

    $errors = [];

    if (empty($data['nickname'])) {
        $errors['nickname'] = 'Введите nickname';
    } elseif (strlen($data['nickname']) < 3) {
        $errors['nickname'] = 'Nickname должен быть не менее 3 символов';
    }

    if (empty($data['email'])) {
        $errors['email'] = 'Введите email';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Неверный формат email';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'Этот email уже занят';
            }
        } catch (PDOException $e) {
            $errors['database'] = 'Ошибка при проверке email: ' . $e->getMessage();
        }
    }

    if (empty($data['password'])) {
        $errors['password'] = 'Введите пароль';
    } elseif (strlen($data['password']) < 6) {
        $errors['password'] = 'Пароль должен быть не менее 6 символов';
    } elseif ($data['password'] !== $data['password_confirm']) {
        $errors['password'] = 'Пароли не совпадают';
    }

    if (empty($errors)) {
        try {
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            if (!$passwordHash) {
                throw new Exception('Ошибка при создании хеша пароля');
            }

            $stmt = $pdo->prepare("INSERT INTO users (nickname, email, password) VALUES (?, ?, ?)");
            $result = $stmt->execute([$data['nickname'], $data['email'], $passwordHash]);

            if (!$result || $stmt->rowCount() !== 1) {
                throw new Exception('Ошибка при добавлении пользователя в базу данных');
            }

            $_SESSION['success'] = 'Регистрация прошла успешно! Теперь вы можете войти.';
            header('Location: /?registration=success');
            exit;
            
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            $errors['database'] = 'Произошла ошибка при регистрации. Пожалуйста, попробуйте позже.';
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            $errors['registration'] = $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['validation'] = $errors;
        $_SESSION['old'] = $data;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    header('Location: /');
    exit;
}