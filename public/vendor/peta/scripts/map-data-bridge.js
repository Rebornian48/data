// Redirects the map's data fetch from Google Sheets/CSV to Laravel's API.
// Load AFTER jQuery and BEFORE scripts/map.js. Depends on window.PETA_DATA_URL.
(function () {
  if (!window.PETA_DATA_URL) return;

  var dataPromise = $.getJSON(window.PETA_DATA_URL); // eager fetch, single round trip

  // Force map.js's "CSV exists?" HEAD check into its error branch so it takes
  // the sheets-API code path — which we then intercept below.
  var origAjax = $.ajax;
  $.ajax = function (opts) {
    if (opts && typeof opts.url === 'string'
        && opts.url.indexOf('./csv/Options.csv') === 0
        && opts.type === 'HEAD') {
      dataPromise.always(function () { opts.error && opts.error(); });
      return;
    }
    return origAjax.apply(this, arguments);
  };

  var origGetJSON = $.getJSON;
  $.getJSON = function (url, cb) {
    if (typeof url !== 'string' || url.indexOf('sheets.googleapis.com') === -1) {
      return origGetJSON.apply(this, arguments);
    }

    // Sheets metadata: list sheet names.
    if (url.indexOf('/values/') === -1) {
      return deferredFrom(dataPromise, function (res) {
        var sheets = [
          { properties: { title: 'Options'   } },
          { properties: { title: 'Points'    } },
          { properties: { title: 'Polylines' } },
        ];
        for (var i = 0; i < (res.polygons || []).length; i++) {
          sheets.push({ properties: { title: i === 0 ? 'Polygons' : 'Polygons' + (i + 1) } });
        }
        var data = { sheets: sheets };
        cb && cb(data);
        return [data, 'success', {}];
      });
    }

    // Sheet values: pull the right slice out of our payload.
    var name = decodeURIComponent(url.split('/values/')[1].split('?')[0]);
    return deferredFrom(dataPromise, function (res) {
      var rows;
      if (name === 'Options')        rows = res.options;
      else if (name === 'Points')    rows = res.points;
      else if (name === 'Polylines') rows = res.polylines;
      else {
        var n = name === 'Polygons' ? 0 : parseInt(name.replace('Polygons', ''), 10) - 1;
        rows = (res.polygons || [])[n] || [];
      }
      var data = { values: rowsToValues(rows) };
      cb && cb(data);
      return [data, 'success', {}];
    });
  };

  // Build a jQuery Deferred that resolves with [data, status, xhr] so that
  // $.when(...).done(function(a,b,c) { a[0] ... }) still works.
  function deferredFrom(promise, project) {
    var d = $.Deferred();
    promise.done(function (res) {
      var args = project(res);
      d.resolve.apply(d, args);
    }).fail(function () { d.reject(); });
    return d.promise();
  }

  function rowsToValues(rows) {
    if (!rows || !rows.length) return [];
    var headers = Object.keys(rows[0]);
    var out = [headers];
    for (var i = 0; i < rows.length; i++) {
      var r = [];
      for (var j = 0; j < headers.length; j++) {
        var v = rows[i][headers[j]];
        r.push(v == null ? '' : String(v));
      }
      out.push(r);
    }
    return out;
  }
})();
