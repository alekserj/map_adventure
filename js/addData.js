document.getElementById("view-form").addEventListener("submit", function(e) {
  e.preventDefault();

  fetch('/include/check-auth.php')
      .then(response => response.json())
      .then(data => {
          if (!data.isAuth) {
              alert('Для добавления объекта необходимо войти в аккаунт');
              document.querySelector("#account-menu").classList.add("menu-is-active");
              return;
          }

          const formData = new FormData(this);
          fetch('../include/add_object.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.text())
          .then(result => {
              alert(result);
              location.reload();
          })
          .catch(error => {
              console.error('Ошибка:', error);
              alert('Ошибка при добавлении объекта');
          });
      })
      .catch(error => {
          console.error('Ошибка проверки авторизации:', error);
          alert('Ошибка при проверке авторизации');
      });
});