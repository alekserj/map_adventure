<?php
    require_once'include/data.php';
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <title>Версия 0.1.0</title>
    <link rel="stylesheet" href="/css/normalize.css" />
    <link rel="stylesheet" href="/css/choices.min.css" />
    <link rel="stylesheet" href="/css/style.css" />
    <script
      src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=d5ab4df7-e824-4704-8f48-be9d6f558514"
      type="text/javascript"
    ></script>
  </head>
  <body>
    <section class="view">
      <button class="view__menu-btn view__menu-btn_burger" id="burger">
        <img class="view__img" src="/img/close.svg" alt="открыть меню" />
      </button>
      <div class="view__menu" id="view-menu">
        <button class="view__menu-btn" id="burger-close">
          <img src="/img/close.svg" alt="закрыть меню" />
        </button>
        <form
          class="view__form"
          id="view-form"
          
        >
          <h2 class="view__title">Добавить объект</h2>
          <input
            class="view_input"
            type="text"
            placeholder="Название"
            name="object_name"
            id = "object_name"
          />
          <input
            class="view_input"
            type="hidden"
            id="coordinates1"
            name="longitude"
            id = "longitude"
          />
          <input
            class="view_input"
            type="hidden"
            id="coordinates2"
            name="latitude"
            id = "latitude"
          />
          <input
            class="view_input"
            type="text"
            id="address"
            name="address"
            id = "address"
          />
          <input
            class="view_input"
            type="hidden"
            id="valueSelect"
            name="select"
          />
          <!-- Категория достопремечательности -->
          <select class="view_select" id="selectCustom" required>
            <option>Выберите категорию достопремечательности</option>
            <option value="Музеи">Музеи</option>
            <option value="Культурные">Культурные</option>
            <option value="Архитектурные">Архитектурные</option>
            <option value="Природные">Природные</option>
            <option value="Религиозные">Религиозные</option>
          </select>
          <!-- Категория достопремечательности -->
          <button class="view__form-btn" type="submit" id="addObjectDB">
            Добавить
          </button>
        </form>
      </div>
      <div class="view__map" id="map"></div>
    </section>
    <script>
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
          var content = `
            <strong>Название:</strong> ${point.name}<br> 
            <strong>Улица:</strong> ${point.street || 'Не указано'}<br> 
            <strong>Категория:</strong> ${point.category || 'Не указано'} `;
          
            var iconHref = '/img/point.svg';
            var iconSize = [40, 40];
            var iconOffset = [-20, -40];
            if (point.category === 'Религиозные') {
                iconHref = '/img/religion.svg';
            } else if (point.category === 'Культурные') {
                iconHref = 'img/culture.svg';
            } else if (point.category === 'Музеи') {
                iconHref = 'img/museum.svg';
            } else if (point.category === 'Природные') {
                iconHref = 'img/park.svg';
            } else if (point.category === 'Архитектурные') {
                iconHref = 'img/architecture.svg';
            } else {
                iconSize = [30, 42];
                iconOffset = [-15, -42];
            } 

          var myPlacemark = new ymaps.Placemark(point.coordinates, {
            balloonContent: content,
            }, {
                  iconLayout: 'default#image',
                  iconImageHref: iconHref,
                  iconImageSize: iconSize,
                  iconImageOffset: iconOffset
                });
              myMap.geoObjects.add(myPlacemark); // Добавление метки на карту
            });

        document.getElementById("burger").addEventListener("click", function () {
          addPlacemark(myMap);
        });
      }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/addData.js"></script>
    <script src="/js/addObject.js"></script>
    <script src="/js/choices.min.js"></script>
    <script src="/js/openMenu.js"></script>
    <script src="/js/selectValue.js"></script>  
    <script>
      const element = document.querySelector("#selectCustom");
      const choises = new Choices(element, {
        searchEnabled: false,
      });
    </script>
  </body>
</html>

