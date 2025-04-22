<?php
    session_start();
    require_once 'include/data.php';
    require_once 'include/functions.php';
    
    $isAuth = isset($_SESSION['user']);
    $user = $_SESSION['user'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      if (function_exists('clearValidation')) {
          clearValidation();
      }
  }
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <title>Версия 0.3.0</title>
    <link rel="stylesheet" href="/css/normalize.css" />
    <link rel="stylesheet" href="/css/choices.min.css" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
    />
    <link
      rel="stylesheet"
      href="https://unpkg.com/simplebar@latest/dist/simplebar.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
    <script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="/css/style.css" />
    <script
      src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=d5ab4df7-e824-4704-8f48-be9d6f558514"
      type="text/javascript"
    ></script>
  </head>
  <body>
    <section class="view">
      <nav class="view__nav">
        <ul class="view__list">
          <li class="view__item">
            <button class="view__add-object-menu-btn" id="route">
              <img  src="/img/route2.svg" alt="маршрут" />
            </button>
          </li>
          <li class="view__item">
            <button class="view__add-object-menu-btn">
              <img  src="/img/filter.svg" alt="фильтр" />
            </button>
          </li>
          <li class="view__item">
            <button class="view__add-object-menu-btn" id="plus">
              <img class="view__add-object-menu-img" src="/img/close.svg" alt="добавить объект" />
            </button>
          </li>
          <li class="view__item">
            <button class="view__add-object-menu-btn" id="account">
              <img src="/img/account.svg" alt="аккаунт" style="" />
            </button>
          </li>
        </ul>
      </nav>

      <div class="view__route-menu" id="route-menu">
        <ul class="view__route-btn-list">
          <li>
            <button class="view__add-object-menu-btn" id="route-menu-close">
              <img src="/img/collapse.svg" alt="cвернуть меню" />
            </button>
          </li>
          <li>
            <button class="view__add-object-menu-btn" id="reset-route">
              <img src="/img/close.svg" alt="закрыть меню" />
            </button>
          </li>
        </ul>
        <form
          class="view__form"
          id="route-form"
        >
        <h2 class="view__title">Маршрут</h2>
        <ul id="route-list" class="route-list"></ul>
        <button class="view__form-btn" type="button" id="addFavoriteRoute">Добавить в избранное</button>
        <h2 class="view__title">Тип маршрута:</h2>
        <ul class="route__list-btn" id="route-type-buttons">
          <li><button type="button" data-type="auto" class="route-btn active">Автомобиль</button></li>
          <li><button type="button" data-type="pedestrian" class="route-btn">Пешком</button></li>
          <li><button type="button" data-type="masstransit" class="route-btn">Общественный транспорт</button></li>
        </ul>
        </form>
      </div>

      <div class="view__add-object-menu" id="add-object-menu">
        <button class="view__add-object-menu-btn" id="plus-close">
          <img src="/img/close.svg" alt="закрыть меню" />
        </button>
        <form
          class="view__form"
          id="view-form"
        >
          <h2 class="view__title">Добавить объект</h2>
          <input
            class="view_input"
            type="text"
            placeholder="Название"
            name="object_name"
            id = "object_name"
          />
          <input
            class="view_input"
            type="hidden"
            id="coordinates1"
            name="longitude"
            id = "longitude"
          />
          <input
            class="view_input"
            type="hidden"
            id="coordinates2"
            name="latitude"
            id = "latitude"
          />
          <input
            class="view_input"
            type="text"
            id="address"
            name="address"
            id = "address"
          />
          <input
            class="view_input"
            type="hidden"
            id="valueSelect"
            name="select"
          />
          <!-- Категория достопремечательности -->
          <select class="view_select" id="selectCustom" required>
            <option>Выберите категорию достопремечательности</option>
            <option value="Музеи">Музеи</option>
            <option value="Культурные">Культурные</option>
            <option value="Архитектурные">Архитектурные</option>
            <option value="Природные">Природные</option>
            <option value="Религиозные">Религиозные</option>
          </select>
          <!-- Категория достопремечательности -->
          <button class="view__form-btn" type="submit" id="addObjectDB">
            Добавить
          </button>
        </form>
      </div>

      <div class="view__add-information-menu" id="add-information-menu">
        <button class="view__add-object-menu-btn" id="add-information-menu-close">
          <img src="/img/close.svg" alt="закрыть меню" />
        </button>
        <form
          class="view__form"
          id="add-information-menu-form"
        >
          <div class="customScroll" data-simplebar>
            <textarea
              class="view__textarea"
              type="text"
              placeholder="Описание"
              name="object_description"
              id = "objectDescription"
            ></textarea>
            <input 
              type="file" 
              id="fileInput" 
              accept="image/*" 
              multiple 
              style="display: none;"
            >
            <button type="button" class="view__form-btn view__form-btn_add-img" id="addObjectInformationImg">
            </button>
            <ul class="view__picture-list" id="pictureList"></ul>
          </div>
          <button class="view__form-btn" type="submit" id="addObjectDB">
            Добавить
          </button>
        </form>
      </div>

      <div class="view__account-menu" id="account-menu" style="<?php echo $isAuth ? 'display: none;' : ''; ?>">
        <button class="view__add-object-menu-btn" id="account-menu-close">
            <img src="/img/close.svg" alt="закрыть меню" />
        </button>
        <form class="view__form" id="account-form" method="post" action="/include/login.php">
          <h2 class="view__title">Войти в Личный кабинет</h2>     
          <div class="view__account-section">
              <strong class="view__account-title">Введите E-mail</strong>
              <input
                  class="view_input <?php echo hasValidationError('email') ? 'is-invalid' : ''; ?>"
                  type="email"
                  placeholder="E-mail"
                  name="account-login"
                  value="<?php echo old('email'); ?>"
              />
              <?php if(hasValidationError('email')): ?>
                  <small class="view__validation-error">
                      <?php echo validationErrorMessage('email'); ?>
                  </small>
              <?php endif; ?>
          </div>
          <div class="view__account-section">
              <strong class="view__account-title">Введите пароль</strong>
              <input
                  class="view_input <?php echo hasValidationError('password') ? 'is-invalid' : ''; ?>"
                  type="password"
                  placeholder="Пароль"
                  name="account-password"
              />
              <?php if(hasValidationError('password')): ?>
                  <small class="view__validation-error">
                      <?php echo validationErrorMessage('password'); ?>
                  </small>
              <?php endif; ?>
              <button class="view__account-btn">Забыли пароль?</button>
          </div>
          <?php if(hasMessage('error') || !empty($_SESSION['validation'])): ?>
              <div class="view__account-error">
                  <?php 
                  if(hasMessage('error')) {
                      echo getMessage('error') . '<br>';
                  }
                  if(!empty($_SESSION['validation'])) {
                      foreach($_SESSION['validation'] as $error) {
                          echo htmlspecialchars($error) . '<br>';
                      }
                  }
                  ?>
              </div>
          <?php endif; ?>
          <button class="view__form-btn" type="submit">Войти</button>
          <button class="view__form-btn view__form-btn_white" type="button" id="createAccount">Создать аккаунт</button>
        </form>
      </div>

      <div class="view__registration-menu" id="registration-menu">
        <button class="view__add-object-menu-btn" id="registration-menu-close">
          <img src="/img/close.svg" alt="закрыть меню" />
        </button>
        <form
          class="view__form"
          id="registration-form"
          method="post"
          action="/include/register.php"
        >
          <h2 class="view__title">Регистрация</h2>
          <div class="view__account-section">
            <strong class = "view__account-title">Придумайте nickname</strong>
            <input
              class="view_input"
              type="text"
              placeholder="Nickname"
              name="registration-login"
              id = "registration-login"
            />
          </div>
          <div class="view__account-section">
            <strong class = "view__account-title">Введите ваш E-mail</strong>
            <input
              class="view_input"
              type="email"
              placeholder="E-mail"
              name="registration-email"
              id = "registration-email"
            />
          </div>
          <div class="view__account-section">
            <strong class = "view__account-title">Придумайте пароль</strong>
            <input
              class="view_input"
              type="password"
              placeholder = "Пароль"
              name="registration-password"
              id="registration-password"
            />
          </div>
          <div class="view__account-section">
            <strong class = "view__account-title">Подтвердите пароль</strong>
            <input
              class="view_input"
              type="password"
              placeholder = "Подтвердите пароль"
              name="registration-password-confirm"
              id="registration-password-confirm"
            />
          </div>
          <button class="view__form-btn" type="submit">
            Зарегистрироваться
          </button>
          <button class="view__form-btn view__form-btn_white" type="button" id="accountExists">
            Уже есть аккаунт?
          </button>
        </form>
      </div>

      <div class="view__cabinet-menu" id="cabinet-menu" style="<?php echo $isAuth ? '' : 'display: none;'; ?>">
        <button class="view__add-object-menu-btn" id="cabinet-menu-close">
          <img src="/img/close.svg" alt="закрыть меню" />
        </button>
        <div class="view__form">
          <h2 class="view__title">Личный кабинет @<?php echo htmlspecialchars($user['nickname'] ?? ''); ?></h2>
          <button class="view__form-btn" type="submit">
            Избранные места
          </button>
          <button class="view__form-btn view__form-btn_white" type="button" id="accountExists">
            Избранные маршруты
          </button>
          <a href="/include/logout.php" class="view__form-btn view__form-btn_red">Выйти</a>
        </div>
      </div>

      <div class="view__map" id="map"></div>
    </section>
  </body>
  <script>
      ymaps.ready(init);
      function init() {
        myMap = new ymaps.Map("map", {
        center: [51.73470896697555, 36.19070462924623],
        zoom: 13,
        });

        myMap.controls.remove("trafficControl"); //удаления плашки пробок
        myMap.controls.remove("typeSelector"); //удаление переключателя слоев карты
        myMap.controls.remove("fullscreenControl"); //удаление полноэкранного режима
        myMap.controls.remove("rulerControl"); //удаление линейки
        //удаление кнопок "Открыть в Яндекс картах", "Создать свою карту" и "Доехать на такси", а также удаление плашки с условиями пользования Яндекс сделано через CSS

        setMapInstance(myMap);

        var points = <?php echo json_encode($points); ?>;

        points.forEach(function(point) {
          var content = `
            ${point.swiperHtml}
            <div>
              <h1 class="baloon__title">
                ${point.name}
                <button class="baloon__favorite baloon__favorite-grey" id="addFavoritePoint"></button>
              </h1>
              <p><strong>Улица:</strong> ${point.street || 'Не указано'}</p> 
              <p><strong>Категория:</strong> ${point.category || 'Не указано'}</p> 
              <p><strong>Описание:</strong> ${point.description || 'Не указано'}</p> 
              <button class="baloon__btn" id ="toRoute">Добавить в маршрут</button>
              <button class="baloon__information-btn" id="addInformation">Добавить информацию о объекте</button>
              <input type="hidden" value=${point.id} id="informationId"></input>
            </div>`
            ;
            var iconHref = '/img/point.svg';
            var iconSize = [40, 40];
            var iconOffset = [-20, -40];
            if (point.category === 'Религиозные') {
                iconHref = '/img/religion.svg';
            } else if (point.category === 'Культурные') {
                iconHref = 'img/culture.svg';
            } else if (point.category === 'Музеи') {
                iconHref = 'img/museum.svg';
            } else if (point.category === 'Природные') {
                iconHref = 'img/park.svg';
            } else if (point.category === 'Архитектурные') {
                iconHref = 'img/architecture.svg';
            } else {
                iconSize = [30, 42];
                iconOffset = [-15, -42];
            } 

          var myPlacemark = new ymaps.Placemark(point.coordinates, {
            balloonContent: content,
            }, {
                  iconLayout: 'default#image',
                  iconImageHref: iconHref,
                  iconImageSize: iconSize,
                  iconImageOffset: iconOffset
                });
                myPlacemark.events.add('balloonopen', function() {
                  const swiper = new Swiper('.swiper', {
                    loop: true,
                    pagination: {
                      el: '.swiper-pagination',
                    },
                  });
                  myPlacemark.swiperInstance = swiper;

                  const pointId = document.getElementById('informationId').value;
                  attachFavoriteButtonHandler(pointId);

                  checkFavoriteStatus(pointId);
                                  
                  const script = document.createElement('script');
                  script.src = '/js/addObjectInformation.js';
                  document.body.appendChild(script);

                  attachRouteButtonHandler(point.coordinates, point.name);
                });
  
                myPlacemark.events.add('balloonclose', function() {

                if (myPlacemark.swiperInstance) {
                  myPlacemark.swiperInstance.destroy();
                  myPlacemark.swiperInstance = null;
                }

            
              });
              myMap.geoObjects.add(myPlacemark); // Добавление метки на карту
            });

        document.getElementById("plus").addEventListener("click", function () {
          addPlacemark(myMap);
        });
      }

      function checkFavoriteStatus(pointId) {
        fetch('/include/auth.php')
          .then(response => response.json())
          .then(data => {
            if (!data.isAuth) return;
            
            fetch(`/include/check_favorite.php?point_id=${pointId}`)
              .then(response => response.json())
              .then(data => {
                if (data.isFavorite) {
                  const button = document.querySelector('#addFavoritePoint');
                  if (button) {
                    button.classList.remove('baloon__favorite-grey');
                    button.classList.add('baloon__favorite-gold');
                  }
                }
              });
          });
      }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/routeHandler.js"></script>
    <script src="/js/addData.js"></script>
    <script src="/js/addObject.js"></script>
    <script src="/js/addObjectPicture.js"></script>
    <script src="/js/choices.min.js"></script>
    <script src="/js/favoritePoint.js"></script>
    <script src="/js/openRouteMenu.js"></script>
    <script src="/js/openAddMenu.js"></script>
    <script src="/js/openAccountMenu.js"></script>
    <script src="/js/openRegistrationMenu.js"></script>
    <script src="/js/selectValue.js"></script>  
    <script>
      const element = document.querySelector("#selectCustom");
      const choises = new Choices(element, {
        searchEnabled: false,
      });
    </script>
</html>

