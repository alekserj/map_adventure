window.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#route").addEventListener("click", function () {
      document.querySelector("#route-menu").classList.toggle("menu-is-active");
    });
  
    document
      .querySelector("#route-menu-close")
      .addEventListener("click", function () {
        document.querySelector("#route-menu").classList.toggle("menu-is-active");
      });
  });