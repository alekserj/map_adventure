document.querySelector("#add-information-menu-close").addEventListener("click", function() {
    document.querySelector("#add-information-menu").classList.remove("menu-is-active");
    const title = document.querySelector("#viewTitle");
    if (title) title.remove();
  });