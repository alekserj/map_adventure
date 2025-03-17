document.getElementById("addObjectDB").addEventListener("click", function () {
  const formData = new FormData(document.getElementById("view-form"));
  fetch("../include/add_object.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((data) => {
      alert(data);
    })
    .catch((error) => {
      console.error("Ошибка:", error);
    });
});
