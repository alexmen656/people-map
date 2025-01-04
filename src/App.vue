<template>
  <div id="app">
    <div class="container">
      <div id="map" style="width: 100%; height: 100vh"></div>
      <div id="infoPopup">
        <div class="info">
          <span>State:</span>
          <span class="info-country"></span>
        </div>
        <div class="info">
          <span>Population:</span>
          <span class="info-population"></span>
        </div>
      </div>
      <div class="map-legend"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "App",
  mounted() {
    this.initMap();
  },
  methods: {
    fetchUserCounts() {
      this.$axios
        .get("http://localhost/path/to/your/backend/data.php")
        .then((response) => {
          this.userCounts = response.data;
          console.log(this.userCounts);
        })
        .catch((error) => {
          console.error("Error fetching user counts:", error);
        });
    },
    async initMap() {
       await  window.mapkit.init({
          authorizationCallback: function (done) {
            fetch("https://alex.polan.sk/people-map/verify.php")
              .then((res) => res.text())
              .then(done);
          },
          language: "en",
        });
     
      const MAP_COLORS = [
        {
          color: "#fcc5c0",
          range: "0",
          num: 1,
        },
        {
          color: "#fa9fb5",
          range: "10+",
          num: 10,
        },
        {
          color: "#f768a1",
          range: "30+",
          num: 30,
        },
        {
          color: "#dd3497",
          range: "50+",
          num: 50,
        },
        {
          color: "#ae017e",
          range: "100+",
          num: 100,
        },
      ];

      var region = new window.mapkit.CoordinateRegion(
    new window.mapkit.Coordinate(0.0, 180.0), // Zentrum der Karte: Äquator und Nullmeridian
    new window.mapkit.CoordinateSpan(180.0, 360.0) // Weltkarte, zeigt die gesamte Erde
);


      var map = new window.mapkit.Map("map", {
        mapType: window.mapkit.Map.MapTypes.Satellite,
        center: new window.mapkit.Coordinate(0.0, 0.0), // Zentrum der Karte: Äquator + Nullmeridian
        region: region, // Definierte Region
        ///showsUserLocation: true,
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

        //From demo
        itemForFeature: function (overlay, geoJSON) {
          var counter = geoJSON.properties.count;

          // Add data to the overlay to be shown when it is selected.
          overlay.data = {
            name: geoJSON.properties.name,
            counter: geoJSON.properties.count,
          };

          // Find the right color for the population and the set the style.
          for (var i = 0; i < MAP_COLORS.length; ++i) {
            if (counter < MAP_COLORS[i].num) {
              overlay.style = new window.mapkit.Style({
                fillOpacity: 0.7,
                lineWidth: 0.5,
                fillColor: MAP_COLORS[i].color,
              });
              break;
            }
          }
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

      var mapLegend = document.querySelector(".map-legend");

      function addLegend() {
        var el, textNode;
        MAP_COLORS.forEach(function (mColor) {
          el = document.createElement("div");
          textNode = document.createTextNode(mColor.range);
          el.appendChild(textNode);
          el.style.background = mColor.color;
          mapLegend.appendChild(el);
        });
      }

      addLegend();

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
          this.$axios
            .post(
              "data.php",
              this.$qs.stringify({
                country: country,
              })
            )
            .then((response) => {
              console.log(response.data);
              if (response.data.status === "success") {
                alert("Data submitted successfully");

                window.mapkit.importGeoJSON(
                  "https://alex.polan.sk/people-map/countries.php",
                  geoJSONParserDelegate
                );
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

.container .map-legend {
  position: absolute;
  z-index: 1000;
  top: 7px;
  left: 7px;
}

.map-legend div {
  margin-bottom: 5px;
  width: 40px;
  font-size: 12px;
  color: #fff;
  padding: 4px 7px;
}

#infoPopup {
  display: none;
  top: 7px;
  left: 67px;
  background: #ffffff;
  padding: 5px 15px;
  position: absolute;
  z-index: 1000;
  min-width: 180px;
  font: 13px/16px "-apple-system-font", "HelveticaNeue-Medium", "Helvetica",
    "Arial", "sans-serif";
  color: #212121;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}

.container {
  position: relative;
}

#infoPopup .info {
  padding: 10px 0;
  box-sizing: border-box;
}

#infoPopup .info:first-child {
  border-bottom: 1px solid rgba(0, 0, 0, 0.2);
}

#infoPopup .info-country,
#infoPopup .info-population {
  margin-left: 5px;
  color: #464545;
  font-style: italic;
}
</style>
