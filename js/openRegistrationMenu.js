window.addEventListener("DOMContentLoaded", function () {
    const registrationForm = document.getElementById("registration-form");
    const registrationMenu = document.querySelector("#registration-menu");
    
    document.querySelector("#createAccount").addEventListener("click", function (e) {
        e.preventDefault();
        document.querySelector("#account-menu").classList.remove("menu-is-active");
        registrationMenu.classList.add("menu-is-active");
    });
  
    document.querySelector("#accountExists").addEventListener("click", function (e) {
        e.preventDefault();
        registrationMenu.classList.remove("menu-is-active");
        document.querySelector("#account-menu").classList.add("menu-is-active");
    });
  
    document.querySelector("#registration-menu-close").addEventListener("click", function () {
        registrationMenu.classList.remove("menu-is-active");
    });
 
    if (registrationForm) {
        registrationForm.addEventListener("submit", function(e) {
            e.preventDefault();

            clearErrors();

            const nickname = document.getElementById("registration-login").value.trim();
            const email = document.getElementById("registration-email").value.trim();
            const password = document.getElementById("registration-password").value;
            const passwordConfirm = document.getElementById("registration-password-confirm").value;
            
            let isValid = true;

            if (!nickname) {
                showError("registration-login", "Введите nickname");
                isValid = false;
            } else if (nickname.length < 3) {
                showError("registration-login", "Nickname должен быть не менее 3 символов");
                isValid = false;
            }

            if (!email) {
                showError("registration-email", "Введите email");
                isValid = false;
            } else if (!validateEmail(email)) {
                showError("registration-email", "Неверный формат email");
                isValid = false;
            }

            if (!password) {
                showError("registration-password", "Введите пароль");
                isValid = false;
            } else if (password.length < 6) {
                showError("registration-password", "Пароль должен быть не менее 6 символов");
                isValid = false;
            }

            if (password !== passwordConfirm) {
                showError("registration-password-confirm", "Пароли не совпадают");
                isValid = false;
            }

            if (isValid) {
                registrationForm.submit();
            }
        });
    }

    if (window.location.search.includes('registration=success')) {
        registrationMenu.classList.remove("menu-is-active");
        document.querySelector("#account-menu").classList.add("menu-is-active");

        const successMessage = document.createElement('div');
        successMessage.className = 'view__account-success';
        successMessage.textContent = 'Регистрация прошла успешно! Теперь вы можете войти.';
        document.querySelector("#account-form").prepend(successMessage);

        setTimeout(() => {
            successMessage.remove();
        }, 5000);
    }

    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        field.classList.add('is-invalid');

        const errorElement = document.createElement('small');
        errorElement.className = 'view__validation-error';
        errorElement.textContent = message;

        field.parentNode.insertBefore(errorElement, field.nextSibling);
    }

    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        document.querySelectorAll('.view__validation-error').forEach(el => {
            el.remove();
        });
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
  });