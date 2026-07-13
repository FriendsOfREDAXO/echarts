(function () {
  'use strict';

  function decodeOptions(encoded) {
    if (!encoded) {
      return null;
    }

    try {
      var binary = atob(encoded);
      var json;

      if (typeof TextDecoder !== 'undefined') {
        var bytes = new Uint8Array(binary.length);
        for (var i = 0; i < binary.length; i += 1) {
          bytes[i] = binary.charCodeAt(i);
        }
        json = new TextDecoder('utf-8').decode(bytes);
      } else {
        json = decodeURIComponent(escape(binary));
      }

      return JSON.parse(json);
    } catch (error) {
      return null;
    }
  }

  function normalizeText(value) {
    if (typeof value !== 'string') {
      return '';
    }

    return value
      .toLowerCase()
      .replace(/ä/g, 'ae')
      .replace(/ö/g, 'oe')
      .replace(/ü/g, 'ue')
      .replace(/ß/g, 'ss')
      .trim();
  }

  function parsePoint(rawPoint) {
    if (!rawPoint || typeof rawPoint !== 'object') {
      return null;
    }

    if (Array.isArray(rawPoint.coords) && rawPoint.coords.length >= 2) {
      return [Number(rawPoint.coords[0]), Number(rawPoint.coords[1])];
    }

    if (Array.isArray(rawPoint.point) && rawPoint.point.length >= 2) {
      return [Number(rawPoint.point[0]), Number(rawPoint.point[1])];
    }

    if (typeof rawPoint.lon === 'number' && typeof rawPoint.lat === 'number') {
      return [rawPoint.lon, rawPoint.lat];
    }

    return null;
  }

  function cross(o, a, b) {
    return (a[0] - o[0]) * (b[1] - o[1]) - (a[1] - o[1]) * (b[0] - o[0]);
  }

  function convexHull(points) {
    if (!Array.isArray(points) || points.length < 3) {
      return points || [];
    }

    var sorted = points.slice().sort(function (p1, p2) {
      if (p1[0] === p2[0]) {
        return p1[1] - p2[1];
      }
      return p1[0] - p2[0];
    });

    var lower = [];
    for (var i = 0; i < sorted.length; i += 1) {
      while (lower.length >= 2 && cross(lower[lower.length - 2], lower[lower.length - 1], sorted[i]) < 0) {
        lower.pop();
      }
      lower.push(sorted[i]);
    }

    var upper = [];
    for (var j = sorted.length - 1; j >= 0; j -= 1) {
      while (upper.length >= 2 && cross(upper[upper.length - 2], upper[upper.length - 1], sorted[j]) < 0) {
        upper.pop();
      }
      upper.push(sorted[j]);
    }

    lower.pop();
    upper.pop();
    return lower.concat(upper);
  }

  function uniquePoints(points) {
    if (!Array.isArray(points)) {
      return [];
    }

    var seen = {};
    var out = [];
    for (var i = 0; i < points.length; i += 1) {
      var p = points[i];
      if (!Array.isArray(p) || p.length < 2) {
        continue;
      }
      var key = String(p[0].toFixed(6)) + ':' + String(p[1].toFixed(6));
      if (seen[key]) {
        continue;
      }
      seen[key] = true;
      out.push(p);
    }

    return out;
  }

  function chaikinSmooth(ring, iterations) {
    if (!Array.isArray(ring) || ring.length < 3) {
      return ring || [];
    }

    var out = ring.slice();
    var rounds = Math.max(0, iterations || 0);

    for (var r = 0; r < rounds; r += 1) {
      var next = [];
      for (var i = 0; i < out.length; i += 1) {
        var p0 = out[i];
        var p1 = out[(i + 1) % out.length];
        next.push([
          0.75 * p0[0] + 0.25 * p1[0],
          0.75 * p0[1] + 0.25 * p1[1],
        ]);
        next.push([
          0.25 * p0[0] + 0.75 * p1[0],
          0.25 * p0[1] + 0.75 * p1[1],
        ]);
      }
      out = next;
    }

    return out;
  }

  function municipalityContour(points) {
    var uniq = uniquePoints(points);
    if (uniq.length === 0) {
      return [];
    }

    if (uniq.length === 1) {
      return fallbackPolygon(uniq);
    }

    if (uniq.length === 2) {
      return fallbackPolygon(uniq);
    }

    var center = centroidOfRing(uniq);
    if (!center) {
      return convexHull(uniq);
    }

    var sorted = uniq.slice().sort(function (a, b) {
      var aa = Math.atan2(a[1] - center[1], a[0] - center[0]);
      var bb = Math.atan2(b[1] - center[1], b[0] - center[0]);
      return aa - bb;
    });

    var expanded = [];
    for (var i = 0; i < sorted.length; i += 1) {
      var dx = sorted[i][0] - center[0];
      var dy = sorted[i][1] - center[1];
      var scale = 1.13;
      expanded.push([center[0] + dx * scale, center[1] + dy * scale]);
    }

    return chaikinSmooth(expanded, 2);
  }

  function ellipsePolygon(center, radiusLon, radiusLat, segments) {
    var lon = center[0];
    var lat = center[1];
    var out = [];
    var count = Math.max(8, segments || 12);

    for (var i = 0; i < count; i += 1) {
      var t = (Math.PI * 2 * i) / count;
      out.push([
        lon + Math.cos(t) * radiusLon,
        lat + Math.sin(t) * radiusLat,
      ]);
    }

    return out;
  }

  function capsulePolygon(p1, p2) {
    var dx = p2[0] - p1[0];
    var dy = p2[1] - p1[1];
    var len = Math.sqrt(dx * dx + dy * dy);

    if (len < 1e-7) {
      return ellipsePolygon(p1, 0.06, 0.045, 12);
    }

    var ux = dx / len;
    var uy = dy / len;
    var px = -uy;
    var py = ux;

    var padAlong = Math.max(len * 0.28, 0.03);
    var padCross = Math.max(len * 0.22, 0.025);

    return [
      [p1[0] - ux * padAlong + px * padCross, p1[1] - uy * padAlong + py * padCross],
      [p1[0] - ux * padAlong - px * padCross, p1[1] - uy * padAlong - py * padCross],
      [p2[0] + ux * padAlong - px * padCross, p2[1] + uy * padAlong - py * padCross],
      [p2[0] + ux * padAlong + px * padCross, p2[1] + uy * padAlong + py * padCross],
    ];
  }

  function fallbackPolygon(points) {
    if (!Array.isArray(points) || points.length === 0) {
      return [];
    }

    if (points.length === 1) {
      return ellipsePolygon(points[0], 0.07, 0.055, 12);
    }

    if (points.length === 2) {
      return capsulePolygon(points[0], points[1]);
    }

    var minLon = points[0][0];
    var maxLon = points[0][0];
    var minLat = points[0][1];
    var maxLat = points[0][1];

    for (var i = 1; i < points.length; i += 1) {
      minLon = Math.min(minLon, points[i][0]);
      maxLon = Math.max(maxLon, points[i][0]);
      minLat = Math.min(minLat, points[i][1]);
      maxLat = Math.max(maxLat, points[i][1]);
    }

    var padLon = Math.max((maxLon - minLon) * 0.18, 0.03);
    var padLat = Math.max((maxLat - minLat) * 0.18, 0.025);

    return [
      [minLon - padLon, minLat - padLat],
      [maxLon + padLon, minLat - padLat],
      [maxLon + padLon, maxLat + padLat],
      [minLon - padLon, maxLat + padLat],
    ];
  }

  function shrinkRing(ring, factor) {
    if (!Array.isArray(ring) || ring.length < 3) {
      return ring;
    }

    var safeFactor = typeof factor === 'number' ? Math.max(0.45, Math.min(1, factor)) : 0.88;
    if (safeFactor === 1) {
      return ring;
    }

    var cx = 0;
    var cy = 0;
    for (var i = 0; i < ring.length; i += 1) {
      cx += ring[i][0];
      cy += ring[i][1];
    }
    cx = cx / ring.length;
    cy = cy / ring.length;

    var out = [];
    for (var j = 0; j < ring.length; j += 1) {
      out.push([
        cx + (ring[j][0] - cx) * safeFactor,
        cy + (ring[j][1] - cy) * safeFactor,
      ]);
    }

    return out;
  }

  function clipPolygonWithHalfPlane(polygon, centerA, centerB) {
    if (!Array.isArray(polygon) || polygon.length === 0) {
      return [];
    }

    var midX = (centerA[0] + centerB[0]) / 2;
    var midY = (centerA[1] + centerB[1]) / 2;
    var nx = centerB[0] - centerA[0];
    var ny = centerB[1] - centerA[1];

    function signedDistance(p) {
      return (p[0] - midX) * nx + (p[1] - midY) * ny;
    }

    var output = [];
    for (var i = 0; i < polygon.length; i += 1) {
      var current = polygon[i];
      var next = polygon[(i + 1) % polygon.length];
      var dCurrent = signedDistance(current);
      var dNext = signedDistance(next);
      var currentInside = dCurrent <= 0;
      var nextInside = dNext <= 0;

      if (currentInside && nextInside) {
        output.push(next);
      } else if (currentInside && !nextInside) {
        var tOut = dCurrent / (dCurrent - dNext);
        output.push([
          current[0] + (next[0] - current[0]) * tOut,
          current[1] + (next[1] - current[1]) * tOut,
        ]);
      } else if (!currentInside && nextInside) {
        var tIn = dCurrent / (dCurrent - dNext);
        output.push([
          current[0] + (next[0] - current[0]) * tIn,
          current[1] + (next[1] - current[1]) * tIn,
        ]);
        output.push(next);
      }
    }

    return uniquePoints(output);
  }

  function buildVoronoiGeoJsonFromPointGroups(groups) {
    var groupCenters = [];
    var allPoints = [];

    for (var i = 0; i < groups.length; i += 1) {
      var group = groups[i];
      if (!group || typeof group.name !== 'string' || !Array.isArray(group.points)) {
        continue;
      }

      var groupPoints = [];
      for (var j = 0; j < group.points.length; j += 1) {
        var p = parsePoint(group.points[j]);
        if (p && !Number.isNaN(p[0]) && !Number.isNaN(p[1])) {
          groupPoints.push(p);
          allPoints.push(p);
        }
      }

      if (groupPoints.length === 0) {
        continue;
      }

      var center = centroidOfRing(groupPoints);
      if (!center) {
        continue;
      }

      groupCenters.push({
        name: group.name,
        center: center,
        shrink: typeof group.shrink === 'number' ? group.shrink : 0.99,
      });
    }

    if (groupCenters.length === 0 || allPoints.length < 3) {
      return buildGeoJsonFromPointGroups(groups);
    }

    var boundary = convexHull(allPoints);
    if (!Array.isArray(boundary) || boundary.length < 3) {
      boundary = municipalityContour(allPoints);
    }
    boundary = chaikinSmooth(boundary, 2);

    var features = [];
    for (var g = 0; g < groupCenters.length; g += 1) {
      var cell = boundary.slice();

      for (var h = 0; h < groupCenters.length; h += 1) {
        if (g === h) {
          continue;
        }

        cell = clipPolygonWithHalfPlane(cell, groupCenters[g].center, groupCenters[h].center);
        if (!Array.isArray(cell) || cell.length < 3) {
          break;
        }
      }

      if (!Array.isArray(cell) || cell.length < 3) {
        continue;
      }

      cell = shrinkRing(cell, groupCenters[g].shrink);
      cell.push(cell[0]);

      features.push({
        type: 'Feature',
        properties: { name: groupCenters[g].name },
        geometry: {
          type: 'Polygon',
          coordinates: [cell],
        },
      });
    }

    return {
      type: 'FeatureCollection',
      features: features,
    };
  }

  function buildGeoJsonFromPointGroups(groups) {
    var features = [];

    for (var i = 0; i < groups.length; i += 1) {
      var group = groups[i];
      if (!group || typeof group.name !== 'string' || !Array.isArray(group.points)) {
        continue;
      }

      var points = [];
      for (var j = 0; j < group.points.length; j += 1) {
        var point = parsePoint(group.points[j]);
        if (!point || Number.isNaN(point[0]) || Number.isNaN(point[1])) {
          continue;
        }
        points.push(point);
      }

      if (points.length === 0) {
        continue;
      }

      var ring = municipalityContour(points);
      if (!Array.isArray(ring) || ring.length < 3) {
        ring = points.length >= 3 ? convexHull(points) : fallbackPolygon(points);
      }
      ring = shrinkRing(ring, typeof group.shrink === 'number' ? group.shrink : 0.92);
      ring.push(ring[0]);

      features.push({
        type: 'Feature',
        properties: { name: group.name },
        geometry: {
          type: 'Polygon',
          coordinates: [ring],
        },
      });
    }

    return {
      type: 'FeatureCollection',
      features: features,
    };
  }

  function flattenCoordinates(geometry) {
    if (!geometry || !Array.isArray(geometry.coordinates)) {
      return [];
    }

    if (geometry.type === 'Polygon') {
      return Array.isArray(geometry.coordinates[0]) ? geometry.coordinates[0] : [];
    }

    if (geometry.type === 'MultiPolygon') {
      var all = [];
      for (var i = 0; i < geometry.coordinates.length; i += 1) {
        var polygon = geometry.coordinates[i];
        if (Array.isArray(polygon) && Array.isArray(polygon[0])) {
          all = all.concat(polygon[0]);
        }
      }
      return all;
    }

    return [];
  }

  function centroidOfRing(ring) {
    if (!Array.isArray(ring) || ring.length === 0) {
      return null;
    }

    var sx = 0;
    var sy = 0;
    for (var i = 0; i < ring.length; i += 1) {
      sx += Number(ring[i][0]) || 0;
      sy += Number(ring[i][1]) || 0;
    }

    return [sx / ring.length, sy / ring.length];
  }

  function centroidOfFeature(feature) {
    if (!feature || !feature.geometry) {
      return null;
    }

    var ring = flattenCoordinates(feature.geometry);
    return centroidOfRing(ring);
  }

  function centroidOfGroupPoints(points) {
    if (!Array.isArray(points) || points.length === 0) {
      return null;
    }

    var parsed = [];
    for (var i = 0; i < points.length; i += 1) {
      var p = parsePoint(points[i]);
      if (p && !Number.isNaN(p[0]) && !Number.isNaN(p[1])) {
        parsed.push(p);
      }
    }

    return centroidOfRing(parsed);
  }

  function assignFeaturesToNearestGroup(geoJson, pointGroups) {
    if (!geoJson || !Array.isArray(geoJson.features) || !Array.isArray(pointGroups)) {
      return geoJson;
    }

    var groupCentroids = [];
    for (var i = 0; i < pointGroups.length; i += 1) {
      var group = pointGroups[i];
      if (!group || typeof group.name !== 'string' || !Array.isArray(group.points)) {
        continue;
      }
      var groupCenter = centroidOfGroupPoints(group.points);
      if (!groupCenter) {
        continue;
      }
      groupCentroids.push({ name: group.name, center: groupCenter });
    }

    if (groupCentroids.length === 0) {
      return geoJson;
    }

    var featureCenters = [];
    for (var j = 0; j < geoJson.features.length; j += 1) {
      var feature = geoJson.features[j];
      if (!feature || !feature.properties) {
        continue;
      }

      var featureCenter = centroidOfFeature(feature);
      if (!featureCenter) {
        continue;
      }

      featureCenters.push({ index: j, center: featureCenter });

      var bestName = null;
      var bestDistance = Number.POSITIVE_INFINITY;
      for (var g = 0; g < groupCentroids.length; g += 1) {
        var gc = groupCentroids[g];
        var dx = featureCenter[0] - gc.center[0];
        var dy = featureCenter[1] - gc.center[1];
        var distance = dx * dx + dy * dy;
        if (distance < bestDistance) {
          bestDistance = distance;
          bestName = gc.name;
        }
      }

      if (bestName) {
        feature.properties.name = bestName;
      }
    }

    var usedGroups = {};
    for (var k = 0; k < geoJson.features.length; k += 1) {
      var currentName = geoJson.features[k] && geoJson.features[k].properties
        ? geoJson.features[k].properties.name
        : null;
      if (typeof currentName === 'string' && currentName !== '') {
        usedGroups[currentName] = true;
      }
    }

    for (var h = 0; h < groupCentroids.length; h += 1) {
      var groupName = groupCentroids[h].name;
      if (usedGroups[groupName]) {
        continue;
      }

      var nearestFeatureIndex = -1;
      var nearestFeatureDistance = Number.POSITIVE_INFINITY;

      for (var m = 0; m < featureCenters.length; m += 1) {
        var fc = featureCenters[m];
        var dx2 = fc.center[0] - groupCentroids[h].center[0];
        var dy2 = fc.center[1] - groupCentroids[h].center[1];
        var d2 = dx2 * dx2 + dy2 * dy2;
        if (d2 < nearestFeatureDistance) {
          nearestFeatureDistance = d2;
          nearestFeatureIndex = fc.index;
        }
      }

      if (nearestFeatureIndex >= 0 && geoJson.features[nearestFeatureIndex] && geoJson.features[nearestFeatureIndex].properties) {
        geoJson.features[nearestFeatureIndex].properties.name = groupName;
        usedGroups[groupName] = true;
      }
    }

    return geoJson;
  }

  function attachLabelHoverSync(chart, options) {
    if (!chart || !options || !options._echartsAddon || !options._echartsAddon.labelHoverSync) {
      return;
    }

    var sync = options._echartsAddon.labelHoverSync;
    var mapSeriesName = typeof sync.mapSeriesName === 'string' ? sync.mapSeriesName : '';
    var labelSeriesName = typeof sync.labelSeriesName === 'string' ? sync.labelSeriesName : '';
    if (!mapSeriesName || !labelSeriesName) {
      return;
    }

    var series = Array.isArray(options.series) ? options.series : [];
    var mapSeriesIndex = -1;
    var labelSeriesIndex = -1;
    for (var i = 0; i < series.length; i += 1) {
      if (series[i] && series[i].name === mapSeriesName) {
        mapSeriesIndex = i;
      }
      if (series[i] && series[i].name === labelSeriesName) {
        labelSeriesIndex = i;
      }
    }

    if (mapSeriesIndex < 0 || labelSeriesIndex < 0) {
      return;
    }

    var labelData = Array.isArray(series[labelSeriesIndex].data) ? series[labelSeriesIndex].data : [];
    var labelIndexByName = {};
    for (var j = 0; j < labelData.length; j += 1) {
      if (labelData[j] && typeof labelData[j].name === 'string') {
        labelIndexByName[labelData[j].name] = j;
      }
    }

    var activeLabelDataIndex = null;

    chart.on('mouseover', function (params) {
      if (!params || params.seriesIndex !== mapSeriesIndex || typeof params.name !== 'string') {
        return;
      }

      var labelDataIndex = labelIndexByName[params.name];
      if (typeof labelDataIndex !== 'number') {
        return;
      }

      if (activeLabelDataIndex !== null && activeLabelDataIndex !== labelDataIndex) {
        chart.dispatchAction({
          type: 'downplay',
          seriesIndex: labelSeriesIndex,
          dataIndex: activeLabelDataIndex,
        });
      }

      activeLabelDataIndex = labelDataIndex;
      chart.dispatchAction({
        type: 'highlight',
        seriesIndex: labelSeriesIndex,
        dataIndex: labelDataIndex,
      });
    });

    chart.on('mouseout', function (params) {
      if (!params || params.seriesIndex !== mapSeriesIndex) {
        return;
      }

      if (activeLabelDataIndex !== null) {
        chart.dispatchAction({
          type: 'downplay',
          seriesIndex: labelSeriesIndex,
          dataIndex: activeLabelDataIndex,
        });
        activeLabelDataIndex = null;
      }
    });
  }

  function initGeoMap(el) {
    if (!(el instanceof HTMLElement) || el.dataset.echartsGeoInit === '1') {
      return;
    }

    if (typeof window.echarts === 'undefined') {
      return;
    }

    var mapUrl = el.getAttribute('data-map-geojson-url') || '';
    var mapGeoJsonEncoded = el.getAttribute('data-map-geojson') || '';
    var mapGeoJson = decodeOptions(mapGeoJsonEncoded);
    var extraMapsEncoded = el.getAttribute('data-map-extra-maps') || '';
    var extraMaps = decodeOptions(extraMapsEncoded);
    var mapName = el.getAttribute('data-map-name') || '';
    var filterProp = el.getAttribute('data-map-filter-prop') || '';
    var filterValue = el.getAttribute('data-map-filter-value') || '';
    var nameProp = el.getAttribute('data-map-name-prop') || '';
    var pointGroupsEncoded = el.getAttribute('data-map-point-groups') || '';
    var pointGroups = decodeOptions(pointGroupsEncoded);
    var pointGroupsMode = el.getAttribute('data-map-point-groups-mode') || 'contour';
    var groupsEncoded = el.getAttribute('data-map-groups') || '';
    var groupSourceProp = el.getAttribute('data-map-group-source-prop') || 'NAME_3';
    var groupsDropUnmatched = el.getAttribute('data-map-groups-drop-unmatched') === '1';
    var groups = decodeOptions(groupsEncoded);
    var optionsEncoded = el.getAttribute('data-map-options') || '';
    var options = decodeOptions(optionsEncoded);

    if (!mapName || !options) {
      return;
    }

    if (Array.isArray(extraMaps)) {
      for (var e = 0; e < extraMaps.length; e += 1) {
        var extra = extraMaps[e];
        if (!extra || typeof extra.name !== 'string' || extra.name === '' || !extra.geoJson || !Array.isArray(extra.geoJson.features)) {
          continue;
        }
        window.echarts.registerMap(extra.name, extra.geoJson);
      }
    }

    if (mapGeoJson && Array.isArray(mapGeoJson.features)) {
      window.echarts.registerMap(mapName, mapGeoJson);

      var inlineChart = window.echarts.init(el);
      inlineChart.setOption(options);
      attachLabelHoverSync(inlineChart, options);
      el.dataset.echartsGeoInit = '1';

      window.addEventListener('resize', function () {
        inlineChart.resize();
      });
      return;
    }

    if (Array.isArray(pointGroups) && pointGroups.length > 0 && (pointGroupsMode === 'contour' || pointGroupsMode === 'municipality-voronoi')) {
      var generated = pointGroupsMode === 'municipality-voronoi'
        ? buildVoronoiGeoJsonFromPointGroups(pointGroups)
        : buildGeoJsonFromPointGroups(pointGroups);
      window.echarts.registerMap(mapName, generated);

      var pointChart = window.echarts.init(el);
      pointChart.setOption(options);
      attachLabelHoverSync(pointChart, options);
      el.dataset.echartsGeoInit = '1';

      window.addEventListener('resize', function () {
        pointChart.resize();
      });
      return;
    }

    if (!mapUrl) {
      return;
    }

    fetch(mapUrl, { credentials: 'same-origin' })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('geojson-load-failed');
        }
        return response.json();
      })
      .then(function (geoJson) {
        if (
          filterProp !== '' &&
          filterValue !== '' &&
          geoJson &&
          Array.isArray(geoJson.features)
        ) {
          geoJson = {
            type: geoJson.type || 'FeatureCollection',
            features: geoJson.features.filter(function (feature) {
              if (!feature || !feature.properties) {
                return false;
              }

              return String(feature.properties[filterProp] || '') === filterValue;
            }),
          };
        }

        if (nameProp !== '' && geoJson && Array.isArray(geoJson.features)) {
          for (var i = 0; i < geoJson.features.length; i += 1) {
            var feature = geoJson.features[i];
            if (!feature || !feature.properties) {
              continue;
            }

            var mappedName = feature.properties[nameProp];
            if (typeof mappedName === 'string' && mappedName !== '') {
              feature.properties.name = mappedName;
            }
          }
        }

        if (Array.isArray(groups) && groups.length > 0 && geoJson && Array.isArray(geoJson.features)) {
          var filteredFeatures = [];

          for (var j = 0; j < geoJson.features.length; j += 1) {
            var current = geoJson.features[j];
            if (!current || !current.properties) {
              continue;
            }

            var sourceName = normalizeText(String(current.properties[groupSourceProp] || ''));
            var matchedGroup = null;

            for (var g = 0; g < groups.length; g += 1) {
              var group = groups[g];
              if (!group || typeof group.name !== 'string' || !Array.isArray(group.keywords)) {
                continue;
              }

              for (var k = 0; k < group.keywords.length; k += 1) {
                var keyword = normalizeText(String(group.keywords[k] || ''));
                if (keyword !== '' && sourceName.indexOf(keyword) !== -1) {
                  matchedGroup = group;
                  break;
                }
              }

              if (matchedGroup) {
                break;
              }
            }

            if (matchedGroup) {
              current.properties.name = matchedGroup.name;
              filteredFeatures.push(current);
            } else if (!groupsDropUnmatched) {
              filteredFeatures.push(current);
            }
          }

          geoJson = {
            type: geoJson.type || 'FeatureCollection',
            features: filteredFeatures,
          };
        }

        if (Array.isArray(pointGroups) && pointGroups.length > 0 && pointGroupsMode === 'nearest') {
          geoJson = assignFeaturesToNearestGroup(geoJson, pointGroups);
        }

        window.echarts.registerMap(mapName, geoJson);

        var chart = window.echarts.init(el);
        chart.setOption(options);
        attachLabelHoverSync(chart, options);
        el.dataset.echartsGeoInit = '1';

        window.addEventListener('resize', function () {
          chart.resize();
        });
      })
      .catch(function () {
        el.innerHTML = '<div style="padding:12px;color:#b00020">Karte konnte nicht geladen werden.</div>';
      });
  }

  function initAll(root) {
    var scope = root || document;
    var nodes = scope.querySelectorAll('.js-echarts-geo-map');
    for (var i = 0; i < nodes.length; i += 1) {
      initGeoMap(nodes[i]);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    initAll(document);
  });

  document.addEventListener('rex:ready', function (event) {
    var root = event && event.detail && event.detail[0] ? event.detail[0] : document;
    initAll(root);
  });
})();
