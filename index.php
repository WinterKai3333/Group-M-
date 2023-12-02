<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

    <!-- AJAX library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>

    <!-- AJAX library -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> -->
    <!-- <script src="https://unpkg.com/axios/dist/axios.min.js"></script> -->
    <!-- Leaflet map library -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- Openrouteservice api -->
    <script src="openrouteservice-js-master/dist/ors-js-client.js"></script>

    <title>Campus Nav</title>


</head>
<body>
    
    <div id="map"></div>
    <!-- Search box container at top-left -->
    <div id="search-container">

        <input type="text" id="search-input-from" placeholder="From" >
        <!-- <div id="from-dropdown"></div> -->


        <input type="text" id="search-input-to" placeholder="To" >
        <!-- <div id="to-dropdown"></div> -->

        <button id="go-button" onclick="calculateRoute()">Go</button>
    </div>
  

    <script>
	
        function routeBlueColor() {
          var color = '#0000FF';
          return color;
        }
        var map = L.map('map', {
          center: [2.94444,101.87603],
          zoom: 16.5,
        });
    
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
          attribution: '© <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.control.zoom({ position: 'topright' }).addTo(map);


        var goalIcon = new L.Icon({
          iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
          shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
          iconSize: [25, 41],
          iconAnchor: [12, 41],
          popupAnchor: [1, -34],
        //   shadowSize: [41, 41]
        });

        var startIcon = new L.Icon({
          iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-black.png',
          shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
          iconSize: [25, 41],
          iconAnchor: [12, 41],
          popupAnchor: [1, -34],
        //   shadowSize: [41, 41]
        });
        

        // Using GET
        async function calculateRoute() {
            
            var startLocation = document.getElementById('search-input-from').value;
            var endLocation = document.getElementById('search-input-to').value;

            if (!startLocation || !endLocation) {
                alert('Please enter both start and end locations.');
                return;
            }

            // Get geographic coordinates for start and end locations using geocoding
            var startCoord = await getCoordinates(startLocation);
            var endCoord = await getCoordinates(endLocation);
            

            // Request route using obtained coordinates
            var apiKey = '5b3ce3597851110001cf6248b605ff4ca15d4a4fb4b71dd1123decd2'; // API key

            var req = `https://api.openrouteservice.org/v2/directions/foot-walking?api_key=${apiKey}&start=${startCoord[0]},${startCoord[1]}&end=${endCoord[0]},${endCoord[1]}`;

            var request = new XMLHttpRequest();
            request.open('GET', req);
            request.setRequestHeader('Accept', 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8');

            request.onreadystatechange = function () {
                if (this.readyState === 4) {
                    console.log('Body:', this.responseText);
                
                    let json = JSON.parse(this.responseText);
                    console.log(json);
                    let coordinates = json.features[0].geometry.coordinates;
                
                
                    showRouteOnMap(coordinates);
                }
            };
            request.send();            
            
        }

        async function getCoordinates(location) {
            var apiKey = '5b3ce3597851110001cf6248b605ff4ca15d4a4fb4b71dd1123decd2'; // Api key
        
            var geocodingReq = `https://api.openrouteservice.org/geocode/search?api_key=${apiKey}&text=${encodeURIComponent(location)}`;
        
            return new Promise(resolve => {
                var geocodingRequest = new XMLHttpRequest();
                geocodingRequest.open('GET', geocodingReq);
                geocodingRequest.setRequestHeader('Accept', 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8');
            
                geocodingRequest.onreadystatechange = function () {
                    if (this.readyState === 4) {
                        let geocodingJson = JSON.parse(this.responseText);
                        let coordinates = geocodingJson.features[0].geometry.coordinates;
                        resolve(coordinates);
                    }
                };
                geocodingRequest.send();
            });
        }

        function showRouteOnMap(coordinates) {
            let correctCoordinates = coordinates.map(coordinate => {
                let temp = coordinate[0];
                coordinate[0] = coordinate[1];
                coordinate[1] = temp;
                return coordinate;
            });

            var startPoint = L.marker(correctCoordinates[0], {
                icon: startIcon
            }).addTo(map);
            var endPoint = L.marker(correctCoordinates[correctCoordinates.length - 1], {
                icon: goalIcon
            }).addTo(map);
            var route = L.polyline(correctCoordinates, {
                color: routeBlueColor()
            }).addTo(map);
        };



    </script>
    
</body>
</html>



