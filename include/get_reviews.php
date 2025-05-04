<?php
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_GET['object_id'])) {
    echo json_encode(['error' => 'Object ID is required']);
    exit;
}

$objectId = (int)$_GET['object_id'];

try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.nickname 
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.object_id = :object_id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([':object_id' => $objectId]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reviews);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>