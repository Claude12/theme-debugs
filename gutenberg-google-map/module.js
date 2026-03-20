function google_map_init() {
  const maps = Array.from(document.querySelectorAll('.km-google-map')
  );
  maps.forEach((map) => initMap(map));
}

window.addEventListener('DOMContentLoaded',google_map_init,false);


function initMap(block) {
  const markers = Array.prototype.slice.call(
    block.querySelectorAll('.km-google-map-marker')
  );

  const mapTarget = block.querySelector('.km-gm-wrap');
  const mapZoom = mapTarget.dataset.zoom || 16;

  const mapProps = {
    //zoom: parseInt(mapZoom),
    mapTypeId: mapTarget.dataset.type || google.maps.MapTypeId.ROADMAP,
  };

  const map = new google.maps.Map(mapTarget, mapProps);

  // Add markers
  map.markers = [];
  markers.forEach((marker) => initMarker(marker, map));

  map.setZoom(parseInt(mapZoom));

  centerMap(map);

  // Return map instance.
  return map;
}

function initMarker(marker, map) {
  const lat = marker.dataset.lat;
  const long = marker.dataset.long;
  const icon = marker.dataset.icon || null;

  const latLng = {
    lat: parseFloat(lat),
    lng: parseFloat(long),
  };

  const markerProps = {
    position: latLng,
    map: map,
  };

  //console.log(markerProps);

  if (icon) markerProps.icon = icon;

  // Create marker instance.
  const markerInst = new google.maps.Marker(markerProps);

  // Append to reference for later use.
  map.markers.push(markerInst);

  if (marker.innerHTML.trim() !== '') {
    const infowindow = new google.maps.InfoWindow({
      content: marker.innerHTML,
    });

    // Show info window when marker is clicked.
    markerInst.addListener('click', function () {
      infowindow.open(map, markerInst);
    });
  }
}

function centerMap(map) {
  const bounds = new google.maps.LatLngBounds();

  map.markers.forEach((marker) => {
    var coords = new google.maps.LatLng({
      lat: marker.getPosition().lat(),
      lng: marker.getPosition().lng(),
    });

    bounds.extend(coords);
  });

  // Case: Single marker.
  if (map.markers.length == 1) {
    map.setCenter(bounds.getCenter());

    // Case: Multiple markers.
  } else {
    map.fitBounds(bounds);
  }
}
