(function () {
  'use strict';

  function decodeOptions(encoded) {
    if (!encoded) {
      return null;
    }

    try {
      var json = atob(encoded);
      return JSON.parse(json);
    } catch (error) {
      return null;
    }
  }

  function pickTooltipFromParams(params) {
    if (!params) {
      return '';
    }

    if (Array.isArray(params)) {
      for (var i = 0; i < params.length; i += 1) {
        var item = params[i];
        if (item && item.data && typeof item.data === 'object' && typeof item.data.tooltip === 'string' && item.data.tooltip !== '') {
          return item.data.tooltip;
        }
      }
      return '';
    }

    if (params.data && typeof params.data === 'object' && typeof params.data.tooltip === 'string' && params.data.tooltip !== '') {
      return params.data.tooltip;
    }

    return '';
  }

  function optionsContainCustomTooltip(options) {
    if (!options || !Array.isArray(options.series)) {
      return false;
    }

    for (var i = 0; i < options.series.length; i += 1) {
      var series = options.series[i];
      if (!series || !Array.isArray(series.data)) {
        continue;
      }
      for (var j = 0; j < series.data.length; j += 1) {
        var point = series.data[j];
        if (point && typeof point === 'object' && !Array.isArray(point) && typeof point.tooltip === 'string' && point.tooltip !== '') {
          return true;
        }
      }
    }

    return false;
  }

  function ensureCustomTooltipFormatter(options) {
    if (!optionsContainCustomTooltip(options)) {
      return;
    }

    if (!options.tooltip || typeof options.tooltip !== 'object') {
      options.tooltip = { trigger: 'item' };
    }

    options.tooltip.formatter = function (params) {
      var custom = pickTooltipFromParams(params);
      if (custom !== '') {
        return custom;
      }

      if (Array.isArray(params) && params.length > 0) {
        var first = params[0];
        var title = (first.axisValueLabel || first.name || '').toString();
        var lines = [];
        if (title !== '') {
          lines.push(title);
        }
        for (var i = 0; i < params.length; i += 1) {
          var p = params[i];
          var value = p && p.value;
          if (Array.isArray(value)) {
            value = value.length > 1 ? value[1] : value[0];
          }
          if (value === null || typeof value === 'undefined' || value === '') {
            continue;
          }
          lines.push((p.marker || '') + (p.seriesName || '') + ': ' + value);
        }

        return lines.join('<br/>');
      }

      if (params && typeof params === 'object') {
        var pv = params.value;
        if (Array.isArray(pv)) {
          pv = pv.length > 1 ? pv[1] : pv[0];
        }
        var pname = (params.name || params.seriesName || '').toString();
        if (pname !== '') {
          return pname + ': ' + pv;
        }
        return String(pv);
      }

      return '';
    };
  }

  function initChartElement(element) {
    if (!element || element.dataset.echartsInitialized === '1') {
      return;
    }

    if (typeof window.echarts === 'undefined') {
      return;
    }

    var options = decodeOptions(element.getAttribute('data-echarts-options'));
    if (!options) {
      return;
    }

    ensureCustomTooltipFormatter(options);

    var chart = window.echarts.init(element);
    chart.setOption(options);
    element.dataset.echartsInitialized = '1';

    chart.on('click', function (params) {
      var link = '';

      if (params && params.data && typeof params.data === 'object' && !Array.isArray(params.data)) {
        if (typeof params.data.link === 'string' && params.data.link !== '') {
          link = params.data.link;
        }
      }

      if (!link && params && typeof params.seriesIndex === 'number' && options && Array.isArray(options.series)) {
        var series = options.series[params.seriesIndex];
        if (series && typeof series === 'object' && typeof series.link === 'string' && series.link !== '') {
          link = series.link;
        }
      }

      if (typeof link === 'string' && link !== '') {
        window.location.href = link;
      }
    });

    window.addEventListener('resize', function () {
      chart.resize();
    });
  }

  function initAll(scope) {
    var root = scope || document;
    if (root.matches && root.matches('[data-echarts-options]')) {
      initChartElement(root);
    }
    var nodes = root.querySelectorAll('[data-echarts-options]');
    for (var i = 0; i < nodes.length; i += 1) {
      initChartElement(nodes[i]);
    }
  }

  function registerMutationObserver() {
    if (typeof MutationObserver === 'undefined') {
      return;
    }

    var observer = new MutationObserver(function (mutations) {
      for (var i = 0; i < mutations.length; i += 1) {
        var mutation = mutations[i];
        if (!mutation.addedNodes || mutation.addedNodes.length === 0) {
          continue;
        }

        for (var j = 0; j < mutation.addedNodes.length; j += 1) {
          var node = mutation.addedNodes[j];
          if (!node || node.nodeType !== 1) {
            continue;
          }
          initAll(node);
        }
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initAll(document);
    registerMutationObserver();
  });

  document.addEventListener('rex:ready', function (event) {
    var root = event && event.detail && event.detail[0] ? event.detail[0] : document;
    initAll(root);
  });
})();
