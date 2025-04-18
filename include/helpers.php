<?php

session_start();

function redirect(string $path){
    header("Location: $path");
    die();
}

function addValidationError(string $fieldName, string $message){
    $_SESSION['validation'][$fieldName] = $message;
}

function hasValidationError(string $fieldName): bool
{
    return isset($_SESSION['validation'][$fieldName]);
}

function validationErrorAttr(string $fieldName){
    echo isset($_SESSION['validation'][$fieldName]) ? 'style = "border-color: red"' : '';
}

function validationErrorMessage(string $fieldName){
    $message = $_SESSION['validation'][$fieldName] ?? '';
    unset($_SESSION['validation'][$fieldName]);
    echo $message;
}

function addOldValue(string $key, string $value): void{
    $_SESSION['old'][$key] = $value;
}

function old(string $key){
    $value =  $_SESSION['old'][$key] ?? '';
    unset( $_SESSION['old'][$key]);
    return $value;
}

function setMessage(string $key, string $message): void{
    $_SESSION['message'][$key] = $message;
}

function hasMessage(string $key): bool{
    return isset($_SESSION['message'][$key]);
}

function getMessage(string $key){
    $message = $_SESSION['message'][$key] ?? '';
    unset($_SESSION['message'][$key]);
    return $message;
}

function getPDO(){
    try{
        return new \PDO('mysql:host=localhost; charset=utf8; dbname=map', 'root', '');
    } catch (PDOException $e){
        die($e->getMessage());
    }
}

function findUser(string $email)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE `email` = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}
?>