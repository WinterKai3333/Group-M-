<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


    <!-- Including jQuery. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

   <!-- Include axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Leaflet map library -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Including scripting file. -->
    <script src="displayScript.js"></script>
    <script src="calculateRoute.js"></script>

    

    <!-- Openrouteservice api -->
    <script src="openrouteservice-js-master/dist/ors-js-client.js"></script>

    <title>Campus Nav</title>


</head>
<body>
    
    <div id="map"></div>
    <!-- Search box container at top-left -->
    <div id="search-container">

        <input type="text" id="From" placeholder="From" >
        <div id="displayFrom"></div>
        <!-- <div id="from-dropdown"></div> -->

        <input type="text" id="To" placeholder="To" >
        <div id="displayTo"></div>
        <!-- <div id="to-dropdown"></div> -->

        <!-- <button id="go-button" onclick="calculateRoute()">Go</button> -->
        <button id="go-button" onclick="calculateRoute()">Go</button>
    </div>
  

    <script>
	
      var map = L.map('map', {
        center: [2.94444,101.87603],
        zoom: 16.5
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
        shadowSize: [41, 41]
      });

      var startIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-black.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
      });

      var routeFromIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
      });

      var routeToIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
      });
        
        
      let fromLocation = null;
      let toLocation = null;
      var fromMarkerLayer, routeFromLayer,toMarkerLayer,routeToLayer,fromPolygonLayer, toPolygonLayer;

        
      function showOnMap(Value,longitude,latitude,buildingPolygon, routingLongiutde, routingLatitude)
      {


        // if you look for building, the lon and lat is for the said building, the polygon is also the building's pol
        // if you look for room, the lon and lat is for the room, the polygonis for the building where the room is
        // both building and room will share the same routingLongitude and routingLatitude

        // update : 27.12.23 - IT WORKS!!

        var fromID = document.getElementById('From').value;
        var toID = document.getElementById('To').value;


        // console.log("---------SHOW ON MAP FUNCTION-------------");
        // console.log("Value : ", Value);
        // console.log("Longitude : ", longitude);
        // console.log("Latitude : ", latitude);
        // console.log("Polygon : ", buildingPolygon);
        // console.log("Routing Longitude : ", routingLongiutde);
        // console.log("Routing Latitude : ", routingLatitude);
        // console.log("---------END SHOW ON MAP FUNCTION-------------");
        
        if(fromID == Value) {
            
          if (fromMarkerLayer || routeFromLayer || fromPolygonLayer) {
            map.removeLayer(fromMarkerLayer);
            map.removeLayer(routeFromLayer);
            map.removeLayer(fromPolygonLayer);
          }

          fromID = [latitude, longitude];
          fromLocation = [buildingPolygon];
          routeFrom = [routingLatitude,routingLongiutde];
          var parsedFromLocation = JSON.parse(fromLocation);
          
          fromMarkerLayer = L.marker(fromID, {
              icon: startIcon
          }).addTo(map);

          routeFromLayer = L.marker(routeFrom, {
              icon: routeFromIcon
          }).addTo(map);

          fromPolygonLayer = L.geoJSON(parsedFromLocation).addTo(map);
          map.fitBounds(L.geoJSON(parsedFromLocation).getBounds());
                
        }
          
        if (toID == Value) {

          if (
            toMarkerLayer 
          // || routeToLayer
          || 
          toPolygonLayer) {
            map.removeLayer(toMarkerLayer);
            // map.removeLayer(routeToLayer);
            map.removeLayer(toPolygonLayer);
          }


          toID = [latitude,longitude];
          toLocation = [buildingPolygon];
          routeTo = [routingLatitude,routingLongiutde];
          var parsedToLocation = JSON.parse(toLocation);
          
          toMarkerLayer = L.marker(toID, {
              icon: goalIcon
          }).addTo(map);

          // routeToLayer = L.marker(routeTo, {
          //     icon: routeToIcon
          // }).addTo(map);

          toPolygonLayer = L.geoJSON(parsedToLocation).addTo(map);
          map.fitBounds(L.geoJSON(parsedToLocation).getBounds());

        }
            
      }        
    </script>
    
</body>
</html>

