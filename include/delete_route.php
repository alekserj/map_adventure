<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['route_id']) || empty($data['route_id'])) {
    echo json_encode(['success' => false, 'message' => 'Не указан ID маршрута']);
    exit;
}

try {
    $userId = $_SESSION['user']['id'];
    $routeId = $data['route_id'];
    
    // Проверяем, что маршрут принадлежит пользователю
    $checkStmt = $pdo->prepare("SELECT id FROM favorite_routes WHERE id = :id AND user_id = :user_id");
    $checkStmt->execute([':id' => $routeId, ':user_id' => $userId]);
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Маршрут не найден или не принадлежит пользователю']);
        exit;
    }
    
    // Удаляем маршрут
    $deleteStmt = $pdo->prepare("DELETE FROM favorite_routes WHERE id = :id");
    $deleteStmt->execute([':id' => $routeId]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
}