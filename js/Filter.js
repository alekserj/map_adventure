document.addEventListener("DOMContentLoaded", function () {
  const checkboxes = document.querySelectorAll('#filter-form input[type="checkbox"]');

  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateFilters);
  });

  function updateFilters() {
    const selectedCategories = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);

    if (!window.placemarks || !window.myMap) {
      console.warn("Метки или карта ещё не загружены");
      return;
    }

    window.placemarks.forEach(marker => {
      const { placemark, category, isOnMap } = marker;

      if (selectedCategories.includes(category)) {
        if (!marker.isOnMap) {
          window.myMap.geoObjects.add(placemark);
          marker.isOnMap = true;
        }
      } else {
        if (marker.isOnMap) {
          window.myMap.geoObjects.remove(placemark);
          marker.isOnMap = false;
        }
      }
    });
  }
});
