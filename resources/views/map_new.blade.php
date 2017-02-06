<!DOCTYPE html>
<html>
  <head>
    <title>Place Autocomplete</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #origin-input,
      #destination-input,#time-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 200px;
      }

      #origin-input:focus,
      #destination-input:focus,#time-input:focus {
        border-color: #4d90fe;
      }

      #mode-selector {
        color: #fff;
        background-color: #4d90fe;
        margin-left: 12px;
        padding: 5px 11px 0px 11px;
      }

      #mode-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }

      #left_panel{
        height: 100%;
        float: left;
        width: 390px;
        overflow: auto;
        display:none;
        line-height: 30px;
        padding-left: 10px;
      }
      #left-panel select, #left-panel input {
        font-size: 15px;
      }

      #left-panel select {
        width: 100%;
      }

      #left-panel i {
        font-size: 12px;
      }
      #left-panel {
        height: 100%;
        float: right;
        width: 390px;
        overflow: auto;
      }

    </style>
  </head>
  <body>
    <input id="origin-input" class="controls" type="text"
        placeholder="Enter an origin location" >  

    <input id="destination-input" class="controls" type="text"
        placeholder="Enter a destination location">

    <input id="time-input" class="controls" type="time"
        placeholder="Enter a time">

    <div id="mode-selector" class="controls" style="display:none">
      <input type="radio" name="type" id="changemode-walking" checked="checked">
      <label for="changemode-walking">Walking</label>

      <input type="radio" name="type" id="changemode-transit">
      <label for="changemode-transit">Transit</label>

      <input type="radio" name="type" id="changemode-driving">
      <label for="changemode-driving">Driving</label>
    </div>
    <div id="left_panel"></div>
    <div id="map"></div>
    <script
    src="https://code.jquery.com/jquery-3.1.1.min.js"
    integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
    crossorigin="anonymous"></script>
    <script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      function initMap() {
        
        var map = new google.maps.Map(document.getElementById('map'), {
          mapTypeControl: false,
          center: {lat: 1.371016, lng: 103.818853},
          zoom: 12
        });
        new AutocompleteDirectionsHandler(map);
         
      }

       /**
        * @constructor
       */


      function AutocompleteDirectionsHandler(map) {
        this.map = map;
        this.originPlaceId = null;
        this.destinationPlaceId = null;
        this.travelMode = 'DRIVING';
        this.directionsRenderers = [];
        this.inforwindows = [];
        var originInput = document.getElementById('origin-input');
        var destinationInput = document.getElementById('destination-input');
        var timeInput = document.getElementById('time-input');
        var modeSelector = document.getElementById('mode-selector');
        this.directionsService = new google.maps.DirectionsService;
        this.directionsDisplay = new google.maps.DirectionsRenderer;
        this.directionsDisplay.setMap(map);
        var left_panel = document.getElementById('left_panel');
        this.directionsDisplay.setPanel(left_panel);
        //this.directionsDisplay.setPanel(document.getElementById('right-panel'));
        var originAutocomplete = new google.maps.places.Autocomplete(
            originInput, {placeIdOnly: true});
        var destinationAutocomplete = new google.maps.places.Autocomplete(
            destinationInput, {placeIdOnly: true});

        this.setupClickListener('changemode-walking', 'WALKING');
        this.setupClickListener('changemode-transit', 'TRANSIT');
        this.setupClickListener('changemode-driving', 'DRIVING');

        this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
        this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');

        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(destinationInput);
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(timeInput);
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(modeSelector);
        
       // this.map.controls[google.maps.ControlPosition.LEFT_TOP].push(left_panel);
      }

      // Sets a listener on a radio button to change the filter type on Places
      // Autocomplete.
      AutocompleteDirectionsHandler.prototype.setupClickListener = function(id, mode) {
        var radioButton = document.getElementById(id);
        var me = this;
        radioButton.addEventListener('click', function() {
          me.travelMode = mode;
          me.route();
        });
      };

      AutocompleteDirectionsHandler.prototype.setupPlaceChangedListener = function(autocomplete, mode) {
        var me = this;
        autocomplete.bindTo('bounds', this.map);
        autocomplete.addListener('place_changed', function() {
          var place = autocomplete.getPlace();
          if (!place.place_id) {
            window.alert("Please select an option from the dropdown list.");
            return;
          }
          if (mode === 'ORIG') {
            me.originPlaceId = place.place_id;
          } else {
            me.destinationPlaceId = place.place_id;
          }
          me.route();
        });

      };

      AutocompleteDirectionsHandler.prototype.route = function() {
        if (!this.originPlaceId || !this.destinationPlaceId) {
          return;
        }
        var me = this;
        clearAll();
        this.directionsService.route({
          origin: {'placeId': this.originPlaceId},
          destination: {'placeId': this.destinationPlaceId},
          travelMode: this.travelMode,
          provideRouteAlternatives : true
        }, function(response, status) {
          if (status === 'OK') {
            me.directionsDisplay.setDirections(response);
            var left_panel = document.getElementById('left_panel');
            $('#left_panel').css("display","block");
            me.directionsDisplay.setPanel(left_panel);
            /*var i=0;
            response.routes.forEach(function(route) {
                var directionsRenderer = new google.maps.DirectionsRenderer({
                  directions: response,
                  routeIndex: i,
                  map: me.map,
                  polylineOptions:  getPolylineFormat(i)
                });
                me.directionsRenderers.push(directionsRenderer);
                var infowindow2 = new google.maps.InfoWindow();
                infowindow2.setContent(response.routes[i].legs[0].distance.text + "<br>" + response.routes[i].legs[0].duration.text + " ");
                infowindow2.setPosition(response.routes[i].legs[0].steps[response.routes[i].legs[0].steps.length-4].end_location);
                infowindow2.open(me.map);
                me.inforwindows.push(infowindow2);
                 i++;
            })   */
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });

        function getPolylineFormat(index) {
            if (index == 0) return {
                    strokeColor: "blue"
                  }
            else return {
                    strokeColor: "green",
                    strokeOpacity: 0.8
                  }
          }

          function clearAll() {
              me.directionsRenderers.forEach(function(direction) {
                  direction.setMap(null);
              })
			  me.inforwindows.forEach(function(infoWindow) {
				  infoWindow.close();
			  })
          }
      };

    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBn9e6Fo0_Elk8lYC8y5NgUDB0MSFD3zio&libraries=places&callback=initMap"
        async defer></script>
  </body>
</html>