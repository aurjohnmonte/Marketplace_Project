<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Leaflet Routing with Re-Routing</title>
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet/dist/leaflet.css"
    />
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"
    />
    <style>
      #map {
        height: 100vh;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script>
      const map = L.map("map").setView([0, 0], 15);

      // Base map layer
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
      }).addTo(map);

      // Destination (example: Manila)
      const destination = L.latLng(14.5995, 120.9842);
      L.marker(destination).addTo(map).bindPopup("Destination").openPopup();

      let userMarker;
      let routingControl;

      // Watch user position continuously
      if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
          (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const userLatLng = L.latLng(lat, lng);

            // Update or create user marker
            if (!userMarker) {
              userMarker = L.marker(userLatLng).addTo(map).bindPopup("You are here");
              map.setView(userLatLng, 15);
            } else {
              userMarker.setLatLng(userLatLng);
            }

            // Recalculate route
            if (routingControl) {
              routingControl.setWaypoints([userLatLng, destination]);
            } else {
              routingControl = L.Routing.control({
                waypoints: [userLatLng, destination],
                routeWhileDragging: false,
                addWaypoints: false,
                show: false,
              }).addTo(map);
            }
          },
          (error) => {
            console.error("Error getting location:", error);
          },
          { enableHighAccuracy: true }
        );
      } else {
        alert("Geolocation is not supported by your browser.");
      }
    </script>
  </body>
</html>
