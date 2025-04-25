<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'db.php';

$response = [
    'success' => false,
    'message' => 'Неизвестная ошибка',
    'points' => []
];

try {
    if (!isset($_SESSION['user'])) {
        $response['message'] = 'Требуется авторизация';
        http_response_code(401);
        echo json_encode($response);
        exit;
    }

    $userId = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("
        SELECT 
            p.id, 
            p.name, 
            p.street, 
            p.category, 
            p.description,
            ST_AsText(p.geo) AS geo_text
        FROM favorite_points fp
        JOIN points p ON fp.point_id = p.id
        WHERE fp.user_id = ?
    ");
    
    if (!$stmt->execute([$userId])) {
        throw new Exception('Ошибка выполнения запроса');
    }

    $points = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($points as &$point) {
        if (preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $point['geo_text'], $matches)) {
            $point['longitude'] = (float)$matches[1];
            $point['latitude'] = (float)$matches[2];
        }
        unset($point['geo_text']); 
    }

    $response = [
        'success' => true,
        'message' => '',
        'points' => $points
    ];

} catch (PDOException $e) {
    $response['message'] = 'Ошибка базы данных: ' . $e->getMessage();
    error_log('DB Error: ' . $e->getMessage());
    http_response_code(500);
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
exit;
?>