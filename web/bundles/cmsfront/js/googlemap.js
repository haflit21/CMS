var infoBubbles = [];

function initialize(id, center) {
 
  var mapOptions = {
    zoom: 16,
    center: center,
    mapTypeId: google.maps.MapTypeId.TERRAIN
  };
  map = new google.maps.Map(document.getElementById(id), mapOptions);
  showOverlays();
}

function addMarker(location, contentString) {
  marker = new google.maps.Marker({
    position: location,
    map: map,
    animation: google.maps.Animation.DROP
  });
  var infowindow;
  if(contentString != '') {
	  infowindow = new google.maps.InfoWindow({
	      content: contentString
	  });
	  google.maps.event.addListener(marker, 'click', function() {infowindow.open(map, this); });
  }
  markersArray.push(marker);
}



function showOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(map);
    }
  }
}
