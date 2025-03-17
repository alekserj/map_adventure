document.getElementById("selectCustom").addEventListener("change", function () {
  var selectedValue = this.value;
  console.log(selectedValue);

  document.getElementById("valueSelect").value = selectedValue;
});
