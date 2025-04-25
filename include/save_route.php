<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Для сохранения маршрута необходимо авторизоваться'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Недопустимый метод запроса'
    ]);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false, 
        'message' => 'Ошибка формата JSON: ' . json_last_error_msg()
    ]);
    exit;
}

if (!isset($data['route']) || empty($data['route'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Не получены данные маршрута'
    ]);
    exit;
}

if (!isset($data['name']) || empty(trim($data['name']))) {
    echo json_encode([
        'success' => false, 
        'message' => 'Не указано название маршрута'
    ]);
    exit;
}

try {
    $routeName = trim($data['name']);
    $routePoints = json_decode($data['route'], true);
    
    if (!is_array($routePoints) || count($routePoints) < 2) {
        throw new Exception('Маршрут должен содержать минимум 2 точки');
    }
    
    $wktPoints = [];
    foreach ($routePoints as $point) {
        if (!isset($point['coords']) || !is_array($point['coords']) || count($point['coords']) !== 2) {
            throw new Exception('Неверный формат координат точки');
        }
        
        $lon = (float)$point['coords'][1];
        $lat = (float)$point['coords'][0];
        
        if ($lon < -180 || $lon > 180 || $lat < -90 || $lat > 90) {
            throw new Exception('Неверные значения координат');
        }
        
        $wktPoints[] = "$lon $lat";
    }
    
    $wkt = 'LINESTRING(' . implode(',', $wktPoints) . ')';
    $userId = $_SESSION['user']['id'];
    $routeType = $data['routeType'] ?? 'auto';
    
    $stmt = $pdo->prepare("
    INSERT INTO favorite_routes 
    (name, route, user_id, route_type) 
    VALUES (:name, ST_GeomFromText(:wkt, 4326), :user_id, :route_type)
");

$result = $stmt->execute([
    ':name' => $routeName,
    ':wkt' => $wkt,
    ':user_id' => $userId,
    ':route_type' => $routeType
]);
    
    if (!$result) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('Ошибка базы данных: ' . ($errorInfo[2] ?? 'неизвестная ошибка'));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Маршрут успешно сохранен'
    ]);
    
} catch (PDOException $e) {
    error_log('PDO Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Ошибка базы данных: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}