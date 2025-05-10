<?php
session_start();

if (!isset($_SESSION['user'])) {
    die();
}

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
    $point_id = $stmt->insert_id;

    $statusSql = "INSERT INTO point_status (point_id, is_approved) VALUES (?, ?)";
    $isApproved = ($_SESSION['user']['email'] === 'admin@admin.adm') ? 1 : 0;
    $statusStmt = $conn->prepare($statusSql);
    $statusStmt->bind_param("ii", $point_id, $isApproved);
    $statusStmt->execute();
    $statusStmt->close();
    
    echo json_encode(['success' => true, 'message' => 'Объект добавлен и ожидает модерации']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>