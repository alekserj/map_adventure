<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
    exit;
}

try {
    $userId = $_SESSION['user']['id'];
    
    $stmt = $pdo->prepare("
        SELECT id, name, ST_AsText(route) as route, route_type 
        FROM favorite_routes 
        WHERE user_id = :user_id
        ORDER BY id DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    
    $routes = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Парсим WKT (Well-Known Text) формат маршрута
        preg_match('/LINESTRING\((.+)\)/', $row['route'], $matches);
        $coords = explode(',', $matches[1]);
        
        $points = [];
        foreach ($coords as $coord) {
            $parts = explode(' ', trim($coord));
            $points[] = [
                'lon' => (float)$parts[0],
                'lat' => (float)$parts[1]
            ];
        }
        
        $routes[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'points' => $points,
            'type' => $row['route_type']
        ];
    }
    
    echo json_encode(['success' => true, 'routes' => $routes]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
}