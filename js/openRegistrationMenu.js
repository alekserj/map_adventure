window.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#createAccount").addEventListener("click", function () {
      document.querySelector("#account-menu").classList.toggle("menu-is-active");
      document.querySelector("#registration-menu").classList.toggle("menu-is-active");
    });
  
    document
      .querySelector("#registration-menu-close")
      .addEventListener("click", function () {
        document.querySelector("#registration-menu").classList.toggle("menu-is-active");
      });
  });