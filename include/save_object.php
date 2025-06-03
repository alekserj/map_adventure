<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

function log_error($message) {
    file_put_contents(__DIR__ . '/upload_errors.log', date('[Y-m-d H:i:s]') . ' ' . $message . PHP_EOL, FILE_APPEND);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Разрешены только POST-запросы', 405);
    }

    if (empty($_POST['objectId']) || !is_numeric($_POST['objectId'])) {
        throw new Exception('Неверный ID объекта', 400);
    }

    $objectId = (int)$_POST['objectId'];
    $description = $_POST['object_description'] ?? null;
    $hasImages = isset($_FILES['images']);

    $db = new mysqli('localhost', 'root', '', 'map');
    if ($db->connect_error) {
        throw new Exception('Ошибка подключения к БД: ' . $db->connect_error, 500);
    }

    $descriptionUpdated = false;
    if ($description !== null && $description !== '') {
        if (isset($_SESSION['user']) && $_SESSION['user']['email'] === 'admin@admin.adm') {
            $stmt = $db->prepare("UPDATE points SET description = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Ошибка подготовки запроса: ' . $db->error, 500);
            }
            
            $stmt->bind_param("si", $description, $objectId);
            if (!$stmt->execute()) {
                throw new Exception('Ошибка обновления описания: ' . $stmt->error, 500);
            }
            $stmt->close();
            $descriptionUpdated = true;
        } else {
            $stmt = $db->prepare("UPDATE point_status SET pending_description = ? WHERE point_id = ?");
            if (!$stmt) {
                throw new Exception('Ошибка подготовки запроса: ' . $db->error, 500);
            }
            
            $stmt->bind_param("si", $description, $objectId);
            if (!$stmt->execute()) {
                throw new Exception('Ошибка сохранения описания на модерацию: ' . $stmt->error, 500);
            }
            $stmt->close();
            $descriptionUpdated = true;
        }
    }

    $uploadedImagesCount = 0;
    if ($hasImages) {
        $uploadDir = __DIR__ . '/object_pictures/';
        
        if (!file_exists($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            throw new Exception('Не удалось создать директорию для загрузки', 500);
        }

        $isAdmin = isset($_SESSION['user']) && $_SESSION['user']['email'] === 'admin@admin.adm';
        $isPending = $isAdmin ? 0 : 1;
        $isApproved = $isAdmin ? 1 : 0;

        $stmt = $db->prepare("INSERT INTO pictures (object_id, link, is_approved, is_pending) VALUES (?, ?, ?, ?)");
        if (!$stmt) throw new Exception('Ошибка подготовки SQL: ' . $db->error, 500);

        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            if (empty($tmpName)) continue;

            $error = $_FILES['images']['error'][$key];
            if ($error !== UPLOAD_ERR_OK) {
                log_error("Ошибка загрузки: {$error} - " . $_FILES['images']['name'][$key]);
                continue;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmpName);
            finfo_close($finfo);

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];

            if (!isset($allowedTypes[$mime])) {
                log_error("Недопустимый тип {$mime}: " . $_FILES['images']['name'][$key]);
                continue;
            }

            $ext = $allowedTypes[$mime];
            $filename = "obj_{$objectId}_" . uniqid() . ".{$ext}";
            $filepath = $uploadDir . $filename;

            if (!move_uploaded_file($tmpName, $filepath)) {
                $lastError = error_get_last();
                log_error("Ошибка перемещения: " . ($lastError['message'] ?? 'Unknown error'));
                continue;
            }

            $webPath = '/object_pictures/' . $filename;
            $stmt->bind_param("isii", $objectId, $webPath, $isApproved, $isPending);
            
            if (!$stmt->execute()) {
                log_error("DB Error: " . $stmt->error);
                unlink($filepath);
                continue;
            }

            $uploadedImagesCount++;
        }
        $stmt->close();
    }

    $response = [
        'success' => true,
        'message' => isset($_SESSION['user']) && $_SESSION['user']['email'] === 'admin@admin.adm' 
            ? 'Данные успешно сохранены' 
            : 'Данные отправлены на модерацию',
        'description_updated' => $descriptionUpdated,
        'images_uploaded' => $uploadedImagesCount
    ];

    echo json_encode($response, JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    log_error('Ошибка: ' . $e->getMessage() . ' в файле ' . $e->getFile() . ' на строке ' . $e->getLine());
    
    http_response_code($e->getCode() >= 400 && $e->getCode() <= 599 ? $e->getCode() : 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ], JSON_UNESCAPED_SLASHES);
}
?>