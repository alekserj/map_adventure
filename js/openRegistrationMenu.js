window.addEventListener("DOMContentLoaded", function () {
    const registrationForm = document.getElementById("registration-form");
    const registrationMenu = document.querySelector("#registration-menu");
    
    // Переход от формы входа к регистрации
    document.querySelector("#createAccount").addEventListener("click", function (e) {
        e.preventDefault();
        document.querySelector("#account-menu").classList.remove("menu-is-active");
        registrationMenu.classList.add("menu-is-active");
    });
  
    // Переход от регистрации к форме входа
    document.querySelector("#accountExists").addEventListener("click", function (e) {
        e.preventDefault();
        registrationMenu.classList.remove("menu-is-active");
        document.querySelector("#account-menu").classList.add("menu-is-active");
    });
  
    // Закрытие меню регистрации
    document.querySelector("#registration-menu-close").addEventListener("click", function () {
        registrationMenu.classList.remove("menu-is-active");
    });
  
    // Валидация формы регистрации
    if (registrationForm) {
        registrationForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            // Очищаем предыдущие ошибки
            clearErrors();
            
            // Получаем значения полей
            const nickname = document.getElementById("registration-login").value.trim();
            const email = document.getElementById("registration-email").value.trim();
            const password = document.getElementById("registration-password").value;
            const passwordConfirm = document.getElementById("registration-password-confirm").value;
            
            let isValid = true;
            
            // Валидация nickname
            if (!nickname) {
                showError("registration-login", "Введите nickname");
                isValid = false;
            } else if (nickname.length < 3) {
                showError("registration-login", "Nickname должен быть не менее 3 символов");
                isValid = false;
            }
            
            // Валидация email
            if (!email) {
                showError("registration-email", "Введите email");
                isValid = false;
            } else if (!validateEmail(email)) {
                showError("registration-email", "Неверный формат email");
                isValid = false;
            }
            
            // Валидация пароля
            if (!password) {
                showError("registration-password", "Введите пароль");
                isValid = false;
            } else if (password.length < 6) {
                showError("registration-password", "Пароль должен быть не менее 6 символов");
                isValid = false;
            }
            
            // Подтверждение пароля
            if (password !== passwordConfirm) {
                showError("registration-password-confirm", "Пароли не совпадают");
                isValid = false;
            }
            
            // Если валидация прошла успешно, отправляем форму
            if (isValid) {
                registrationForm.submit();
            }
        });
    }
    
    // Обработка успешной регистрации (если есть флаг в URL)
    if (window.location.search.includes('registration=success')) {
        registrationMenu.classList.remove("menu-is-active");
        document.querySelector("#account-menu").classList.add("menu-is-active");
        
        // Можно добавить отображение сообщения о успешной регистрации
        const successMessage = document.createElement('div');
        successMessage.className = 'view__account-success';
        successMessage.textContent = 'Регистрация прошла успешно! Теперь вы можете войти.';
        document.querySelector("#account-form").prepend(successMessage);
        
        // Удаляем сообщение через 5 секунд
        setTimeout(() => {
            successMessage.remove();
        }, 5000);
    }
    
    // Функция для отображения ошибки
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
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
    
    // Функция для очистки ошибок
    function clearErrors() {
        // Удаляем классы ошибок
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Удаляем сообщения об ошибках
        document.querySelectorAll('.view__validation-error').forEach(el => {
            el.remove();
        });
    }
    
    // Функция для проверки email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
  });