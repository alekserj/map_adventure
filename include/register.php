<?php

require_once __DIR__ . '/helpers.php';

$nick = $_POST['registration-login'] ?? null;
$email = $_POST['registration-email'] ?? null;
$password = $_POST['registration-password'] ?? null;
$passwordConfirm = $_POST['registration-password-confirm'] ?? null;

if (empty($nick)) {
    addValidationError('name', 'Nickname не может быть пустым');
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    addValidationError('email', 'Email указан неправильно');
}


if(empty($password)){
    addValidationError('password', 'Пароль не может быть пустым');
}

if($password !== $passwordConfirm){
    addValidationError('password', 'Пароли не совпадают');
}

if(!empty($_SESSION['validation'])){
    addOldValue('nick', $nick);
    addOldValue('email', $email);
    redirect('../index.php');
}

$pdo  = getPDO();

$query = "INSERT INTO users (nickname, email, password) VALUES (:nickname, :email, :password)";
$params = [
    'nickname' => $nick,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT)
];
$stmt = $pdo->prepare($query);
try {
    $stmt->execute($params);
} catch (Exception $e) {
    die($e->getMessage());
}

redirect('../index.php');
?>