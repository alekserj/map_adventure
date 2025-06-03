<?php
header('Content-Type: application/json');
require_once 'db.php';

$objectId = $_GET['object_id'] ?? 0;

$mysqli = new mysqli("localhost", "root", "", "map");
$sql = "SELECT link FROM pictures WHERE object_id = ? AND is_pending = 0";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $objectId);
$stmt->execute();
$result = $stmt->get_result();

$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row['link'];
}

echo json_encode($images);
?>