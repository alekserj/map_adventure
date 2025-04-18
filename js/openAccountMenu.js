window.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#account").addEventListener("click", function () {
      document.querySelector("#account-menu").classList.toggle("menu-is-active");
    });
  
    document
      .querySelector("#account-menu-close")
      .addEventListener("click", function () {
        document.querySelector("#account-menu").classList.toggle("menu-is-active");
      });

      document.querySelector("#accountExists").addEventListener("click", function () {
        document.querySelector("#registration-menu").classList.toggle("menu-is-active");
        document.querySelector("#account-menu").classList.toggle("menu-is-active");
      });
  });