// favoritePoint.js - обработка избранных точек на карте

// Глобальная переменная для хранения последней удаленной точки
window.lastRemovedPoint = null;

/**
 * Привязывает обработчик к кнопке добавления в избранное в балуне
 * @param {number} pointId - ID точки
 */
function attachFavoriteButtonHandler(pointId) {
    setTimeout(() => {
        const button = document.querySelector('#addFavoritePoint');
        if (button) {
            button.onclick = async () => {
                const result = await addToFavorites(pointId);
                if (result && result.success) {
                    updateFavoriteButtonInBalloon(pointId, result.action === 'added');
                }
            };
        }
    }, 100);
}

/**
 * Добавляет/удаляет точку из избранного
 * @param {number} pointId - ID точки
 * @returns {Promise<Object>} - Результат операции
 */
async function addToFavorites(pointId) {
    try {
        const authCheck = await fetch('../include/auth.php');
        const authData = await authCheck.json();
        
        if (!authData.isAuth) {
            alert('Для добавления в избранное необходимо авторизоваться');
            return;
        }
        
        const response = await fetch('../include/save_point.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `point_id=${pointId}`
        });
        
        if (!response.ok) throw new Error('Ошибка сервера');
        
        const data = await response.json();
        
        if (data.success) {
            // Обновляем список избранных точек
            await loadFavoritePoints();
            
            return data;
        } else {
            throw new Error(data.message || 'Произошла ошибка');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Произошла ошибка при работе с избранным');
        throw error;
    }
}

/**
 * Загружает список избранных точек
 * @returns {Promise<void>}
 */
async function loadFavoritePoints() {
    try {
        const response = await fetch('/include/get_favorite_points.php');
        if (!response.ok) return;
        
        const data = await response.json();
        if (data.success) {
            renderFavoritePoints(data.points);
        }
    } catch (error) {
        console.error('Ошибка загрузки избранного:', error);
    }
}

/**
 * Отображает список избранных точек
 * @param {Array} points - Массив точек
 */
function renderFavoritePoints(points) {
    const container = document.getElementById('favoritePoints');
    if (!container) return;

    container.innerHTML = '';

    if (!points || points.length === 0) {
        container.innerHTML = '<li class="no-points">Нет избранных точек</li>';
        return;
    }

    points.forEach(point => {
        const li = document.createElement('li');
        li.className = 'view__favorite-item';
        li.dataset.pointId = point.id;
        
        if (point.latitude && point.longitude) {
            li.dataset.lat = point.latitude;
            li.dataset.lng = point.longitude;
        }

        li.innerHTML = `
            <span class="point-name">${point.name || 'Без названия'}</span>
            <button class="view__favorite-remove">×</button>
        `;

        // Обработчик клика по точке (фокусировка на карте)
        li.addEventListener('click', function(e) {
            if (!e.target.classList.contains('view__favorite-remove')) {
                focusPointOnMap(point);
            }
        });

        // Обработчик удаления точки
        li.querySelector('.view__favorite-remove').addEventListener('click', function(e) {
            e.stopPropagation();
            removeFromFavorites(point.id, li, point);
        });

        container.appendChild(li);
    });
}

/**
 * Удаляет точку из избранного
 * @param {number} pointId - ID точки
 * @param {HTMLElement} element - DOM-элемент точки
 * @param {Object} point - Объект точки
 */
async function removeFromFavorites(pointId, element, point) {
    try {
        const response = await fetch('../include/save_point.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `point_id=${pointId}`
        });
        
        if (!response.ok) throw new Error('Ошибка сервера');
        
        const data = await response.json();
        
        if (data.success) {
            // Сохраняем информацию о последней удаленной точке
            window.lastRemovedPoint = point;
            
            // Анимация удаления элемента из списка
            element.style.transition = 'opacity 0.3s, transform 0.3s';
            element.style.opacity = '0';
            element.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                element.remove();
                
                // Обновляем кнопку в балуне
                updateFavoriteButtonInBalloon(pointId, false);
                
                // Закрываем и переоткрываем балун
                reopenBalloonForPoint(point);
                
                // Проверяем, остались ли точки
                const container = document.getElementById('favoritePoints');
                if (container && container.children.length === 0) {
                    container.innerHTML = '<li class="no-points">Нет избранных точек</li>';
                }
            }, 300);
        } else {
            throw new Error(data.message || 'Произошла ошибка');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Произошла ошибка при удалении из избранного');
    }
}

/**
 * Закрывает и переоткрывает балун для точки
 * @param {Object} point - Объект точки
 */
function reopenBalloonForPoint(point) {
    if (!window.myMap || !point.latitude || !point.longitude) return;
    
    // Находим метку на карте
    window.myMap.geoObjects.each(geoObject => {
        if (geoObject.geometry) {
            const geoCoords = geoObject.geometry.getCoordinates();
            if (Math.abs(geoCoords[0] - point.latitude) < 0.0001 &&
                Math.abs(geoCoords[1] - point.longitude) < 0.0001) {
                
                // Закрываем балун, если открыт
                if (geoObject.balloon.isOpen()) {
                    geoObject.balloon.close();
                    
                    // Переоткрываем через небольшой таймаут
                    setTimeout(() => {
                        geoObject.balloon.open();
                    }, 300);
                }
                return false;
            }
        }
    });
}

/**
 * Обновляет состояние кнопки в балуне
 * @param {number} pointId - ID точки
 * @param {boolean} isFavorite - Статус избранного
 */
function updateFavoriteButtonInBalloon(pointId, isFavorite) {
    const balloons = document.querySelectorAll('.ymaps-balloon');
    
    balloons.forEach(balloon => {
        const infoId = balloon.querySelector('#informationId');
        if (infoId && parseInt(infoId.value) === pointId) {
            const favoriteBtn = balloon.querySelector('#addFavoritePoint');
            if (favoriteBtn) {
                favoriteBtn.classList.toggle('baloon__favorite-gold', isFavorite);
                favoriteBtn.classList.toggle('baloon__favorite-grey', !isFavorite);
                
                // Анимация для визуальной обратной связи
                favoriteBtn.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    favoriteBtn.style.transform = '';
                }, 200);
            }
        }
    });
}

/**
 * Фокусирует карту на точке
 * @param {Object} point - Объект точки
 */
function focusPointOnMap(point) {
    if (!window.myMap || !point.latitude || !point.longitude) return;
    
    const coords = [point.latitude, point.longitude];
    window.myMap.setCenter(coords, 15);
    
    // Открываем балун для этой точки
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

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    loadFavoritePoints();
});