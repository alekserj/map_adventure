<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "map"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];
$address = $_POST['address'];
$object_name = $_POST['object_name'];
$category = $_POST['select'];

$sql = "INSERT INTO points (name, geo, street, category) VALUES (?, POINT(?, ?), ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $object_name, $longitude, $latitude, $address, $category);

if ($stmt->execute()) {
    echo "Новый объект успешно добавлен!";
} else {
    echo "Ошибка: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>