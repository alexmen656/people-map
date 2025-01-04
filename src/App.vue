<template>
  <div id="app">
    <div id="map" style="width: 100%; height: 100vh"></div>
  </div>
</template>

<script>
export default {
  name: "App",
  mounted() {
    this.initMap();
  },
  methods: {
    async initMap() {
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

          return overlay;
        },
        geoJSONDidComplete: function (result, geoJSON) {
          console.log("GeoJSONDelegate.geoJSONDidComplete");
          console.log(result);
          console.log(geoJSON);
        },
        geoJSONDidError: function (error, geoJSON) {
          console.log("GeoJSONDelegate.geoJSONDidError");
          console.log(error);
          console.log(geoJSON);
        },
      };

      window.mapkit.importGeoJSON(
        "https://alex.polan.sk/people-map/countries.php",
        geoJSONParserDelegate
      );

      map.addEventListener("select", (event) => {
      const country = event.overlay.data.name;
      console.log(country);

      if (event.overlay) {
        console.log("You selected an overlay. " + country);

        // Submit data to backend using axios
        this.$axios.post("data.php", this.$qs.stringify({
          country: country,
        }))
        .then((response) => {
          console.log(response.data);
          if (response.data.status === "success") {
            alert("Data submitted successfully");
          } else {
            alert("Error: " + response.data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
        });
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
