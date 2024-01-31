function routeBlueColor() {
    var color = '#0000FF';
    return color;
}

var routeLayer;

function calculateRoute() {
    var fromID = document.getElementById('From').value;
    var toID = document.getElementById('To').value;

    // if (!fromID || !toID) {
    // alert('Please enter both starting and ending locations.');
    // return;
    // }

    //AJAX request getLonLan.php, get coordinates of starting location
    $.ajax({

      type: 'POST',
      url: 'getLonLat.php',
      data: {
          coord: fromID,
          identifier: 'from'
      },
      success: function (fromData) {
        var startCoordinates = [fromData.routingLatitude, fromData.routingLongitude];

        //AJAX request getLonLan.php, get coordinates for the ending location
        $.ajax({
          type: 'POST',
          url: 'getLonLat.php',
          data: {
              coord: toID,
              identifier: 'to'
          },
          success: function (toData) {
            var endCoordinates = [toData.routingLatitude, toData.routingLongitude];
            var apiKey = '5b3ce3597851110001cf6248b605ff4ca15d4a4fb4b71dd1123decd2';

            //testing for polyline
            // var myRoute = [startCoordinates, [1.0,1.0], endCoordinates]; 

            // if (routeLayer){
            //   map.removeLayer(routeLayer);
            // }

            // routeLayer = L.polyline(myRoute, {color: routeBlueColor() }).addTo(map);
            // map.setZoom(16);
            // map.fitBounds(routeLayer.getBounds());

            //requesting to the ors api
            axios.get(`https://api.openrouteservice.org/v2/directions/foot-walking?api_key=${apiKey}&start=${startCoordinates[1]},${startCoordinates[0]}&end=${endCoordinates[1]},${endCoordinates[0]}`)
              .then(response => {
                console.log('API response: ', response.data);
                var route = response.data.features[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
                      
                //Clearing previous layer
                if (routeLayer) {
                  map.removeLayer(routeLayer);
                }

               //Adding new layer
                routeLayer = L.polyline(route, { color: routeBlueColor() }).addTo(map);

                //Setting zoom for the map
                map.setZoom(16);
                map.fitBounds(routeLayer.getBounds());
              })
              .catch(error => {
                console.error('Error fetching route:', error);
              });
          },
          error: function (error) {
            console.error('Error fetching coordinates for the ending location:', error);
          }
        });
      },
      error: function (error) {
        console.error('Error fetching coordinates for the starting location:', error);
      }
    });
}