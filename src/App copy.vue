<template>
  <div id="app">
    <div id="map" style="width: 100%; height: 100vh"></div>
  </div>
</template>

<script>
export default {
  name: "App",
  /*mounted() {
    this.initMap();
  },*/
  methods: {
    async initMap() {
      mapkit.init({
        authorizationCallback: function (done) {
          var xhr = new XMLHttpRequest();
          xhr.open("GET", "/services/jwt");
          xhr.addEventListener("load", function () {
            done(this.responseText);
          });
          xhr.send();
        },
      });
      var region = new window.mapkit.CoordinateRegion(
        new window.mapkit.Coordinate(20.0, 0.0),
        new window.mapkit.CoordinateSpan(140.0, 360.0)
      );

      var map = new window.mapkit.Map("map", {
        center: new window.mapkit.Coordinate(47.3769, 8.5417),
        region: region,
        showsUserLocation: true,
        showsUserLocationControl: true,
      });

      let geoJSONParserDelegate = {
        itemForPolygon: function (overlay) {
          overlay.style = new window.mapkit.Style({
            strokeColor: "#000",
            strokeOpacity: 0.8,
            lineWidth: 1,
            fillOpacity: 0.8,
            fillColor: "#CACACA",
          });
          map.addOverlay(overlay);

          // Add event listener for click events
          overlay.addEventListener("select", (event) => {
            console.log("Country clicked: " + event.target.title);
            alert("Country clicked: " + event.target.title);
            fetch("https://webhook.site/2460076e-1ad9-4156-8323-f8db9bc94708");
          });

          return overlay;
        },
      };

      // Assuming you have a GeoJSON data source
      let geoJSON = {}; // Your GeoJSON data here
      let geoJSONParser = new window.mapkit.GeoJSONParser(geoJSON, {
        map: map,
        itemForPolygon: geoJSONParserDelegate.itemForPolygon,
      });

      geoJSONParser.parse();

      map.addEventListener("select", function (event) {
        if (event.overlay) {
          console.log("You selected an overlay.");
          fetch("https://webhook.site/2460076e-1ad9-4156-8323-f8db9bc94708");
        }
      });
    },
  },
};
</script>

<style>
#map {
  width: 100%;
  height: 100%;
}

body,
html {
  width: 100%;
  height: 100%;
  padding: 0;
  margin: 0;
}
</style>
