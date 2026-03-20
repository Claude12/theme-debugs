    var lat_lng  = gcode.lat_lng;
    var nearest=10; 
    var center_marker_lat;
    var center_marker_lng;
    var locat ;
    var brack_if = true;
    var nam = '';
    var cou = '';
    var geocoder = new google.maps.Geocoder();
    var testMarker=[];
function initialize() {
    var myLatlng =  new google.maps.LatLng(lat_lng[0]['lat'], lat_lng[0]['lng']);
    
    var style = [];    
    
    var mapOptions = {
        zoom: Number(gcode.zoom),
        center: myLatlng,
        panControl:false,
        zoomControl:true,
        mapTypeControl:false,
        scaleControl:false,
        streetViewControl:false,
        styles: style,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById(gcode.mapId), mapOptions);

    for (i = 0; i < lat_lng.length; i++) { 
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat_lng[i]['lat'], lat_lng[i]['lng']),
                map: map,
                icon:lat_lng[i]['marker'],
            });
            testMarker[i]=marker;

            var infowindow = new google.maps.InfoWindow();
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent(lat_lng[i]['address']);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }

        var mapTitle=gcode.title;
        var mapAddress=gcode.address;
        var mapAdd=gcode.add;
        var contentString = "<div id='infocontent'><div class='google_title'>"+mapTitle+"</div><div class='sub_title'><a href='https://maps.google.com/maps?q="+mapAdd+"' target='_blank'>"+mapAddress+"</a></div></div>";
        if(gcode.address != ''){
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
        }
        

        // var bounds = new google.maps.LatLngBounds();
        // // Go through each...
        // jQuery.each(testMarker, function (index, marker_bon) {
        //     bounds.extend(marker_bon.position);
        // });

        // // Fit these bounds to the map
        // map.fitBounds(bounds);

        // google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
        //     if (this.getZoom() < 10) {
        //     this.setZoom(8);
        // }
        // });
        //dynamic bound function 
           displayMapCenter();
 
}


google.maps.event.addDomListener(window, 'load', initialize);


function displayMapCenter(){
  google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
    var windowSize = jQuery(window).width();

        if (windowSize <= 479) {
            this.setZoom(0.5);
        }
        else if (windowSize <= 768) {
            this.setZoom(1);
        }
        else if (windowSize <= 1920) {
            this.setZoom(2);
        }
        else if (windowSize <= 2500 ) {
            this.setZoom(2);
        }
  });
}
