let userLocationMarker = null;
let isLocationVisible = false;
let locationWatchId = null;

function initLocationToggle(mapInstance) {
    const toggleBtn = document.getElementById('toggle-location-btn');
    if (!toggleBtn || !navigator.geolocation) return;

    toggleBtn.addEventListener('click', () => {
        if (isLocationVisible) {
            // Остановить слежение и удалить метку
            if (locationWatchId !== null) {
                navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = null;
            }
            if (userLocationMarker) {
                mapInstance.geoObjects.remove(userLocationMarker);
                userLocationMarker = null;
            }
            isLocationVisible = false;
            toggleBtn.textContent = "Показать местоположение";
        } else {
            // Начать слежение
            locationWatchId = navigator.geolocation.watchPosition(position => {
                const coords = [position.coords.latitude, position.coords.longitude];

                if (!userLocationMarker) {
                    userLocationMarker = new ymaps.Placemark(coords, {
                        iconCaption: 'Вы здесь'
                    }, {
                        preset: 'islands#blueDotIcon'
                    });
                    mapInstance.geoObjects.add(userLocationMarker);
                } else {
                    userLocationMarker.geometry.setCoordinates(coords);
                }

                mapInstance.setCenter(coords); // Можно убрать, если не нужно постоянно центрировать
            }, err => {
                alert('Ошибка получения геопозиции: ' + err.message);
            }, {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 5000
            });

            isLocationVisible = true;
            toggleBtn.textContent = "Скрыть местоположение";
        }
    });
}
