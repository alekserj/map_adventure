<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Необходимо авторизоваться']);
    exit;
}

if (!isset($_POST['point_id'])) {
    echo json_encode(['success' => false, 'message' => 'Не указан ID точки']);
    exit;
}

$pointId = (int)$_POST['point_id'];
$userId = $_SESSION['user']['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM favorite_points WHERE user_id = ? AND point_id = ?");
    $stmt->execute([$userId, $pointId]);
    $isFavorite = $stmt->fetch();
    
    if ($isFavorite) {
        $stmt = $pdo->prepare("DELETE FROM favorite_points WHERE user_id = ? AND point_id = ?");
        $stmt->execute([$userId, $pointId]);
        echo json_encode([
            'success' => true,
            'action' => 'removed',
            'message' => 'Точка удалена из избранного'
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO favorite_points (user_id, point_id) VALUES (?, ?)");
        $stmt->execute([$userId, $pointId]);
        echo json_encode([
            'success' => true,
            'action' => 'added',
            'message' => 'Точка добавлена в избранное'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>