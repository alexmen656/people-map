const fs = require('fs');
const geoJSONPath = './original.geojson';

fs.readFile(geoJSONPath, 'utf8', (err, data) => {
  if (err) {
    console.error("Error reading GeoJSON file:", err);
    return;
  }

  let geoJSON;
  try {
    geoJSON = JSON.parse(data);
  } catch (parseErr) {
    console.error("Error parsing GeoJSON file:", parseErr);
    return;
  }

  // Split MultiPolygons into individual Polygons and assign the same name
  const updatedFeatures = [];
  geoJSON.features.forEach((feature) => {
    if (feature.geometry.type === 'MultiPolygon') {
      feature.geometry.coordinates.forEach((polygonCoords) => {
        updatedFeatures.push({
          type: 'Feature',
          properties: { ...feature.properties },
          geometry: {
            type: 'Polygon',
            coordinates: polygonCoords,
          },
        });
      });
    } else {
      updatedFeatures.push(feature);
    }
  });

  geoJSON.features = updatedFeatures.map((feature) => {
    if (feature.properties && feature.properties.name) {
      feature.geometry = {
        type: feature.geometry.type,
        name: feature.properties.name,
        coordinates: feature.geometry.coordinates,
      };
      //delete feature.properties.name;
    }
    return feature;
  });

  // Save the updated GeoJSON file
  fs.writeFile(
    "countries.json",
    JSON.stringify(geoJSON, null, 2),
    'utf8',
    (writeErr) => {
      if (writeErr) {
        console.error("Error writing GeoJSON file:", writeErr);
        return;
      }
      console.log("GeoJSON file updated successfully.");
    }
  );
});