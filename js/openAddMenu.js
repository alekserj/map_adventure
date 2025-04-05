window.addEventListener("DOMContentLoaded", function () {
  document.querySelector("#plus").addEventListener("click", function () {
    document.querySelector("#add-object-menu").classList.toggle("menu-is-active");
  });

  document
    .querySelector("#plus-close")
    .addEventListener("click", function () {
      document.querySelector("#add-object-menu").classList.toggle("menu-is-active");
    });
});
