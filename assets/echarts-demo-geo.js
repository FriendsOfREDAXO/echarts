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

  function initGeoMap(el) {
    if (!(el instanceof HTMLElement) || el.dataset.echartsGeoInit === '1') {
      return;
    }

    if (typeof window.echarts === 'undefined') {
      return;
    }

    var mapUrl = el.getAttribute('data-map-geojson-url') || '';
    var mapName = el.getAttribute('data-map-name') || '';
    var filterProp = el.getAttribute('data-map-filter-prop') || '';
    var filterValue = el.getAttribute('data-map-filter-value') || '';
    var nameProp = el.getAttribute('data-map-name-prop') || '';
    var groupsEncoded = el.getAttribute('data-map-groups') || '';
    var groupSourceProp = el.getAttribute('data-map-group-source-prop') || 'NAME_3';
    var groupsDropUnmatched = el.getAttribute('data-map-groups-drop-unmatched') === '1';
    var groups = decodeOptions(groupsEncoded);
    var optionsEncoded = el.getAttribute('data-map-options') || '';
    var options = decodeOptions(optionsEncoded);

    if (!mapUrl || !mapName || !options) {
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

        window.echarts.registerMap(mapName, geoJson);

        var chart = window.echarts.init(el);
        chart.setOption(options);
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
