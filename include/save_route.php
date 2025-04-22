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

try {
    $routePoints = json_decode($data['route'], true);
    
    if (!is_array($routePoints) || count($routePoints) < 2) {
        throw new Exception('Маршрут должен содержать минимум 2 точки');
    }
    
    $wktPoints = [];
    foreach ($routePoints as $point) {
        if (!isset($point['coords']) || !is_array($point['coords']) || count($point['coords']) !== 2) {
            throw new Exception('Неверный формат координат точки');
        }
        
        $lon = (float)$point['coords'][1]; // долгота
        $lat = (float)$point['coords'][0]; // широта
        
        if ($lon < -180 || $lon > 180 || $lat < -90 || $lat > 90) {
            throw new Exception('Неверные значения координат: долгота должна быть от -180 до 180, широта от -90 до 90');
        }
        
        $wktPoints[] = "$lon $lat";
    }
    
    $wkt = 'LINESTRING(' . implode(',', $wktPoints) . ')';
    
    $userId = $_SESSION['user']['id'];
    
    $stmt = $pdo->prepare("
        INSERT INTO favorite_routes 
        (route, user_id) 
        VALUES (ST_GeomFromText(:wkt, 4326), :user_id)
    ");
    
    $result = $stmt->execute([
        ':wkt' => $wkt,
        ':user_id' => $userId
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