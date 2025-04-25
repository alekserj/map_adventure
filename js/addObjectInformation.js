document.querySelector("#addInformation").addEventListener("click", function () {
  fetch('/include/auth.php')
      .then(response => response.json())
      .then(data => {
          if (!data.isAuth) {
              alert('Для добавления информации необходимо войти в аккаунт');
              document.querySelector("#account-menu").classList.add("menu-is-active");
              return;
          }
          
          document.querySelector("#add-information-menu").classList.toggle("menu-is-active");
          document.querySelector("#viewTitle")?.remove();
          const title = document.createElement("h2")
          title.classList.add("view__title")
          title.id = "viewTitle"
          title.innerHTML = `Добавить информацию о объекте <br> "${document.querySelector(".baloon__title").textContent}"`
          const inputId = document.createElement("input")
          inputId.type = "hidden"
          inputId.name = "objectId"
          inputId.value = document.getElementById("informationId").value
          console.log(inputId.value)
          document.querySelector("#add-information-menu-form").prepend(title)
          document.querySelector("#add-information-menu-form").appendChild(inputId)
          document.getElementById('pictureList').innerHTML = ""
      });
});

document.querySelector("#add-information-menu-close").addEventListener("click", function () {
    document.querySelector("#add-information-menu").classList.toggle("menu-is-active");
    document.querySelector("#viewTitle").remove()
});