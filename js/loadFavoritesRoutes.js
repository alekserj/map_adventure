document.addEventListener('DOMContentLoaded', loadFavoriteRoutes);

function loadFavoriteRoutes() {
    fetch('../include/get_favorite_routes.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderFavoriteRoutes(data.routes || []);
            } else {
                console.error('Error loading routes:', data.message);
                renderFavoriteRoutes([]);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            renderFavoriteRoutes([]);
        });
}

function renderFavoriteRoutes(routes) {
    const routesList = document.getElementById('favoriteRoutes');
    if (!routesList) return;

    routesList.innerHTML = '';

    if (!routes || routes.length === 0) {
        const emptyItem = document.createElement('li');
        emptyItem.className = 'no-points';
        emptyItem.textContent = 'Нет избранных маршрутов';
        routesList.appendChild(emptyItem);
        return;
    }

    routes.forEach(route => {
        const li = document.createElement('li');
        li.className = 'favorite-point-item';
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
        deleteBtn.className = 'remove-point-btn';
        deleteBtn.innerHTML = '×';
        deleteBtn.onclick = (e) => {
            e.stopPropagation();
            deleteFavoriteRoute(route.id);
        };
        
        routeElement.appendChild(name);
        routeElement.appendChild(type);
        li.appendChild(routeElement);
        li.appendChild(deleteBtn);
        
        li.addEventListener('click', () => {
            loadRouteToMap(route);
            const cabinetMenu = document.getElementById('cabinet-menu');
            if (cabinetMenu) {
                cabinetMenu.classList.toggle("menu-is-active");
            }
        });
        
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
    resetRoute();
    routePoints = [];

    if (route.points.length > 0) {
        const firstPoint = route.points[0];
        routePoints.push({
            coords: [firstPoint.lat, firstPoint.lon],
            name: 'Стартовая точка',
            address: '',
            isStartPoint: true
        });

        const promises = route.points.slice(1).map(point => {
            return new Promise(resolve => {
                getPointNameFromDB([point.lat, point.lon], (name) => {
                    resolve({
                        coords: [point.lat, point.lon],
                        name: name || `Точка маршрута`,
                        address: ''
                    });
                });
            });
        });
        
        Promise.all(promises).then(points => {
            routePoints = routePoints.concat(points);

            currentRouteType = route.type;
            document.querySelectorAll('.route-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.type === route.type);
            });

            drawCustomRoute();

            document.getElementById('route-menu').classList.add('menu-is-active');

            updateRouteListUI();
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
            document.querySelector(`#favoriteRoutes li[data-route-id="${routeId}"]`)?.remove();
            if (currentRoute && currentRoute.properties?.get('routeId') === routeId) {
                resetRoute();
            }
        } else {
            alert('Ошибка при удалении маршрута: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}