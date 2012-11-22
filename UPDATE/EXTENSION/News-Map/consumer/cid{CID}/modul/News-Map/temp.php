<?php

// temporary javascript functions

?>

    function showAddress(address) {
      geocoder.getLatLng(
        address,
        function(point) {
          if (!point) {
            alert(address + " not found");
          } else {
            map.setCenter(point, 13);
            var marker = new GMarker(point);
            map.addOverlay(marker);
              GEvent.addListener(marker, "click", function() {
                var myHtml = "Adresse: <b>"+address+"</b>";
                map.openInfoWindowHtml(point, myHtml);
              });
          }
        }
      );
    }

