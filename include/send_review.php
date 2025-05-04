<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Для отправки отзыва необходимо авторизоваться']);
    exit;
}

if (empty($_POST['review']) || empty($_POST['object_id'])) {
    echo json_encode(['success' => false, 'message' => 'Текст отзыва не может быть пустым']);
    exit;
}

require_once 'db.php';

try {
    $stmt = $pdo->prepare("INSERT INTO reviews (object_id, user_id, review) VALUES (:object_id, :user_id, :review)");
    $stmt->execute([
        ':object_id' => $_POST['object_id'],
        ':user_id' => $_SESSION['user']['id'],
        ':review' => htmlspecialchars($_POST['review'])
    ]);

    $reviewId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("
        SELECT r.*, u.nickname 
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.id = :id
    ");
    $stmt->execute([':id' => $reviewId]);
    $newReview = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Отзыв успешно добавлен',
        'review' => $newReview
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении отзыва: ' . $e->getMessage()]);
}
?>