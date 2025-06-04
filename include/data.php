<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "map");

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$isAdmin = isset($_SESSION['user']) && $_SESSION['user']['email'] === 'admin@admin.adm';

$sql = "SELECT p.id, p.name, p.street, p.category, 
               IF(ps.is_info_approved = 1 OR ? = 1, p.description, NULL) as description, 
               ST_AsText(p.geo) AS geo_text 
        FROM points p
        LEFT JOIN point_status ps ON p.id = ps.point_id
        WHERE ps.is_approved = 1 OR ? = 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $isAdmin, $isAdmin);
$stmt->execute();
$result = $stmt->get_result();

$points = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $row['geo_text'], $matches);
        if (count($matches) == 3) {
            $longitude = (float)$matches[1];
            $latitude = (float)$matches[2];

            $images = [];
            $imageQuery = $mysqli->prepare("SELECT link FROM pictures WHERE object_id = ? AND is_pending = 0");
            $imageQuery->bind_param("i", $row['id']);
            $imageQuery->execute();
            $imageResult = $imageQuery->get_result();
            
            while ($imageRow = $imageResult->fetch_assoc()) {
                $images[] = $imageRow['link'];
            }
            $imageQuery->close();

            $swiperHtml = '<div class="swiper"><div class="swiper-wrapper">';
            
            if (!empty($images)) {
                foreach ($images as $image) {
                    $swiperHtml .= '<div class="swiper-slide" style="background-image: url(/include'.$image.')"></div>';
                }
            } else {
                $swiperHtml .= '<div class="swiper-slide" style="background-image: url(../img/hero_img.jpg)"></div>';
            }
            
            $swiperHtml .= '</div><div class="swiper-pagination"></div></div>';
            
            $points[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'street' => $row['street'],
                'category' => $row['category'],
                'description' => $row['description'],
                'coordinates' => [$latitude, $longitude],
                'images' => $images,
                'swiperHtml' => $swiperHtml
            ];
        }
    }
}

if ($isAdmin) {
    $adminPoints = [];
    $adminSql = "SELECT p.id, p.name, p.street, p.category, 
                        p.description, 
                        ST_AsText(p.geo) AS geo_text 
                 FROM points p";
    $adminResult = $mysqli->query($adminSql);
    
    if ($adminResult->num_rows > 0) {
        while ($row = $adminResult->fetch_assoc()) {
            preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $row['geo_text'], $matches);
            if (count($matches) == 3) {
                $longitude = (float)$matches[1];
                $latitude = (float)$matches[2];
                
                $adminPoints[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'street' => $row['street'],
                    'category' => $row['category'],
                    'description' => $row['description'],
                    'coordinates' => [$latitude, $longitude]
                ];
            }
        }
    }
}

$mysqli->close();
return $points;
?>