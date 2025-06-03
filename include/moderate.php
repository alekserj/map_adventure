<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['email'] !== 'admin@admin.adm') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
$type = $input['type'] ?? null;
$action = $input['action'] ?? null;

if (!$id || !$type || !$action) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
    exit;
}

try {
    $pdo->beginTransaction();

    if ($type === 'point') {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE point_status SET is_approved = 1 WHERE point_id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM point_status WHERE point_id = ?");
            $stmt->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM points WHERE id = ?");
            $stmt->execute([$id]);
        }
    } 
    elseif ($type === 'description') {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("SELECT pending_description FROM point_status WHERE point_id = ?");
            $stmt->execute([$id]);
            $desc = $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("UPDATE points SET description = ? WHERE id = ?");
            $stmt->execute([$desc, $id]);
            
            $stmt = $pdo->prepare("UPDATE point_status SET pending_description = NULL, is_info_approved = 1 WHERE point_id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("UPDATE point_status SET pending_description = NULL WHERE point_id = ?");
            $stmt->execute([$id]);
        }
    }
    elseif ($type === 'image') {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE pictures SET is_approved = 1, is_pending = 0 WHERE id = ?");
            $stmt->execute([$id]);

            $stmt = $pdo->prepare("SELECT object_id FROM pictures WHERE id = ?");
            $stmt->execute([$id]);
            $object_id = $stmt->fetchColumn();

            $stmt = $pdo->prepare("UPDATE point_status SET is_info_approved = 1 WHERE point_id = ?");
            $stmt->execute([$object_id]);
        } else {
            $stmt = $pdo->prepare("SELECT link FROM pictures WHERE id = ?");
            $stmt->execute([$id]);
            $path = __DIR__ . $stmt->fetchColumn();

            $stmt = $pdo->prepare("DELETE FROM pictures WHERE id = ?");
            $stmt->execute([$id]);

            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
    
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>