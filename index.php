<?php
    require_once'include/data.php';
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="/css/normalize.css" />
    <link rel="stylesheet" href="/css/style.css" />
    <script
      src="https://api-maps.yandex.ru/2.1/?apikey=ваш API-ключ&lang=ru_RU"
      type="text/javascript"
    ></script>
    <script type="text/javascript">
      ymaps.ready(init);
      function init() {
        var myMap = new ymaps.Map("map", {
          center: [51.73470896697555, 36.19070462924623],
          zoom: 13,
        });

        myMap.controls.remove("trafficControl"); //удаления плашки пробок
        myMap.controls.remove("typeSelector"); //удаление переключателя слоев карты
        myMap.controls.remove("fullscreenControl"); //удаление полноэкранного режима
        myMap.controls.remove("rulerControl"); //удаление линейки
        //удаление кнопок "Открыть в Яндекс картах", "Создать свою карту" и "Доехать на такси", а также удаление плашки с условиями пользования Яндекс сделано через CSS


        var points = <?php echo json_encode($points); ?>;

            points.forEach(function(point) {
                var myPlacemark = new ymaps.Placemark(point.coordinates, {
                    balloonContent: point.name // Название точки
                }, {
                    iconLayout: 'default#image',
                    iconImageHref: '/img/point.svg',
                    iconImageSize: [30, 42],
                    iconImageOffset: [-3, -42]
                });

                myMap.geoObjects.add(myPlacemark); // Добавление метки на карту
            });
      }
    </script>
  </head>
  <body>
    <div class="map" id="map"></div>
  </body>
</html>
