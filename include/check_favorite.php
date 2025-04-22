<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['isFavorite' => false]);
    exit;
}

if (!isset($_GET['point_id'])) {
    echo json_encode(['isFavorite' => false]);
    exit;
}

$pointId = (int)$_GET['point_id'];
$userId = $_SESSION['user']['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM favorite_points WHERE user_id = ? AND point_id = ?");
    $stmt->execute([$userId, $pointId]);
    $isFavorite = $stmt->fetch();
    
    echo json_encode(['isFavorite' => (bool)$isFavorite]);
} catch (PDOException $e) {
    echo json_encode(['isFavorite' => false]);
}
?>