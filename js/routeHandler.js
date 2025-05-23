document.addEventListener('DOMContentLoaded', setupRouteTypeButtons);
document.addEventListener('DOMContentLoaded', setupResetButton);
document.addEventListener('DOMContentLoaded', setupRouteModalHandlers);
document.addEventListener("DOMContentLoaded", initInstructionsToggle);


let mapInstance = null;
let routePoints = [];
let currentRoute = null;
let currentRouteType = 'auto';

function setMapInstance(map) {
  mapInstance = map;
}

function setupRouteTypeButtons() {
  const buttons = document.querySelectorAll('.route-btn');
  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      currentRouteType = btn.dataset.type;
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      if (routePoints.length >= 2) {
        drawCustomRoute();
      }
    });
  });

  document.getElementById('optimize-route-btn').addEventListener('click', optimizeRoute);
}

function optimizeRoute() {
  if (!routePoints || routePoints.length <= 1) return;

  const optimizedRoute = [routePoints[0]];
  const remainingPoints = [...routePoints.slice(1)];

  let lastPoint = optimizedRoute[0];

  while (remainingPoints.length > 0) {
    let closestPointIndex = -1;
    let minDistance = Infinity;

    remainingPoints.forEach((point, index) => {
      const distance = getDistance(lastPoint.coords, point.coords);
      if (distance < minDistance) {
        minDistance = distance;
        closestPointIndex = index;
      }
    });

    optimizedRoute.push(remainingPoints[closestPointIndex]);
    lastPoint = remainingPoints[closestPointIndex];
    remainingPoints.splice(closestPointIndex, 1);
  }

  routePoints = optimizedRoute;
  drawCustomRoute();
  updateRouteListUI();
}

function getDistance(coords1, coords2) {
  const [lat1, lon1] = coords1;
  const [lat2, lon2] = coords2;

  const R = 6371;
  const dLat = toRad(lat2 - lat1);
  const dLon = toRad(lon2 - lon1);

  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  
  return R * c;
}

function toRad(degrees) {
  return degrees * (Math.PI / 180);
}

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
  const alreadyExists = routePoints.some(p =>
    p.coords[0] === coords[0] && p.coords[1] === coords[1]
  );

  if (alreadyExists) {
    alert("Эта точка уже добавлена в маршрут.");
    return;
  }

  if (routePoints.length === 0) {
    navigator.geolocation.getCurrentPosition(
      pos => {
        console.log('Geo success:', pos);
        const from = [pos.coords.latitude, pos.coords.longitude];
        routePoints.push({ 
          coords: from, 
          name: 'Стартовая точка', 
          address: '',
          isStartPoint: true 
        });

        getPointNameFromDB(coords, (name) => {
          routePoints.push({ 
            coords, 
            name: name || balloonTitle, 
            address: '' 
          });
          drawCustomRoute();
        });
      },
      err => {
        console.warn('Geo error, fallback used:', err.message);
        const fallback = [51.7347, 36.1907];
        routePoints.push({ 
          coords: fallback, 
          name: 'Стартовая точка', 
          address: '',
          isStartPoint: true 
        });

        getPointNameFromDB(coords, (name) => {
          routePoints.push({ 
            coords, 
            name: name || balloonTitle, 
            address: '' 
          });
          drawCustomRoute();
        });
      }
    );
  } else {
    getPointNameFromDB(coords, (name) => {
      routePoints.push({ 
        coords, 
        name: name || balloonTitle, 
        address: '' 
      });
      drawCustomRoute();
    });
  }
}

function getPointNameFromDB(coords, callback) {
  fetch(`/include/get_point_name.php?lat=${coords[0]}&lon=${coords[1]}`)
    .then(response => response.json())
    .then(data => {
      if (data.success && data.name) {
        callback(data.name);
      } else {
        callback(null);
      }
    })
    .catch(() => callback(null));
}

function drawCustomRoute() {
  if (!mapInstance) return;

  if (routePoints.length < 2) {
    resetRoute();
    return;
  }

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

  if (window.currentRouteId) {
    multiRoute.properties.set('routeId', window.currentRouteId);
  }

  mapInstance.geoObjects.add(multiRoute);
  currentRoute = multiRoute;

  multiRoute.model.events.add('requestsuccess', function() {
    const activeRoute = multiRoute.getActiveRoute();
    if (activeRoute) {
      const distanceInMeters = activeRoute.properties.get('distance').value;
      const timeInSeconds = activeRoute.properties.get('duration').value;

      const distanceInKm = (distanceInMeters / 1000).toFixed(1);
      const timeInMinutes = Math.ceil(timeInSeconds / 60);

      const routeInfoElement = document.getElementById('route-info');
      if (routeInfoElement) {
        routeInfoElement.innerHTML = ` 
          <p class="route-info__distance">Длина маршрута: ${distanceInKm} км</p>
          <p class="route-info__time">Время в пути: ${formatTime(timeInMinutes)}</p>
        `;
        routeInfoElement.style.display = 'block';
        const toggleBtn = document.getElementById('toggle-instructions-btn');
        toggleBtn.style.display = 'block';
        toggleBtn.textContent = 'Показать подробности';
        showNavigationInstructionsFromMultiRoute(multiRoute.getActiveRoute());
      }
    }
  });

  setTimeout(updateRouteListUI, 50);
  enableRouteListSorting();
  getAddressesForRoutePoints();
}

function formatTime(minutes) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  if (hours > 0) {
    return `${hours} ч ${mins} мин`;
  }
  return `${mins} мин`;
}

function openRouteMenu() {
  const menu = document.getElementById('route-menu');
  if (menu && !menu.classList.contains('menu-is-active')) {
    menu.classList.add('menu-is-active');
  }
}

function updateRouteListUI() {
  const list = document.getElementById('route-list');
  if (!list) return;

  list.innerHTML = '';

  routePoints.forEach((point, index) => {
    const li = document.createElement('li');
    li.dataset.index = index;
    li.classList.add('route-list-item');

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

function getAddressesForRoutePoints() {
  routePoints.forEach((point, index) => {
    const geocoder = ymaps.geocode(point.coords);
    geocoder.then(res => {
      const firstGeoObject = res.geoObjects.get(0);
      const address = firstGeoObject.getAddressLine();
      routePoints[index].address = address;
      updateRouteListUI();
    });
  });
}

function setupResetButton() {
  const resetButton = document.getElementById('reset-route');
  if (resetButton) {
    resetButton.addEventListener('click', () => {
      resetRoute();
      document.querySelector("#route-menu").classList.toggle("menu-is-active");
    });
  }
}

function resetRoute() {
  routePoints = [];
  if (currentRoute) {
    mapInstance.geoObjects.remove(currentRoute);
    currentRoute = null;

    const routeInfoElement = document.getElementById('route-info');
    if (routeInfoElement) {
      routeInfoElement.style.display = 'none';
    }

    const toggleBtn = document.getElementById('toggle-instructions-btn');
    const instructionsContainer = document.getElementById('navigation-instructions');

    toggleBtn.style.display = 'none';
    instructionsContainer.style.display = 'none';
  }
  updateRouteListUI();
}


function removeRoutePoint(index) {
  routePoints.splice(index, 1);
  drawCustomRoute();
}

function setupRouteModalHandlers() {
  const routeInfoElement = document.getElementById('route-info');
    if (routeInfoElement) {
      routeInfoElement.style.display = 'none';
    }

  const addFavoriteBtn = document.getElementById('addFavoriteRoute');
  if (addFavoriteBtn) {
    addFavoriteBtn.addEventListener('click', showRouteNameModal);
  }

  const closeModalBtn = document.getElementById('close-route-name-modal');
  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', hideRouteNameModal);
  }

  const confirmBtn = document.getElementById('confirm-route-name');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', saveFavoriteRouteWithName);
  }
}

function showRouteNameModal() {
  fetch('../include/auth.php')
    .then(response => response.json())
    .then(data => {
      if (!data.isAuth) {
        alert('Для сохранения маршрута необходимо авторизоваться');
        return;
      }
      
      if (routePoints.length < 2) {
        alert('Маршрут должен содержать хотя бы 2 точки');
        return;
      }
      
      document.getElementById('route-name-modal').style.display = 'flex';
    });
}

function hideRouteNameModal() {
  document.getElementById('route-name-modal').style.display = 'none';
}

function saveFavoriteRouteWithName() {
  const routeName = document.getElementById('route-name-input').value.trim();
  
  if (!routeName) {
    alert('Пожалуйста, введите название маршрута');
    return;
  }
  
  const routeData = JSON.stringify(routePoints);
  
  fetch('../include/save_route.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      route: routeData,
      routeType: currentRouteType,
      name: routeName
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Маршрут "' + routeName + '" успешно сохранен в избранное');
      hideRouteNameModal();
      document.getElementById('route-name-input').value = '';
      loadFavoriteRoutes();
    } else {
      alert('Ошибка при сохранении маршрута: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Произошла ошибка при сохранении маршрута');
  });
}



// Инициализация поведения кнопки
function initInstructionsToggle() {
  const toggleBtn = document.getElementById('toggle-instructions-btn');
  const instructionsContainer = document.getElementById('navigation-instructions');

  if (!toggleBtn || !instructionsContainer) return;

  toggleBtn.addEventListener('click', () => {
    const visible = instructionsContainer.style.display === 'block';
    instructionsContainer.style.display = visible ? 'none' : 'block';
    toggleBtn.textContent = visible ? 'Показать подробности' : 'Скрыть подробности';
  });

  // Скрыты по умолчанию
  toggleBtn.style.display = 'none';
  instructionsContainer.style.display = 'none';
}


function showNavigationInstructionsFromMultiRoute(route) {
  const instructionsContainer = document.getElementById('navigation-instructions');
  if (!instructionsContainer) return;

  const paths = route.getPaths();
  if (!paths || paths.getLength() === 0) return;

  let html = '<h3>Пошаговая навигация:</h3><ol>';

  for (let i = 0; i < paths.getLength(); i++) {
    const path = paths.get(i);
    const segments = path.getSegments();

    for (let j = 0; j < segments.getLength(); j++) {
      const segment = segments.get(j);
      const text = segment.properties.get('text');
      if (text) {
        html += `<li>${text}</li>`;
      }
    }
  }

  html += '</ol>';
  instructionsContainer.innerHTML = html;
}