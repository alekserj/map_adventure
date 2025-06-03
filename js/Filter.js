document.addEventListener("DOMContentLoaded", function () {
  const checkboxes = document.querySelectorAll('#filter-form input[type="checkbox"]');
  const objectsList = document.getElementById('objects-list');

  function addObjectToList(point) {
    const li = document.createElement('li');
    li.className = 'view__reviews-menu-item';
    li.innerHTML = `
      <div class="view__reviews-menu-item-content">
        <h3 class="view__reviews-menu-item-title">${point.name}</h3>
        <p class="view__reviews-menu-item-text"><strong>Категория:</strong> ${point.category}</p>
        <p class="view__reviews-menu-item-text"><strong>Адрес:</strong> ${point.street || 'Не указан'}</p>
        <ul class="filter-btn-list">
          <li><button class="view__form-btn view__obj-list-btn" data-coords="${point.coordinates.join(',')}">Показать на карте</button></li>
          <li><button class="view__form-btn view__obj-list-btn" id="full-information">О объекте</button></li>
        </ul>
      </div>
    `;
    objectsList.appendChild(li);

    li.querySelector('button').addEventListener('click', function() {
      const coords = this.getAttribute('data-coords').split(',').map(Number);
      window.myMap.setCenter(coords, 17, {
        checkZoomRange: true
      });
      
      const placemark = window.placemarks.find(p => 
        p.placemark.geometry.getCoordinates()[0] === coords[0] && 
        p.placemark.geometry.getCoordinates()[1] === coords[1]
      );
      
      if (placemark) {
        placemark.placemark.balloon.open();
      }
    });

    li.querySelector('#full-information').addEventListener('click', function() {
      document.querySelector("#obj-info-menu").classList.add("menu-is-active");
      
      const titleElement = document.querySelector("#obj-info-menu .view__title");
      const descriptionElement = document.querySelector("#obj-info-menu .customScroll p");
      const swiperWrapper = document.querySelector("#obj-info-swiper-wrapper");
      
      titleElement.textContent = point.name;
      descriptionElement.textContent = point.description || 'Описание отсутствует';
      
      // Очищаем слайдер
      swiperWrapper.innerHTML = '';
      
      // Загружаем изображения для этого объекта
      fetch(`/include/get_images.php?object_id=${point.id}`)
        .then(response => response.json())
        .then(images => {
          if (images && images.length > 0) {
            images.forEach(image => {
              const slide = document.createElement('div');
              slide.className = 'swiper-slide';
              slide.style.backgroundImage = `url(/include${image})`;
              swiperWrapper.appendChild(slide);
            });
          } else {
            // Если изображений нет, добавляем заглушку
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';
            slide.style.backgroundImage = 'url(/img/hero_img.jpg)';
            swiperWrapper.appendChild(slide);
          }
          
          // Инициализируем Swiper
          if (window.objInfoSwiper) {
            window.objInfoSwiper.destroy();
          }
          
          window.objInfoSwiper = new Swiper('#obj-info-menu .swiper', {
            loop: true,
            pagination: {
              el: '#obj-info-menu .swiper-pagination',
              clickable: true,
            },
            navigation: {
              nextEl: '#obj-info-menu .swiper-button-next',
              prevEl: '#obj-info-menu .swiper-button-prev',
            },
          });
        });
    });
  }

  function updateFilters() {
    const selectedCategories = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);

    objectsList.innerHTML = '';

    if (!window.placemarks) {
      console.warn("Метки ещё не загружены");
      return;
    }

    window.placemarks.forEach(marker => {
      const { placemark, category, isOnMap, pointData } = marker;

      if (window.myMap) {
        if (selectedCategories.includes(category)) {
          if (!isOnMap) {
            window.myMap.geoObjects.add(placemark);
            marker.isOnMap = true;
          }
        } else {
          if (isOnMap) {
            window.myMap.geoObjects.remove(placemark);
            marker.isOnMap = false;
          }
        }
      }

      if (selectedCategories.includes(category)) {
        addObjectToList(pointData);
      }
    });

    if (objectsList.children.length === 0) {
      objectsList.innerHTML = '<li class="no-objects">Выберите категории для отображения объектов</li>';
    }
  }

  function initialize() {
    const checkPlacemarks = setInterval(() => {
      if (window.placemarks) {
        clearInterval(checkPlacemarks);
        updateFilters();
      }
    }, 100);
  }

  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateFilters);
  });

  initialize();
});