<?php
$mysqli = new mysqli("localhost", "root", "", "map");

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}