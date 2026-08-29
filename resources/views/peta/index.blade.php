@php
    $settings = $map->settingsMap();
    $title    = $settings['Map Title'] ?? $map->title;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="{{ asset('vendor/peta/favicon.png') }}">
  <title>{{ $title }}</title>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <script src="https://cdn.jsdelivr.net/npm/papaparse@5.3.0/papaparse.min.js"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/js/fontawesome.min.js"></script>

  <script src="https://unpkg.com/leaflet-providers@2.0.0/leaflet-providers.js"></script>

  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/peta/scripts/Leaflet.awesome-markers/dist/leaflet.awesome-markers.css') }}">
  <script src="{{ asset('vendor/peta/scripts/Leaflet.awesome-markers/dist/leaflet.awesome-markers.js') }}"></script>

  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster.layersupport@2.0.1/dist/leaflet.markercluster.layersupport.js"></script>

  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.css" />
  <script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-locatecontrol/0.72.0/L.Control.Locate.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-locatecontrol/0.72.0/L.Control.Locate.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.11/jquery.csv.min.js"></script>

  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/peta/style.css') }}">

  <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
  <div class="loader">Loading...</div>
  <div id="map"></div>
  <table id="maptable" class="display"></table>

  <script>
    var map = L.map('map', {
      attributionControl: false,
      zoomControl: false,
      scrollWheelZoom: false,
      tap: false
    }).setView([-6.2, 106.8], 11);

    // Backend supplies the same payload shape the Google Sheets API returned.
    window.PETA_DATA_URL = @json(route('peta.data', ['slug' => $map->slug]));
    // Kept for the attribution link only — no data fetched from Google.
    window.googleDocURL  = @json($map->google_sheet_id
        ? "https://docs.google.com/spreadsheets/d/{$map->google_sheet_id}/edit"
        : '');
  </script>

  <script src="{{ asset('vendor/peta/scripts/constants.js') }}"></script>
  <script src="{{ asset('vendor/peta/scripts/palette.js') }}"></script>
  <script src="{{ asset('vendor/peta/scripts/polylabel.js') }}"></script>
  {{-- Bridge must run BEFORE map.js — it redirects Sheets/CSV fetches to /api/peta/{slug} --}}
  <script src="{{ asset('vendor/peta/scripts/map-data-bridge.js') }}"></script>
  <script src="{{ asset('vendor/peta/scripts/map.js') }}"></script>
</body>
</html>
