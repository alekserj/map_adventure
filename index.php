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
    <title>Версия 0.3.10</title>
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
            <button class="view__add-object-menu-btn" id="search">
              <img class="view__add-object-menu-pic" src="/img/search.svg" alt="поиск" />
            </button>
          </li>
          <li class="view__item">
            <button class="view__add-object-menu-btn" id="route">
              <img class="view__add-object-menu-pic" src="/img/route2.svg" alt="маршрут" />
            </button>
          </li>
          <li class="view__item">
            <button class="view__add-object-menu-btn" id="filter">
              <img class="view__add-object-menu-pic" src="/img/filter.svg" alt="фильтр" />
            </button>
          </li>
          <li class="view__item">
            <button class="view__add-object-menu-btn" id="plus">
              <img class="view__add-object-menu-img view__add-object-menu-pic" src="/img/close.svg" alt="добавить объект" />
            </button>
          </li>
          <li class="view__item">
            <button class="view__add-object-menu-btn" id="account">
              <img class="view__add-object-menu-pic" src="/img/account.svg" alt="аккаунт" style="" />
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
        <div class="customScroll view__route-menu-scroll" data-simplebar>
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
            <button type="button" class="view__form-btn" id="optimize-route-btn">Оптимизировать маршрут</button>

            <h2 class="view__title">Кольцевой маршрут</h2>
            <div class="circular-route-block">
              <div class="circular-route-inputs">
                <label>
                  Длина маршрута (км):
                  <input type="number" id="circular-distance" min="1" max="100" value="5" />
                </label>
                <label>
                  Время в пути (мин):
                  <input type="number" id="circular-duration" min="1" max="300" value="30" />
                </label>
              </div>
              <div class="circular-route-block-btns">
                <button type="button" class="view__form-btn" id="select-circular-center">Выбрать место на карте</button>
                <button type="button" class="view__form-btn circle" id="build-circular-route">Построить кольцевой маршрут</button>
              </div>
            </div>
            <div class="route-info" id="route-info">
              <p class="route-info__distance">Длина маршрута: 0 км</p>
              <p class="route-info__time">Время в пути: 0 мин</p>
            </div>
            <button class="view__form-btn" type="button" id="toggle-instructions-btn" style="display: none;">Показать подробности</button>
            <div id="navigation-instructions" style="display: none;"></div>
          </form>
        </div>
      </div>

      <div class="view__filter-menu" id="filter-menu">
        <button class="view__add-object-menu-btn" id="filter-menu-close">
            <img src="/img/close.svg" alt="закрыть меню" />
          </button>
          <form class="view__form" id="filter-form">
            <h2 class="view__title">Фильтр достопримечательностей</h2>
            <ul class="filter__checkbox-list">
              <li><label><input type="checkbox" value="Музеи" checked /> Музеи</label></li>
              <li><label><input type="checkbox" value="Культурные" checked /> Культурные</label></li>
              <li><label><input type="checkbox" value="Архитектурные" checked /> Архитектурные</label></li>
              <li><label><input type="checkbox" value="Природные" checked /> Природные</label></li>
              <li><label><input type="checkbox" value="Религиозные" checked /> Религиозные</label></li>
            </ul>
            <h2 class="view__title">Список достопримечательностей</h2>
          </form>
          <div class="customScroll reviews-scroll" data-simplebar>
            <ul class="view__reviews-menu-list" id="objects-list">

            </ul>
          </div>
      </div>

      <div class="view__search-menu" id="search-menu">
        <form class="view__form view__form_reviews view__form_reviews_search" id="search-form">
          <input type="hidden" id="review-object-id" name="object_id" value="">
          <textarea
            class="view__textarea reviews-textarea"
            type="text"
            placeholder="Введите название или адрес объекта"
            name="search-object"
            id = "search-object"
          ></textarea>
          <button class="view__add-object-menu-btn" id="search_object">
              <img class="view__add-object-menu-pic" src="/img/search.svg" alt="оставить отзыв" />
          </button>
        </form>
        <button class="view__add-object-menu-btn" id="search-menu-close">
          <img src="/img/close.svg" alt="закрыть меню" />
        </button>
      </div>

      <div class="view__obj-info-menu" id="obj-info-menu">
        <button class="view__add-object-menu-btn" id="obj-info-menu-close">
          <img src="/img/close.svg" alt="закрыть меню" />
        </button>
        <div class="view__form">
          <h2 class="view__title">@Название достопримечательности</h2>
          <div class="swiper">
            <div class="swiper-wrapper" id="obj-info-swiper-wrapper">

            </div>
            <div class="swiper-pagination"></div>

            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>

            <div class="swiper-scrollbar"></div>
          </div>
          <div class="customScroll reviews-scroll inf-scroll" data-simplebar>
            <p style="margin: 0; padding-left: 5px; padding-right: 5px; text-align: justify;">@описание</p>
          </div>
        </div>
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

      <div class="view__cabinet-menu" id="cabinet-menu" style="<?php echo ($isAuth && $user['email'] !== 'admin@admin.adm') ? '' : 'display: none;'; ?>">
        <ul class="view__route-btn-list">
          <li>
            <a class="view__add-object-menu-btn" href="/include/logout.php">
              <img src="/img/logout.svg" alt="выйти из аккаунта">
            </a>
          </li>
          <li>
            <button class="view__add-object-menu-btn" id="cabinet-menu-close">
              <img src="/img/close.svg" alt="закрыть меню" />
            </button>
          </li>
        </ul>
        <div class="view__form">
          <h2 class="view__title">Личный кабинет @<?php echo htmlspecialchars($user['nickname'] ?? ''); ?></h2>
          <div class="view__favorite-chapters">
            <h3 class="view__favorite-title">Избранные места</h3>
            <h3 class="view__favorite-title">Избранные маршруты</h3>
            <div class="favoriteScroll" data-simplebar>
                <ul class="view__favorite-list" id="favoritePoints">
                </ul>
            </div>
            <div class="favoriteScroll" data-simplebar>
                <ul class="view__favorite-list" id="favoriteRoutes">
                </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="modal" id="route-name-modal" style="display: none;">
        <div class="modal-content">
          <button class="close-modal-btn" id="close-route-name-modal">&times;</button>
          <h2>Введите название маршрута</h2>
          <input type="text" id="route-name-input" placeholder="Название маршрута">
          <button id="confirm-route-name">Сохранить</button>
        </div>
      </div>

      <div class="view__reviews-menu" id="reviews-menu">
        <button class="view__add-object-menu-btn" id="reviews-menu-close">
          <img src="/img/close.svg" alt="закрыть меню" />
        </button>
        <div class="view__reviews-menu-title">
          <h2 class="view__title">Отзывы</h2>
        </div>
        <div class="customScroll reviews-scroll" data-simplebar>
          <ul class="view__reviews-menu-list" id="reviews-list">

          </ul>
        </div>
        <form
          class="view__form view__form_reviews"
          id="reviews-menu-form"
        >
          <input type="hidden" id="review-object-id" name="object_id" value="">
          <textarea
            class="view__textarea reviews-textarea"
            type="text"
            placeholder="Ваш отзыв"
            name="review"
            id = "review"
          ></textarea>
          <button class="view__add-object-menu-btn" id="send_review">
              <img class="view__add-object-menu-pic" src="/img/review.svg" alt="оставить отзыв" />
          </button>
        </form>
      </div>

      <div id="map-container" style="position: relative; width: 100%; height: 100%;">
        <div class="view__map" id="map"></div>
          <button id="toggle-location-btn">Показать местоположение</button>
        </div>
     
        <div class="view__admin-menu" id="admin-menu" style="<?php echo ($isAuth && $user['email'] === 'admin@admin.adm') ? '' : 'display: none;'; ?>">
          <ul class="view__route-btn-list">
              <li>
                  <a class="view__add-object-menu-btn" href="/include/logout.php">
                      <img src="/img/logout.svg" alt="выйти из аккаунта">
                  </a>
              </li>
              <li>
                  <button class="view__add-object-menu-btn" id="admin-menu-close">
                      <img src="/img/close.svg" alt="закрыть меню" />
                  </button>
              </li>
          </ul>
          <div class="view__form">
              <h2 class="view__title">Панель администратора</h2>
              <div class="view__admin-sections">
                  <div class="admin-section">
                      <h3 class="view__admin-title">Ожидающие одобрения объекты</h3>
                      <div class="customScroll reviews-scroll admin-applications" data-simplebar>
                          <ul class="view__admin-list" id="pending-objects">
                              <?php
                              $mysqli = new mysqli("localhost", "root", "", "map");
                              $sql = "SELECT p.id, p.name, p.street, p.category 
                                      FROM points p
                                      JOIN point_status ps ON p.id = ps.point_id
                                      WHERE ps.is_approved = 0";
                              $result = $mysqli->query($sql);
                              
                              if ($result && $result->num_rows > 0) {
                                  while ($row = $result->fetch_assoc()) {
                                      echo '<li class="view__admin-item">
                                              <h4 class="view__admin-item-title">'.$row['name'].'</h4>
                                              <p>Категория: '.$row['category'].'</p>
                                              <p>Адрес: '.$row['street'].'</p>
                                              <div class="view__admin-buttons">
                                                <button class="admin-btn admin-btn-approve approve-btn" data-id="'.$row['id'].'" data-type="point">Одобрить</button>
                                                <button class="admin-btn admin-btn-reject  reject-btn" data-id="'.$row['id'].'" data-type="point">Отклонить</button>
                                              </div>
                                            </li>';
                                  }
                              } else {
                                  echo '<li class="no-points">Нет объектов на модерации</li>';
                              }
                              ?>
                          </ul>
                      </div>
                  </div>
                  <div class="admin-section">
                      <h3 class="view__admin-title">Ожидающие одобрения описания</h3>
                      <div class="customScroll reviews-scroll admin-applications" data-simplebar>
                          <ul class="view__admin-list" id="pending-descriptions">
                              <?php
                              $sql = "SELECT p.id, p.name, ps.pending_description 
                                      FROM points p
                                      JOIN point_status ps ON p.id = ps.point_id
                                      WHERE ps.pending_description IS NOT NULL AND ps.is_info_approved = 0";
                              $result = $mysqli->query($sql);
                              
                              if ($result && $result->num_rows > 0) {
                                  while ($row = $result->fetch_assoc()) {
                                      echo '<li class="view__admin-item">
                                              <h4 class="view__admin-item-title">'.$row['name'].'</h4>
                                              <div class="customScroll reviews-scroll admin-applications admin-application-scroll" data-simplebar>
                                                Новое описание: '.substr($row['pending_description'], 0, 1000000000000).'
                                              </div>
                                              <div class="view__admin-buttons">
                                                <button class="admin-btn admin-btn-approve approve-btn" data-id="'.$row['id'].'" data-type="description">Одобрить</button>
                                                <button class="admin-btn admin-btn-reject reject-btn" data-id="'.$row['id'].'" data-type="description">Отклонить</button>
                                              </div>
                                            </li>';
                                  }
                              } else {
                                  echo '<li class="no-points">Нет описаний на модерации</li>';
                              }
                              ?>
                          </ul>
                      </div>
                  </div>
                  <div class="admin-section">
                      <h3 class="view__admin-title">Ожидающие одобрения изображения</h3>
                      <div class="customScroll reviews-scroll admin-applications" data-simplebar>
                          <ul class="view__admin-list" id="pending-images">
                              <?php
                              $sql = "SELECT p.id as point_id, p.name, pic.id as pic_id, pic.link 
                                      FROM pictures pic
                                      JOIN points p ON pic.object_id = p.id
                                      WHERE pic.is_pending = 1";
                              $result = $mysqli->query($sql);
                              
                              if ($result && $result->num_rows > 0) {
                                  while ($row = $result->fetch_assoc()) {
                                      echo '<li class="view__admin-item">
                                              <h4 class="view__admin-item-title">'.$row['name'].'</h4>
                                              <img class="view__admin-item-img" src="/include'.$row['link'].'" style="max-width: 100px; max-height: 100px;">
                                              <div class="view__admin-buttons">
                                                <button class="admin-btn admin-btn-approve approve-btn" data-id="'.$row['pic_id'].'" data-type="image">Одобрить</button>
                                                <button class="admin-btn admin-btn-reject reject-btn" data-id="'.$row['pic_id'].'" data-type="image">Отклонить</button>
                                              </div>
                                            </li>';
                                  }
                              } else {
                                  echo '<li class="no-points">Нет изображений на модерации</li>';
                              }
                              $mysqli->close();
                              ?>
                          </ul>
                      </div>
                  </div>
              </div>
              <h2 class="view__title">Список достопримечательностей</h2>
              <div class="customScroll reviews-scroll admin-scroll" data-simplebar>
                <ul class="view__reviews-menu-list" id="objects-admin-list">
                    <?php foreach ($adminPoints as $point): ?>
                    <li class="view__reviews-menu-item">
                        <div class="view__reviews-menu-item-content">
                            <h3 class="view__reviews-menu-item-title"><?= htmlspecialchars($point['name']) ?></h3>
                            <p class="view__reviews-menu-item-text"><strong>Категория:</strong> <?= htmlspecialchars($point['category']) ?></p>
                            <p class="view__reviews-menu-item-text"><strong>Адрес:</strong> <?= htmlspecialchars($point['street'] ?? 'Не указан') ?></p>
                            <button class="view__form-btn view__obj-list-btn delete-btn" 
                                    data-id="<?= $point['id'] ?>">Удалить</button>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
              </div>
          </div>
      </div>    

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

        initLocationToggle(myMap);
    
        var points = <?php echo json_encode($points); ?>;

        setPointsInstance(points);

        window.placemarks = [];

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
              <p><strong>Описание:</strong> ${getFirstParagraph(point.description) || 'Не указано'} <button class="baloon__information-btn baloon__information-btn_mod" id="full-obj-information">Подробнее</button></p>
              <button class="baloon__btn" id ="toRoute">Добавить в маршрут</button>
              <div class="baloon__title-menu">
                <button class="baloon__information-btn" id="addReview">Отзывы</button>
                <button class="baloon__information-btn" id="addInformation">Добавить информацию о объекте</button>
              </div>
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

            function getFirstParagraph(description) {
              if (!description) return null;
              const firstParagraph = description.split('\n')[0];
              return firstParagraph.length > 100 ? firstParagraph.substring(0, 100) + '...' : firstParagraph;
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
                  
                  const favoriteBtn = document.querySelector('#addFavoritePoint');
                  if (favoriteBtn) {
                    favoriteBtn.onclick = async () => {
                      const result = await addToFavorites(pointId);
                      if (result && result.success) {
                        favoriteBtn.classList.toggle('baloon__favorite-grey', result.action !== 'added');
                        favoriteBtn.classList.toggle('baloon__favorite-gold', result.action === 'added');
                        window.updateAllOpenBalloons(pointId, result.action === 'added');
                      }
                    };
                  }

                  const addInfoBtn = document.querySelector('#addInformation');
                  if (addInfoBtn) {
                    addInfoBtn.onclick = function() {
                      fetch('/include/auth.php')
                        .then(response => response.json())
                        .then(data => {
                          if (!data.isAuth) {
                            alert('Для добавления информации необходимо войти в аккаунт');
                            document.querySelector("#account-menu").classList.add("menu-is-active");
                            return;
                          }
                          
                          document.querySelector("#add-information-menu").classList.add("menu-is-active");
                          document.querySelector("#viewTitle")?.remove();
                          const title = document.createElement("h2")
                          title.classList.add("view__title")
                          title.id = "viewTitle"
                          title.innerHTML = `Добавить информацию о объекте <br> "${document.querySelector(".baloon__title").textContent}"`
                          const inputId = document.createElement("input")
                          inputId.type = "hidden"
                          inputId.name = "objectId"
                          inputId.value = document.getElementById("informationId").value
                          document.querySelector("#add-information-menu-form").prepend(title)
                          document.querySelector("#add-information-menu-form").appendChild(inputId)
                          document.getElementById('pictureList').innerHTML = ""
                        });
                    };
                  }

                  document.querySelector('#addReview').addEventListener("click", function() {
                    const objectId = document.getElementById("informationId").value;
                    document.querySelector("#reviews-menu").classList.add("menu-is-active");
                    document.querySelector("#review-object-id").value = objectId;
                    loadReviews(objectId); 
                  });
                  
                  document.getElementById('reviews-menu-close').addEventListener('click', function() {
                    document.querySelector("#reviews-menu").classList.remove("menu-is-active");
                    document.getElementById('reviews-list').innerHTML = '';
                  });

                  document.querySelector('#full-obj-information').addEventListener('click', function() {
                    document.querySelector("#obj-info-menu").classList.add("menu-is-active");
                    
                    const titleElement = document.querySelector("#obj-info-menu .view__title");
                    const descriptionElement = document.querySelector("#obj-info-menu .customScroll p");
                    const swiperWrapper = document.querySelector("#obj-info-swiper-wrapper");
                    
                    titleElement.textContent = point.name;
                    descriptionElement.textContent = point.description || 'Описание отсутствует';
                    
                    swiperWrapper.innerHTML = '';
                    
                    if (point.images && point.images.length > 0) {
                      point.images.forEach(image => {
                        const slide = document.createElement('div');
                        slide.className = 'swiper-slide';
                        slide.style.backgroundImage = `url(/include${image})`;
                        swiperWrapper.appendChild(slide);
                      });
                    } else {
                      const slide = document.createElement('div');
                      slide.className = 'swiper-slide';
                      slide.style.backgroundImage = 'url(/img/hero_img.jpg)';
                      swiperWrapper.appendChild(slide);
                    }
                    
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

                  checkFavoriteStatus(pointId);                  
                  attachRouteButtonHandler(point.coordinates, point.name);
                });
  
                myPlacemark.events.add('balloonclose', function() {

                if (myPlacemark.swiperInstance) {
                  myPlacemark.swiperInstance.destroy();
                  myPlacemark.swiperInstance = null;
                }

            
              });
              const activeCategories = Array.from(document.querySelectorAll('#filter-form input[type="checkbox"]:checked')).map(cb => cb.value);
              const isInitiallyVisible = activeCategories.includes(point.category);

              if (isInitiallyVisible) {
                myMap.geoObjects.add(myPlacemark);
              }

              window.placemarks.push({
                placemark: myPlacemark,
                category: point.category,
                isOnMap: isInitiallyVisible,
                pointData: point
              });
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
    <script>
      document.addEventListener('DOMContentLoaded', function() {
          document.querySelectorAll('.approve-btn, .reject-btn').forEach(btn => {
              btn.addEventListener('click', function() {
                  const id = this.getAttribute('data-id');
                  const type = this.getAttribute('data-type');
                  const isApprove = this.classList.contains('approve-btn');
                  const item = this.closest('.view__admin-item');
                  const containerId = type === 'image' ? 'pending-images' : 
                                    type === 'description' ? 'pending-info' : 'pending-objects';
                  
                  fetch('/include/moderate.php', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                      },
                      body: JSON.stringify({
                          id: id,
                          type: type,
                          action: isApprove ? 'approve' : 'reject'
                      })
                  })
                  .then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          item.remove();
                          const container = document.getElementById(containerId);
                          if (container.querySelectorAll('.view__admin-item').length === 0) {
                              container.innerHTML = '<li class="no-points">Нет элементов на модерации</li>';
                          }
                      } else {
                          alert('Ошибка: ' + data.message);
                      }
                  });
              });
          });
      });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/routeHandler.js"></script>
    <script src="/js/addData.js"></script>
    <script src="/js/addObject.js"></script>
    <script src="/js/addObjectPicture.js"></script>
    <script src="/js/addObjectInformation.js"></script>
    <script src="/js/addReview.js"></script>
    <script src="/js/choices.min.js"></script>
    <script src="/js/deletePoint.js"></script>
    <script src="/js/favoritePoint.js"></script>
    <script src="/js/openRouteMenu.js"></script>
    <script src="/js/openFilterMenu.js"></script>
    <script src="/js/openSearchMenu.js"></script>
    <script src="/js/openObjectInformationMenu.js"></script>
    <script src="/js/openAddMenu.js"></script>
    <script src="/js/openAccountMenu.js"></script>
    <script src="/js/openRegistrationMenu.js"></script>
    <script src="/js/selectValue.js"></script>  
    <script src="/js/loadFavoritesPoints.js"></script>
    <script src="/js/loadFavoritesRoutes.js"></script>
    <script src="/js/Filter.js"></script>
    <script src="/js/Location.js"></script>
    <script>
      const element = document.querySelector("#selectCustom");
      const choises = new Choices(element, {
        searchEnabled: false,
      });
    </script>         
</html>