function addPlacemark(map) {
  let myPlacemark = new ymaps.Placemark(
    map.getCenter(),
    {},
    {
      draggable: true,
    }
  );

  map.geoObjects.add(myPlacemark);

  // Обновление адреса и координат при перемещении метки
  myPlacemark.events.add("dragend", function () {
    updateAddressAndCoordinates(myPlacemark.geometry.getCoordinates());
  });

  // Функция для обновления адреса и координат
  function updateAddressAndCoordinates(coords) {
    ymaps.geocode(coords).then(function (res) {
      var firstGeoObject = res.geoObjects.get(0);
      var address = firstGeoObject.getAddressLine();
      document.getElementById("address").value = address;

      // Вывод координат
      var latitude = coords[0].toFixed(6);
      var longitude = coords[1].toFixed(6);
      document.getElementById("coordinates1").value = longitude;
      document.getElementById("coordinates2").value = latitude;
    });
  }

  // Привязываем обработчик события к кнопке
  document
    .getElementById("burger-close")
    .addEventListener("click", function () {
      if (myPlacemark) {
        map.geoObjects.remove(myPlacemark);
        myPlacemark = null; // Очищаем ссылку на метку
        document.getElementById("address").textContent = ""; // Очищаем адрес
        document.getElementById("coordinates").textContent = ""; // Очищаем координаты
      }
    });

  // Инициализация адреса и координат при загрузке страницы
  updateAddressAndCoordinates(map.getCenter());
}
