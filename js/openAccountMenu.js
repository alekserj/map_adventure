document.addEventListener("DOMContentLoaded", function() {
    const accountBtn = document.getElementById("account");
    const accountMenu = document.getElementById("account-menu");
    const cabinetMenu = document.getElementById("cabinet-menu");
    const adminMenu = document.getElementById("admin-menu");
    const accountMenuClose = document.getElementById("account-menu-close");
    const cabinetMenuClose = document.getElementById("cabinet-menu-close");
    const adminMenuClose = document.getElementById("admin-menu-close");
    const createAccountBtn = document.getElementById("createAccount");
    const accountExistsBtn = document.getElementById("accountExists");

    hideAllMenus();

  accountBtn.addEventListener("click", async function() {
    try {
        const authStatus = await checkAuth();
        
        if (authStatus.isAuth) {
            if (authStatus.email === 'admin@admin.adm') {
                showMenu(adminMenu);
            } else {
                showMenu(cabinetMenu);
            }
            hideMenu(accountMenu);
        } else {
            showMenu(accountMenu);
            hideMenu(cabinetMenu);
            hideMenu(adminMenu);
        }
    } catch (error) {
        console.error("Auth check failed:", error);
        showMenu(accountMenu);
        hideMenu(cabinetMenu);
        hideMenu(adminMenu);
    }
});

accountMenuClose.addEventListener("click", () => hideMenu(accountMenu));
cabinetMenuClose.addEventListener("click", () => hideMenu(cabinetMenu));
adminMenuClose.addEventListener("click", () => hideMenu(adminMenu));

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

async function checkAuth() {
    try {
        const response = await fetch('include/auth.php', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include' 
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

const accountForm = document.getElementById("account-form");
if (accountForm) {
    accountForm.addEventListener("submit", function(e) {
        e.preventDefault();

        clearAuthErrors();

        const email = accountForm.querySelector("input[name='account-login']").value.trim();
        const password = accountForm.querySelector("input[name='account-password']").value.trim();
        
        let isValid = true;

        if (!email) {
            showAuthError("account-login", "Введите email");
            isValid = false;
        } else if (!validateEmail(email)) {
            showAuthError("account-login", "Неверный формат email");
            isValid = false;
        }

        if (!password) {
            showAuthError("account-password", "Введите пароль");
            isValid = false;
        }

        if (isValid) {
            accountForm.submit();
        }
    });
}

function showAuthError(fieldName, message) {
    const field = accountForm.querySelector(`input[name='${fieldName}']`);
    if (!field) return;

    field.classList.add('is-invalid');

    const errorElement = document.createElement('small');
    errorElement.className = 'view__validation-error';
    errorElement.textContent = message;

    field.parentNode.insertBefore(errorElement, field.nextSibling);
}

function clearAuthErrors() {
    accountForm.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    accountForm.querySelectorAll('.view__validation-error').forEach(el => {
        el.remove();
    });

    const generalError = accountForm.querySelector('.view__account-error');
    if (generalError) {
        generalError.remove();
    }
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}