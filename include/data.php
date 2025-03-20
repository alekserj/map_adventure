<?php
$mysqli = new mysqli("localhost", "root", "", "map");

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$sql = "SELECT id, name, street, category, ST_AsText(geo) AS geo_text FROM points";
$result = $mysqli->query($sql);

$points = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $row['geo_text'], $matches);
        if (count($matches) == 3) {
            $longitude = (float)$matches[1];
            $latitude = (float)$matches[2];   
            $points[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'street' => $row['street'],
                'category' => $row['category'],
                'coordinates' => [$latitude, $longitude] 
            ];
        }
    }
}

$mysqli->close();
?>
