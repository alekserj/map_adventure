document.addEventListener("DOMContentLoaded", function() {
  // Элементы меню
  const accountBtn = document.getElementById("account");
  const accountMenu = document.getElementById("account-menu");
  const cabinetMenu = document.getElementById("cabinet-menu");
  const accountMenuClose = document.getElementById("account-menu-close");
  const cabinetMenuClose = document.getElementById("cabinet-menu-close");
  const createAccountBtn = document.getElementById("createAccount");
  const accountExistsBtn = document.getElementById("accountExists");

  // Инициализация - скрываем все меню
  hideAllMenus();

  // Обработчик кнопки аккаунта
  accountBtn.addEventListener("click", async function() {
      try {
          const authStatus = await checkAuth();
          
          if (authStatus.isAuth) {
              // Показываем кабинет, скрываем остальное
              showMenu(cabinetMenu);
              hideMenu(accountMenu);
          } else {
              // Показываем форму входа, скрываем остальное
              showMenu(accountMenu);
              hideMenu(cabinetMenu);
          }
      } catch (error) {
          console.error("Auth check failed:", error);
          // В случае ошибки показываем форму входа
          showMenu(accountMenu);
          hideMenu(cabinetMenu);
      }
  });

  // Закрытие меню
  accountMenuClose.addEventListener("click", () => hideMenu(accountMenu));
  cabinetMenuClose.addEventListener("click", () => hideMenu(cabinetMenu));

  // Переключение между меню
  createAccountBtn.addEventListener("click", (e) => {
      e.preventDefault();
      hideMenu(accountMenu);
      showMenu(document.getElementById("registration-menu"));
  });

  accountExistsBtn.addEventListener("click", (e) => {
      e.preventDefault();
      hideMenu(document.getElementById("registration-menu"));
      showMenu(accountMenu);
  });

  // Проверка авторизации при загрузке
  async function initialize() {
      try {
          const authStatus = await checkAuth();
          if (authStatus.isAuth) {
              alert("Пользователь успешно авторизирован");
          }
      } catch (error) {
          console.log("Auth check on load failed:", error);
      }
  }
  
  initialize();
});

// Функция проверки авторизации
async function checkAuth() {
  try {
      const response = await fetch('include/check-auth.php', {
          headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
          },
          credentials: 'include' // Для передачи кук
      });

      if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      return data;
  } catch (error) {
      console.error("Error checking auth:", error);
      throw error;
  }
}

// Вспомогательные функции
function showMenu(menuElement) {
  menuElement.classList.add("menu-is-active");
}

function hideMenu(menuElement) {
  menuElement.classList.remove("menu-is-active");
}

function hideAllMenus() {
  document.querySelectorAll('.view__account-menu, .view__cabinet-menu, .view__registration-menu').forEach(menu => {
      menu.classList.remove("menu-is-active");
  });
}

// Валидация формы авторизации
const accountForm = document.getElementById("account-form");
if (accountForm) {
    accountForm.addEventListener("submit", function(e) {
        e.preventDefault();
        
        // Очищаем предыдущие ошибки
        clearAuthErrors();
        
        // Получаем значения полей
        const email = accountForm.querySelector("input[name='account-login']").value.trim();
        const password = accountForm.querySelector("input[name='account-password']").value.trim();
        
        let isValid = true;
        
        // Валидация email
        if (!email) {
            showAuthError("account-login", "Введите email");
            isValid = false;
        } else if (!validateEmail(email)) {
            showAuthError("account-login", "Неверный формат email");
            isValid = false;
        }
        
        // Валидация пароля
        if (!password) {
            showAuthError("account-password", "Введите пароль");
            isValid = false;
        }
        
        // Если валидация прошла успешно, отправляем форму
        if (isValid) {
            accountForm.submit();
        }
    });
}

// Функция для отображения ошибки авторизации
function showAuthError(fieldName, message) {
    const field = accountForm.querySelector(`input[name='${fieldName}']`);
    if (!field) return;
    
    // Добавляем класс ошибки к полю
    field.classList.add('is-invalid');
    
    // Создаем элемент с сообщением об ошибке
    const errorElement = document.createElement('small');
    errorElement.className = 'view__validation-error';
    errorElement.textContent = message;
    
    // Вставляем сообщение об ошибке после поля
    field.parentNode.insertBefore(errorElement, field.nextSibling);
}

// Функция для очистки ошибок авторизации
function clearAuthErrors() {
    // Удаляем классы ошибок
    accountForm.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    // Удаляем сообщения об ошибках
    accountForm.querySelectorAll('.view__validation-error').forEach(el => {
        el.remove();
    });
    
    // Удаляем общее сообщение об ошибке, если есть
    const generalError = accountForm.querySelector('.view__account-error');
    if (generalError) {
        generalError.remove();
    }
}

// Функция для проверки email (можно использовать ту же, что и в регистрации)
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}