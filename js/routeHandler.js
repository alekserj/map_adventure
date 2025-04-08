let mapInstance = null;
let routePoints = [];
let currentRoute = null;
let currentRouteType = 'auto'; // по умолчанию

// Устанавливаем экземпляр карты
function setMapInstance(map) {
  mapInstance = map;
}

// Настройка кнопок выбора типа маршрута
function setupRouteTypeButtons() {
  const buttons = document.querySelectorAll('.route-btn');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      currentRouteType = btn.dataset.type;

      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      if (routePoints.length >= 2) {
        drawCustomRoute(); // Строим маршрут заново с новым типом
      }
    });
  });
}

// Обработчик кнопки для добавления точки в маршрут
function attachRouteButtonHandler(destinationCoords) {
  setTimeout(() => {
    const button = document.querySelector('#toRoute');
    if (button) {
      button.onclick = () => {
        addRoutePoint(destinationCoords);
        openRouteMenu();
      };
    }
  }, 100);
}

// Добавляем точку в маршрут
function addRoutePoint(coords) {
  if (routePoints.length === 0) {
    // Первая точка — стартовая, берём геолокацию
    navigator.geolocation.getCurrentPosition(
      pos => {
        const from = [pos.coords.latitude, pos.coords.longitude];
        routePoints.push(from, coords);
        drawCustomRoute();  // Строим маршрут
      },
      () => {
        const fallback = [51.7347, 36.1907];
        routePoints.push(fallback, coords);
        drawCustomRoute();  // Строим маршрут
      }
    );
  } else {
    // Добавляем промежуточную точку
    routePoints.push(coords);
    drawCustomRoute();  // Строим маршрут
  }
}

// Функция для рисования маршрута с использованием multiRoute
function drawCustomRoute() {
  if (!mapInstance) return;

  // Удаляем старый маршрут
  if (currentRoute) {
    mapInstance.geoObjects.remove(currentRoute);
  }

  // Строим новый маршрут с использованием multiRoute
  const multiRoute = new ymaps.multiRouter.MultiRoute({
    referencePoints: routePoints,  // Точки маршрута
    params: {
      routingMode: currentRouteType  // Тип маршрута (авто, пешком, общественный транспорт)
    }
  }, {
    boundsAutoApply: true  // Автоматически применяем границы карты
  });

  // Добавляем маршрут на карту
  mapInstance.geoObjects.add(multiRoute);
  currentRoute = multiRoute;  // Сохраняем маршрут для дальнейшего использования
}

// Обновляем поля формы с адресами
function updateRouteFormFields(fromCoords, toCoords) {
  ymaps.geocode(fromCoords).then(res => {
    const address = res.geoObjects.get(0)?.getAddressLine() || fromCoords.join(', ');
    const fromInput = document.getElementById('route-from');
    if (fromInput) fromInput.value = address;
  });

  ymaps.geocode(toCoords).then(res => {
    const address = res.geoObjects.get(0)?.getAddressLine() || toCoords.join(', ');
    const toInput = document.getElementById('route-to');
    if (toInput) toInput.value = address;
  });
}

// Открытие меню маршрута
function openRouteMenu() {
  const menu = document.getElementById('route-menu');
  if (menu && !menu.classList.contains('menu-is-active')) {
    menu.classList.add('menu-is-active');
  }
}
