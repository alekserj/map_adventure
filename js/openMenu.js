window.addEventListener("DOMContentLoaded", function () {
  document.querySelector("#burger").addEventListener("click", function () {
    document.querySelector("#view-menu").classList.toggle("menu-is-active");
  });

  document
    .querySelector("#burger-close")
    .addEventListener("click", function () {
      document.querySelector("#view-menu").classList.toggle("menu-is-active");
    });
});
