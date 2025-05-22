window.addEventListener("DOMContentLoaded", function () {
    document
    .querySelector("#obj-info-menu-close")
    .addEventListener("click", function () {
      document.querySelector("#obj-info-menu").classList.toggle("menu-is-active");
    });
});
