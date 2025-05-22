window.addEventListener("DOMContentLoaded", function () {
  document.querySelector("#search").addEventListener("click", function () {
    document.querySelector("#search-menu").classList.toggle("menu-is-active");
  });

  document
    .querySelector("#search-menu-close")
    .addEventListener("click", function () {
      document.querySelector("#search-menu").classList.toggle("menu-is-active");
    });
    document.getElementById('search_object').addEventListener('click', function(e) {
    e.preventDefault();
    const searchQuery = document.getElementById('search-object').value.trim().toLowerCase();
    
    if (!searchQuery) {
        alert('Введите название или адрес объекта');
        return;
    }
    
    let foundPlacemark = null;
    
    window.placemarks.forEach(pm => {
        const name = pm.pointData.name.toLowerCase();
        const address = pm.pointData.street ? pm.pointData.street.toLowerCase() : '';
        
        if (name.includes(searchQuery)) {
            foundPlacemark = pm.placemark;
            return;
        }
        
        if (address && address.includes(searchQuery)) {
            foundPlacemark = pm.placemark;
            return;
        }
    });
    
    if (foundPlacemark) {
        myMap.setCenter(foundPlacemark.geometry.getCoordinates(), 17, {
            checkZoomRange: true
        });

        foundPlacemark.balloon.open();

        document.getElementById('search-menu').classList.remove('menu-is-active');
        document.getElementById('search-object').value = '';
    } else {
        alert('Объект не найден');
    }
});

document.getElementById('search-object').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('search_object').click();
    }
});
});

