let mapInstance = null;

// Устанавливаем экземпляр карты
function setMapInstance(map) {
    mapInstance = map;
    addRoutePanel(map);
    setupRouteTypeChangeHandler();
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

// Обработчик изменения типа маршрута в форме
function setupRouteTypeChangeHandler() {
    const typeSelector = document.getElementById('routeType');
    if (!typeSelector) return;

    typeSelector.addEventListener('change', () => {
        const value = typeSelector.value;

        if (mapInstance && mapInstance.routePanelControl) {
            mapInstance.routePanelControl.routePanel.options.set({
                types: {
                    auto: value === 'auto',
                    pedestrian: value === 'pedestrian',
                    masstransit: value === 'masstransit'
                }
            });
        }
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

// Установка маршрута и заполнение полей формы
function buildRoute(fromCoords, toCoords) {
    mapInstance.routePanelControl.routePanel.state.set({
        from: fromCoords,
        to: toCoords
    });

    // Заполняем поля в форме
    const fromInput = document.getElementById('route-from');
    const toInput = document.getElementById('route-to');

    if (fromInput) fromInput.value = fromCoords.join(', ');
    if (toInput) toInput.value = toCoords.join(', ');

    // Показываем форму маршрута
    const form = document.getElementById('route-form');
    if (form) form.classList.remove('hidden');
}
