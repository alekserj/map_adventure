<?php
$mysqli = new mysqli("localhost", "root", "", "map");

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$sql = "SELECT id, name, street, category, description, ST_AsText(geo) AS geo_text FROM points";
$result = $mysqli->query($sql);

$points = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $row['geo_text'], $matches);
        if (count($matches) == 3) {
            $longitude = (float)$matches[1];
            $latitude = (float)$matches[2];

            $images = [];
            $imageQuery = $mysqli->prepare("SELECT link FROM pictures WHERE object_id = ?");
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
                    $swiperHtml .= '<div class="swiper-slide" style="background-image: url(/include/'.$image.')"></div>';
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
                'swiperHtml' => $swiperHtml
            ];
        }
    }
}

$mysqli->close();
?>