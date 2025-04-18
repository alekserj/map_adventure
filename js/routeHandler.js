document.addEventListener('DOMContentLoaded', setupRouteTypeButtons);
document.addEventListener('DOMContentLoaded', setupResetButton);

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
function attachRouteButtonHandler(destinationCoords, balloonTitle) {
  setTimeout(() => {
    const button = document.querySelector('#toRoute');
    if (button) {
      button.onclick = () => {
        addRoutePoint(destinationCoords, balloonTitle);
        openRouteMenu();
      };
    }
  }, 100);
}

function addRoutePoint(coords, balloonTitle) {
  // Проверка: не добавлена ли уже такая точка
  const alreadyExists = routePoints.some(p =>
    p.coords[0] === coords[0] && p.coords[1] === coords[1]
  );

  if (alreadyExists) {
    // Уведомление
    alert("Эта точка уже добавлена в маршрут.");
    return;
  }

  if (routePoints.length === 0) {
    // Первая точка — стартовая, берём геолокацию
    navigator.geolocation.getCurrentPosition(
      pos => {
        const from = [pos.coords.latitude, pos.coords.longitude];
        routePoints.push({ coords: from, name: 'Стартовая точка', address: '' });
        routePoints.push({ coords, name: balloonTitle, address: '' });
        drawCustomRoute();  // Строим маршрут
      },
      () => {
        const fallback = [51.7347, 36.1907];
        routePoints.push({ coords: fallback, name: 'Стартовая точка', address: '' });
        routePoints.push({ coords, name: balloonTitle, address: '' });
        drawCustomRoute();  // Строим маршрут
      }
    );
  } else {
    // Добавляем промежуточную точку
    routePoints.push({ coords, name: balloonTitle, address: '' });
    drawCustomRoute();  // Строим маршрут
  }
}


function drawCustomRoute() {
  if (!mapInstance) return;

  if (currentRoute) {
    mapInstance.geoObjects.remove(currentRoute);
  }

  const multiRoute = new ymaps.multiRouter.MultiRoute({
    referencePoints: routePoints.map(point => point.coords),
    params: {
      routingMode: currentRouteType
    }
  }, {
    boundsAutoApply: true
  });
  mapInstance.geoObjects.add(multiRoute);
  currentRoute = multiRoute;

  // Обновим список чуть позже
  setTimeout(updateRouteListUI, 50);
  enableRouteListSorting();

  // Получим адреса для каждой точки
  getAddressesForRoutePoints();
}

// Открытие меню маршрута
function openRouteMenu() {
  const menu = document.getElementById('route-menu');
  if (menu && !menu.classList.contains('menu-is-active')) {
    menu.classList.add('menu-is-active');
  }
}

function updateRouteListUI() {
  const list = document.getElementById('route-list');
  if (!list) return;

  list.innerHTML = ''; // Очищаем список

  routePoints.forEach((point, index) => {
    const li = document.createElement('li');
    li.dataset.index = index;
    li.classList.add('route-list-item'); // добавим класс

    const content = document.createElement('div');
    content.classList.add('route-content');

    const name = document.createElement('h3');
    name.classList.add('route-point-name');
    name.textContent = point.name;

    const address = document.createElement('p');
    address.classList.add('route-point-address');
    address.textContent = point.address || 'Загружается...';

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.classList.add('remove-point-btn');
    removeBtn.innerHTML = '×';
    removeBtn.title = 'Удалить точку';
    removeBtn.onclick = () => removeRoutePoint(index);

    content.appendChild(name);
    content.appendChild(address);
    li.appendChild(content);
    li.appendChild(removeBtn);
    list.appendChild(li);

  });
}

function enableRouteListSorting() {
  const list = document.getElementById('route-list');
  if (!list) return;

  new Sortable(list, {
    animation: 150,
    onEnd: function (evt) {
      const newPoints = [];
      const items = list.querySelectorAll('li');
      items.forEach(li => {
        const index = parseInt(li.dataset.index);
        newPoints.push(routePoints[index]);
      });
      routePoints = newPoints;
      drawCustomRoute();
    }
  });
}

// Функция для получения адресов для каждой точки
function getAddressesForRoutePoints() {
  routePoints.forEach((point, index) => {
    const geocoder = ymaps.geocode(point.coords);
    geocoder.then(res => {
      const firstGeoObject = res.geoObjects.get(0);
      const address = firstGeoObject.getAddressLine();
      routePoints[index].address = address;
      updateRouteListUI(); // Обновляем список после получения адреса
    });
  });
}

function setupResetButton() {
  const resetButton = document.getElementById('reset-route');
  if (resetButton) {
    resetButton.addEventListener('click', () => {
      resetRoute();
    });
  }
}

// Функция для сброса маршрута
function resetRoute() {
  routePoints = [];
  if (currentRoute) {
    mapInstance.geoObjects.remove(currentRoute);
    currentRoute = null;
  }
  updateRouteListUI();  // Очищаем UI списка маршрута
}

function removeRoutePoint(index) {
  routePoints.splice(index, 1);
  drawCustomRoute(); // Перестраиваем маршрут
}