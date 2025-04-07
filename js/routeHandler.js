let mapInstance = null;

// Устанавливаем экземпляр карты
function setMapInstance(map) {
    mapInstance = map;
    addRoutePanel(map);
}

// Добавление routePanel на карту
function addRoutePanel(map) {
    const routePanelControl = new ymaps.control.RoutePanel({
        options: {
            visible: false,
            showHeader: true,
            maxWidth: '300',
        }
    });

    // Настройки панели маршрутов по умолчанию
    routePanelControl.routePanel.options.set({
        types: { auto: true }, // Только автомобильный маршрут
    });

    // Добавляем панель на карту
    map.controls.add(routePanelControl);

    // Сохраняем панель в объект карты
    map.routePanelControl = routePanelControl;
}

// установка обработчика
function setupRouteTypeButtons() {
    const buttons = document.querySelectorAll('.route-btn');
  
    if (!buttons.length) {
      console.log('Кнопки маршрута не найдены');
      return;
    }
  
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        const type = btn.dataset.type;
  
        // Обновление маршрута на карте
        mapInstance.routePanelControl.routePanel.options.set({
          types: {
            auto: type === 'auto',
            pedestrian: type === 'pedestrian',
            masstransit: type === 'masstransit'
          }
        });
  
        // Подсветка активной кнопки
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
  
        console.log(`Тип маршрута установлен: ${type}`);
      });
    });
  }


// Строим маршрут по нажатию кнопки "Добавить в маршрут"
function attachRouteButtonHandler(destinationCoords) {
    setTimeout(() => {
        const button = document.querySelector('#toRoute');
        if (button) {
            button.onclick = () => {
                if (!mapInstance || !mapInstance.routePanelControl) {
                    alert("Карта или панель маршрутов не инициализированы.");
                    return;
                }

                const fallbackCoords = [51.73470896697555, 36.19070462924623];

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const userCoords = [position.coords.latitude, position.coords.longitude];
                        buildRoute(userCoords, destinationCoords);
                    },
                    function () {
                        buildRoute(fallbackCoords, destinationCoords);
                    }
                );
            };
        }
    }, 100);
}


function buildRoute(fromCoords, toCoords) {
    mapInstance.routePanelControl.routePanel.state.set({
        from: fromCoords,
        to: toCoords
    });

    // Обратное геокодирование для отображения адресов
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

    // Показываем форму маршрута, если она скрыта
    const menu = document.getElementById('route-menu');
    if (menu && !menu.classList.contains('menu-is-active')) {
        menu.classList.add('menu-is-active');
    }
}

