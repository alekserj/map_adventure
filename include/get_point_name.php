<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    echo json_encode(['success' => false, 'message' => 'Не указаны координаты']);
    exit;
}

$lat = (float)$_GET['lat'];
$lon = (float)$_GET['lon'];

try {
    $stmt = $pdo->prepare("
        SELECT name 
        FROM points 
        WHERE ST_Distance_Sphere(geo, POINT(:lon, :lat)) <= 50
        LIMIT 1
    ");
    
    $stmt->execute([':lon' => $lon, ':lat' => $lat]);
    $point = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($point) {
        echo json_encode(['success' => true, 'name' => $point['name']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Точка не найдена']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
}