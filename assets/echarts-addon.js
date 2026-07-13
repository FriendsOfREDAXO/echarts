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

  function pickTooltipFromParams(params) {
    if (!params) {
      return '';
    }

    if (Array.isArray(params)) {
      if (params.length === 0) {
        return '';
      }

      var referenceIndex = typeof params[0].dataIndex === 'number' ? params[0].dataIndex : null;
      if (referenceIndex === null) {
        return '';
      }

      for (var i = 0; i < params.length; i += 1) {
        var item = params[i];

        if (!item || !item.data || typeof item.data !== 'object') {
          continue;
        }

        if (typeof item.dataIndex === 'number' && item.dataIndex !== referenceIndex) {
          continue;
        }

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

        var axisDefault = lines.join('<br/>');
        var axisCustom = pickTooltipFromParams(params);
        if (axisCustom !== '') {
          return axisCustom + '<br/>' + axisDefault;
        }

        return axisDefault;
      }

      if (params && typeof params === 'object') {
        var pv = params.value;
        if (Array.isArray(pv)) {
          pv = pv.length > 1 ? pv[1] : pv[0];
        }
        var pname = (params.name || params.seriesName || '').toString();
        var itemCustom = pickTooltipFromParams(params);
        var valueLine = '';
        if (pname !== '') {
          valueLine = pname + ': ' + pv;
        } else {
          valueLine = String(pv);
        }

        if (itemCustom !== '') {
          return itemCustom + '<br/>' + valueLine;
        }

        return valueLine;
      }

      return '';
    };
  }

  function exportChartAsPdf(chart, fileName) {
    var imageUrl = chart.getDataURL({
      type: 'png',
      pixelRatio: 2,
      backgroundColor: '#ffffff',
      excludeComponents: ['toolbox']
    });

    if (typeof imageUrl !== 'string' || imageUrl.length < 200) {
      imageUrl = chart.getDataURL({
        type: 'svg',
        pixelRatio: 2,
        backgroundColor: '#ffffff',
        excludeComponents: ['toolbox']
      });
    }

    var popup = window.open('', '_blank');
    if (!popup) {
      return;
    }

    var title = (fileName || 'chart').toString();
    popup.document.open();
    popup.document.write('<!doctype html><html><head><meta charset="utf-8"><title>' + title + '</title></head><body></body></html>');
    popup.document.close();

    popup.document.body.style.margin = '0';
    popup.document.body.style.padding = '16px';
    popup.document.body.style.background = '#ffffff';
    popup.document.body.style.fontFamily = 'Arial,sans-serif';

    var img = popup.document.createElement('img');
    img.alt = 'chart';
    img.style.maxWidth = '100%';
    img.style.height = 'auto';
    img.style.display = 'block';
    img.style.margin = '0 auto';

    var triggerPrint = function () {
      popup.focus();
      popup.print();
    };

    var toolbar = popup.document.createElement('div');
    toolbar.style.marginBottom = '12px';

    var printButton = popup.document.createElement('button');
    printButton.type = 'button';
    printButton.textContent = 'PDF drucken';
    printButton.style.padding = '8px 12px';
    printButton.style.border = '1px solid #355070';
    printButton.style.background = '#355070';
    printButton.style.color = '#ffffff';
    printButton.style.borderRadius = '4px';
    printButton.style.cursor = 'pointer';
    printButton.onclick = triggerPrint;

    var hint = popup.document.createElement('span');
    hint.textContent = '  oder Cmd/Ctrl+P';
    hint.style.marginLeft = '8px';
    hint.style.color = '#555';
    hint.style.fontSize = '12px';

    toolbar.appendChild(printButton);
    toolbar.appendChild(hint);

    img.onload = function () {};

    img.onerror = function () {
      hint.textContent = '  Vorschau konnte nicht geladen werden.';
      hint.style.color = '#b00020';
    };

    popup.document.body.appendChild(toolbar);
    popup.document.body.appendChild(img);
    img.src = imageUrl;
  }

  function getPdfOutConfig() {
    if (!window.rex || !window.rex.echarts_pdf_export || typeof window.rex.echarts_pdf_export !== 'object') {
      return null;
    }

    var cfg = window.rex.echarts_pdf_export;
    if (!cfg.enabled || !cfg.url || !cfg.token) {
      return null;
    }

    return cfg;
  }

  function applySansSerifTypography(chart) {
    if (!chart || typeof chart.setOption !== 'function') {
      return;
    }

    var fontFamily = 'DejaVu Sans, Arial, Helvetica, sans-serif';
    chart.setOption({
      textStyle: { fontFamily: fontFamily },
      title: { textStyle: { fontFamily: fontFamily }, subtextStyle: { fontFamily: fontFamily } },
      legend: { textStyle: { fontFamily: fontFamily } },
      toolbox: { textStyle: { fontFamily: fontFamily } },
      xAxis: { axisLabel: { fontFamily: fontFamily }, nameTextStyle: { fontFamily: fontFamily } },
      yAxis: { axisLabel: { fontFamily: fontFamily }, nameTextStyle: { fontFamily: fontFamily } },
      radar: { name: { textStyle: { fontFamily: fontFamily } } }
    }, false, true);
  }

  function decodeSvgDataUrl(dataUrl) {
    if (dataUrl.indexOf('data:image/svg+xml;base64,') === 0) {
      var base64 = dataUrl.substring('data:image/svg+xml;base64,'.length);
      return decodeURIComponent(escape(atob(base64)));
    }

    var comma = dataUrl.indexOf(',');
    if (comma === -1) {
      return '';
    }

    return decodeURIComponent(dataUrl.substring(comma + 1));
  }

  function encodeSvgDataUrl(svgText) {
    return 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgText)));
  }

  function normalizeSvgFonts(dataUrl) {
    if (dataUrl.indexOf('data:image/svg+xml') !== 0) {
      return dataUrl;
    }

    var svg = decodeSvgDataUrl(dataUrl);
    if (!svg) {
      return dataUrl;
    }

    var fontStyle = '<style>text,tspan{font-family:DejaVu Sans,Arial,Helvetica,sans-serif !important;}</style>';
    if (svg.indexOf('<defs>') !== -1) {
      svg = svg.replace('<defs>', '<defs>' + fontStyle);
    } else {
      svg = svg.replace(/<svg([^>]*)>/, '<svg$1><defs>' + fontStyle + '</defs>');
    }

    return encodeSvgDataUrl(svg);
  }

  function rasterizeSvgToPng(svgDataUrl, width, height) {
    return new Promise(function (resolve, reject) {
      var img = new Image();
      img.onload = function () {
        try {
          var canvas = document.createElement('canvas');
          var w = Math.max(640, Math.round(width * 2));
          var h = Math.max(360, Math.round(height * 2));
          canvas.width = w;
          canvas.height = h;

          var ctx = canvas.getContext('2d');
          if (!ctx) {
            reject(new Error('no-canvas-context'));
            return;
          }

          ctx.fillStyle = '#ffffff';
          ctx.fillRect(0, 0, w, h);
          ctx.drawImage(img, 0, 0, w, h);
          resolve(canvas.toDataURL('image/png'));
        } catch (error) {
          reject(error);
        }
      };

      img.onerror = function () {
        reject(new Error('svg-rasterize-failed'));
      };

      img.src = svgDataUrl;
    });
  }

  function getChartImageDataForPdf(chart) {
    applySansSerifTypography(chart);

    var imageUrl = chart.getDataURL({
      type: 'png',
      pixelRatio: 2,
      backgroundColor: '#ffffff',
      excludeComponents: ['toolbox']
    });

    if (typeof imageUrl !== 'string' || imageUrl.length < 200) {
      imageUrl = chart.getDataURL({
        type: 'svg',
        pixelRatio: 2,
        backgroundColor: '#ffffff',
        excludeComponents: ['toolbox']
      });
    }

    if (typeof imageUrl === 'string' && imageUrl.indexOf('data:image/svg+xml') === 0) {
      var normalized = normalizeSvgFonts(imageUrl);
      var width = typeof chart.getWidth === 'function' ? chart.getWidth() : 1200;
      var height = typeof chart.getHeight === 'function' ? chart.getHeight() : 700;
      return rasterizeSvgToPng(normalized, width, height).catch(function () {
        return normalized;
      });
    }

    return Promise.resolve(imageUrl);
  }

  function getChartTitle(chart, fallback) {
    try {
      var option = chart.getOption();
      var title = option && option.title && option.title[0] && option.title[0].text ? option.title[0].text : '';
      if (typeof title === 'string' && title.trim() !== '') {
        return title.trim();
      }
    } catch (error) {
      // ignore
    }

    return fallback;
  }

  function exportChartWithPdfOut(chart, fileName) {
    var cfg = getPdfOutConfig();
    if (!cfg) {
      return false;
    }

    var safeName = (fileName || 'chart').toString();
    var chartTitle = getChartTitle(chart, safeName);

    getChartImageDataForPdf(chart)
      .then(function (imageUrl) {
        var formData = new FormData();
        formData.append('_csrf_token', cfg.token);
        formData.append('image_data', imageUrl);
        formData.append('file_name', safeName);
        formData.append('chart_title', chartTitle);
        formData.append('page_title', document.title || '');
        formData.append('source_url', window.location.href || '');

        return fetch(cfg.url, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        });
      })
      .then(function (response) {
        var contentType = response.headers.get('content-type') || '';
        if (!response.ok || contentType.indexOf('application/pdf') === -1) {
          throw new Error('PDF export failed');
        }
        return response.blob();
      })
      .then(function (blob) {
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = safeName + '.pdf';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      })
      .catch(function () {
        window.alert('PDF-Export fehlgeschlagen. Bitte PDFOut-Konfiguration prüfen.');
      });

    return true;
  }

  function applyAddonToolboxFeatures(options, chart) {
    if (!options || !options.toolbox || typeof options.toolbox !== 'object') {
      return;
    }

    var feature = options.toolbox.feature;
    if (!feature || typeof feature !== 'object') {
      return;
    }

    var pdfFeature = feature.myExportPdf;
    if (!pdfFeature || typeof pdfFeature !== 'object' || !pdfFeature.__echarts_addon_pdf) {
      return;
    }

    var pdfTitle = typeof pdfFeature.title === 'string' && pdfFeature.title !== '' ? pdfFeature.title : 'PDF';
    var pdfIcon = typeof pdfFeature.icon === 'string' && pdfFeature.icon !== '' ? pdfFeature.icon : null;
    var pdfName = typeof pdfFeature.name === 'string' && pdfFeature.name !== '' ? pdfFeature.name : 'chart';

    feature.myExportPdf = {
      show: true,
      title: pdfTitle,
      icon: pdfIcon,
      onclick: function () {
        if (!exportChartWithPdfOut(chart, pdfName)) {
          window.alert('PDF-Export ist nur mit installiertem PDFOut-AddOn verfügbar.');
        }
      }
    };
  }

  function hasTimelineOptions(options) {
    if (!options || typeof options !== 'object') {
      return false;
    }

    if (options.baseOption && options.baseOption.timeline) {
      return true;
    }

    return !!options.timeline;
  }

  function applyTimelineViewportStart(chart, options, addonMeta, element) {
    if (!hasTimelineOptions(options)) {
      return;
    }

    var timelineMeta = addonMeta && addonMeta.timeline && typeof addonMeta.timeline === 'object'
      ? addonMeta.timeline
      : null;

    if (!timelineMeta || timelineMeta.startInViewport !== true) {
      return;
    }

    var pauseWhenOutOfView = timelineMeta.pauseWhenOutOfView === true;
    var once = timelineMeta.once !== false;

    var play = function () {
      chart.dispatchAction({
        type: 'timelinePlayChange',
        playState: true
      });
    };

    var pause = function () {
      chart.dispatchAction({
        type: 'timelinePlayChange',
        playState: false
      });
    };

    if (typeof IntersectionObserver === 'undefined') {
      play();
      return;
    }

    var hasStarted = false;
    var observer = new IntersectionObserver(function (entries) {
      for (var i = 0; i < entries.length; i += 1) {
        var entry = entries[i];
        if (entry.target !== element) {
          continue;
        }

        if (entry.isIntersecting) {
          play();
          hasStarted = true;
          if (once && !pauseWhenOutOfView) {
            observer.unobserve(element);
          }
          continue;
        }

        if (pauseWhenOutOfView && hasStarted) {
          pause();
        }
      }
    }, {
      root: null,
      threshold: 0.35
    });

    observer.observe(element);
  }

  function initChartElement(element) {
    if (!element || element.dataset.echartsInitialized === '1' || element.dataset.echartsInitialized === 'pending') {
      return;
    }

    if (typeof window.echarts === 'undefined') {
      return;
    }

    var options = decodeOptions(element.getAttribute('data-echarts-options'));
    if (!options) {
      return;
    }

    var addonMeta = options._echartsAddon && typeof options._echartsAddon === 'object'
      ? options._echartsAddon
      : {};
    if (options._echartsAddon) {
      delete options._echartsAddon;
    }

    ensureCustomTooltipFormatter(options);

    var initOpts = {};
    if (addonMeta.renderer === 'svg') {
      initOpts.renderer = 'svg';
    }

    var chart = window.echarts.init(element, null, initOpts);
    applyAddonToolboxFeatures(options, chart);

    var renderChart = function () {
      chart.setOption(options);
      applyTimelineViewportStart(chart, options, addonMeta, element);
      element.dataset.echartsInitialized = '1';
    };

    var startInViewport = addonMeta && addonMeta.startInViewport === true;
    if (startInViewport) {
      element.dataset.echartsInitialized = 'pending';

      if (typeof IntersectionObserver === 'undefined') {
        renderChart();
      } else {
        var observer = new IntersectionObserver(function (entries) {
          for (var i = 0; i < entries.length; i += 1) {
            var entry = entries[i];
            if (entry.target === element && entry.isIntersecting) {
              observer.unobserve(element);
              renderChart();
              break;
            }
          }
        }, {
          root: null,
          threshold: 0.35
        });

        observer.observe(element);
      }
    } else {
      renderChart();
    }

    chart.on('click', function (params) {
      var link = '';
      var linkTarget = '_self';

      if (params && params.data && typeof params.data === 'object' && !Array.isArray(params.data)) {
        if (typeof params.data.link === 'string' && params.data.link !== '') {
          link = params.data.link;
        } else if (params.data.link && typeof params.data.link === 'object' && typeof params.data.link.href === 'string' && params.data.link.href !== '') {
          link = params.data.link.href;
          if (typeof params.data.link.target === 'string' && params.data.link.target !== '') {
            linkTarget = params.data.link.target;
          }
        }

        if (typeof params.data.link_target === 'string' && params.data.link_target !== '') {
          linkTarget = params.data.link_target;
        }
      }

      if (!link && params && typeof params.seriesIndex === 'number' && options && Array.isArray(options.series)) {
        var series = options.series[params.seriesIndex];
        if (series && typeof series === 'object' && typeof series.link === 'string' && series.link !== '') {
          link = series.link;
        }
      }

      if (typeof link === 'string' && link !== '') {
        if (linkTarget === '_blank') {
          var win = window.open(link, '_blank', 'noopener,noreferrer');
          if (win) {
            win.opener = null;
          }
          return;
        }

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
