<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

try {
    session_start();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод не разрешен', 405);
    }
    
    if (!isset($_SESSION['user'])) {
        throw new Exception('Необходима авторизация', 401);
    }
    
    if ($_SESSION['user']['email'] !== 'admin@admin.adm') {
        throw new Exception('Недостаточно прав', 403);
    }
    
    if (!isset($_POST['point_id']) || !is_numeric($_POST['point_id'])) {
        throw new Exception('Неверный ID точки', 400);
    }
    
    $pointId = (int)$_POST['point_id'];
    
    require_once 'db_connect.php';
    
    if ($mysqli->connect_error) {
        throw new Exception('Ошибка подключения к БД: ' . $mysqli->connect_error, 500);
    }
    
    $mysqli->begin_transaction();
    
    try {
        $images = $mysqli->query("SELECT link FROM pictures WHERE object_id = $pointId");
        if ($images === false) {
            throw new Exception('Ошибка при получении изображений: ' . $mysqli->error);
        }
        
        while ($image = $images->fetch_assoc()) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/include' . $image['link'];
            if (file_exists($filePath) && !unlink($filePath)) {
                throw new Exception('Не удалось удалить файл: ' . $filePath);
            }
        }
        
        if (!$mysqli->query("DELETE FROM pictures WHERE object_id = $pointId")) {
            throw new Exception('Ошибка при удалении изображений: ' . $mysqli->error);
        }

        if (!$mysqli->query("DELETE FROM reviews WHERE object_id = $pointId")) {
            throw new Exception('Ошибка при удалении изображений: ' . $mysqli->error);
        }
        
        $tables = ['point_status', 'favorite_points'];
        foreach ($tables as $table) {
            if (!$mysqli->query("DELETE FROM $table WHERE point_id = $pointId")) {
                throw new Exception("Ошибка при удалении из $table: " . $mysqli->error);
            }
        }
        
        if (!$mysqli->query("DELETE FROM points WHERE id = $pointId")) {
            throw new Exception('Ошибка при удалении точки: ' . $mysqli->error);
        }
        
        if ($mysqli->affected_rows === 0) {
            throw new Exception('Точка не найдена', 404);
        }
        
        $mysqli->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Точка успешно удалена'
        ]);
        
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    ob_end_clean();
    
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
    
    exit;
}

ob_end_flush();
?>