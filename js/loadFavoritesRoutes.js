document.addEventListener('DOMContentLoaded', loadFavoriteRoutes);

function loadFavoriteRoutes() {
    fetch('../include/get_favorite_routes.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.routes.length > 0) {
                renderFavoriteRoutes(data.routes);
            } else if (!data.success) {
                console.error('Error loading routes:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderFavoriteRoutes(routes) {
    const routesList = document.getElementById('favoriteRoutes');
    if (!routesList) return;

    routesList.innerHTML = '';

    routes.forEach(route => {
        const li = document.createElement('li');
        li.className = 'view__favorite-item';
        li.dataset.routeId = route.id;
        
        const routeElement = document.createElement('div');
        routeElement.className = 'view__favorite-element';
        
        const name = document.createElement('h3');
        name.className = 'view__favorite-name';
        name.textContent = route.name;
        
        const type = document.createElement('p');
        type.className = 'view__favorite-type';
        type.textContent = getRouteTypeName(route.type);
        
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'view__favorite-delete';
        deleteBtn.innerHTML = '×';
        deleteBtn.onclick = (e) => {
            e.stopPropagation();
            deleteFavoriteRoute(route.id);
        };
        
        routeElement.appendChild(name);
        routeElement.appendChild(type);
        li.appendChild(routeElement);
        li.appendChild(deleteBtn);
        
        li.addEventListener('click', () => loadRouteToMap(route));
        
        routesList.appendChild(li);
    });
}

function getRouteTypeName(type) {
    const types = {
        'auto': 'Автомобиль',
        'pedestrian': 'Пешком',
        'masstransit': 'Общественный транспорт'
    };
    return types[type] || type;
}

function loadRouteToMap(route) {
    // Очищаем текущий маршрут
    resetRoute();
    
    // Конвертируем точки в формат для маршрута
    routePoints = route.points.map(point => ({
        coords: [point.lat, point.lon],
        name: `Точка маршрута`,
        address: ''
    }));
    
    // Устанавливаем тип маршрута
    currentRouteType = route.type;
    document.querySelectorAll('.route-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.type === route.type);
    });
    
    // Рисуем маршрут
    drawCustomRoute();
    
    // Открываем меню маршрута
    document.getElementById('route-menu').classList.add('menu-is-active');
    
    // Обновляем информацию о маршруте
    updateRouteListUI();
}

function deleteFavoriteRoute(routeId) {
    if (!confirm('Вы уверены, что хотите удалить этот маршрут?')) return;
    
    fetch('../include/delete_route.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ route_id: routeId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Удаляем элемент из списка
            document.querySelector(`#favoriteRoutes li[data-route-id="${routeId}"]`)?.remove();
            // Если удаляемый маршрут сейчас отображен, очищаем карту
            if (currentRoute && currentRoute.properties?.get('routeId') === routeId) {
                resetRoute();
            }
        } else {
            alert('Ошибка при удалении маршрута: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}