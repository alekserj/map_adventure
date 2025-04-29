window.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#filter").addEventListener("click", function () {
      document.querySelector("#filter-menu").classList.toggle("menu-is-active");
    });
  
    document
      .querySelector("#filter-menu-close")
      .addEventListener("click", function () {
        document.querySelector("#filter-menu").classList.toggle("menu-is-active");
      });
  });