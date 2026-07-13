(function () {
  'use strict';

  function decodeOptionsMap(encoded) {
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

  function cloneOptions(options) {
    if (!options || typeof options !== 'object') {
      return null;
    }

    try {
      return JSON.parse(JSON.stringify(options));
    } catch (error) {
      return null;
    }
  }

  function applyActiveState(container, activeType) {
    var buttons = container.querySelectorAll('[data-switch-type]');
    for (var i = 0; i < buttons.length; i += 1) {
      var button = buttons[i];
      if (!(button instanceof HTMLElement)) {
        continue;
      }

      var isActive = button.getAttribute('data-switch-type') === activeType;
      if (isActive) {
        button.classList.add('is-active');
        button.classList.add('active');
        button.setAttribute('aria-pressed', 'true');
      } else {
        button.classList.remove('is-active');
        button.classList.remove('active');
        button.setAttribute('aria-pressed', 'false');
      }
    }
  }

  function getInitOptions(options) {
    var initOpts = {};
    if (options && options._echartsAddon && options._echartsAddon.renderer === 'svg') {
      initOpts.renderer = 'svg';
    }
    return initOpts;
  }

  function prepareOptions(options) {
    var normalized = cloneOptions(options);
    if (!normalized) {
      return null;
    }

    if (normalized._echartsAddon) {
      delete normalized._echartsAddon;
    }

    return normalized;
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

  function applyPdfToolboxFeature(options, chart) {
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

  function initSwitcher(container) {
    if (!(container instanceof HTMLElement) || container.dataset.switcherReady === '1') {
      return;
    }

    var chartEl = container.querySelector('.js-echarts-switch-chart');
    if (!(chartEl instanceof HTMLElement) || typeof window.echarts === 'undefined') {
      return;
    }

    var optionsMap = decodeOptionsMap(container.getAttribute('data-switch-options'));
    if (!optionsMap || typeof optionsMap !== 'object') {
      return;
    }

    var defaultType = container.getAttribute('data-switch-default') || 'bar';

    container.addEventListener('click', function (event) {
      var target = event.target;
      if (!(target instanceof Element)) {
        return;
      }

      var button = target.closest('[data-switch-type]');
      if (!(button instanceof HTMLElement)) {
        return;
      }

      var type = button.getAttribute('data-switch-type') || '';
      var nextOptions = optionsMap[type];
      if (!nextOptions) {
        return;
      }

      var chart = window.echarts.getInstanceByDom(chartEl);
      if (!chart) {
        chart = window.echarts.init(chartEl, null, getInitOptions(nextOptions));
      }

      var prepared = prepareOptions(nextOptions);
      if (!prepared) {
        return;
      }

      applyPdfToolboxFeature(prepared, chart);
      chart.setOption(prepared, true, true);
      chart.resize();
      applyActiveState(container, type);
    });

    applyActiveState(container, defaultType);
    container.dataset.switcherReady = '1';
  }

  function initAll(root) {
    var scope = root || document;
    var containers = scope.querySelectorAll('[data-echarts-switcher]');
    for (var i = 0; i < containers.length; i += 1) {
      initSwitcher(containers[i]);
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
