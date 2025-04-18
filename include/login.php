<?php

require_once __DIR__ . '/helpers.php';

$email = $_POST['account-login'] ?? null;
$password = $_POST['account-password'] ?? null;

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
    addOldValue('email', $email);
    addValidationError('email', 'Email указан неправильно');
    setMessage('error', 'Ошибка валидации');
    redirect('../index.php');
}

$user = findUser($email);

if(!$user){
    setMessage('error', "Пользователь $email не найден");
    redirect('../index.php');
}

if(!password_verify($password, $user['password'])){
    setMessage('error', "Неверный пароль");
    redirect('../index.php');
}

$_SESSION['user']['id'] = $user['id'];
redirect('../index.php');
?>