<template>
  <div id="app">
    <div class="container">
      <header class="app-header">
        <h1>{{ $t("title") }}</h1>
        <p class="app-description">
          {{ $t("description") }}
        </p>
      </header>
      <div id="map" style="width: 100%; height: 100vh"></div>
      <RangList class="rang-list" />
      <div class="map-legend"></div>
    </div>
  </div>
</template>

<script>
import RangList from "@/components/RangList.vue";
import { eventBus } from '@/eventBus';

export default {
  name: "App",
  components: {
    RangList,
  },
  mounted() {
    if (localStorage.getItem("language") === null) {
      localStorage.setItem("language", "en");
    }
    this.$i18n.locale = localStorage.getItem("language");
    this.initMap();
  },
  methods: {
    async initMap() {
      await window.mapkit.init({
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
          range: "1+",
          num: 10,
        },
        {
          color: "#f768a1",
          range: "10+",
          num: 30,
        },
        {
          color: "#dd3497",
          range: "30+",
          num: 50,
        },
        {
          color: "#ae017e",
          range: "50+",
          num: 100,
        },
        {
          color: "#7a0177",
          range: "100+",
          num: Infinity,
        },
      ];

      const region = new window.mapkit.CoordinateRegion(
        new window.mapkit.Coordinate(25.0, 15.0),
        new window.mapkit.CoordinateSpan(180.0, 360.0)
      );

      const map = new window.mapkit.Map("map", {
        mapType: window.mapkit.Map.MapTypes.Satellite,
        center: new window.mapkit.Coordinate(25.0, 15.0),
        region: region,
        ///showsUserLocation: true,
        //showsUserLocationControl: true,
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

        itemForFeature: function (overlay, geoJSON) {
          const counter = geoJSON.properties.count;

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
        let el, textNode;
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

          if (
            !localStorage.getItem("voted") ||
            localStorage.getItem("voted") !== "true"
          ) {
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
                  localStorage.setItem("voted", "true");
                  eventBus.emit('update');
                  //alert("Data submitted successfully");

                  // Clear existing overlays
                  map.overlays.forEach((overlay) => map.removeOverlay(overlay));

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
          } else {
            alert("You have already selected a country.");
          }
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
  font-family: ui-sans-serif, system-ui, sans-serif, Apple Color Emoji,
    Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
  cursor: pointer;
}

.container .map-legend {
  position: absolute;
  z-index: 1005;
  top: 15px;
  left: 15px;
}

.map-legend div {
  margin-bottom: 5px;
  width: 40px;
  font-size: 12px;
  color: #fff;
  padding: 4px 7px;
  border-radius: 5px;
}

.container {
  position: relative;
}

.app-header {
  text-align: center;
  margin: 0;
  z-index: 1001; /* Ensure the header is above other elements */
  position: absolute;
  background-color: transparent; /* Completely transparent background */
  text-align: center;
  justify-content: center;
  width: 100%;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.6),
    rgba(0, 0, 0, 0)
  ); /* Gradient shadow from top */
  margin-bottom: 10px;
}

.app-header h1 {
  font-size: 2em;
  color: #fff;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
  margin-bottom: 5px;
}

.app-description {
  font-size: 1.2em;
  color: #fff;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.5;
  padding: 0 20px;
  margin-top: 5px;
}

/*class="mk-top-right-controls-container mk-top-right-controls-container-children-two mk-controls-container-controls-larger"*/

.mk-top-right-controls-container {
  z-index: 1010;
}
</style>
