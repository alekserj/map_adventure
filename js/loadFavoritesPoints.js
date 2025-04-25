class FavoritePointsLoader {
    constructor() {
        this.init();
    }

    async init() {
        try {
            await this.loadFavoritePoints();
        } catch (error) {
            console.error('Ошибка инициализации:', error);
        }
    }

    async loadFavoritePoints() {
        try {
            const response = await fetch('/include/get_favorite_points.php');
            if (!response.ok) return;
            
            const data = await response.json();
            if (data.success) {
                this.renderFavoritePoints(data.points);
            }
        } catch (error) {
            console.error('Ошибка загрузки избранного:', error);
        }
    }

    renderFavoritePoints(points) {
        const container = document.getElementById('favoritePoints');
        if (!container) return;

        container.innerHTML = '';

        if (!points || points.length === 0) {
            container.innerHTML = '<li class="no-points">Нет избранных точек</li>';
            return;
        }

        points.forEach(point => {
            if (!point.latitude || !point.longitude) return;

            const li = document.createElement('li');
            li.className = 'favorite-point-item';
            li.dataset.pointId = point.id;
            li.dataset.lat = point.latitude;
            li.dataset.lng = point.longitude;

            li.innerHTML = `
                <span class="point-name">${point.name || 'Без названия'}</span>
                <button class="remove-point-btn" title="Удалить из избранного">
                    ×
                </button>
            `;

            li.addEventListener('click', (e) => {
                if (!e.target.classList.contains('remove-point-btn')) {
                    this.focusPointOnMap(point);
                }
            });

            const removeBtn = li.querySelector('.remove-point-btn');
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.removeFavoritePoint(point.id, li, point);
            });

            container.appendChild(li);
        });
    }

    async removeFavoritePoint(pointId, element, point) {
        try {
            const response = await fetch('/include/save_point.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `point_id=${pointId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                window.lastRemovedPoint = point;
                
                element.style.transition = 'opacity 0.3s, transform 0.3s';
                element.style.opacity = '0';
                element.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    element.remove();
                    if (window.updateFavoriteButtonInBalloon) {
                        window.updateFavoriteButtonInBalloon(pointId, false);
                    }

                    if (window.reopenBalloonForPoint) {
                        window.reopenBalloonForPoint(point);
                    }

                    this.loadFavoritePoints();
                }, 300);
            } else {
                throw new Error(data.message || 'Ошибка удаления');
            }
        } catch (error) {
            console.error('Ошибка удаления:', error);
        }
    }
    
    focusPointOnMap(point) {
        if (!window.myMap || !point.latitude || !point.longitude) return;
        
        const coords = [point.latitude, point.longitude];
        window.myMap.setCenter(coords, 15);
        
        window.myMap.geoObjects.each(geoObject => {
            if (geoObject.geometry) {
                const geoCoords = geoObject.geometry.getCoordinates();
                if (Math.abs(geoCoords[0] - coords[0]) < 0.0001 &&
                    Math.abs(geoCoords[1] - coords[1]) < 0.0001) {
                    geoObject.balloon.open();
                    return false;
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FavoritePointsLoader();
});