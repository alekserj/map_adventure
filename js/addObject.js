function addPlacemark(map) {
  let myPlacemark = new ymaps.Placemark(
    map.getCenter(),
    {},
    {
      draggable: true,
      iconLayout: 'default#image', 
      iconImageHref: '../img/add_mark.svg', 
      iconImageSize: [40, 70],
      iconImageOffset: [-15, -50]
    }
  );

  map.geoObjects.add(myPlacemark);


  myPlacemark.events.add("dragend", function () {
    updateAddressAndCoordinates(myPlacemark.geometry.getCoordinates());
  });

  function updateAddressAndCoordinates(coords) {
    ymaps.geocode(coords).then(function (res) {
      var firstGeoObject = res.geoObjects.get(0);
      var address = firstGeoObject.getAddressLine();
      document.getElementById("address").value = address;

      var latitude = coords[0].toFixed(6);
      var longitude = coords[1].toFixed(6);
      document.getElementById("coordinates1").value = longitude;
      document.getElementById("coordinates2").value = latitude;
    });
  }

  
  document
    .getElementById("plus-close")
    .addEventListener("click", function () {
      if (myPlacemark) {
        map.geoObjects.remove(myPlacemark);
        myPlacemark = null; 
        document.getElementById("address").textContent = ""; 
        document.getElementById("coordinates1").textContent = "";
        document.getElementById("coordinates2").textContent = "";
      }
    });

  updateAddressAndCoordinates(map.getCenter());
}
